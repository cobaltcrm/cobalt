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
use Cobalt\Table\TagsTable;
use Joomla\Registry\Registry;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Tags extends DefaultModel
{
	public function store()
	{
		// Load Tables
		$row  = $this->getTable('Tags');
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

	/**
	 * Get list of stages
	 *
	 * @param   integer  $id  Specific search id
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getTags($id = null)
	{
		$query = $this->db->getQuery(true);

		$query->select('*')
			->from('#__people_tags')
			->order($state->get('Tags.filter_order') . ' ' . $state->get('Tags.filter_order_Dir'));

		if ($id)
		{
			$query->where('t.id = ' . $id);
		}

		return $this->db->setQuery($query)->loadAssocList();
	}

	public function populateState()
	{
		//get states
		$filter_order     = $this->app->getUserStateFromRequest('Tags.filter_order', 'filter_order', 't.name');
		$filter_order_Dir = $this->app->getUserStateFromRequest('Tags.filter_order_Dir', 'filter_order_Dir', 'asc');

		//set states
		$state = new Registry;

		$state->set('Tags.filter_order', $filter_order);
		$state->set('Tags.filter_order_Dir', $filter_order_Dir);

		$this->setState($state);
	}

	public function remove($id)
	{
		$table = $this->getTable('Tags');
		$table->delete($id);
	}
}
