<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Model;

defined('_CEXEC') or die;

use Cobalt\Factory;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseFactory;
use Joomla\Filesystem\Exception\FilesystemException;
use Joomla\Filesystem\File as JFile;
use Joomla\Language\Text;
use Joomla\Model\AbstractModel;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class Install extends AbstractModel
{
    protected $config = null;
    protected $error = null;
    protected $options = null;
    protected $admin = null;

    /**
     * List of Drivers according installation SQL
     *
     * @var    array
     * @since  1.0
     */
    protected $dbDrivers = array('Mysqli', 'Postgresql', 'Sqlsrv');

	/**
	 * Instantiate the model.
	 *
	 * @param   Registry  $state  The model state.
	 *
	 * @since   1.0
	 */
	public function __construct(Registry $state = null)
	{
		parent::__construct($state);

		Text::setLanguage(Factory::getApplication()->getLanguage());
	}

    /**
     * Gets PHP options.
     *
     * @return	array  Array of PHP config options
     *
   	 * @since   1.0
     */
    public function getPhpOptions()
    {
        $options = array();

        // Check the PHP Version.
        $option = new \stdClass;
        $option->label  = Text::_('INSTL_PHP_VERSION') . ' >= 5.3.10';
        $option->state  = version_compare(PHP_VERSION, '5.3.10', '>=');
        $option->notice = null;
        $options[] = $option;

        // Check for magic quotes gpc.
        $option = new \stdClass;
        $option->label  = Text::_('INSTL_MAGIC_QUOTES_GPC');
        $option->state  = (ini_get('magic_quotes_gpc') == false);
        $option->notice = null;
        $options[] = $option;

        // Check for register globals.
        $option = new \stdClass;
        $option->label  = Text::_('INSTL_REGISTER_GLOBALS');
        $option->state  = (ini_get('register_globals') == false);
        $option->notice = null;
        $options[] = $option;

        // Check for zlib support.
        $option = new \stdClass;
        $option->label  = Text::_('INSTL_ZLIB_COMPRESSION_SUPPORT');
        $option->state  = extension_loaded('zlib');
        $option->notice = null;
        $options[] = $option;

        // Check for XML support.
        $option = new \stdClass;
        $option->label  = Text::_('INSTL_XML_SUPPORT');
        $option->state  = extension_loaded('xml');
        $option->notice = null;
        $options[] = $option;

        // Check for database support.
        // We are satisfied if there is at least one database driver available.
        $available = $this->dboDrivers();
        $option = new \stdClass;
        $option->label  = Text::_('INSTL_DATABASE_SUPPORT');
        $option->label .= '<br />(' . implode(', ', $available) . ')';
        $option->state  = count($available);
        $option->notice = null;
        $options[] = $option;

        // Check for mbstring options.
        if (extension_loaded('mbstring'))
        {
            // Check for default MB language.
            $option = new \stdClass;
            $option->label  = Text::_('INSTL_MB_LANGUAGE_IS_DEFAULT');
            $option->state  = (strtolower(ini_get('mbstring.language')) == 'neutral');
            $option->notice = ($option->state) ? null : Text::_('INSTL_NOTICEMBLANGNOTDEFAULT');
            $options[] = $option;

            // Check for MB function overload.
            $option = new \stdClass;
            $option->label  = Text::_('INSTL_MB_STRING_OVERLOAD_OFF');
            $option->state  = (ini_get('mbstring.func_overload') == 0);
            $option->notice = ($option->state) ? null : Text::_('INSTL_NOTICEMBSTRINGOVERLOAD');
            $options[] = $option;
        }

        // Check for a missing native parse_ini_file implementation
        $option = new \stdClass;
        $option->label  = Text::_('INSTL_PARSE_INI_FILE_AVAILABLE');
        $option->state  = $this->getIniParserAvailability();
        $option->notice = null;
        $options[] = $option;

        // Check for missing native json_encode / json_decode support
        $option = new \stdClass;
        $option->label  = Text::_('INSTL_JSON_SUPPORT_AVAILABLE');
        $option->state  = function_exists('json_encode') && function_exists('json_decode');
        $option->notice = null;
        $options[] = $option;

        // Check for configuration file writable.
        $writable = (is_writable(JPATH_CONFIGURATION . '/configuration.php')
            || (!file_exists(JPATH_CONFIGURATION . '/configuration.php') && is_writable(JPATH_ROOT)));

        $option = new \stdClass;
        $option->label  = Text::sprintf('INSTL_WRITABLE', 'configuration.php');
        $option->state  = $writable;
        $option->notice = ($option->state) ? null : Text::_('INSTL_NOTICEYOUCANTINSTALL');
        $options[] = $option;

        return $options;
    }

    public function getIniParserAvailability()
    {
        $disabled_functions = ini_get('disable_functions');

        if (!empty($disabled_functions))
        {
            // Attempt to detect them in the disable_functions black list
            $disabled_functions = explode(',', trim($disabled_functions));
            $number_of_disabled_functions = count($disabled_functions);

            for ($i = 0; $i < $number_of_disabled_functions; $i++)
            {
                $disabled_functions[$i] = trim($disabled_functions[$i]);
            }

            $result = !in_array('parse_ini_string', $disabled_functions);
        }
        else
        {
            // Attempt to detect their existence; even pure PHP implementation of them will trigger a positive response, though.
            $result = function_exists('parse_ini_string');
        }

        return $result;
    }

    /**
     * List of PDO Drivers according with available SQL
     *
     * @return array
     */
    public function dboDrivers()
    {
        return array_intersect($this->dbDrivers, DatabaseDriver::getConnectors());
    }

    /**
     * Run Install Process
     *
     * @param   array  $postData  Input data
     *
     * @return  boolean
     *
   	 * @since   1.0
     * @throws  \RuntimeException
     */
    public function install(array $postData)
    {
	    // Validation post data
	    $check = array_filter(array_values($postData));

	    if (empty($postData['database_password']))
	    {
		    $check[] = '';
	    }

	    if (empty($check) || count($check) < count($postData))
	    {
		    throw new \RuntimeException(Text::_('INSTL_CHECK_REQUIRED_FIELDS'));
	    }

	    if (!$this->uploadLogo())
	    {
		    return false;
	    }

	    $config = array(
		    'sitename' => $postData['site_name'],
	        'host' => $postData['database_host'],
	        'user' => $postData['database_user'],
	        'password' => $postData['database_password'],
	        'db' => $postData['database_name'],
	        'dbprefix' => $postData['database_prefix'],
	        'dbtype' => strtolower($postData['db_drive']),
	        'mailfrom' => $postData['email'],
	        'fromname' => $postData['first_name'] . ' ' . $postData['last_name'],
	        'sendmail' => '/usr/sbin/sendmail',
	        'log_path' => JPATH_ROOT . '/logs',
	        'tmp_path' => JPATH_ROOT . '/tmp',
	        'offset' => 'UTC',
	        'error_reporting' => 'maximum',
	        'debug' => '1',
	        'secret' => $this->genRandomPassword(16),
	        'sef' => '1',
	        'sef_rewrite' => '1',
	        'sef_suffix' => '1',
	        'unicodeslugs' => '0',
	        'language' => 'en-GB'
	    );

	    $this->config = new Registry($config);

	    $file = JPATH_CONFIGURATION . '/configuration.php';

	    $content = $this->config->toString('php', array('class' => 'JConfig'));

		// Determine if the configuration file path is writable.
		if (file_exists($file))
		{
			$canWrite = is_writable($file);
		}
		else
		{
			$canWrite = is_writable(JPATH_CONFIGURATION . '/');
		}

	    if (!$canWrite)
	    {
		    throw new \RuntimeException(Text::_('INSTL_NOTICEYOUCANSTILLINSTALL'));
	    }

	    // Write the config file to the filesystem
	    try
	    {
		    $isWritten = JFile::write($file, $content);
	    }
	    catch (FilesystemException $exception)
	    {
		    throw new \RuntimeException('Could not write configuration file to the filesystem: ' . $exception->getMessage());
	    }

	    if (!$isWritten)
	    {
		    throw new \RuntimeException('Could not write configuration file to the filesystem.');
	    }

	    // Populate database
	    if (!$this->createDb())
	    {
		    throw new \RuntimeException(Text::_('INSTL_ERROR_IMPORT_DATABASE'));
	    }

	    // Populate crm
	    if (!$this->createCrm())
	    {
		    throw new \RuntimeException(Text::_('INSTL_ERROR_IMPORT_DATABASE'));
	    }

	    //create admin user
	    $admin = array(
		    'username'   => $postData['username'],
		    'password'   => $postData['password'],
		    'email'      => $postData['email'],
		    'first_name' => $postData['first_name'],
		    'last_name'  => $postData['last_name']
	    );

	    if (!$this->createAdmin($admin))
	    {
		    throw new \RuntimeException(Text::_('INSTL_ERROR_CREATE_USER'));
	    }

	    return true;
    }

	/**
	 * Checks that the logo file can be uploaded
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 * @todo    Convert to throwing exceptions based on the error code
	 */
    public function canUpload()
    {
	    $file = Factory::getApplication()->input->files->get('logo', array(), 'array');

	    if (!isset($file['error']))
	    {
		    return false;
	    }

	    if ($file['error'] === UPLOAD_ERR_OK)
	    {
		    return true;
	    }

	    return false;
    }

	/**
	 * Uploads the logo file
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
    public function uploadLogo()
    {
	    // Skip this for CLI installations, we'll handle this in the CLI command
	    if (COBALT_CLI)
	    {
		    return true;
	    }

	    $file = Factory::getApplication()->input->files->get('logo', array(), 'array');

	    if (!$this->canUpload())
	    {
		    return false;
	    }

	    $allowedImageTypes = array('image/pjpeg', 'image/jpeg', 'image/jpg', 'image/png', 'image/x-png', 'image/gif');

	    if (!in_array($file['type'], $allowedImageTypes))
	    {
		    $this->setError(Text::_('INSTL_ERROR_LOGO_FILE_TYPE'));

		    return false;
	    }
	    elseif (!JFile::upload($file['tmp_name'], JPATH_UPLOADS . '/logo/' . JFile::makeSafe($file['name'])))
	    {
		    $this->setError(Text::_('INSTL_ERROR_UPLOAD_LOGO'));

		    return false;
	    }

	    return true;
    }

    /**
     * Return Database Object
     *
     * @param $driver
     * @param $host
     * @param $user
     * @param $password
     * @param $database
     * @param string $prefix
     * @param bool $select
     * @return DatabaseDriver
     */
    public function getDbo($driver, $host, $user, $password, $database, $prefix = '', $select = true)
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
            $db = DatabaseDriver::getInstance($options);
        }
        return $db;
    }

	/**
	 * Method to create a new database.
	 *
	 * @param JDatabaseDriver $db JDatabase object.
	 * @param JObject $options JObject coming from "initialise" function to pass user
	 * and database name to database driver.
	 * @param boolean $utf True if the database supports the UTF-8 character set.
	 *
	 * @return boolean True on success.
	 *
	 * @since 1.0
	 */
	public function createDatabase($db, $options, $utf)
	{
		// Build the create database query.
		try
		{
			// Run the create database query.
			$this->__createDatabase($db, $options, $utf);
		}
		catch (\RuntimeException $e)
		{
			// If an error occurred return false.
			return false;
		}
		return true;
	}

    /**
     * Create a new database using information from $options object, obtaining query string
     * from protected member.
     *
     * @param stdClass $options Object used to pass user and database name to database driver.
     * This object must have "db_name" and "db_user" set.
     * @param boolean $utf True if the database supports the UTF-8 character set.
     *
     * @return string The query that creates database
     *
     * @since 12.2
     * @throws RuntimeException
     */
    public function __createDatabase($db, $options, $utf = true)
    {
        if (is_null($options))
        {
            throw new \RuntimeException('$options object must not be null.');
        }
        elseif (empty($options->db_name))
        {
            throw new \RuntimeException('$options object must have db_name set.');
        }
        elseif (empty($options->db_user))
        {
            throw new \RuntimeException('$options object must have db_user set.');
        }
        $db->setQuery($this->getCreateDatabaseQuery($db, $options, $utf));
        return $db->execute();
    }

    /**
     * Return the query string to create new Database.
     * Each database driver, other than MySQL, need to override this member to return correct string.
     *
     * @param stdClass $options Object used to pass user and database name to database driver.
     * This object must have "db_name" and "db_user" set.
     * @param boolean $utf True if the database supports the UTF-8 character set.
     *
     * @return string The query that creates database
     *
     * @since 12.2
     */
    protected function getCreateDatabaseQuery($db, $options, $utf)
    {
        if ($utf)
        {
            return 'CREATE DATABASE ' . $db->quoteName($options->db_name) . ' CHARACTER SET `utf8`';
        }
        return 'CREATE DATABASE ' . $this->quoteName($options->db_name);
    }

    public function createDb()
    {
        $this->options = array(
            'host' => $this->config->get('host'),
            'user' => $this->config->get('user'),
            'password' => $this->config->get('password'),
            'database' => $this->config->get('db'),
            'prefix' => $this->config->get('dbprefix'),
            'select' => false
        );

		$dbFactory = new DatabaseFactory;

		$this->db = $dbFactory->getDriver(
				$this->config->get('dbtype'),
				$this->options
		);

		// Try to select the database
		try
		{
			$this->db->select($this->options['database']);
		}
		catch (\RuntimeException $e)
		{
			// Get database's UTF support
			$utfSupport = $this->db->hasUTFSupport();

            $createDbConfig = array(
                'db_name' => $this->options['database'],
                'db_user' => $this->options['user'],
            );

			// If the database could not be selected, attempt to create it and then select it.
			if ($this->createDatabase($this->db, ArrayHelper::toObject($createDbConfig), $utfSupport))
			{
				$this->db->select($this->options['database']);
			}
			else
			{
				return false;
			}
		}

        $db_driver = $this->config->get('dbtype');
        if ($db_driver == 'mysqli') {
            $db_driver = 'mysql';
        }

        $schema = JPATH_ROOT."/src/meta/sql/".$db_driver."/joomla.sql";

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
                catch (\RuntimeException $e)
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
		$db_driver = $this->config->get('dbtype');
		if ($db_driver == 'mysqli') {
			$db_driver = 'mysql';
		}

		$schema = JPATH_ROOT."/src/meta/sql/crm/" . $db_driver . "/crm.sql";

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
                } catch (\RuntimeException $e) {
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
	    $cryptpass = \Cobalt\Helper\UsersHelper::hashPassword($admin['password']);

        $query = $this->db->getQuery(true);
        $columns = array($this->db->quoteName('id'), $this->db->quoteName('role_type'), $this->db->quoteName('admin'), $this->db->quoteName('name'), $this->db->quoteName('first_name'), $this->db->quoteName('last_name'), $this->db->quoteName('username'),
            $this->db->quoteName('email'), $this->db->quoteName('password'),
            $this->db->quoteName('block'),
            $this->db->quoteName('sendEmail'), $this->db->quoteName('registerDate'),
            $this->db->quoteName('lastvisitDate'), $this->db->quoteName('activation'), $this->db->quoteName('params'));
        $query->insert('#__users', true);
        $query->columns($columns);

        $query->values(
            $this->db->quote($userId) . ', ' . $this->db->quote("exec") . ' , ' . $this->db->quote("1") . ', ' . $this->db->quote($admin['first_name'].' '.$admin['last_name']) . ', ' . $this->db->quote($admin['first_name']). ', ' . $this->db->quote($admin['last_name']) . ', ' . $this->db->quote($admin['username']) . ', ' .
            $this->db->quote($admin['email']) . ', ' . $this->db->quote($cryptpass) . ', ' .
            $this->db->quote('0') . ', ' . $this->db->quote('1') . ', ' . $this->db->quote(date("Y-m-d H:i:s")) . ', ' . $this->db->quote($this->db->getNullDate()) . ', ' .
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

	/**
	 * Method to keep compatibility with Php 5.3.x because session_status doesn't exists with it
	 *
	 * @return boolean
	 */
	protected function is_session_started()
	{
		if ( php_sapi_name() !== 'cli' )
		{
			if ( version_compare(phpversion(), '5.4.0', '>=') )
			{
				return session_status() === PHP_SESSION_ACTIVE ? true : false;
			}
			else
			{
				return session_id() === '' ? false : true;
			}
		}

		return false;
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

		if ($this->is_session_started() === false )
		{
			session_start();
		}

        //JSession::getInstance('none', array())->set('error', $this->error);
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
