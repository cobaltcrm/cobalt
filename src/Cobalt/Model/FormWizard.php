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

use Cobalt\Table\FormWizardTable;
use JFactory;
use Joomla\Registry\Registry;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class FormWizard extends DefaultModel
{
    public $_view = "formwizard";

    public function populateState()
    {
        //get states
        $app = JFactory::getApplication();
        $filter_order = $app->getUserStateFromRequest('Formwizard.filter_order','filter_order','f.name');
        $filter_order_Dir = $app->getUserStateFromRequest('Formwizard.filter_order_Dir','filter_order_Dir','asc');

        $state = new Registry;

        //set states
        $state->set('Formwizard.filter_order', $filter_order);
        $state->set('Formwizard.filter_order_Dir',$filter_order_Dir);

        $this->setState($state);
    }

    public function store()
    {
        $app = JFactory::getApplication();

        //Load Tables
        $row = new FormWizardTable;
        $data = $app->input->getRequest( 'post' );

        $userId = JFactory::getUser()->id;

        //date generation
        $date = date('Y-m-d H:i:s');
        $data['modified'] = $date;
        $data['modified_by'] = $userId;
        if ( !array_key_exists('id',$data) ) {
            $data['created'] = $date;
            $data['created_by'] = $userId;
        }

        if ( array_key_exists('fields',$data) ) {
            $data['fields'] = serialize($data['fields']);
        }

        if ( array_key_exists('html',$data) ) {
            $data['html'] = $_POST['html'];
        }

        //TODO: This poses a problem if the user creates a form and copies HTML immediately on new page before saving
        // they could potentially have an existing ID and then have the wrong code in their copied HTML
        // This would be rare and only if multiple users are simultaneously adding custom forms...

        if (array_key_exists('temp_id',$data) ) {

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('COUNT(*) as existing, MAX(id) AS greatest')
                    ->from('#__formwizard')
                    ->where('id = '.$data['temp_id']);
            $db->setQuery($query);
            $existing = $db->loadAssoc();

            if ($existing['existing'] > 0) {
                $nextId = $existing['greatest']+1;
                $data['html'] = preg_replace('/name="form_id" value="(.*?)"/','name="form_id" value="'.$nextId.'"',$data['html']);
            }

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

    public function _buildQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query
            ->select("f.*,CONCAT(user.first_name,' ',user.last_name) AS owner_name")
            ->from("#__formwizard AS f")
            ->leftJoin("#__users AS user ON user.id = f.owner_id");

        return $query;
    }

    public function getForms()
    {
        $query = $this->_buildQuery();
        $db = JFactory::getDBO();
        $query->order($this->getState('Formwizard.filter_order') . ' ' . $this->getState('Formwizard.filter_order_Dir'));
        $db->setQuery($query);
        $results = $db->loadAssocList();
        if ( count($results) > 0 ) {
            foreach ($results as $key => $result) {
                $results[$key]['fields'] = unserialize($result['fields']);
                $results[$key]['html'] = $result['html'];
            }
        }

        return $results;
    }

    public function getForm($formId=null)
    {
        $formId = $formId ? $formId : $this->id;

        if ($formId > 0) {

            $query = $this->_buildQuery();
            $db = JFactory::getDBO();
            $query->where("f.id=".$formId);
            $db->setQuery($query);
            $result = $db->loadAssoc();
            $result['fields'] = unserialize($result['fields']);
            $result['html'] = $result['html'];

            return $result;

        } else {
            return (array) new FormWizardTable;

        }
    }

    public function delete($ids)
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->delete("#__formwizard");
                if ( is_array($ids) ) {
                    $query->where("id IN(".implode(','.$ids).")");
                } else {
                    $query->where("id=".$ids);
                }
        $db->setQuery($query);
        if ( $db->execute() ) {
            return true;
        } else {
            return false;
        }
    }

    public function getTempFormId()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('MAX(id)')
                ->from('#__formwizard');
        $db->setQuery($query);
        $lastId = $db->loadResult();

        return $lastId+1;
    }

}
