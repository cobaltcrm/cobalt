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

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class PeopleCustom extends DefaultModel
{
    public $_view = "peoplecustom";

	public function store()
	{
		//Load Tables
		$row  = $this->getTable('PeopleCustom');
		$data = $this->app->input->post->getArray();

		//date generation
		$date = date('Y-m-d H:i:s');
		if (!array_key_exists('id', $data))
		{
			$data['created'] = $date;
		}
		$data['modified'] = $date;

		//generate custom values
		$data['values'] = array_key_exists('values', $data) ? json_encode(($data['values'])) : "";

		//filter checkboxes
		if (array_key_exists('required', $data))
		{
			$data['required'] = ($data['required'] == 'on') ? 1 : 0;
		}
		else
		{
			$data['required'] = 0;
		}

		if (array_key_exists('multiple_selections', $data))
		{
			$data['multiple_selections'] = ($data['multiple_selections'] == 'on') ? 1 : 0;
		}
		else
		{
			$data['multiple_selections'] = 0;
		}

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
	    return $this->getDb()->getQuery(true)
	        ->select('c.*')
	        ->from('#__people_custom AS c')
	        ->order($this->getState()->get('Peoplecustom.filter_order') . ' ' . $this->getState()->get('Peoplecustom.filter_order_Dir'));
    }

	/**
	 * Get list of stages
	 *
	 * @return  array
	 */
	public function getCustom()
	{
		$results = $this->getDb()->setQuery($this->_buildQuery())->loadAssocList();

		if (count($results) > 0)
		{
			foreach ($results as $key => $result)
			{
				$results[$key]['values'] = json_decode($result['values']);
			}
		}

		return $results;
	}

	public function getItem($id = null)
	{
		$id = $id ? $id : $this->id;

		if ($id > 0)
		{
			//database
			$db    = $this->getDb();
			$query = $this->_buildQuery();

			$query->where("c.id=$id");

			//return results
			$db->setQuery($query);
			$result = $db->loadAssoc();

			$result['values'] = json_decode($result['values']);

			return $result;
		}

		return (array) $this->getTable('PeopleCustom');
	}

	public function populateState()
	{
		//get states
		$filter_order     = $this->app->getUserStateFromRequest('Peoplecustom.filter_order', 'filter_order', 'c.name');
		$filter_order_Dir = $this->app->getUserStateFromRequest('Peoplecustom.filter_order_Dir', 'filter_order_Dir', 'asc');

		$state = new Registry;

		//set states
		$state->set('Peoplecustom.filter_order', $filter_order);
		$state->set('Peoplecustom.filter_order_Dir', $filter_order_Dir);

		$this->setState($state);
	}

	public function remove($id)
	{
		return $this->getTable('PeopleCustom')->delete($id);
	}
}
