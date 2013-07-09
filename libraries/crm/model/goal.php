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

class CobaltModelGoal extends JModelBase
{
        var $published = 1;

        /**
         * Method to store a record
         * @param $_POST data
         * @return    boolean    True on success
         */
        function store()
        {
            $app = JFactory::getApplication();

            //Load Tables
            $row = JTable::getInstance('goal','Table');
            $oldRow = JTable::getInstance('goal','Table');

            $data = $app->input->getRequest( 'post' );

            //date generation
            $date = CobaltHelperDate::formatDBDate(date('Y-m-d H:i:s'));
            if ( !array_key_exists('id',$data) ){
                $data['created'] = $date;
                $status = "created";
            } else {
                $row->load($data['id']);
                $oldRow->load($data['id']);
                $status = "updated";
            }

            //assign checkboxes
            if ( array_key_exists('leaderboard',$data) ){ $data['leaderboard'] = 1; } else { $data['leaderboard'] = 0; }

            //assign owner id
            $data['owner_id'] = CobaltHelperUsers::getUserId();

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

            CobaltHelperActivity::saveActivity($oldRow, $row,'goal', $status);

            return true;
        }

        /**
         * Get Individual Goals
         * @param int $id specific individual to search for
         * @return mixed $results goals matched
         */
        function getIndividualGoals($id=null){
            //load database
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            //load goals associated with an individual
            $member_id = CobaltHelperUsers::getUserId();
            $query->select("g.*,u.first_name,u.last_name")->from("#__goals AS g");

            //if we are seaching for a specific individual
            if ( $id ){
                $query->where("g.assigned_type='member' AND g.assigned_id=$id");
            } else {
            //else get logged in member
                $query->where("g.assigned_type='member' AND g.assigned_id=$member_id");
            }

            //left join users names
            $query->leftJoin("#__users AS u ON u.id = g.assigned_id");

            $query->where("g.published=".$this->published);

            //get goals
            $db->setQuery($query);
            $results = $db->loadAssocList();

            //get essential data
            $this->won_stage_ids = CobaltHelperDeal::getWonStages();
            if ( count($results) > 0 ){ foreach ( $results as $key=>$goal ){
                    $results[$key]['goal_info'] = $this->goalInfo($goal);
                }
            }

            //return results
            return $results;
        }

        /**
         * Get individual goals for executives
         * @param none
         * @return mixed $results
         */
        function getExecIndividualGoals(){
            //load database
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            //load goals associated with an individual
            $member_id = CobaltHelperUsers::getUserId();
            $query->select("g.*,u.first_name,u.last_name")->from("#__goals AS g");

            //if we are seaching for a specific individual
            $query->where("g.assigned_type='member'");

            //left join users names
            $query->leftJoin("#__users AS u ON u.id = g.assigned_id");

            $query->where("g.published=".$this->published);

            //get goals
            $db->setQuery($query);
            $results = $db->loadAssocList();

            //get essential data
            $this->won_stage_ids = CobaltHelperDeal::getWonStages();
            if ( count($results) > 0 ){ foreach ( $results as $key=>$goal ){
                    $results[$key]['goal_info'] = $this->goalInfo($goal);
                }
            }

            //return results
            return $results;
        }

        /**
         * Get individual goals for managers
         * @param none
         * @return mixed $results
         */
        function getManagerIndividualGoals(){
            //load database
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            //load goals associated with an individual
            $member_id = CobaltHelperUsers::getUserId();
            $team_id = CobaltHelperUsers::getTeamId($member_id);
            $query->select("g.*,u.first_name,u.last_name")->from("#__goals AS g");

             //get team members
            $team_members = CobaltHelperUsers::getTeamUsers($team_id);

            //filter by results having team ids
            $query .= " WHERE g.owner_id IN(";
            for($i=0;$i<count($team_members);$i++){
                $member = $team_members[$i];
                $query .= "".$member['id'].",";
            }
            $query = substr($query,0,-1);
            $query .= ")";
            $query .= " AND g.assigned_type='member'";

            //left join users names
            $query .= " LEFT JOIN #__users AS u ON u.id = g.assigned_id";

            $query .= "g.published=".$this->published;

            //get goals
            $db->setQuery($query);
            $results = $db->loadAssocList();

            //get essential data
            $this->won_stage_ids = CobaltHelperDeal::getWonStages();
            if ( count($results) > 0 ){ foreach ( $results as $key=>$goal ){
                    $results[$key]['goal_info'] = $this->goalInfo($goal);
                }
            }

            //return results
            return $results;
        }

        /**
         * Get Team Goals
         * @param int $id specific team to search for
         * @return mixed $results goals matched
         */
        function getTeamGoals($id=null){
            //get user team id
            $team_id = CobaltHelperUsers::getTeamId();

           //load database
           $db = JFactory::getDBO();
           $query = $db->getQuery(true);

           //load goals associated with team id
           $query->select("g.*,u.first_name,u.last_name,IF(t.name!='',t.name,CONCAT(u.first_name,' ',u.last_name)) AS team_name")->from("#__goals AS g");
           $db->setQuery($query);

           //if we are searching for a specific team
           if ( $id ){
                $query->where("g.assigned_type='team' AND g.assigned_id=$id");
           }else{
           //else load associated team if any
                $query->where("g.assigned_type='team' AND g.assigned_id=$team_id");
           }

           //left join managers name
           $query->leftJoin("#__teams AS t ON t.team_id = g.assigned_id");
           $query->leftJoin("#__users AS u on u.id = t.leader_id");

           $query->where("g.published=".$this->published);


           //load results
           $results = $db->loadAssocList();

            //get essential data
            $this->won_stage_ids = CobaltHelperDeal::getWonStages();
            if ( count($results) > 0 ){ foreach ( $results as $key=>$goal ){
                    $results[$key]['goal_info'] = $this->goalInfo($goal);
                }
            }

           //return goals
           return $results;
        }
        /**
         * Get Team Goals for Executives
         * @param none
         * @return mixed $results
         */
        function getExecTeamGoals(){
            //load database
           $db = JFactory::getDBO();
           $query = $db->getQuery(true);

           //load goals associated with team id
           $query->select("g.*,u.first_name,u.last_name,IF(t.name!='',t.name,CONCAT(u.first_name,' ',u.last_name)) AS team_name")->from("#__goals AS g");
           $db->setQuery($query);

           //if we are searching for a specific team
            $query->where("g.assigned_type='team'");

           //left join managers name
           $query->leftJoin("#__teams AS t ON t.team_id = g.assigned_id");
           $query->leftJoin("#__users AS u on u.id = t.leader_id");

           $query->where("g.published=".$this->published);

           //load results
           $results = $db->loadAssocList();

            //get essential data
            $this->won_stage_ids = CobaltHelperDeal::getWonStages();
            if ( count($results) > 0 ){ foreach ( $results as $key=>$goal ){
                    $results[$key]['goal_info'] = $this->goalInfo($goal);
                }
            }

           //return goals
           return $results;
        }

        /**
         * Get Company Goals
         * @param none
         * @return mixed $results goals matched
         */
        function getCompanyGoals(){
            //load database
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            //load goals associated with the company
            $query->select("g.*")->from("#__goals AS g")->where("g.assigned_type='company'");

            $query->where("g.published=".$this->published);

            //left join data essential for making calculations
            $db->setQuery($query);
            $results = $db->loadAssocList();

            //get essential data
            $this->won_stage_ids = CobaltHelperDeal::getWonStages();
            if ( count($results) > 0 ){ foreach ( $results as $key=>$goal ){
                    $results[$key]['goal_info'] = $this->goalInfo($goal);
                }
            }

           //return goals
           return $results;
        }

        /**
         * Get a leaderboard entry
         * @param int $id id of leaderboard to get
         * @return mixed $results db results of leaderboard info
         */
        function getLeaderBoards($id=null){
            //load database
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            //load goals and associate with user depending on team//role that have a leaderboard flag in the database
            $query->select("g.*")->from("#__goals AS g")->where("g.leaderboard=1");
            $query->leftJoin("#__users AS u ON u.id = g.assigned_id");

            //search for specific leaderboards and join essential data
            if ( $id ){
                //search for id
                $query->where("g.id=$id");

            }else{
                //filter based on member access roles
                $user_id = CobaltHelperUsers::getUserId();
                $member_role = CobaltHelperUsers::getRole();
                $team_id = CobaltHelperUsers::getTeamId();

                if ( $member_role != 'exec' ){

                    if ( $member_role == 'manager' ){
                        $query->where("u.team_id=$team_id");
                    }else{
                        $query->where("(g.owner_id=$user_id)");
                    }

                }

            }

            $query->where("g.published=".$this->published);

            //get results
            $db->setQuery($query);
            $results = $db->loadAssocList();

            //get data for leaderboard
            for($i=0;$i<count($results);$i++){
               $results[$i]['members'] = $this->leaderboardInfo($results[$i]);
            }

            //return goals
            return $results;
        }

        /**
         * Get Goal Information
         * @param $goal mixed array of goal information
         * @return $results int information relating to goal
         */
        function goalInfo($goal){
            //get DBO
             $db = JFactory::getDBO();
             $query = $db->getQuery(true);

            //win_cash
            if ( $goal['goal_type'] == 'win_cash' ){
                $query->select("SUM(amount)");
                $query->from("#__deals");
                //filter based on company
                if ( $goal['assigned_type'] != 'company' ){
                    //filter by member
                    if ( $goal['assigned_type'] == 'member' ){
                        $query->where("owner_id=".$goal['assigned_id']);
                    }
                    //filter by team
                    if ( $goal['assigned_type'] == 'team' ){
                        //get team members
                        $team_members = CobaltHelperUsers::getTeamUsers($goal['assigned_id']);
                        //filter by results having team ids
                        $query .= " WHERE owner_id IN(";
                        for($i=0;$i<count($team_members);$i++){
                            $member = $team_members[$i];
                            $query .= "".$member['id'].",";
                        }
                        $query = substr($query,0,-1);
                        $query .= ")";
                    }
                    $query .= " AND stage_id IN (".implode(',',$this->won_stage_ids).")";
                }else{
                    $query->where("stage_id IN (".implode(',',$this->won_stage_ids).")");
                }
                 //filter by start and end date
                $query .= " AND modified >= '".$goal['start_date']."'";
                $query .= " AND modified <= '".$goal['end_date']."'";
            }

            //win_deals
            if ( $goal['goal_type'] == 'win_deals' ){
                $query->select("COUNT(id)");
                $query->from("#__deals");
                //filter based on company
                if ( $goal['assigned_type'] != 'company' ){
                    //filter based on member
                    if ( $goal['assigned_type'] == 'member' ){
                        $query->where("owner_id=".$goal['assigned_id']);
                    }
                    //filter based on team
                    if ( $goal['assigned_type'] == 'team' ){
                        //get team members
                        $team_members = CobaltHelperUsers::getTeamUsers($goal['assigned_id']);
                        //filter by results having team ids
                        $query .= " WHERE owner_id IN(";
                        for($i=0;$i<count($team_members);$i++){
                            $member = $team_members[$i];
                            $query .= "".$member['id'].",";
                        }
                        $query = substr($query,0,-1);
                        $query .= ")";
                    }
                    $query .= " AND stage_id IN (".implode(',',$this->won_stage_ids).")";
                }else{
                    $query->where("stage_id IN (".implode(',',$this->won_stage_ids).")");
                }
                 //filter by start and end date
                $query .= " AND modified >= '".$goal['start_date']."'";
                $query .= " AND modified <= '".$goal['end_date']."'";
            }

            //move_deals
            if ( $goal['goal_type'] == 'move_deals' ){
                $query->select("COUNT(id)");
                $query->from("#__deals");
                //filter based on company
                if ( $goal['assigned_type'] != 'company' ){
                    //filter based on member
                    if ( $goal['assigned_type'] == 'member' ){
                        $query->where("owner_id=".$goal['assigned_id']);
                    }
                    //filter based on team
                    if ( $goal['assigned_type'] == 'team' ){
                        //get team members
                        $team_members = CobaltHelperUsers::getTeamUsers($goal['assigned_id']);
                        //filter by results having team ids
                        $query .= " WHERE owner_id IN(";
                        for($i=0;$i<count($team_members);$i++){
                            $member = $team_members[$i];
                            $query .= "".$member['id'].",";
                        }
                        $query = substr($query,0,-1);
                        $query .= ")";
                    }
                    $query .= " AND stage_id=".$goal['stage_id'];
                }else{
                    $query->where("stage_id=".$goal['stage_id']);
                }
                 //filter by start and end date
                $query .= " AND modified >= '".$goal['start_date']."'";
                $query .= " AND modified <= '".$goal['end_date']."'";
            }

            //complete_tasks
            if ( $goal['goal_type'] == 'complete_tasks' ){
                $query->select("COUNT(id)");
                $query->from("#__events");
                //filter based on company
                if ( $goal['assigned_type'] != 'company' ){
                    //filter based on member
                    if ( $goal['assigned_type'] == 'member' ){
                        $query->where("assignee_id=".$goal['assigned_id']);
                    }
                    //filter based on team
                    if ( $goal['assigned_type'] == 'team' ){
                        //get team members
                        $team_members = CobaltHelperUsers::getTeamUsers($goal['assigned_id']);
                        //filter by results having team ids
                        $query .= " WHERE assignee_id IN(";
                        for($i=0;$i<count($team_members);$i++){
                            $member = $team_members[$i];
                            $query .= "".$member['id'].",";
                        }
                        $query = substr($query,0,-1);
                        $query .= ")";
                    }
                    $query .= " AND category_id=".$goal['category_id'];
                    $query .= " AND completed=1";
                }else{
                    $query->where("category_id=".$goal['category_id']);
                    $query->where("completed=1");
                }
                 //filter by start and end date
                $query .= " AND modified >= '".$goal['start_date']."'";
                $query .= " AND modified <= '".$goal['end_date']."'";
            }

            //write_notes
            if ( $goal['goal_type'] == 'write_notes' ){
                $query->select("COUNT(id)");
                $query->from("#__notes");
                //filter by company
                if ( $goal['assigned_type'] != 'company'){
                    //filter based on member
                    if ( $goal['assigned_type'] == 'member' ){
                        $query->where("owner_id=".$goal['assigned_id']);
                    }
                    //filter based on team
                    if ( $goal['assigned_type'] == 'team' ){
                        //get team members
                        $team_members = CobaltHelperUsers::getTeamUsers($goal['assigned_id']);
                        //filter by results having team ids
                        $query .= " WHERE owner_id IN(";
                        for($i=0;$i<count($team_members);$i++){
                            $member = $team_members[$i];
                            $query .= "".$member['id'].",";
                        }
                        $query = substr($query,0,-1);
                        $query .= ")";
                    }
                    $query .= " AND category_id=".$goal['category_id'];
                }else{
                    $query->where("category_id=".$goal['category_id']);
                }
                 //filter by start and end date
                $query .= " AND created >= '".$goal['start_date']."'";
                $query .= " AND created <= '".$goal['end_date']."'";
            }

            //create_deals
            if ( $goal['goal_type'] == 'create_deals' ){
                $query->select("COUNT(id)");
                $query->from("#__deals");
                //filter by company
                if ( $goal['assigned_type'] != 'company' ){
                    //filter based on member
                    if ( $goal['assigned_type'] == 'member' ){
                        $query->where("owner_id=".$goal['assigned_id']);
                    }
                    //filter by team
                    if ( $goal['assigned_type'] == 'team' ){
                        //get team members
                        $team_members = CobaltHelperUsers::getTeamUsers($goal['assigned_id']);
                        //filter by results having team ids
                        $query .= " WHERE owner_id IN(";
                        for($i=0;$i<count($team_members);$i++){
                            $member = $team_members[$i];
                            $query .= "".$member['id'].",";
                        }
                        $query = substr($query,0,-1);
                        $query .= ")";
                    }
                }
                 //filter by start and end date
                $query .= " AND created >= '".$goal['start_date']."'";
                $query .= " AND created <= '".$goal['end_date']."'";
            }

            $query .= "AND published=".$this->published;

            //return info
            $db->setQuery($query);
            $results = $db->loadResult();

            return $db->loadResult();
        }

        /**
         * Get leaderboard info
         * @param mixed $leaderboard leaderboard object
         * @return mixed $results leaderboard information
         */
        function leaderboardInfo($leaderboard){

            //get db
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            //get won stage id
            $won_stage_ids = CobaltHelperDeal::getWonStages();

            //assign start_date and end_date
            $start_date = $leaderboard['start_date'];
            $end_date   = $leaderboard['end_date'];


            //select members from database and join essential data
            //get members
            $query->select("u.id,u.first_name,u.last_name");
            $query->from("#__users AS u");

            //left join data
            //win_cash
            if ( $leaderboard['goal_type'] == 'win_cash' ){
                $query->select("SUM(d.amount) AS cash_won");
                $query->leftJoin("#__deals AS d ON d.owner_id = u.id AND d.stage_id IN (".implode(",",$won_stage_ids).") AND d.modified >= '$start_date' AND d.modified <= '$end_date' AND d.published>0");
                $query->order('SUM(d.amount) desc');
            }
            //win_deals
            if ( $leaderboard['goal_type'] == 'win_deals' ){
                $query->select("COUNT(d.id) AS deals_won");
                $query->leftJoin("#__deals AS d ON d.owner_id = u.id AND d.stage_id IN (".implode(",",$won_stage_ids).") AND d.modified >= '$start_date' AND d.modified <= '$end_date' AND d.published>0");
                $query->order('COUNT(d.id) desc');
            }
            //move_deals
            if ( $leaderboard['goal_type'] == 'move_deals' ){
                $query->select("COUNT(d.id) AS deals_moved");
                $query->leftJoin("#__deals AS d ON d.owner_id = u.id AND d.stage_id=".$leaderboard['stage_id']." AND d.modified >= '$start_date' AND d.modified <= '$end_date' AND d.published>0");
                $query->order('COUNT(d.id) desc');
            }
            //complete_tasks
            if ( $leaderboard['goal_type'] == 'complete_tasks' ){
                $query->select("COUNT(e.id) AS tasks_completed");
                $query->leftJoin("#__events AS e ON e.assignee_id = u.id AND e.completed=1 AND e.category_id=".$leaderboard['category_id']." AND e.modified >= '$start_date' AND e.modified <= '$end_date' AND e.published>0");
                $query->order('COUNT(e.id) desc');
            }
            //write_notes
            if ( $leaderboard['goal_type'] == 'write_notes' ){
                $query->select("COUNT(n.id) AS notes_written");
                $query->leftJoin("#__notes AS n ON n.owner_id = u.id AND n.category_id=".$leaderboard['category_id']." AND n.created >= '$start_date' AND n.created <= '$end_date' AND n.published>0");
                $query->order('COUNT(n.id) desc');
            }
            //create_deals
            if ( $leaderboard['goal_type'] == 'create_deals' ){
                $query->select("COUNT(d.id) AS deals_created");
                $query->leftJoin("#__deals AS d ON d.owner_id = u.id AND d.created >= '$start_date' AND d.created <= '$end_date' AND d.published>0");
                $query->order('COUNT(d.id) desc');
            }

            //switch depending on leaderboard type
            switch ( $leaderboard['assigned_type'] ){
                case "team":
                    $query->where("u.team_id=".$leaderboard['assigned_id']);
                break;
                case "member":
                    $query->where("u.id=".$leaderboard['assigned_id']);
                break;
            }

            //return results
            $query->group("u.id");
            $db->setQuery($query);

            $results = $db->loadAssocList();
            return $results;
        }

}
