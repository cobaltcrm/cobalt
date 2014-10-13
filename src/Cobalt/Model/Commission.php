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

use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\DealHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Commission extends DefaultModel
{
    /**
     * Get Monthly Commission
     * @param $access_type we wish to filter by 'member','team','company'
     * @param $access_id the id of the $access_type we wish to filter by
     * @return mixed $results
     */
    public function getMonthlyCommission($access_type=null,$access_id=null)
    {
        //get member data
        if ($access_type == 'member') {
            return $this->getMonthlyCommissionData( $access_id );
        } else {

            //array to assign our data
            $members = array();
            $results = array();

            //get team data
            if ($access_type == 'team') {
                //get team members
                $team_members = UsersHelper::getTeamUsers($access_id);
                foreach ($team_members as $key => $member) {
                    $members[] = $this->getMonthlyCommissionData($member['id']);
                }
            }

            //get company data
            if ($access_type == 'company') {
                //get company users
                $company_members = UsersHelper::getCompanyUsers();
                foreach ($company_members as $key => $member) {
                    $members[] = $this->getMonthlyCommissionData($member['id']);
                }
            }

            //combine data
            foreach ($members as $key=>$member) {
                foreach ($member as $date_key => $data) {
                    if ( array_key_exists($date_key,$results) ) {
                        $results[$date_key]['y'] += $data['y'];
                    } else {
                        $results[$date_key]['y'] = $data['y'];
                    }
                }
            }

            return $results;
        }
    }

    /**
     * Get Yearly Commission
     * @param $access_type we wish to filter by 'member','team','company'
     * @param $access_id the id of the $access_type we wish to filter by
     * @return mixed $results
     */
    public function getYearlyCommission($access_type=null,$access_id=null)
    {
        //get member data
        if ($access_type == 'member') {
            return $this->getYearlyCommissionData( $access_id );
        } else {

            //array to assign our data
            $members = array();
            $results = array();

            //get team data
            if ($access_type == 'team') {
                //get team members
                $team_members = UsersHelper::getTeamUsers($access_id);
                foreach ($team_members as $key=>$member) {
                    $members[] = $this->getYearlyCommissionData($member['id']);
                }
            }

            //get company data
            if ($access_type == 'company') {
                //get company users
                $company_members = UsersHelper::getCompanyUsers();
                foreach ($company_members as $key=>$member) {
                    $members[] = $this->getYearlyCommissionData($member['id']);
                }
            }

            //combine data
            foreach ($members as $key=>$member) {
                foreach ($member as $date_key=>$data) {
                    if ( array_key_exists($date_key,$results) ) {
                        $results[$date_key]['y'] += $data['y'];
                    } else {
                        $results[$date_key]['y'] = $data['y'];
                    }
                }
            }

            return $results;
        }

    }

    /**
     * Get monthly commission data for member
     * @param $id to search for
     * @return mixed $results
     */
    public function getMonthlyCommissionData($id)
    {
        //get current month
        $current_month = DateHelper::formatDBDate(date('Y-m-01 00:00:00'));

        //get weeks in month
        $weeks = DateHelper::getWeeksInMonth($current_month);

        //get stage id to filter deals by
        $won_stage_ids = DealHelper::getWonStages();

        //gen query
        $results = array();
        foreach ($weeks as $week) {
            $start_date = $week['start_date'];
            $end_date = $week['end_date'];

            //flush query
            $query = $this->db->getQuery(true)
                ->select("d.owner_id, SUM(d.amount) AS y")
                ->from("#__deals AS d")
                ->where("d.stage_id IN (".implode(',',$won_stage_ids).")")
                ->where("d.modified >= " . $this->db->quote($start_date))
                ->where("d.modified < " . $this->db->quote($end_date))
                ->where("d.modified IS NOT NULL")
                ->where("d.owner_id=$id")
                ->group("d.owner_id")
                ->where("d.published>0");

            $results[] = $this->db->setQuery($query)->loadAssoc();
        }

        //clean data for commission rate
        foreach ($results as $key => $result) {
            $commission_rate = UsersHelper::getCommissionRate($result['owner_id']);
            $results[$key]['y'] = (int) $result['y']*($commission_rate/100);
        }

        return $results;
    }

    /**
     * Get yearly commission data for user
     * @param  int   $id to search for
     * @return mixed $results
     */
    public function getYearlyCommissionData($id)
    {
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
            $query = $this->db->getQuery(true)
                ->select("d.owner_id,d.modified,SUM(d.amount) AS y")
                ->from("#__deals AS d")
                ->where("d.stage_id IN (".implode(',',$won_stage_ids).")")
                ->where("d.modified >= " . $this->db->quote($start_date))
                ->where("d.modified < " . $this->db->quote($end_date))
                ->where("d.modified IS NOT NULL")
                ->where("d.owner_id=$id")
                ->group("d.owner_id, d.modified")
                ->where("d.published>0");

            $results[] = $this->db->setQuery($query)->loadAssoc();
        }

        //clean data for commission rate
        foreach ($results as $key=>$result) {
            $commission_rate = UsersHelper::getCommissionRate($result['owner_id']);
            $results[$key]['y'] = (int) $result['y']*($commission_rate/100);
        }

        return $results;
    }

}
