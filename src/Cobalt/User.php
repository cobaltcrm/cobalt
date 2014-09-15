<?php
/**
 * Part of the Joomla Tracker Authentication Package
 *
 * @copyright  Copyright (C) 2012 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Cobalt;

use Cobalt\Table\UserTable;

use Joomla\Database\DatabaseDriver;
use Joomla\Date\Date;
use Joomla\Registry\Registry;

/**
 * Abstract class containing the application user object
 *
 * @since  1.0
 */
class User
{
	/**
	 * Id.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	public $id = 0;

	/**
	 * User name.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $username = '';

	/**
	 * Role type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $role_type = '';

	/**
	 * If a user has special "admin" rights.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $admin = false;

	/**
	 * Exports.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $exports = false;

	/**
	 * User has right to delete.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $can_delete = false;

	/**
	 * Team ID reference.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $team_id = 0;

	/**
	 * First Name.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $first_name = '';

	/**
	 * Last Name.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $last_name = '';

	/**
	 * Modified date.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $modified = '';

	/**
	 * Created date.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $created = '';

	/**
	 * Timezone.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $time_zone = 'America/New_York';

	/**
	 * Date format.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $date_format = 'm/d/y';

	/**
	 * Time format.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $time_format = 'g:i A';

	/**
	 * Daily agenda
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $daily_agenda = false;

	/**
	 * Morning coffee
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $morning_coffee = false;

	/**
	 * Weekly team report
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $weekly_team_report = false;

	/**
	 * Weekly personal report
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $weekly_personal_report = false;

	/**
	 * Reminder notifications
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $reminder_notifications = false;

	/**
	 * SMS number.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $sms_number = '';

	/**
	 * Text messages
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $text_messages = false;

	/**
	 * Home page chart.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $home_page_chart = '';

	/**
	 * Commission rate.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	public $commision_rate = '';

	/**
	 * Deals columns.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $deals_columns = '';

	/**
	 * People columns.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $people_columns = '';

	/**
	 * Companies columns.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $companies_columns = '';

	/**
	 * Full Screen
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $fullscreen = false;

	/**
	 * Color.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $color = '';

	/**
	 * Published
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $published = true;

	/**
	 * Password hash.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $password = '';

	/**
	 * Display Name.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $name = '';

	/**
	 * E-mail.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $email = '';

	/**
	 * User Type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $usertype = '';

	/**
	 * Block user
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $block = false;

	/**
	 * Send email
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	public $sendEmail = false;

	/**
	 * Register date.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $registerDate = '';

	/**
	 * Last visit date.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $lastvisitDate = '';

	/**
	 * Activation.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $activation = '';

	/**
	 * User parameters.
	 *
	 * @var    Registry
	 * @since  1.0
	 */
	public $params = null;

	/**
	 * Language.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $language = '';

	/**
	 * Database object
	 *
	 * @var    DatabaseDriver
	 * @since  1.0
	 */
	protected $database = null;

	/**
     * guest == 1 ... unregistered user
     * guest == 0 ... registered user
     * unregistered by default
     * 
     * @var    boolean
	 * @since  1.0
     */
    public $guest = 1;

    /**
	 * E-mails from users_email_cf table.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $emails = array();

	/**
	 * Constructor.
	 *
	 * @param   DatabaseDriver  $database    The database connector.
	 * @param   integer         $identifier  The primary key of the user to load..
	 *
	 * @since   1.0
	 */
	public function __construct(DatabaseDriver $database, $identifier = 0)
	{
		$this->setDatabase($database);

		// Create the user parameters object.
		$this->params = new Registry;

		// Load the user if it exists
		if ($identifier)
		{
			$this->load($identifier);
		}
	}

	/**
	 * Load data by a given user name.
	 *
	 * @param   string  $userName  The user name
	 *
	 * @return  UserTable
	 *
	 * @since   1.0
	 */
	public function loadByUserName($userName)
	{
		$db = $this->database;

		$table = new UserTable($db);

		$table->loadByUserName($userName);

		if (!$table->id)
		{
			// Register a new user
			$date               = new Date;
			$this->registerDate = $date->format($db->getDateFormat());

			$table->save($this);
		}

		$this->id = $table->id;
		$this->params->loadString($table->params);

		return $this;
	}

	/**
	 * Method to load a User object by user id number.
	 *
	 * @param   mixed  $identifier  The user id of the user to load.
	 *
	 * @return  $this  Method allows chaining
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function load($identifier)
	{
		// Create the user table object
		$table = new UserTable(null, null, $this->database);

		// Load the User object based on the user id or throw a warning.
		if (!$table->load($identifier))
		{
			// Reset to guest user
            $this->guest = 1;
			throw new \RuntimeException('Unable to load the user with id: ' . $identifier);
		}

		// Assuming all is well at this point let's bind the data
		foreach ($table->getFields() as $key => $value)
		{
			if (isset($this->$key) && $key != 'params')
			{
				$this->$key = $table->$key;
			}
		}

		$this->params->loadString($table->params);

		// The user is no longer a guest
        if ($this->id != 0)
        {
            $this->guest = 0;
        }
        else
        {
            $this->guest = 1;
        }

		return $this;
	}

	/**
	 * Check if a user can edit her own item.
	 *
	 * @param   string  $username  The user name of the "owner" of the item to edit.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function canEditOwn($username)
	{
		return ($this->check('editown') && $this->username == $username);
	}

	/**
	 * Check if a user is authorized to perform a given action.
	 *
	 * @param   string  $action  The action to check.
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function check($action)
	{
		if (array_key_exists($action, $this->cleared))
		{
			return $this->cleared[$action] ? true : false;
		}

		try
		{
			$this->authorize($action);

			return true;
		}
		catch (AuthenticationException $e)
		{
			return false;
		}
	}

	/**
	 * Check if the user is authorized to perform a given action.
	 *
	 * @param   string  $action  The action.
	 *
	 * @return  $this  Method allows chaining
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 * @throws  AuthenticationException
	 */
	public function authorize($action)
	{
		if (array_key_exists($action, $this->cleared))
		{
			if (0 == $this->cleared[$action])
			{
				throw new AuthenticationException($this, $action);
			}

			return $this;
		}

		if ($this->isAdmin)
		{
			// "Admin users" are granted all permissions - globally.
			return $this;
		}

		if ('admin' == $action)
		{
			// "Admin action" requested for non "Admin user".
			$this->cleared[$action] = 0;
			throw new AuthenticationException($this, $action);
		}

		if (false == in_array($action, $this->getProject()->getDefaultActions()))
		{
			throw new \InvalidArgumentException('Undefined action: ' . $action);
		}

		/* @type \App\Projects\TrackerProject $project */
		$project = $this->getProject();

		if ($project->getAccessGroups($action, 'Public'))
		{
			// Project has public access for the action.
			$this->cleared[$action] = 1;

			return $this;
		}

		if ($this->id)
		{
			if ($project->getAccessGroups($action, 'User'))
			{
				// Project has User access for the action.
				$this->cleared[$action] = 1;

				return $this;
			}

			// Check if a User has access to a custom group
			$groups = $project->getAccessGroups($action);

			foreach ($groups as $group)
			{
				if (in_array($group, $this->accessGroups))
				{
					// The User is member of the group.
					$this->cleared[$action] = 1;

					return $this;
				}
			}
		}

		$this->cleared[$action] = 0;

		throw new AuthenticationException($this, $action);
	}

	/**
	 * Serialize the object
	 *
	 * @return  string  The string representation of the object or null
	 *
	 * @since   1.0
	 */
	public function serialize()
	{
		$props = array();

		foreach (get_object_vars($this) as $key => $value)
		{
			if (in_array($key, array('cleared', 'database')))
			{
				continue;
			}

			$props[$key] = $value;
		}

		return serialize($props);
	}

	/**
	 * Unserialize the object
	 *
	 * @param   string  $serialized  The serialized string
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function unserialize($serialized)
	{
		$data = unserialize($serialized);

		foreach ($data as $key => $value)
		{
			$this->$key = $value;
		}
	}

	/**
	 * Method to set the database connector.
	 *
	 * @param   DatabaseDriver  $database  The Database connector.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setDatabase(DatabaseDriver $database)
	{
		$this->database = $database;

		return $this;
	}

	public function getTable()
	{
		return new UserTable(null, null, $this->database);
	}

	public function setLastVisit($id = null)
    {

		$table = $this->getTable();

        if (!$id)
        {
        	$id = $this->id;
        }

        if ($id)
        {
        	$date = new Date();
	        $table->store(array('id' => $id, 'lastvisitDate' => $date->__toString()));
        }
        
        return false;
    }

    /**
     * Select a user by conditions.
     *
     * @param array $where    an associative array of WHERE contitions
     * @param array $where    an associative array of SELECT contitions
     * @return object object of founded info about user
     */
    public function getUserBy(array $where, array $select = array('*'))
    {
        $query = $this->db->getQuery(true);
        $query->select(implode(',', $select))->from('#__users');

        foreach ($where as $column => $value)
        {
            $query->where($column . '=' . $this->db->q($value));
        }

        return $this->db->setQuery($query)->loadObject();
    }

    /**
     * Select a user by conditions.
     *
     * @param array $where    an associative array of WHERE contitions
     * @param array $where    an associative array of SELECT contitions
     * @return object object of founded info about user
     */
    public function getEmails()
    {
    	if ($this->emails)
    	{
    		return $this->emails;
    	}

        $query = $this->database->getQuery(true);
        $query->select('email')->from('#__users_email_cf');
        $query->where('member_id=' . (int)$this->get('id'));
        $this->emails = $this->database->setQuery($query)->loadObjectList();
        
        return $this->emails;
    }

	/**
     * Modifies a property of the object, creating it if it does not already exist.
     *
     * @param string $property The name of the property.
     * @param mixed  $value    The value of the property to set.
     *
     * @return mixed Previous value of the property.
     *
     * @since   11.1
     */
    public function set($property, $value = null)
    {
        $previous = isset($this->$property) ? $this->$property : null;
        $this->$property = $value;

        return $previous;
    }

    /**
     * returns a property of the object, even if it's protected.
     *
     * @param string $property The name of the property.
     * @param mixed  $default  Default value if the property doesn't exist.
     *
     * @return mixed The value of the property.
     */
    public function get($property, $default = null)
    {
        if (isset($this->$property))
        {
            return $this->$property;
        }

        return $default;
    }
}
