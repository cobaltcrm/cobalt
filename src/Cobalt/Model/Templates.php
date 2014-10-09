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

use Cobalt\Table\TemplateDataTable;
use Cobalt\Table\TemplatesTable;
use Cobalt\Helper\RouteHelper;
use Joomla\Registry\Registry;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Templates extends DefaultModel
{
    public $_view = "templates";

    public function store()
    {
        //Load Tables
        $row = $this->getTable('Templates');
        $data = $this->app->input->post->getArray();

        //date generation
        $date = date('Y-m-d H:i:s');

        if (!array_key_exists('id', $data))
        {
            $data['created'] = $date;
        }

        $data['modified'] = $date;

        //assign default
        //TODO make this a function that updates the database table so there is only ONE default
        $data['default'] = ( array_key_exists('default',$data) AND $data['default'] == 'on' ) ? 1 : 0;

        //generate custom items for template
        $items = array();

        for ($i = 0; $i < count($data['items']); $i++)
        {
            $id   = $data['items'][$i];
            $name = $data['names'][$i];
            $day  = $data['days'][$i];
            $type = $data['types'][$i];
            $items[] = array(   'name'  =>  $name,
                                'id'    =>  $id,
                                'day'   =>  $day,
                                'type'  =>  $type   );
        }

        unset($data['items']);
        unset($data['names']);
        unset($data['days']);
        unset($data['types']);

        // Bind the form fields to the table
        if (!$row->bind($data))
        {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Make sure the record is valid
        if (!$row->check())
        {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Store the web link table to the database
        if (!$row->store())
        {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        //get newly inserted template id
        if (!array_key_exists('id', $data))
        {
            $template_id = $this->db->insertid();
        }
        else
        {
            $template_id = $data['id'];
        }

        //loop through template events and bind the tables to update the database
        //TODO remove ids that are no longer used associated with the template
        for ($i = 0; $i < count($items); $i++)
        {
	        $temp_table = $this->getTable('TemplateData');
            $item = $items[$i];
            $item['template_id'] = $template_id;

            if (!array_key_exists('id',$item) AND $item['id'] == null)
            {
                $data['created'] = $date;
            }

            $data['modified'] = $date;

	        try
	   	    {
	   		    $temp_table->save($item);
	   	    }
	   	    catch (\Exception $exception)
	   	    {
	   		    $this->app->enqueueMessage($exception->getMessage(), 'error');

	   		    return false;
	   	    }
        }

        return true;
    }

    public function _buildQuery()
    {
        $query = $this->db->getQuery(true);

        //query
        $query->select("t.*");
        $query->from("#__templates AS t");

        return $query;

    }

    /**
     * Get list of templates
     * @param  int   $id specific search id
     * @return mixed $results results
     */
    public function getTemplates()
    {
        //database
        $query = $this->_buildQuery();

        /** ------------------------------------------
         * Set query limits/ordering and load results
         */
        $limit = $this->getState($this->_view . '_limit');
        $limitStart = $this->getState($this->_view . '_limitstart');

        if ($limit != 0)
        {
            if ($limitStart >= $this->getTotal())
            {
                $limitStart = 0;
                $limit = 10;
                $limitStart = ($limit != 0) ? (floor($limitStart / $limit) * $limit) : 0;
                $this->state->set($this->_view . '_limit', $limit);
                $this->state->set($this->_view . '_limitstart', $limitStart);
            }
        }

        //sort
        $query->order($this->getState('Templates.filter_order') . ' ' . $this->getState('Templates.filter_order_Dir'));

        //return results
        $this->db->setQuery($query, $limitStart, $limit);
        $results = $this->db->loadAssocList();

        //return data
        return $results;

    }

    public function getTemplate($id = null)
    {
        $id = $id ? $id : $this->id;

        if ($id > 0)
        {
            //database
            $query = $this->_buildQuery();

            $query->where("t.id = $id");

            //return results
            $this->db->setQuery($query);
            $result = $this->db->loadObject();

            //left join essential data if we are searching for a specific template
            // @Jan: what is that?
            // $query = $this->db->getQuery(true);
            // $query->select("t.*");
            // $query->from("#__template_data AS t");
            // $query->where("t.template_id = $id");
            // $this->db->setQuery($query);
            // $result = $this->db->loadObject();

            //return data
            return $result;
        }
        else
        {
            return $this->getTable('Templates');
        }

    }

    public function populateState()
    {
        //get states
        $filter_order = $this->app->getUserStateFromRequest('Templates.filter_order','filter_order','t.name');
        $filter_order_Dir = $this->app->getUserStateFromRequest('Templates.filter_order_Dir','filter_order_Dir','asc');

        $state = new Registry;

        //set states
        $state->set('Templates.filter_order', $filter_order);
        $state->set('Templates.filter_order_Dir',$filter_order_Dir);

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
        $table = $this->getTable('Templates');
        $table->delete($id);
    }

    /**
     * Describe and configure columns for jQuery dataTables here.
     *
     * 'data'      ... column id
     * 'orderable'  ... if the column can be ordered by user or not
     * 'ordering'   ... name of the column in SQL query with table prefix
     * 'sClass'  ... CSS class applied to the column
     * (other settings can be found at dataTable documentation)
     *
     * @return array
     */
    public function getDataTableColumns()
    {
        $columns = array();
        $columns[] = array('data' => 'id', 'orderable' => false, 'sClass' => 'text-center');
        $columns[] = array('data' => 'name', 'ordering' => 't.name');
        $columns[] = array('data' => 'type', 'ordering' => 't.type');
        $columns[] = array('data' => 'created', 'ordering' => 't.created');
        $columns[] = array('data' => 'modified', 'ordering' => 't.modified');
        $columns[] = array('data' => 'default', 'ordering' => 't.default');

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
            $items = $this->getTemplates();
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
                $template .= '<a href="' . RouteHelper::_('index.php?view=templates&layout=edit&id=' . $item->id) . '">' . $item->name . '</a>';
                break;
            case 'default':
                if ($item->default)
                {
                    $template .=  'Default';
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
