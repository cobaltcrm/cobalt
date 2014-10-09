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
use Cobalt\Helper\TextHelper;
use Joomla\Registry\Registry;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class FormWizard extends DefaultModel
{
    public $_view = "formwizard";

    public function populateState()
    {
        //get states
        $filter_order = $this->app->getUserStateFromRequest('Formwizard.filter_order','filter_order','f.name');
        $filter_order_Dir = $this->app->getUserStateFromRequest('Formwizard.filter_order_Dir','filter_order_Dir','asc');

        $state = new Registry;

        //set states
        $state->set('Formwizard.filter_order', $filter_order);
        $state->set('Formwizard.filter_order_Dir', $filter_order_Dir);

        // Get pagination request variables
        $limit = $this->app->getUserStateFromRequest($this->_view . '_limit', 'limit', 10);
        $limitstart = $this->app->getUserStateFromRequest($this->_view . '_limitstart', 'limitstart', 0);

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $state->set($this->_view . '_limit', $limit);
        $state->set($this->_view . '_limitstart', $limitstart);

        $this->setState($state);
    }

    public function store()
    {
        //Load Tables
        $row = $this->getTable('FormWizard');
        $data = $this->app->input->post->getArray();

        $user = $this->app->getUser();
        $userId = $user->get('id');

        //date generation
        $date = date('Y-m-d H:i:s');
        $data['modified'] = $date;
        $data['modified_by'] = $userId;

        if (!array_key_exists('id', $data))
        {
            $data['created'] = $date;
            $data['created_by'] = $userId;
        }

        if (array_key_exists('fields', $data))
        {
            $data['fields'] = json_encode($data['fields']);
        }

        if (array_key_exists('html', $data))
        {
            $data['html'] = $_POST['html'];
        }

        //TODO: This poses a problem if the user creates a form and copies HTML immediately on new page before saving
        // they could potentially have an existing ID and then have the wrong code in their copied HTML
        // This would be rare and only if multiple users are simultaneously adding custom forms...

        if (array_key_exists('temp_id', $data))
        {

            $db = $this->getDb();
            $query = $db->getQuery(true);
            $query->select('COUNT(*) as existing, MAX(id) AS greatest')
                    ->from('#__formwizard')
                    ->where('id = '.$data['temp_id']);
            $db->setQuery($query);
            $existing = $db->loadAssoc();

            if ($existing['existing'] > 0)
            {
                $nextId = $existing['greatest']+1;
                $data['html'] = preg_replace('/name="form_id" value="(.*?)"/','name="form_id" value="'.$nextId.'"',$data['html']);
            }
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

        return $row->id;
    }

    public function _buildQuery()
    {
        $db = $this->getDb();
        $query = $db->getQuery(true);
        $query
            ->select("f.*," . $query->concatenate(array('user.first_name', $db->quote(' '), 'user.last_name')) . " AS owner_name")
            ->from("#__formwizard AS f")
            ->leftJoin("#__users AS user ON user.id = f.owner_id");

        return $query;
    }

    public function getForms()
    {
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

        $query->order($this->getState()->get('Formwizard.filter_order') . ' ' . $this->getState()->get('Formwizard.filter_order_Dir'));
        $this->db->setQuery($query, $limitStart, $limit);
        
        $results = $this->db->loadAssocList();

        if (count($results) > 0)
        {
            foreach ($results as $key => $result)
            {
                $results[$key]['fields'] = json_decode($result['fields']);
                $results[$key]['html'] = $result['html'];
            }
        }

        return $results;
    }

    /**
     * Alias for getForm
     */
    public function getFormwizard($id = null)
    {
        return $this->getForm($id);
    }

    public function getForm($formId=null)
    {
        $formId = $formId ? $formId : $this->id;

        if ($formId > 0)
        {
            $query = $this->_buildQuery();
            $db = $this->getDb();
            $query->where("f.id = " . $formId);
            $db->setQuery($query);
            $result = $db->loadObject();
            $result->fields = json_decode($result->fields);
            $result->html = $result->html;

            return $result;
        }
        else
        {
            return $this->getTable('FormWizard');
        }
    }

    public function delete($ids)
    {
	    return $this->getTable('FormWizard')->delete($ids);
    }

    public function getTempFormId()
    {
        $db = $this->getDb();
        $query = $db->getQuery(true);
        $query->select('MAX(id)')
                ->from('#__formwizard');
        $db->setQuery($query);
        $lastId = $db->loadResult();

        return $lastId+1;
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
        $columns[] = array('data' => 'name', 'ordering' => 'f.name');
        $columns[] = array('data' => 'description', 'ordering' => 'f.description');
        $columns[] = array('data' => 'html', 'orderable' => false, 'sClass' => 'text-center');
        $columns[] = array('data' => 'shortcode', 'ordering' => 'f.id', 'sClass' => 'text-center');

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
            $items = $this->getForms();
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
            case 'shortcode':
                $template .= '[cobaltform' . $item->id . ']';
                break;
            case 'html':
                $template .= '<input onclick="selectTextarea(\'html_text_' . $item->id . '\')" type="button" class="btn btn-primary btn-xs" data-toggle="modal" href="#form_' . $item->id . '" id="show_fields_button" value="' . TextHelper::_('COBALT_VIEW_HTML') . '" />';
                $template .= '<div class="modal hide" id="form_' . $item->id . '">';
                $template .= '<div class="modal-header">';
                $template .= '<button type="button" class="close" data-dismiss="modal">×</button>';
                $template .= '<h3>' . TextHelper::_('COBALT_FORM_HTML') . '</h3>';
                $template .= '</div>';
                $template .= '<div class="modal-body">';
                $template .= '<textarea rel="tooltip" data-original-title="' . TextHelper::_('COBALT_FORM_HTML_TOOLTIP') . '" wrap="off" cols="20" rows="15" style="width:500px !important;" onclick="selectTextarea(this);" rel="" id="html_text_' . $item->id . '">' . $item->html . '</textarea>';
                $template .= '</div>';
                $template .= '<div class="modal-footer">';
                $template .= '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>';
                $template .= '</div>';
                $template .= '</div>';
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
