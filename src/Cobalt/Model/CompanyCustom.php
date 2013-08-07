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

use Cobalt\Table\CompanyCustomTable;
use Joomla\Registry\Registry;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class CompanyCustom extends DefaultModel
{
    public $id = null;
    public $_view = "companycustom";

    public function store()
    {
        $app = \Cobalt\Container::get('app');

        //Load Tables
        $row = new CompanyCustomTable;
        $data = $app->input->getRequest( 'post' );

        //date generation
        $date = date('Y-m-d H:i:s');
        if ( !array_key_exists('id',$data) ) {
            $data['created'] = $date;
        }
        $data['modified'] = $date;

        //generate custom values
        $data['values'] = array_key_exists('values',$data) ? json_encode(($data['values'])) : "";

        //filter checkboxes
        if ( array_key_exists('required',$data) ) {
            $data['required'] = ($data['required'] == 'on') ? 1 : 0;
        } else {
            $data['required'] = 0;
        }
        if ( array_key_exists('multiple_selections',$data) ) {
            $data['multiple_selections'] = ($data['multiple_selections'] == 'on') ? 1 : 0;
        } else {
            $data['multiple_selections'] = 0;
        }

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        return true;
    }

    public function _buildQuery()
    {
        return $this->db->getQuery(true)
            ->select("c.*")
            ->from("#__company_custom AS c")
            ->order($this->getState('Companycustom.filter_order') . ' ' . $this->getState('Companycustom.filter_order_Dir'));
    }

    /**
     * Get list of stages
     * @param  int   $id specific search id
     * @return mixed $results results
     */
    public function getCustom($id = null)
    {
        $query = $this->_buildQuery();

        if ($id) {
            $query->where("c.id=$id");
        }

        $results = $this->db->setQuery($query)->loadAssocList();

        if ( count ( $results ) > 0 ) {
            foreach ($results as $key => $result) {
                $results[$key]['values'] = json_decode($result['values']);
            }
        }

        return $results;
    }

    public function getItem($id = null)
    {
        $id = $id ? $id : $this->id;

        if ($id > 0) {

            $query = $this->_buildQuery();

            $query->where("c.id=$id");

            $result = $this->db->setQuery($query)->loadAssoc();

            $result['values'] = json_decode($result['values']);

            return $result;

        } else {
            return (array) new CompanyCustomTable;
        }

    }

    public function populateState()
    {
        //get states
        $app = \Cobalt\Container::get('app');
        $filter_order = $app->getUserStateFromRequest('Companycustom.filter_order','filter_order','c.name');
        $filter_order_Dir = $app->getUserStateFromRequest('Companycustom.filter_order_Dir','filter_order_Dir','asc');

        $state = new Registry;

        //set states
        $state->set('Companycustom.filter_order', $filter_order);
        $state->set('Companycustom.filter_order_Dir',$filter_order_Dir);

        $this->setState($state);
    }

    public function remove($id)
    {
        $query = $this->db->getQuery(true)
            ->delete('#__company_custom')
            ->where('id = '.(int) $id);

        $this->db->setQuery($query)->execute();
    }

}
