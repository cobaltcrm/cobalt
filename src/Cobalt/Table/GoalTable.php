<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Table;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class GoalTable extends AbstractTable
{
    protected $tableName = '#__goals';

    /**
     * Added to filter dates to SQL format
     *
     * @param mixed $array
     * @param array $ignore
     * @return $this
     */
    public function bind($array, $ignore = array())
    {
        //transform date to SQL
        if (!empty($array['start_date'])) {
            $array['start_date'] = $this->dateToSql($array['start_date']);
        }
        if (!empty($array['end_date'])) {
            $array['end_date'] = $this->dateToSql($array['end_date']);
        }

        return parent::bind($array, $ignore);
    }

    private function dateToSql($date)
    {
        $start_date = \JDate::getInstance(str_replace('/','-',$date));
        return $start_date->toSql();
    }
}
