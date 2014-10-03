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
			->from("#__sources AS s")
			->order($this->getState()->get('Sources.filter_order') . ' ' . $this->getState()->get('Sources.filter_order_Dir'));
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
		return $this->db->setQuery($this->_buildQuery())->loadAssocList();
	}

	public function getSource($id = null)
	{
		$id = $id ? $id : $this->id;

		if ($id > 0)
		{
			$query = $this->_buildQuery()
				->where("s.id=$id");

			return $this->db->setQuery($query)->loadAssoc();
		}
		else
		{
			return (array) $this->getTable('Sources');
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

		$this->setState($state);
	}

	public function remove($id)
	{
		$table = $this->getTable('Sources');
		$table->delete($id);
	}
}
