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

use Cobalt\Helper\RouteHelper;
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
	        ->from('#__people_custom AS c');
    }

    /**
	 * Alias for getCustom
	 */
    public function getPeoplecustom()
    {
    	return $this->getCustom();
    }

	/**
	 * Get list of stages
	 *
	 * @return  array
	 */
	public function getCustom()
	{
		$query = $this->_buildQuery();

		/** ------------------------------------------
         * Set query limits/ordering and load results
         */
        $limit = $this->getState($this->_view . '_limit');
        $limitStart = $this->getState($this->_view . '_limitstart');

        if ($limit != 0)
        {
            $query->order($this->getState('Peoplecustom.filter_order') . ' ' . $this->getState('Peoplecustom.filter_order_Dir'));

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

		$results = $this->getDb()->setQuery($query)->loadAssocList();

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

			$query->where("c.id = $id");

			//return results
			$db->setQuery($query);
			$result = $db->loadObject();

			$result->values = json_decode($result->values);

			return $result;
		}

		return $this->getTable('PeopleCustom');
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
		return $this->getTable('PeopleCustom')->delete($id);
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
        $columns[] = array('data' => 'type', 'ordering' => 'c.type', 'sClass' => 'text-center');

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
            $items = $this->getCustom();
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
                $template .= '<a href="'.RouteHelper::_('index.php?view=peoplecustom&layout=edit&id='.$item->id).'">'.$item->name.'</a>';
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
