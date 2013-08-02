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

use JFactory;
use Joomla\Registry\Registry;
use Cobalt\Table\CategoriesTable;;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Categories extends DefaultModel
{

    public $_view = "categories";

    /**
     *
     *
     * @access  public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    public function store()
    {
        //Load Tables
        $app = JFactory::getApplication();
        $row = new CategoriesTable;
        $data = $app->input->getRequest( 'post' );

        //date generation
        $date = date('Y-m-d H:i:s');

        if ( !array_key_exists('id',$data) ) {
            $data['created'] = $date;
        }

        $data['modified'] = $date;

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

    public function _buildQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("c.*");
        $query->from("#__notes_categories AS c");

        return $query;

    }

    /**
     * Get list of stages
     * @param  int   $id specific search id
     * @return mixed $results results
     */
    public function getCategories($id=null)
    {
        //database
        $db = JFactory::getDBO();
        $query = $this->_buildQuery();

        //sort
        $query->order($this->getState('Categories.filter_order') . ' ' . $this->getState('Categories.filter_order_Dir'));

        //return results
        $db->setQuery($query);

        return $db->loadAssocList();

    }

    public function getCategory($id=null)
    {
        $id = $id ? $id : $this->id;

        if ($id > 0) {

            //database
            $db = JFactory::getDBO();
            $query = $this->_buildQuery();

            //sort
            $query->order($this->getState('Categories.filter_order') . ' ' . $this->getState('Categories.filter_order_Dir'));
            if ($id) {
                $query->where("c.id=$id");
            }

            //return results
            $db->setQuery($query);

            return $db->loadAssoc();

        } else {
            return (array) JTable::getInstance('categories','Table');

        }

    }

    public function populateState()
    {
        //get states
        $app = JFactory::getApplication();
        $filter_order = $app->getUserStateFromRequest('Categories.filter_order','filter_order','c.name');
        $filter_order_Dir = $app->getUserStateFromRequest('Categories.filter_order_Dir','filter_order_Dir','asc');

        $state = new Registry;

        //set states
        $state->set('Categories.filter_order', $filter_order);
        $state->set('Categories.filter_order_Dir',$filter_order_Dir);

        $this->setState($state);
    }

    public function remove($id)
    {
        //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //delete id
        $query->delete('#__notes_categories')->where('id = '.$id);
        $db->setQuery($query);
        $db->query();
    }

}
