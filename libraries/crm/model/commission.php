<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class CobaltModelCommission extends JModelBase
{

        /**
         * Get Monthly Commission
         * @param $access_type we wish to filter by 'member','team','company'
         * @param $access_id the id of the $access_type we wish to filter by
         * @return mixed $results
         */
        function getMonthlyCommission($access_type=null,$access_id=null)
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
                    $team_members = CobaltHelperUsers::getTeamUsers($access_id);
                    foreach ($team_members as $key=>$member) {
                        $members[] = $this->getMonthlyCommissionData($member['id']);
                    }
                }

                //get company data
                if ($access_type == 'company') {
                    //get company users
                    $company_members = CobaltHelperUsers::getCompanyUsers();
                    foreach ($company_members as $key=>$member) {
                        $members[] = $this->getMonthlyCommissionData($member['id']);
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
         * Get Yearly Commission
         * @param $access_type we wish to filter by 'member','team','company'
         * @param $access_id the id of the $access_type we wish to filter by
         * @return mixed $results
         */
        function getYearlyCommission($access_type=null,$access_id=null)
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
                    $team_members = CobaltHelperUsers::getTeamUsers($access_id);
                    foreach ($team_members as $key=>$member) {
                        $members[] = $this->getYearlyCommissionData($member['id']);
                    }
                }

                //get company data
                if ($access_type == 'company') {
                    //get company users
                    $company_members = CobaltHelperUsers::getCompanyUsers();
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

                //return data
                return $results;

            }

        }

        /**
         * Get monthly commission data for member
         * @param $id to search for
         * @return mixed $results
         */
        function getMonthlyCommissionData($id)
        {
            //get db
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            //get current month
            $current_month = CobaltHelperDate::formatDBDate(date('Y-m-01 00:00:00'));

            //get weeks in month
            $weeks = CobaltHelperDate::getWeeksInMonth($current_month);

            //get stage id to filter deals by
            $won_stage_ids = CobaltHelperDeal::getWonStages();

            //gen query
            $results = array();
            foreach ($weeks as $week) {
                $start_date = $week['start_date'];
                $end_date = $week['end_date'];

                //flush query
                $query = $db->getQuery(true);

                //gen query string
                $query->select("d.owner_id, SUM(d.amount) AS y");
                $query->from("#__deals AS d");
                $query->where("d.stage_id IN (".implode(',',$won_stage_ids).")");
                $query->where("d.modified >= '$start_date'");
                $query->where("d.modified < '$end_date'");
                $query->where("d.modified IS NOT NULL");
                $query->where("d.owner_id=$id");

                //group results
                $query->group("d.owner_id");

                //sort by published deals
                $query->where("d.published>0");

                //return results
                $db->setQuery($query);
                $results[] = $db->loadAssoc();

            }

            //clean data for commission rate
            foreach ($results as $key=>$result) {
                $commission_rate = CobaltHelperUsers::getCommissionRate($result['owner_id']);
                $results[$key]['y'] = (int) $result['y']*($commission_rate/100);
            }

            //return results
            return $results;
        }

        /**
         * Get yearly commission data for user
         * @param int $id to search for
         * @return mixed $results
         */
        function getYearlyCommissionData($id)
        {
            //get db
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            //get current year and months to loop through
            $current_year = CobaltHelperDate::formatDBDate(date('Y-01-01 00:00:00'));
            $month_names = CobaltHelperDate::getMonthNames();
            $months = CobaltHelperDate::getMonthDates();

            //get stage id to filter deals by
            $won_stage_ids = CobaltHelperDeal::getWonStages();

            //gen query
            $results = array();
            foreach ($months as $month) {
                $start_date = $month['date'];
                $end_date = CobaltHelperDate::formatDBDate(date('Y-m-d 00:00:00',strtotime("$start_date + 1 months")));

                //flush the query
                $query = $db->getQuery(true);

                //generate query string
                $query->select("d.owner_id,d.modified,SUM(d.amount) AS y");
                $query->from("#__deals AS d");
                $query->where("d.stage_id IN (".implode(',',$won_stage_ids).")");
                $query->where("d.modified >= '$start_date'");
                $query->where("d.modified < '$end_date'");
                $query->where("d.modified IS NOT NULL");
                $query->where("d.owner_id=$id");

                //group results
                $query->group("d.owner_id");

                //sort by published deals
                $query->where("d.published>0");

                //get results and assign to month
                $db->setQuery($query);
                $results[] = $db->loadAssoc();

            }

            //clean data for commission rate
            foreach ($results as $key=>$result) {
                $commission_rate = CobaltHelperUsers::getCommissionRate($result['owner_id']);
                $results[$key]['y'] = (int) $result['y']*($commission_rate/100);
            }

            //return
            return $results;
        }

}
