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

use Joomla\Registry\Registry;
use Cobalt\Table\CategoriesTable;;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Categories extends DefaultModel
{
    public $_view = "categories";

    public function store()
    {
        //Load Tables
        $app = \Cobalt\Container::fetch('app');
        $row = $this->getTable('Categories');
        $data = $app->input->getRequest('post');

        //date generation
        $date = date('Y-m-d H:i:s');

        if ( !array_key_exists('id',$data) ) {
            $data['created'] = $date;
        }

        $data['modified'] = $date;

        // Bind the form fields to the table
	    try
	    {
		    $row->save($data);
	    }
	    catch (\Exception $exception)
	    {
		    $this->app->enqueueMessage($exception->getMessage(), 'error');

		    return false;
	    }

        return true;
    }

    public function _buildQuery()
    {
        return $this->db->getQuery(true)
            ->select("c.*")
            ->from("#__notes_categories AS c");
    }

    /**
     * Get list of stages
     * @param  int   $id specific search id
     * @return mixed $results results
     */
    public function getCategories($id=null)
    {
        $query = $this->_buildQuery()
            ->order($this->getState('Categories.filter_order') . ' ' . $this->getState('Categories.filter_order_Dir'));

        return $this->db->setQuery($query)->loadAssocList();

    }

    public function getCategory($id=null)
    {
        $id = $id ? $id : $this->id;

        if ($id > 0) {

            $query = $this->_buildQuery()
                ->order($this->getState('Categories.filter_order') . ' ' . $this->getState('Categories.filter_order_Dir'));

            if ($id) {
                $query->where("c.id=$id");
            }

            return $db->setQuery($query)->loadAssoc();

        } else {
            return (array) $this->getTable('Categories');

        }

    }

    public function populateState()
    {
        //get states
        $app = \Cobalt\Container::fetch('app');
        $filter_order = $app->getUserStateFromRequest('Categories.filter_order','filter_order','c.name');
        $filter_order_Dir = $app->getUserStateFromRequest('Categories.filter_order_Dir','filter_order_Dir','asc');

        $state = new Registry;

        //set states
        $state->set('Categories.filter_order', $filter_order);
        $state->set('Categories.filter_order_Dir', $filter_order_Dir);

        $this->setState($state);
    }

    public function remove($id)
    {
        $query = $this->db->getQuery(true)
            ->delete('#__notes_categories')
            ->where('id = '.(int) $id);

        $this->db->setQuery($query)->execute();
    }

}
