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

use JFactory;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\DealHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Revenue extends DefaultModel
{
        /**
     * Get Monthly Revenue
     * @param $access_type we want to filter by 'member','team','company'
     * @param $access_id the id of the $access_type we want to filter by
     * @return mixed $results
     */
    public function getMonthlyRevenue($access_type = null, $access_id = null)
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //get current month
        $current_month = DateHelper::formatDBDate(date('Y-m-01 00:00:00'));

        //get weeks in month
        $weeks = DateHelper::getWeeksInMonth($current_month);

        //get stage id to filter deals by
        $won_stage_ids = DealHelper::getWonStages();

        //gen query
        $data = new \stdClass;
        $data->labels = array();
        $data->datasets = array();
        $totals = array();

        foreach ($weeks as $week)
        {
            $start_date = $week['start_date'];
            $end_date = $week['end_date'];

            $weekDate = new \DateTime($start_date . ' +2 day');
            $data->labels[] = TextHelper::_('COBALT_WEEK') . ' ' . $weekDate->format("W");

            //flush query
            $query = $db->getQuery(true);
            //gen query string
            $query->select("SUM(d.amount)");
            $query->from("#__deals AS d");
            $query->where("d.stage_id IN (" . implode(',', $won_stage_ids) . ")");
            $query->where("d.modified >= '$start_date'");
            $query->where("d.modified < '$end_date'");
            $query->where("d.modified IS NOT NULL");

            //sort by published deals
            $query->where("d.published > 0");

            //filter by owner type
            if ($access_type != 'company')
            {
                //team sorting
                if ($access_type == 'team')
                {
                    //get team members
                    $team_members = UsersHelper::getTeamUsers($access_id);
                    $query .= " AND d.owner_id IN (";
                    //loop to make string
                    foreach ($team_members as $key => $member)
                    {
                        $query .= "'".$member['id']."',";
                    }

                    $query  = substr($query, 0, -1);
                    $query .= ") ";
                }

                //member filter
                if ($access_type == 'member')
                {
                    $query->where("d.owner_id = $access_id");
                }
            }

            //return results
            $db->setQuery($query);

            $total = (int) $db->loadResult();

            if (!$total)
            {
                $total = 0;
            }

            $totals[] = $total;
        }

        $data->datasets[0] = new \stdClass;
        $data->datasets[0]->data = $totals;
        $data->datasets[0]->label = '';
        $data->datasets[0]->fillColor = "rgba(151,187,205,0.5)";
        $data->datasets[0]->strokeColor = "rgba(151,187,205,0.8)";
        $data->datasets[0]->pointColor = "rgba(151,187,205,0.75)";
        $data->datasets[0]->pointStrokeColor = "rgba(151,187,205,1)";

        //return results
        return $data;

    }

    /**
     * Get Yearly Revenue
     * @param $access_type we wish to filter by 'member','team','company'
     * @param $access_id the id of the $access_type we wish to filter by
     * @return mixed $results
     */
    public function getYearlyRevenue($access_type=null,$access_id=null)
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //get current year and months to loop through
        $current_year = DateHelper::formatDBDate(date('Y-01-01 00:00:00'));
        $month_names = DateHelper::getMonthNames();
        $months = DateHelper::getMonthDates();

        //get stage id to filter deals by
        $won_stage_ids = DealHelper::getWonStages();

        //gen query
        $results = array();
        foreach ($months as $month) {
            $start_date = $month['date'];
            $end_date = DateHelper::formatDBDate(date('Y-m-d 00:00:00',strtotime("$start_date + 1 months")));
            //flush the query
            $query = $db->getQuery(true);
            //generate query string
            $query->select("d.modified,SUM(d.amount) AS y");
            $query->from("#__deals AS d");
            $query->where("d.stage_id IN (".implode(',',$won_stage_ids).")");
            $query->where("d.modified >= '$start_date'");
            $query->where("d.modified < '$end_date'");
            $query->where("d.modified IS NOT NULL");

            //sort by published deals
            $query->where("d.published>0");

            //filter by access type
            if ($access_type != 'company') {

                //team sorting
                if ($access_type == 'team') {
                    //get team members
                    $team_members = UsersHelper::getTeamUsers($access_id);
                    $query .= " AND d.owner_id IN (";
                    //loop to make string
                    foreach ($team_members as $key=>$member) {
                        $query .= "'".$member['id']."',";
                    }
                    $query  = substr($query,0,-1);
                    $query .= ") ";
                }

                //member filter
                if ($access_type == 'member') {
                    $query->where("d.owner_id=$access_id");
                }
            }

            //get results and assign to month
            $db->setQuery($query);
            $totals =  $db->loadAssoc();
            if (!$totals) {
                $totals = array('y'=>0);
            }
            $totals['y'] = (int) $totals['y'];
            $results[] =  $totals;
        }
        //return
        return $results;

    }

}
