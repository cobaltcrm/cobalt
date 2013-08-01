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

use Cobalt\Table\TemplatesTable;
use JFactory;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Templates extends DefaultModel
{
    public $_view = "templates";

    public function store()
    {
        $app = JFactory::getApplication();

        //Load Tables
        $row = new TemplatesTable;
        $data = $app->input->getRequest( 'post' );

        //date generation
        $date = date('Y-m-d H:i:s');
        if ( !array_key_exists('id',$data) ) {
            $data['created'] = $date;
        }
        $data['modified'] = $date;

        //assign default
        //TODO make this a function that updates the database table so there is only ONE default
        $data['default'] = ( array_key_exists('default',$data) AND $data['default'] == 'on' ) ? 1 : 0;

        //generate custom items for template
        $items = array();
        for ( $i=0; $i<count($data['items']); $i++ ) {
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
        if (!$row->bind($data)) {
            $this->setError($this->_db->getErrorMsg());

            return false;
        }

        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());

            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->_db->getErrorMsg());

            return false;
        }

        //get newly inserted template id
        if ( !array_key_exists('id',$data) ) {
            $template_id = $this->_db->insertid();
        } else {
            $template_id = $data['id'];
        }

        //loop through template events and bind the tables to update the database
        //TODO remove ids that are no longer used associated with the template
        for ( $i=0; $i<count($items); $i++ ) {
            $temp_table = JTable::getInstance('templatedata','Table');
            $item = $items[$i];
            $item['template_id'] = $template_id;
            if ( !array_key_exists('id',$item) AND $item['id'] == null ) {
                $data['created'] = $date;
            }
            $data['modified'] = $date;
            $temp_table->bind($item);
            $temp_table->check();
            $temp_table->store();
        }

        return true;
    }

    public function _buildQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

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
        $db = JFactory::getDBO();
        $query = $this->_buildQuery();

        //sort
        $query->order($this->getState('Templates.filter_order') . ' ' . $this->getState('Templates.filter_order_Dir'));

        //return results
        $db->setQuery($query);
        $results = $db->loadAssocList();

        //return data
        return $results;

    }

    public function getTemplate($id=null)
    {
        $id = $id ? $id : $this->id;

        if ($id > 0) {

            //database
            $db = JFactory::getDBO();
            $query = $this->_buildQuery();

            //sort
            $query->order($this->getState('Templates.filter_order') . ' ' . $this->getState('Templates.filter_order_Dir'));
            $query->where("t.id=$id");

            //return results
            $db->setQuery($query);
            $result = $db->loadAssoc();

            //left join essential data if we are searching for a specific template
            $query = $db->getQuery(true);
            $query->select("t.*");
            $query->from("#__template_data AS t");
            $query->where("t.template_id=$id");
            $db->setQuery($query);
            $result['data'] = $db->loadAssocList();

            //return data
            return $result;

        } else {
            return (array) new TemplatesTable;

        }

    }

    public function populateState()
    {
        //get states
        $app = JFactory::getApplication();
        $filter_order = $app->getUserStateFromRequest('Templates.filter_order','filter_order','t.name');
        $filter_order_Dir = $app->getUserStateFromRequest('Templates.filter_order_Dir','filter_order_Dir','asc');

        $state = new JRegistry();

        //set states
        $state->set('Templates.filter_order', $filter_order);
        $state->set('Templates.filter_order_Dir',$filter_order_Dir);

        $this->setState($state);
    }

    public function remove($id)
    {
        //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //delete id
        $query->delete('#__templates')->where('id = '.$id);
        $db->setQuery($query);
        $db->query();
    }

}
