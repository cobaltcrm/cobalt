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
use Cobalt\Table\StatusesTable;

use Joomla\Registry\Registry;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Statuses extends DefaultModel
{

	public $id = null;

	public $_view = "statuses";

	public function store()
	{
		// Load Tables
		$row  = $this->getTable('Statuses');
		$data = $this->app->input->post->getArray();

		// Date generation
		$date = DateHelper::formatDBDate('now');

		if (!array_key_exists('id', $data))
		{
			$data['created'] = $date;
		}

		$data['modified'] = $date;
		$data['color']    = str_replace("#", "", $data['color']);

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
		$query = $this->db->getQuery(true);

		return $query->select('s.*')
			->from('#__people_status AS s')
			->order($this->getState()->get('Statuses.filter_order') . ' ' . $this->getState()->get('Statuses.filter_order_Dir'));
	}

	/**
	 * Get list of stages
	 *
	 * @param  int $id specific search id
	 *
	 * @return mixed $results results
	 */
	public function getStatuses($id = null)
	{
		return $this->db->setQuery($this->_buildQuery())->loadAssocList();
	}

	public function getStatus($id = null)
	{
		$id = $id ? $id : $this->id;

		if ($id > 0)
		{
			//database
			$query = $this->_buildQuery()
				->where('s.id = ' . $id);

			//return results
			return $this->db->setQuery($query)->loadAssoc();
		}

		return (array) $this->getTable('Statuses');
	}

	public function populateState()
	{
		//get states
		$filter_order     = $this->app->getUserStateFromRequest('Statuses.filter_order', 'filter_order', 's.name');
		$filter_order_Dir = $this->app->getUserStateFromRequest('Statuses.filter_order_Dir', 'filter_order_Dir', 'asc');

		$state = new Registry;

		//set states
		$state->set('Statuses.filter_order', $filter_order);
		$state->set('Statuses.filter_order_Dir', $filter_order_Dir);

		$this->setState($state);
	}

	public function remove($id)
	{
		$table = $this->getTable('Statuses');
		$table->delete($id);
	}
}
