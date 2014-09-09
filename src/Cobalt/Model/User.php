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

use Cobalt\Table\UserTable;
use JFactory;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\CompanyHelper;
use Cobalt\Helper\DealHelper;
use Cobalt\Helper\PeopleHelper;
use Cobalt\Helper\UsersHelper;

use Joomla\Crypt\Password\Simple;
use Joomla\Language\Text;
use Joomla\Date\Date;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class User extends DefaultModel
{
    /**
     * guest == 1 ... unregistered user
     * guest == 0 ... registered user
     * unregistered by default
     */
    public $guest = 1;

    /**
     * Constructor
     */
    public function __construct($userId = null)
    {
        parent::__construct();
        $app = \Cobalt\Container::fetch('app');
        $this->_view = $app->input->get('view');
        $this->_layout = str_replace('_filter','',$app->input->get('layout'));

        if ($userId)
        {
            $this->load($userId);
        }
    }

    /**
     * Method to load a User object by user id number
     *
     * @param   integer  $id  The user id of the user to load
     *
     * @return  boolean  True on success
     */
    public function load($id)
    {
        $table = new UserTable;

        // Load the UserTable object based on the user id or throw a warning.
        if (!$table->load($id))
        {
            // Reset to guest user
            $this->guest = 1;

            $this->app->enqueueMessage(JText::sprintf('JLIB_USER_ERROR_UNABLE_TO_LOAD_USER', $id), 'error');

            return false;
        }

        $this->setProperties($table->getProperties());

        // The user is no longer a guest
        if ($this->id != 0)
        {
            $this->guest = 0;
        }
        else
        {
            $this->guest = 1;
        }

        return true;
    }

    /**
     * Method to store a record
     *
     * @return boolean True on success
     */
    public function store($data = null)
    {
        //Load Table
        $row = new UserTable;

        if ($data['id']) {
            $row->load($data['id']);
        }

        if (!$data) {
            $data = $this->app->input->getRequest( 'post' );
        }

        if (array_key_exists('fullscreen',$data)) {
            $data['fullscreen'] = !$row->fullscreen;
        }

        //date generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));
        $data['modified'] = $date;

        //update users email address
        if ( array_key_exists('email',$data)) {
            $emails = $data['email'];
            $this->updateEmail($data['id'],$emails);
            unset($data['email']);
        }

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        return true;

    }

    /**
     * Use post data and update a users email address(es) in the users_email_cf db table
     * @param  int   $user_id the user id of the user being updated
     * @param  mixed $emails  an array of new email addresses to be associated with the user
     * @return void
     */
    public function updateEmail($user_id,$emails)
    {
        //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //delete any existing entries
        $query->delete('#__users_email_cf')->where('member_id = '.$user_id);
        $db->setQuery($query);
        $db->query();

        //insert new entries
        $query->clear();
        $values = array();
        foreach ($emails as $email) {
            if ($email != null AND $email != '') {
                if ( !(CobaltHelper::checkEmailName($email))) {
                    $values[] = $user_id.",'".$email."'";
                }
            }
        }
        $query->insert('#__users_email_cf')->columns(array('member_id,email'))->values($values);
         //return
        $db->setQuery($query);
        if ($db->execute()) {
            return true;
        } else {
            print_r($db);
            exit();
        }

    }

    /**
     * Update a users database columns for displaying data on individual pages
     * @param string $loc    the column in the database to update
     * @param string $column the column in the serialized array that will be updated
     */
    public function updateColumns($loc,$column)
    {
        //get user id
        $user_id = UsersHelper::getUserId();

        //get database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //get current array
        $query->select($loc."_columns");
        $query->from("#__users");
        $query->where("id=".$user_id);
        $db->setQuery($query);
        $result = unserialize($db->loadResult());

        //if we have no data assigned grab the defaults
        if ( !is_array($result) ) {
            switch ($loc) {
                case "deals":
                    $result = DealHelper::getDefaultColumnFilters();
                    break;
                case "people":
                    $result = PeopleHelper::getDefaultColumnFilters();
                    break;
                case "companies":
                    $result = CompanyHelper::getDefaultColumnFilters();
                    break;
            }
        }
        //if we do find the value in the array remove it
        if ( in_array($column,$result) ) {
            $key = array_search($column,$result);
            unset($result[$key]);
        } else {
            //if we dont find the value in the array add it
            $result[] = $column;
        }

        //serialize the new array
        $result = serialize($result);

        //update the database
        $query->update('#__users')->set($loc."_columns='".$result."'")->where("id=".$user_id);
        $db->setQuery($query);
        $db->query();

    }

    public function login($credentials, $options)
    {
        $result = $credentials;
        $result['status'] = 0;
        $result['messages'] = array();

        $this->app->triggerEvent('onBeforeUserLogin', array($result, $options));

        if (!isset($credentials['username']) || !$credentials['username'])
        {
            $result['messages'][] = 'COBALT_MSG_MISSING_USERNAME';
        }
        elseif (!isset($credentials['password']) || !$credentials['password'])
        {
            $result['messages'][] = 'COBALT_MSG_MISSING_PASSWORD';
        }
        else
        {
            $userInfo = $this->getUserBy(array('username' => $credentials['username']), array('id', 'password', 'block'));

            if (!$userInfo->password)
            {
                $result['messages'][] = 'COBALT_MSG_USER_NOT_FOUND';
            }
            else
            {
                if ($userInfo->block == 1)
                {
                    $result['messages'][] = 'COBALT_MSG_USER_IS_BLOCKED';
                    $this->app->triggerEvent('onUserAuthorisationFailure', array($result));
                }
                else
                {
                    $authenticate = new Simple;

                    if (!$authenticate->verify($credentials['password'], $userInfo->password))
                    {
                        $result['messages'][] = 'COBALT_MSG_WRONG_PASSWORD';
                    }

                    $result['status'] = 1;
                }
            }
        }

        // OK, the credentials are authenticated and user is authorised.  Lets fire the onLogin event.
        $this->app->triggerEvent('onUserLogin', array($result, $options));

        // Enqueue messages if any
        if (isset($result['messages']) && $result['messages'])
        {
            foreach ($result['messages'] as $msg)
            {
                $this->app->enqueueMessage(Text::_($msg));
            }
        }

        /*
         * If any of the user plugins did not successfully complete the login routine
         * then the whole method fails.
         *
         * Any errors raised should be done in the plugin as this provides the ability
         * to provide much more information about why the routine may have failed.
         */
        if ($result['status'] == 1)
        {
            $this->app->setUser(new User($userInfo->id));

            // Hit the user last visit field
            $this->setLastVisit($userInfo->id);

            return true;
        }

        return false;
    }

    /**
     * Logout user function.
     *
     * @param integer $userid  The user to load and logout
     * @param array   $options Array('clientid' => array of client id's)
     *
     * @return boolean
     */
    public function logout($userid = null, $options = array())
    {
        // Get a user object from the Application.
        $user = $this->app->getUser($userid);

        // Set clientid in the options array if it hasn't been set already.
        if (!isset($options['clientid']))
        {
            $options['clientid'] = $this->app->getClientId();
        }

        // OK, the credentials are built. Lets fire the onLogout event.
        $this->app->triggerEvent('onUserLogout', array($user, $options));

        if (isset($user->id) && $user->id)
        {
            $this->app->clearSession();
            $this->app->setUser(null);
            return true;
        }

        // Trigger onUserLoginFailure Event.
        $this->app->triggerEvent('onUserLogoutFailure', array($user));

        return false;
    }

    public function setLastVisit($id)
    {
        if ($id)
        {
            $date = new Date();
            $this->store(array('id' => $id, 'lastvisitDate' => $date->__toString()));
        }
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

}
