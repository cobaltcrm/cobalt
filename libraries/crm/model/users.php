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

class CobaltModelUsers extends CobaltModelDefault
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

    function store()
    {
        $app = JFactory::getApplication();

        //Load Tables
        $row = JTable::getInstance('users','Table');
        $data = $app->input->getRequest( 'post' );

        $dispatcher = JEventDispatcher::getInstance();
        $dispatcher->trigger('onBeforeCRMUserSave', array(&$data));

        //date generation
        $date = date('Y-m-d H:i:s');

        if ( !array_key_exists('id',$data) ){
            $data['created'] = $date;
            $data['time_zone'] = CobaltHelperConfig::getConfigValue('timezone');
            $data['time_format'] = CobaltHelperConfig::getConfigValue('time_format');

            $data['block'] = 0;
            $data['registerDate'] = $date;
            $data['activation'] = 0;
            $data['params'] = "";
        }

        if ( array_key_exists('password',$data) && $data['password'] != "" ){
            $salt = JUserHelper::genRandomPassword(32);
            $crypt = JUserHelper::getCryptedPassword($data['password'], $salt);
            $cryptpass = $crypt . ':' . $salt;
            $data['password'] = $cryptpass;
        } else {
            unset($data['password']);
        }

        //generate team data
        $model = new CobaltModelTeams();
        if ( array_key_exists('id',$data) && $data['id'] > 0 ){
            $teamId = $this->getTeamId($data['id']);
        }

        //assign user priviliges
        $data['modified'] = $date;
        $data['admin'] = ( array_key_exists ('admin',$data) && $data['admin'] == '1' ) ? 1 : 0;
        $data['exports'] = ( array_key_exists ('exports',$data) && $data['exports'] == 'on' ) ? 1 : 0;
        $data['can_delete'] = ( array_key_exists ('can_delete',$data) && $data['can_delete'] == 'on' ) ? 1 : 0;

        //republish / register users
        if ( array_key_exists('id',$data) && $data['id'] != "" ){
            $db =& JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->clear()->select("id")->from("#__users")->where("id=".$data['id']);
            $db->setQuery($query);
            $id = $db->loadResult();
            if ( $id ){
                $data['id'] = $id;
                $data['published'] = 1;
            }
        }

        if ( array_key_exists('team_id',$data) && $data['team_id'] == "" ){
            unset($data['team_id']);
        }

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

        if ( array_key_exists('role_type',$data) && $data['role_type'] == "manager"  ) {
            $teamModel = new CobaltModelTeams();
            $teamName = array_key_exists('team_name',$data) ? $data['team_name'] : "";
            $teamModel->createTeam($row->id,$teamName);
        }

        //if we are downgrading a users priviliges
        if ( array_key_exists('manager_assignment',$data) && $data['manager_assignment'] != null && $data['manager_assignment'] != "" ) {
            $newTeamId = $this->getTeamId($data['manager_assignment']);
            $model->updateTeam($teamId,$newTeamId);
        }

        $row->id = ( array_key_exists('id',$data) && $data['id'] > 0 ) ? $data['id'] : $this->_db->insertId();
        $this->updateUserMap($row);

        $dispatcher = JEventDispatcher::getInstance();
        $dispatcher->trigger('onAfterCRMUserSave', array(&$data));

        return true;
    }

    function updateUserMap($user){

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->delete("#__user_usergroup_map")->where("user_id=".$user->id);
        $db->setQuery($query);
        $db->query();

        $groupId = $user->admin == 1 ? "2" : "2";
        $query->clear();
        $query->insert("#__user_usergroup_map")->columns(array($db->quoteName('user_id'),$db->quoteName('group_id')))->values($db->quote($user->id).', '.$db->quote($groupId));
        $db->setQuery($query);
        $db->query();

    }

    public function _buildQuery(){

         //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

         //select
        $query->select("u.*,ju.username,ju.email,ju.lastvisitDate as last_login,
                        team_leader.first_name as leader_first_name,team_leader.last_name as leader_last_name,
                        team.leader_id as leader_id,
                        IF(team.name!='',team.name,CONCAT(team_leader.first_name,' ',team_leader.last_name)) AS team_name");
        $query->from("#__users AS u");

        //left join essential data
        $query->leftJoin("#__users AS ju ON ju.id = u.id");
        $query->leftJoin("#__teams AS team ON team.team_id = u.team_id");
        $query->leftJoin("#__users AS team_leader ON team_leader.id = team.leader_id");

        return $query;

    }

    public function getUsers($id=null){
        //get dbo
        $db = JFactory::getDBO();
        $query = $this->_buildQuery();

        //sort
        $query->order($this->getState('Users.filter_order') . ' ' . $this->getState('Users.filter_order_Dir'));
        if( $id ){
            $query->where("u.id=$id");
        }

        $query->where("u.published=1");

        //return results
        $db->setQuery($query);
        return $db->loadAssocList();
    }

    public function getUser($id=null){
        $app = JFactory::getApplication();
        $id = $id ? $id : $app->input->get("id");

        if ( $id > 0 ){

            $db = JFactory::getDBO();
            $query = $this->_buildQuery();

            if( $id ){
                $query->where("u.id=$id");
            }

            $db->setQuery($query);
            return $db->loadAssoc();

        }else{
            return (array)JTable::getInstance('users','Table');
        }

    }

    public function populateState(){
        //get states
        $app = JFactory::getApplication();
        $filter_order = $app->getUserStateFromRequest('Users.filter_order','filter_order','u.last_name');
        $filter_order_Dir = $app->getUserStateFromRequest('Users.filter_order_Dir','filter_order_Dir','asc');

        $state = new JRegistry();

        //set states
        $state->set('Users.filter_order', $filter_order);
        $state->set('Users.filter_order_Dir',$filter_order_Dir);

        $this->setState($state);
    }

    public function getJoomlaUsersToAdd(){
        //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //select
        $query->select("ju.id,ju.name,ju.username,ju.email,cu.id as cid,cu.published");
        $query->from("#__users AS ju");

        //left join essential data
        $query->leftJoin("#__users AS cu ON ju.id = cu.id");

        //return results
        $db->setQuery($query);
        $results = $db->loadAssocList();
        $users = array();
        foreach ( $results as $key => $user ){
            if ( !$user['cid'] || $user['published'] == -1  ){
                $name = explode(" ",$user['name']);
                $user['first_name'] = array_key_exists(0,$name) ? $name[0] : "";
                $user['last_name'] = array_key_exists(1,$name) ? $name[1] : "";
                $users[$user['id']] = $user;
            }
        }
        return $users;
    }

    public function getCobaltUsers($idsOnly=FALSE){
        //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //select
        $query->select("u.id AS value,CONCAT(u.first_name,' ',u.last_name) AS label");
        $query->from("#__users AS u");
        $query->where("u.published=1");

        //return results
        $db->setQuery($query);
        $results = $db->loadAssocList();

        return $results;
    }

    public function getJoomlaUsersToAddList($namesOnly=FALSE){
        //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //select
        $query->select("ju.id,ju.name,ju.username,cu.id as cid,cu.published");
        $query->from("#__users AS ju");

        //left join essential data
        $query->leftJoin("#__users AS cu ON ju.id = cu.id");

        //return results
        $db->setQuery($query);
        $results = $db->loadAssocList();

        $users = array();
        foreach ( $results as $key=>$user){
            if ( !$user['cid'] || $user['published'] == -1 ){
                if ( $namesOnly ){
                    $users[] = $user['name'];
                }else{
                    $users[$user['id']] = $user['name'];
                }
            }
        }
        return $users;
    }

    //return user team id
    function getTeamId($user_id){
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //get id
        $query->select("team_id");
        $query->from("#__users");
        $query->where('id='.$user_id);

        //return id
        $db->setQuery($query);
        return $db->loadResult();
    }

    function delete($ids){
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $dispatcher = JEventDispatcher::getInstance();
        $dispatcher->trigger('onBeforeCRMUserDelete', array(&$ids));

        $query->update("#__users");
                if ( is_array($ids) ){
                    $query->where("id IN(".implode(',',$ids).")");
                }else{
                    $query->where("id=".$ids);
                }
        $query->set("published=-1");
        $db->setQuery($query);
        if ( $db->query() ){

            $dispatcher = JEventDispatcher::getInstance();
            $dispatcher->trigger('onAfterCRMUserDelete', array(&$ids));

            return true;
        }else{
            return false;
        }

    }



}

