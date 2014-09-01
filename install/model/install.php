<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2012 Cobalt. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_CEXEC') or die;

use Joomla\Application\AbstractApplication;
use Joomla\Database;
use Joomla\Crypt\PasswordInterface;
use Joomla\Crypt\Password;

class crmInstallModel
{
    protected $config = null;
    protected $error = null;
    protected $options = null;
    protected $db = null;
    protected $admin = null;

	public static function getDBO($driver, $host, $user, $password, $database, $prefix, $select = true)
	{
		static $db;
		if (!$db)
		{
			// Build the connection options array.
			$options = array(
					'driver' => $driver,
					'host' => $host,
					'user' => $user,
					'password' => $password,
					'database' => $database,
					'prefix' => $prefix,
					'select' => $select
			);
			// Get a database object.
			$db = Database\DatabaseDriver::getInstance($options);
		}
		return $db;
	}

    public function install()
    {
        session_start();
        $_SESSION['error'] = null;

        $logPath = JPATH_BASE."/logs";
        $tmpPath = JPATH_BASE."/tmp";

        //config registry
        require_once(JPATH_BASE."/install/libraries/config.php");
        $this->config = new crmConfig();

        //set configuration settings
        $this->config->set('sitename',$_POST['site_name']);
        $this->config->set("host",$_POST['database_host']);
        $this->config->set("user",$_POST['database_user']);
        $this->config->set("password",$_POST['database_password']);
        $this->config->set("db",$_POST['database_name']);
        $this->config->set("dbprefix",$_POST['database_prefix']);
        $this->config->set("dbtype","mysql");
        $this->config->set("mailfrom",$_POST['email']);
        $this->config->set("fromname",$_POST['first_name'].' '.$_POST['last_name']);
        $this->config->set("sendmail","/usr/sbin/sendmail");
        $this->config->set("log_path",$logPath);
        $this->config->set("tmp_path",$tmpPath);
        $this->config->set("offset","UTC");
        $this->config->set("error_reporting",'maximum');
        $this->config->set("debug","1");
        $this->config->set("secret",$this->genRandomPassword(16));
        $this->config->set("sef","1");
        $this->config->set("sef_rewrite","1");
        $this->config->set("sef_suffix","1");
        $this->config->set("unicodeslugs","0");
        $this->config->set("language","en-GB");

        //write configuration
        //TODO: needs to check if writable
        $file = JPATH_BASE."/configuration.php";
        if ( !@file_put_contents($file, $this->config->toString()) )
        {
            $this->setError('There was an error creating the configuration.php file. Please check your permissions for directory '.JPATH_BASE);

            return false;
        }

        //populate database
        if ( !$this->createDb() )
        {
            $this->setError('There was an error creating the required database. Please review your ');

            return false;
        }

        //populate crm
        if ( !$this->createCrm() )
        {
            $this->setError('There was a problem creating the CRM database. Please review your database settings.');

            return false;
        }

        //create admin user
        $admin = array(
            'username'		=>$_POST['username'],
            'password'		=>$_POST['password'],
            'email'			=>$_POST['email'],
            'first_name'	=>$_POST['first_name'],
            'last_name'		=>$_POST['last_name']
        );

        if ( !$this->createAdmin($admin) )
        {
            $this->setError('There was a problem creating the CRM administrator user. Please review your database settings.');

            return false;
        }

        //rename-move installation folder
        //TODO: needs to check if writable
        if ( !rename(JPATH_BASE."/install",JPATH_BASE."/_install") )
        {
            $this->setError('There was a problem removing the CRM installation folder. Please remove the folder named "install" or optionally rename it.');

            return false;
        }

        return true;

    }

    public function createDb()
    {
        $this->options = array(
            'host' => $this->config->host,
            'user' => $this->config->user,
            'password' => $this->config->password,
            'database' => $this->config->db,
            // 'port' => $this->config->port,
            // 'socket' => $this->config->socket,
            'prefix' => $this->config->dbprefix
        );

        $dbFactory = new Database\DatabaseFactory;

        $this->db = $dbFactory->getDriver(
            $this->config->dbtype,
            $this->options
        );

        $schema = JPATH_BASE."/install/sql/".$this->config->dbtype."/joomla.sql";

        // Get the contents of the schema file.
        if (!($buffer = file_get_contents($schema)))
        {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Get an array of queries from the schema and process them.
        $queries = $this->_splitQueries($buffer);

        foreach ($queries as $query)
        {
            // Trim any whitespace.
            $query = trim($query);

            // If the query isn't empty and is not a MySQL or PostgreSQL comment, execute it.
            if (!empty($query) && ($query{0} != '#') && ($query{0} != '-'))
            {
                // Execute the query.
                $this->db->setQuery($query);

                try
                {
                    $this->db->execute();
                }
                catch (RuntimeException $e)
                {
                    $this->setError($e->getMessage());
                    $return = false;
                }
            }
        }

        return true;

    }

    public function createCrm()
    {
        $schema = JPATH_BASE."/install/sql/crm/crm.mysql.sql";

        // Get the contents of the schema file.
        if (!($buffer = file_get_contents($schema))) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Get an array of queries from the schema and process them.
        $queries = $this->_splitQueries($buffer);
        foreach ($queries as $query) {
            // Trim any whitespace.
            $query = trim($query);

            // If the query isn't empty and is not a MySQL or PostgreSQL comment, execute it.
            if (!empty($query) && ($query{0} != '#') && ($query{0} != '-')) {
                // Execute the query.
                $this->db->setQuery($query);

                try {
                    $this->db->execute();
                } catch (RuntimeException $e) {
                    $this->setError($e->getMessage());
                    $return = false;
                }
            }
        }

        return true;

    }

    public function createAdmin($admin)
    {
        $query = $this->db->getQuery(true);

        $userId = rand(0,500);

        if(!ini_get('date.timezone'))
        {
            date_default_timezone_set('GMT');
        }

        // Create random salt/password for the admin user
        $passwordHelper = new Password\Simple;
        $cryptpass = $passwordHelper->create($admin['password'], PasswordInterface::MD5);

        $query = $this->db->getQuery(true);
        $columns = array($this->db->quoteName('id'), $this->db->quoteName('admin'), $this->db->quoteName('name'), $this->db->quoteName('first_name'), $this->db->quoteName('last_name'), $this->db->quoteName('username'),
            $this->db->quoteName('email'), $this->db->quoteName('password'),
            $this->db->quoteName('block'),
            $this->db->quoteName('sendEmail'), $this->db->quoteName('registerDate'),
            $this->db->quoteName('lastvisitDate'), $this->db->quoteName('activation'), $this->db->quoteName('params'));
        $query->insert('#__users', true);
        $query->columns($columns);

        $query->values(
            $this->db->quote($userId) . ', ' . $this->db->quote("1") . ', ' . $this->db->quote($admin['first_name'].' '.$admin['last_name']) . ', ' . $this->db->quote($admin['first_name']). ', ' . $this->db->quote($admin['last_name']) . ', ' . $this->db->quote($admin['username']) . ', ' .
            $this->db->quote($admin['email']) . ', ' . $this->db->quote($cryptpass) . ', ' .
            $this->db->quote('0') . ', ' . $this->db->quote('1') . ', ' . $this->db->quote(date("Y-m-d H:i:s")) . ', ' . $this->db->quote("0000-00-00 00:00:00") . ', ' .
            $this->db->quote('0') . ', ' . $this->db->quote(''));

        $this->db->setQuery($query);
        $this->db->execute();

        $columns = array($this->db->quoteName('user_id'),$this->db->quoteName('group_id'));
        $values = $this->db->quote($userId).', '.$this->db->quote("2");
        $query->clear();
        $query->insert("#__user_usergroup_map")->columns($columns)->values($values);
        $this->db->setQuery($query);
        $this->db->execute();

        $this->admin = $admin;

        return true;
    }

    public function getAdmin()
    {
        return $this->admin;
    }

    public function getDb()
    {
        return $this->db;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getRegistry()
    {
        $c = $this->getConfig();
        $config = new JRegistry();
        //set configuration settings
        $config->set('sitename',$c->get("sitename"));
        $config->set("host",$c->get("host"));
        $config->set("user",$c->get("user"));
        $config->set("password",$c->get("password"));
        $config->set("db",$c->get("db"));
        $config->set("dbprefix",$c->get("dbprefix"));
        $config->set("dbtype","mysql");
        $config->set("mailfrom",$c->get("mailfrom"));
        $config->set("fromname",$c->get("fromname"));
        $config->set("sendmail","/usr/sbin/sendmail");
        $config->set("log_path",$c->get("log_path"));
        $config->set("tmp_path",$c->get("tmp_path"));
        $config->set("offset","UTC");
        $config->set("error_reporting",'maximum');
        $config->set("debug","1");
        $config->set("secret",$c->get("secret"));

        return $config;
    }

    protected function _splitQueries($sql)
    {
        $buffer    = array();
        $queries   = array();
        $in_string = false;

        // Trim any whitespace.
        $sql = trim($sql);

        // Remove comment lines.
        $sql = preg_replace("/\n\#[^\n]*/", '', "\n" . $sql);

        // Remove PostgreSQL comment lines.
        $sql = preg_replace("/\n\--[^\n]*/", '', "\n" . $sql);

        // find function
        $funct = explode('CREATE OR REPLACE FUNCTION', $sql);
        // save sql before function and parse it
        $sql = $funct[0];

        // Parse the schema file to break up queries.
        for ($i = 0; $i < strlen($sql) - 1; $i++) {
            if ($sql[$i] == ";" && !$in_string) {
                $queries[] = substr($sql, 0, $i);
                $sql = substr($sql, $i + 1);
                $i = 0;
            }

            if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
                $in_string = false;
            } elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\")) {
                $in_string = $sql[$i];
            }
            if (isset ($buffer[1])) {
                $buffer[0] = $buffer[1];
            }
            $buffer[1] = $sql[$i];
        }

        // If the is anything left over, add it to the queries.
        if (!empty($sql)) {
            $queries[] = $sql;
        }

        // add function part as is
        for ($f = 1; $f < count($funct); $f++) {
            $queries[] = 'CREATE OR REPLACE FUNCTION ' . $funct[$f];
        }

        return $queries;
    }

    public function setError($error)
    {
        if ( is_array($this->error) )
        {
            $this->error[] = $error;
        }
        elseif ($this->error != null)
        {
            $this->error = array($this->error);
            $this->error[] = $error;
        }
        else
        {
            $this->error = $error;
        }

        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }

        $_SESSION['error'] = $this->error;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * Generate a random password
     *
     * @static
     * @param   int     $length Length of the password to generate
     * @return  string          Random Password
     * @since   11.1
     */
    public static function genRandomPassword($length = 8)
    {
        $salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $len = strlen($salt);
        $makepass = '';

        $stat = @stat(__FILE__);
        if (empty($stat) || !is_array($stat)) $stat = array(php_uname());

        mt_srand(crc32(microtime() . implode('|', $stat)));

        for ($i = 0; $i < $length; $i ++) {
            $makepass .= $salt[mt_rand(0, $len -1)];
        }

        return $makepass;
    }

}
