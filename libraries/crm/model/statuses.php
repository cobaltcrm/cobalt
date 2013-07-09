<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CobaltModelStatuses extends CobaltModelDefault
{

    var $id = null;
    var $_view = "statuses";

    /**
     *
     *
     * @access  public
     * @return  void
     */
    function __construct()
    {
        parent::__construct();

    }

    function store()
    {
        $app = JFactory::getApplication();

        //Load Tables
        $row = JTable::getInstance('statuses','Table');
        $data = $app->input->getRequest( 'post' );

        //date generation
        $date = date('Y-m-d H:i:s');

        if ( !array_key_exists('id',$data) ){
            $data['created'] = $date;
        }

        $data['modified'] = $date;
        $data['color'] = str_replace("#","",$data['color']);

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

    function _buildQuery(){
        //database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("s.*");
        $query->from("#__people_status AS s");

        //sort
        $query->order($this->getState('Statuses.filter_order') . ' ' . $this->getState('Statuses.filter_order_Dir'));
        return $query;
    }


    /**
     * Get list of stages
     * @param int $id specific search id
     * @return mixed $results results
     */
    function getStatuses($id=null){

        //database
        $db = JFactory::getDBO();
        $query = $this->_buildQuery();

        //return results
        $db->setQuery($query);
        return $db->loadAssocList();

    }

    function getStatus($id=null){
        $id = $id ? $id : $this->id;

        if ( $id > 0 ){

            //database
            $db = JFactory::getDBO();
            $query = $this->_buildQuery();

            $query->where("s.id=$id");

            //return results
            $db->setQuery($query);
            return $db->loadAssoc();

        }else{

            return (array)JTable::getInstance("statuses","Table");

        }


    }

    function populateState(){
        //get states
        $app = JFactory::getApplication();
        $filter_order = $app->getUserStateFromRequest('Statuses.filter_order','filter_order','s.name');
        $filter_order_Dir = $app->getUserStateFromRequest('Statuses.filter_order_Dir','filter_order_Dir','asc');

        $state = new JRegistry();

        //set states
        $state->set('Statuses.filter_order', $filter_order);
        $state->set('Statuses.filter_order_Dir',$filter_order_Dir);

        $this->setState($state);
    }

    function remove($id){
        //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //delete id
        $query->delete('#__people_status')->where('id = '.$id);
        $db->setQuery($query);
        $db->query();
    }



}