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

use Cobalt\Helper\DateHelper;
use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Table\SourcesTable;
use Joomla\Registry\Registry;

// no direct access
defined('_CEXEC') or die('Restricted access');

class Sources extends DefaultModel
{

	public $_view = "sources";

	public function store()
	{
		// Load Tables
		$row  = $this->getTable('Sources');
		$data = $this->app->input->post->getArray();

		// Date generation
		$date = DateHelper::formatDBDate('now');

		if (!array_key_exists('id', $data))
		{
			$data['created'] = $date;
		}

		$data['modified'] = $date;

		// Bind the form fields to the table
		try
		{
			$row->save($data);
		}
		catch (\InvalidArgumentException $exception)
		{
			$this->app->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return true;
	}

	public function _buildQuery()
	{
		return $this->db->getQuery(true)
			->select("s.*")
			->from("#__sources AS s");
	}

	/**
	 * Get list of stages
	 *
	 * @param  int $id specific search id
	 *
	 * @return mixed $results results
	 */
	public function getSources()
	{
		$query = $this->_buildQuery();

		/** ------------------------------------------
         * Set query limits/ordering and load results
         */
        $limit = $this->getState($this->_view . '_limit');
        $limitStart = $this->getState($this->_view . '_limitstart');

		if ($limit != 0)
        {
            $query->order($this->getState('Sources.filter_order') . ' ' . $this->getState('Sources.filter_order_Dir'));

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

	public function getSource($id = null)
	{
		$id = $id ? $id : $this->id;

		if ($id > 0)
		{
			$query = $this->_buildQuery()
				->where("s.id = $id");

			return $this->db->setQuery($query)->loadObject();
		}
		else
		{
			return $this->getTable('Sources');
		}
	}

	public function populateState()
	{
		//get states
		$filter_order     = $this->app->getUserStateFromRequest('Sources.filter_order', 'filter_order', 's.name');
		$filter_order_Dir = $this->app->getUserStateFromRequest('Sources.filter_order_Dir', 'filter_order_Dir', 'asc');

		$state = new Registry;

		//set states
		$state->set('Sources.filter_order', $filter_order);
		$state->set('Sources.filter_order_Dir', $filter_order_Dir);

		// Get pagination request variables
        $limit = $this->app->getUserStateFromRequest($this->_view . '_limit', 'limit', 10);
        $limitstart = $this->app->getUserStateFromRequest($this->_view . '_limitstart', 'limitstart', 0);

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $state->set($this->_view . '_limit', $limit);
        $state->set($this->_view . '_limitstart', $limitstart);

		$this->setState($state);
	}

	public function delete($id)
	{
		$table = $this->getTable('Sources');
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
        $columns[] = array('data' => 'name', 'ordering' => 's.name');
        $columns[] = array('data' => 'cost', 'ordering' => 's.cost', 'sClass' => 'text-center');
        $columns[] = array('data' => 'type', 'ordering' => 's.type', 'sClass' => 'text-center');

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
            $items = $this->getSources();
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
                $template .= '<a href="'.RouteHelper::_('index.php?view=sources&layout=edit&id='.$item->id).'">'.$item->name.'</a>';
                break;
            case 'cost':
                $template .= TextHelper::price($item->cost);
                break;
            case 'type':
            	$template .= ($item->type == "per") ? "Per Lead/Deal" : "Flat Fee";
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
