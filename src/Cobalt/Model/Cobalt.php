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
use Cobalt\Pagination;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Cobalt extends DefaultModel
{
    public $view = null;
    public $_model = null;

    /**
     *
     *
     * @access	public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->getListLimits();
        $this->view = $this->app->input->get('view');
    }

    /**
     * Saves the manually set order of records.
     *
     * @param array   $pks   An array of primary key ids.
     * @param integer $order +1 or -1
     *
     * @return mixed
     *
     * @since   11.1
     */
    public function saveorder($pks = null, $order = null)
    {
        // Initialise variables.
        $data = $this->app->input->post->getArray();

	    $table = $this->getTable(ucfirst($data['view']));
        $conditions = array();

        if (empty($pks)) {
            $this->app->enqueueMessage(TextHelper::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'), 'error');

            return false;
        }

        // update ordering values
        foreach ($pks as $i => $pk) {
            $table->load((int) $pk);
            if ($table->ordering != $order[$i]) {
                $table->ordering = $order[$i];

                if (!$table->store()) {
                    $this->setError($table->getError());

                    return false;
                }

                // Remember to reorder within position and client_id
                $condition = $this->getReorderConditions($table);
                $found = false;

                foreach ($conditions as $cond) {
                    if ($cond[1] == $condition) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $key = $table->getKeyName();
                    $conditions[] = array($table->$key, $condition);
                }
            }
        }

        // Execute reorder for each category.
        foreach ($conditions as $cond) {
            $table->load($cond[0]);
            $table->reorder($cond[1]);
        }

        return true;
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param object $table A JTable object.
     *
     * @return array An array of conditions to add to ordering queries.
     *
     * @since   11.1
     */
    protected function getReorderConditions($table)
    {
        return array();
    }

    public function getListLimits()
    {
        // Get pagination request variables
        $limit = $this->app->getUserStateFromRequest($this->view.'_limit','limit',10);
        $limitstart = $this->app->getUserStateFromRequest($this->view.'_limitstart','limitstart',0);

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $state = new Registry;

        $state->set($this->view.'_limit', $limit);
        $state->set($this->view.'_limitstart', $limitstart);

        $this->setState($state);
    }

    /**
     * Method to adjust the ordering of a row.
     *
     * Returns NULL if the user did not have edit
     * privileges for any of the selected primary keys.
     *
     * @param integer $pks   The ID of the primary key to move.
     * @param integer $delta Increment, usually +1 or -1
     *
     * @return mixed False on failure or error, true on success, null if the $pk is empty (no items selected).
     *
     * @since   11.1
     */
    public function reorder($pks, $delta = 0)
    {
        // Initialise variables.
        $data = $this->app->input->post->getArray();

	    $table = $this->getTable(ucfirst($data['view']));
        $pks = (array) $pks;
        $result = true;

        $allowed = true;

        foreach ($pks as $i => $pk) {
            $table->reset();

            if ($table->load($pk)) {

                $where = $this->getReorderConditions($table);

                if (!$table->move($delta, $where)) {
                    $this->setError($table->getError());
                    unset($pks[$i]);
                    $result = false;
                }

            } else {
                $this->setError($table->getError());
                unset($pks[$i]);
                $result = false;
            }
        }

        if ($allowed === false && empty($pks)) {
            $result = null;
        }

        return $result;
    }

}
