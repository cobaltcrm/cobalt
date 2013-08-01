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

use JTable;
use JFactory;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\CompanyHelper;
use Cobalt\Helper\DealHelper;
use Cobalt\Helper\PeopleHelper;
use Cobalt\Helper\UsersHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class User extends DefaultModel
{
    /**
     * Method to store a record
     *
     * @return boolean True on success
     */
    public function store($data=null)
    {
        $app = JFactory::getApplication();

        //Load Tables
        $row = JTable::getInstance('user','Table');

        if ($data['id']) {
            $row->load($data['id']);
        }

        if (!$data) {
            $data = $app->input->getRequest( 'post' );
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
            $this->setError($this->_db->getErrorMsg());

            return false;
        }

        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());

            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->_db->getErrorMsg());

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
        if ($db->query()) {
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

}
