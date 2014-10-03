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

use Cobalt\Helper\TextHelper;
use Cobalt\Table\UserTable;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\CompanyHelper;
use Cobalt\Helper\DealHelper;
use Cobalt\Helper\PeopleHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\CobaltHelper;

use Joomla\Date\Date;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class User extends DefaultModel
{

	protected $_view;
	protected $_layout;

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
        $table = $this->getTable('User');

        // Load the UserTable object based on the user id or throw a warning.
        if (!$table->load($id))
        {
            $this->app->enqueueMessage(TextHelper::sprintf('JLIB_USER_ERROR_UNABLE_TO_LOAD_USER', $id), 'error');

            return false;
        }

        $this->setProperties($table->getProperties());

        unset($this->password);

        return true;
    }

    /**
     * Alias for load method.
     * This is method required for Save controller so each model has get{item} method.
     *
     * @param integer $id
     * @return UserModel
     */
    public function getUser($id)
    {
        $this->load($id);
        return $this;
    }

    /**
     * Method to store a record
     *
     * @return boolean True on success
     */
    public function store($data = null)
    {
        if (!$data)
        {
            $data = $this->app->input->post->getArray();
        }

        //Load Table
        $row = $this->getTable('User');

        if (isset($data['id']) && $data['id'])
        {
            $row->load($data['id']);
        }

        if (isset($data['fullscreen']))
        {
            $data['fullscreen'] = !$row->fullscreen;
        }

        if (isset($data['password']) && $data['password'])
        {
	        $data['password'] = UsersHelper::hashPassword($data['password']);
        }

        //date generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));
        $data['modified'] = $date;

        // Bind the form fields to the table
        if (!$row->bind($data))
        {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Make sure the record is valid
        if (!$row->check())
        {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Store the web link table to the database
        if (!$row->store())
        {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        //update users email address
        if (array_key_exists('email', $data))
        {
            $this->updateEmail($row->id, $data['email']);
        }

        if (isset($data['team_name']) && $data['team_name'])
        {
            $teamModel = new Teams;
            $teamModel->createTeam($row->id, $data['team_name']);
        }

        $this->app->refreshUser();

        return $row->id;

    }

    /**
     * Use post data and update a users email address(es) in the users_email_cf db table
     * @param  int   $user_id the user id of the user being updated
     * @param  mixed $emails  an array of new email addresses to be associated with the user
     * @return void
     */
    public function updateEmail($user_id, $emails)
    {
        $query = $this->db->getQuery(true);
        $retults = array();

        //delete any existing entries
        $query->delete('#__users_email_cf')->where('member_id = '.$user_id);
        $retults[] = $this->db->setQuery($query)->execute();

        //insert new entries
        if (is_array($emails))
        {
            foreach ($emails as $email)
            {
                if ($email)
                {
                    $emailO = new \stdClass();
                    $emailO->member_id = $user_id;

                    if (!(CobaltHelper::checkEmailName($email)))
                    {
                        $emailO->email = $email;
                        $retults[] = $this->db->insertObject('#__users_email_cf', $emailO);
                    }
                }
            }
        }


        if (in_array(false, $retults))
        {
            return false;
        }

        return true;
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

        $query = $this->db->getQuery(true);

        //get current array
        $query->select($loc."_columns");
        $query->from("#__users");
        $query->where("id=".$user_id);
        $this->db->setQuery($query);
        $result = unserialize($this->db->loadResult());

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
        $this->db->setQuery($query);
        $this->db->execute();

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
