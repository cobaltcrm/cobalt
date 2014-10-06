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
use Cobalt\Table\CategoriesTable;
use Cobalt\Helper\RouteHelper;

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
        $data = $this->app->input->post->getArray();

        //date generation
        $date = date('Y-m-d H:i:s');

        if (!$row->id)
        {
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
    public function getCategories($id = null)
    {
        $query = $this->_buildQuery();

        $view = $this->app->input->get('view');
        $layout = $this->app->input->get('layout');

        /** ------------------------------------------
         * Set query limits/ordering and load results
         */
        $limit = $this->getState($this->_view . '_limit');
        $limitStart = $this->getState($this->_view . '_limitstart');

        if ($limit != 0)
        {
            $query->order($this->getState('Categories.filter_order') . ' ' . $this->getState('Categories.filter_order_Dir'));

            if ($limitStart >= $this->getTotal())
            {
                $limitStart = 0;
                $limit = 10;
                $limitStart = ($limit != 0) ? (floor($limitStart / $limit) * $limit) : 0;
                $this->state->set($this->_view . '_limit', $limit);
                $this->state->set($this->_view . '_limitstart', $limitStart);
            }

            $query .= " LIMIT ".($limit)." OFFSET ".($limitStart);
        }

        return $this->db->setQuery($query)->loadAssocList();

    }

    public function getCategory($id = null)
    {
        $id = $id ? $id : $this->id;

        if ($id > 0)
        {
            $query = $this->_buildQuery()
                ->order($this->getState('Categories.filter_order') . ' ' . $this->getState('Categories.filter_order_Dir'));

            if ($id)
            {
                $query->where("c.id = $id");
            }

            return $this->db->setQuery($query)->loadObject();

        }
        else
        {
            return $this->getTable('Categories');

        }
    }

    public function populateState()
    {
        //get states
        $app = \Cobalt\Container::fetch('app');
        $filter_order = $app->getUserStateFromRequest('Categories.filter_order', 'filter_order', 'c.name');
        $filter_order_Dir = $app->getUserStateFromRequest('Categories.filter_order_Dir', 'filter_order_Dir', 'asc');

        $state = new Registry;

        //set states
        $state->set('Categories.filter_order', $filter_order);
        $state->set('Categories.filter_order_Dir', $filter_order_Dir);

        // Get pagination request variables
        $limit = $app->getUserStateFromRequest($this->_view . '_limit', 'limit', 10);
        $limitstart = $app->getUserStateFromRequest($this->_view . '_limitstart', 'limitstart', 0);

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $state->set($this->_view . '_limit', $limit);
        $state->set($this->_view . '_limitstart', $limitstart);

        $this->setState($state);
    }

    public function delete($id)
    {
        $table = $this->getTable('Categories');
        $table->delete($id);
    }

    /**
     * Describe and configure columns for jQuery dataTables here.
     *
     * 'data'       ... column id
     * 'orderable'  ... if the column can be ordered by user or not
     * 'ordering'   ... name of the column in SQL query with table prefix
     * 'sClass'     ... CSS class applied to the column
     * (other settings can be found at dataTable documentation)
     *
     * @return array
     */
    public function getDataTableColumns()
    {
        $columns = array();
        $columns[] = array('data' => 'id', 'orderable' => false, 'sClass' => 'text-center');
        $columns[] = array('data' => 'name', 'ordering' => 'c.name');

        return $columns;
    }

    /**
     * Method transforms items to the format jQuery dataTables needs.
     * Algorithm is available in parent method, just pass items array.
     *
     * @param   array of object of items from the database
     * @return  array in format dataTables requires
     */
    public function getDataTableItems($items = array())
    {
        if (!$items)
        {
            $items = $this->getCategories();
        }

        return parent::getDataTableItems($items);
    }

    /**
     * Prepare HTML field templates for each dataTable column.
     *
     * @param   string column name
     * @param   object of item
     * @return  string HTML template for propper field
     */
    public function getDataTableFieldTemplate($column, $item)
    {
        $template = '';

        switch ($column)
        {
            case 'id':
                $template .= '<input type="checkbox" class="export" name="ids[]" value="' . $item->id . '" />';
                break;
            case 'name':
                $template .= '<a href="'.RouteHelper::_('index.php?view=categories&layout=edit&id='.$item->id).'">'.$item->name.'</a>';
                break;
            default:
                if (isset($column) && isset($item->{$column}))
                {
                    $template = $item->{$column};
                }
                else
                {
                    $template = '';
                }
                break;
        }

        return $template;
    }

}
