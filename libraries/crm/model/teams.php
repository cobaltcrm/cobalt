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

class CobaltModelTeams extends JModelBase
{
    /**
     *
     *
     * @access  public
     * @return  void
     */
    function __construct()
    {
        parent::__construct();


    }

    /**
     * Get list of existing teams
     * @return mixed $teams array of teams returned
     */
    public function getTeams(){

        //Database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //Query String
        $query->select("t.*,IF(t.name!='',t.name,CONCAT(u.first_name,' ',u.last_name)) AS team_name");
        $query->from("#__teams AS t");
        $query->leftJoin("#__users AS u ON u.team_id = t.leader_id");
        $db->setQuery($query);
        $results = $db->loadAssocList();

        //clean data
        $teams = array();
        if ( count($results) > 0 ){
            foreach ( $results as $key=>$team ){
                $teams[$team['leader_id']] = $team['team_id'];
            }
        }

        //return results
        return $teams;

    }

    /**
     * Create a team if it does not exist in the database
     * @param int $leader_id the id of the leader for the team to create
     * @return int $team_id the id of the newly created team
     */
    public function createTeam($leader_id,$name=NULL){

        //Database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->clear();
        $query->select("team_id")->from("#__users")->where('id='.$leader_id);
        $db->setQuery($query);
        $team_id = $db->loadResult();
        $team_data = array( 'leader_id'=>$leader_id,'name'=>$name );
        $row =& JTable::getInstance('teams','Table');

        if ( $team_id > 0 ){
            $team_data['team_id'] = $team_id;
            $row->load($team_id);
        }

        if (!$row->bind($team_data)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        if (!$row->store()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        $team_id = $row->team_id;
        $this->assignLeader($leader_id,$team_id);
        return $team_id;
    }

    /**
     * Assign a leader to a team and update the users table
     * @param int $leader_id the id of the new leader
     * @param int $team_id the id of the newly created team
     * @return void
     */
    public function assignLeader($leader_id,$team_id){

        //bind user tables
        $row = JTable::getInstance('users','Table');
        $team_data = array ( 'id'=>$leader_id,'team_id'=>$team_id );
        if (!$row->bind($team_data)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        if (!$row->store()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

    }

    public function updateTeam($old_team,$new_team){
        //update the database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //delete old team
        $query->delete('#__teams');
        $query->where("team_id=$old_team");
        $db->setQuery($query);
        $db->query();

        //update users table for new team
        $query->clear()
            ->update("#__users")
            ->set(array("team_id=".$new_team))
            ->where("team_id=".$old_team);

        $db->setQuery($query);
        $db->query();

    }




}