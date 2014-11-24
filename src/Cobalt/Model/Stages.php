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
use Cobalt\Table\StagesTable;
use Joomla\Registry\Registry;

// no direct access
defined('_CEXEC') or die('Restricted access');

class Stages extends DefaultModel
{
	public $_view = "stages";

	public function store()
	{
		// Load Tables
		$row  = $this->getTable('Stages');
		$data = $this->app->input->post->getArray();

		// Date generation
		$date = DateHelper::formatDBDate('now');

		if (!array_key_exists('id', $data))
		{
			$data['created'] = $date;
		}

		$data['modified'] = $date;
		$data['color']	= str_replace("#", "", $data['color']);
		$data['won']	  = array_key_exists('won', $data) ? 1 : 0;

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
			->from("#__stages AS s")
			->order($this->getState()->get('Stages.filter_order') . ' ' . $this->getState()->get('Stages.filter_order_Dir'));
	}

	/**
	 * Get list of stages
	 *
	 * @param  int $id specific search id
	 *
	 * @return mixed $results results
	 */
	public function getStages($id = null)
	{
		$query = $this->_buildQuery();

		/** ------------------------------------------
		 * Set query limits/ordering and load results
		 */
		$limit = $this->getState($this->_view . '_limit');
		$limitStart = $this->getState($this->_view . '_limitstart');

		if ($limit != 0)
		{
			$query->order($this->getState('Stages.filter_order') . ' ' . $this->getState('Stages.filter_order_Dir'));

			if ($limitStart >= $this->getTotal())
			{
				$limitStart = 0;
				$limit = 10;
				$limitStart = ($limit != 0) ? (floor($limitStart / $limit) * $limit) : 0;
				$this->state->set($this->_view . '_limit', $limit);
				$this->state->set($this->_view . '_limitstart', $limitStart);
			}
		}

		return $this->db->setQuery($query, $limitStart, $limit)->loadAssocList();
	}

	public function getStage($id = null)
	{
		$id = $id ? $id : $this->id;

		if ($id > 0 && $id != null)
		{
			$query = $this->_buildQuery()
				->where("id=" . $id);

			return $this->db->setQuery($query)->loadObject();
		}

		return $this->getTable('Stages');
	}

	public function populateState()
	{
		//get states
		$filter_order	 = $this->app->getUserStateFromRequest('Stages.filter_order', 'filter_order', 's.name');
		$filter_order_Dir = $this->app->getUserStateFromRequest('Stages.filter_order_Dir', 'filter_order_Dir', 'asc');

		$state = new Registry;

		//set states
		$state->set('Stages.filter_order', $filter_order);
		$state->set('Stages.filter_order_Dir', $filter_order_Dir);

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
		$table = $this->getTable('Stages');
		$table->delete($id);
	}

	/**
	 * Describe and configure columns for jQuery dataTables here.
	 *
	 * 'data'	   ... column id
	 * 'orderable'  ... if the column can be ordered by user or not
	 * 'ordering'   ... name of the column in SQL query with table prefix
	 * 'sClass'	 ... CSS class applied to the column
	 * (other settings can be found at dataTable documentation)
	 *
	 * @return array
	 */
	public function getDataTableColumns()
	{
		$columns = array();
		$columns[] = array('data' => 'id', 'orderable' => false, 'sClass' => 'text-center');
		$columns[] = array('data' => 'name', 'ordering' => 's.name');
		$columns[] = array('data' => 'color', 'ordering' => 's.color', 'sClass' => 'text-center');
		$columns[] = array('data' => 'percent', 'ordering' => 's.percent', 'sClass' => 'text-center');
		$columns[] = array('data' => 'won', 'ordering' => 's.won', 'sClass' => 'text-center');

		return $columns;
	}

	/**
	 * Method transforms items to the format jQuery dataTables needs.
	 * Algorithm is available in parent method, just pass items array.
	 *
	 * @param   array  $items  of object of items from the database
	 *
	 * @return  array  in format dataTables requires
	 */
	public function getDataTableItems($items = array())
	{
		if (!$items)
		{
			$items = $this->getStages();
		}

		return parent::getDataTableItems($items);
	}

	/**
	 * Prepare HTML field templates for each dataTable column.
	 *
	 * @param   string  $column  name
	 * @param   object  $item    of item
	 *
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
				$template .= '<a href="' . RouteHelper::_('index.php?view=stages&layout=edit&id=' . $item->id) . '">' . $item->name . '</a>';
				break;
			case 'color':
				$template .= '<i class="glyphicon glyphicon-bookmark" style="color:#' . $item->color . '"></i>';
				break;
			case 'won':
				$template .= '';

				if ($item->won)
				{
					$template .= '<i class="glyphicon glyphicon-ok"></i>';
				}

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
