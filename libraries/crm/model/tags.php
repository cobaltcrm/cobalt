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

class CobaltModelTags extends JModelBase
{
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
        //Load Tables
        $row = JTable::getInstance('tags','Table');
        $data = $app->input->getRequest( 'post' );

        //date generation
        $date = date('Y-m-d H:i:s');

        if ( !array_key_exists('id',$data) ){
            $data['created'] = $date;
        }

        $data['modified'] = $date;

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
     * Get list of stages
     * @param int $id specific search id
     * @return mixed $results results
     */
    function getTags($id=null){

        //database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("t.*");
        $query->from("#__people_tags AS t");

        //sort
        $query->order($this->getState('Tags.filter_order') . ' ' . $this->getState('Tags.filter_order_Dir'));
        if( $id ){
            $query->where("t.id=$id");
        }

        //return results
        $db->setQuery($query);
        return $db->loadAssocList();

    }

    function populateState(){
        //get states
        $app = JFactory::getApplication();
        $filter_order = $app->getUserStateFromRequest('Tags.filter_order','filter_order','t.name');
        $filter_order_Dir = $app->getUserStateFromRequest('Tags.filter_order_Dir','filter_order_Dir','asc');

        //set states
        $this->setState('Tags.filter_order', $filter_order);
        $this->setState('Tags.filter_order_Dir',$filter_order_Dir);
    }

    function remove($id){
        //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //delete id
        $query->delete('#__people_tags')->where('id = '.$id);
        $db->setQuery($query);
        $db->query();
    }



}