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

use Cobalt\Helper\TextHelper;
use Cobalt\Table\AbstractTable;
use Joomla\Model\AbstractDatabaseModel;

class Autocomplete extends DefaultModel
{
    /**
     * Table Name
     *
     * @var string
     */
    private $_object = '';

    /**
     * Table Class Name
     *
     * @var string
     */
    private $class_name = '';

    /**
     * @var AbstractTable
     */
    private $table;

    /**
     * Define a Object Table
     *
     * @param $object
     */
    public function setObject($object)
    {
        $this->_object = $object;
        $this->class_name = sprintf('Cobalt\\Table\\%sTable', $this->_object);
    }

    /**
     * Check if Table Class Exists
     *
     * @return bool
     */
    public function hasAutocomplete()
    {
        return class_exists($this->class_name);
    }

    /**
     * Convert Database Types filter values in JInput
     *
     * @return array
     */
    public function getTableFilters()
    {
        $filters = array();
        foreach ($this->table->getFields() as $field) {
            $field_name = $field->Field;
            $field_type = substr($field->Type,0,strpos($field->Type,'('));
            //transform database type to JInput filter type
            switch ($field_type) {
                case 'tinyint':
                case 'int':
                    $field_type = 'int';
                    break;
                default:
                    $field_type = 'string';
                    break;
            }
            $filters[$field_name] = $field_type;
        }

        return $filters;
    }

    /**
     * Filter condition from a object by field name via $_GET
     *
     * @return array
     */
    public function getRequestFilters()
    {
        return array_filter($this->app->input->getArray($this->getTableFilters()));
    }

    /**
     * Search table object
     *
     * @param array $fields
     * @return mixed
     */
    public function getData(array $fields)
    {
        $this->table = new $this->class_name();


        if (empty($fields)) {
            $fields = array(
                $this->table->getKeyName()
            );
        }

        $query = $this->db->getQuery(true);

        // Initialise the query.
        $query = $this->db->getQuery(true);
        $query->select(implode(',', $fields));
        $query->from($this->db->quoteName($this->table->getTableName()));

        foreach ($this->getRequestFilters() as $field => $value) {
            $query->where($this->db->quoteName($field) . ' = ' . $this->db->quote($value));
        }

        $this->db->setQuery($query);
        if (count($fields) == 1) {
            $rows = $this->db->loadColumn();
        } else {
            $rows = $this->db->loadObjectList();
        }

        return $rows;
    }


}