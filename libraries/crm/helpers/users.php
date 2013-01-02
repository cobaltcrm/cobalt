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

 class CobaltHelperUsers extends JObject
 {
    
    //get users depending on logged in member type
    function getUsers($id=null,$idsOnly=FALSE){
        
        //filter based on current logged in user
        $user = CobaltHelperUsers::getUserId();
        $user_role = CobaltHelperUsers::getRole();
        $results = array();
        
        //user role filters
        if( $user_role != 'basic' ){
            
            //get db
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            $select = ( $idsOnly ) ? "id AS value,CONCAT(first_name,' ',last_name) AS label" : "*";
            
            //get users
            $query->select($select);
            $query->from("#__users");
            
            //exec
            if ( $id ){
                $query->where("id=$id");
            }else if ( $user_role == 'exec') {
                $query->where("id<>".$user);
            //manager    
            }else if ( $user_role == 'manager' ){
                $team_id = CobaltHelperUsers::getTeamId();
                $query->where('team_id='.$team_id.' AND id <> '.$user);
            }
            
            //load results
            $query->where("published=1");
            $db->setQuery($query);
            $results = $db->loadAssocList();

        }
        
        //assign other user info
        if ( !$idsOnly ){
            if ( count($results) > 0 ){
                foreach ( $results as $key=>$user ){
                    $results[$key]['emails'] = CobaltHelperUsers::getEmails($user['id']);
                }
            }
        }
        
        //return
        return $results;
    }

    function getFirstName($id=null){

        $id = $id ? $id : self::getLoggedInUser()->id;

        $db =& JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->clear()->select("first_name")->from("#__users")->where("id=".$id);
        $db->setQuery($query);
        return $db->loadResult();

    }

    //get all company users
    function getCompanyUsers($id=null){
        
        //filter based on current logged in user
        $user = JFactory::getUser();
        $user_role = CobaltHelperUsers::getRole();
        $results = array();
        
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
            
        //get users
        $query->select("*");
        $query->from("#__users");
        
        //load results
        $query->where("published=1");
        $db->setQuery($query);
        $results = $db->loadAssocList();
        
        //assign other user info
        foreach ( $results as $key=>$user ){
            $results[$key]['emails'] = CobaltHelperUsers::getEmails($user['id']);
        }
        
        //return
        return $results;
    }

    /**
     * Get user email address for a user
     * @param int $id user id to get emails for
     * @return mixed $results db results
     */
    function getEmails($id=null){

        //Cobalt User ID
        if ( !$id ){
            $id = CobaltHelperUsers::getUserId();
        }
        
        //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        //query
        $query->select("*")->from("#__users_email_cf")->where("member_id=".$id);
        
        //load and return results
        $db->setQuery($query);
        $email_cf = $db->loadAssocList();

        $query->clear()
                ->select("j.email,u.id AS member_id")
                ->from("#__users AS u")
                ->leftJoin("#__users AS j ON j.id=u.id")
                ->where("u.id=".$id);

        $primary = $db->loadAssocList();

        $emails = array_merge($email_cf,$primary);

        return $emails;
        
    }
    
    //return current logged in Cobalt user ID based on Joomla Id
    function getUserId(){
        
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        //logged in user
        $user = JFactory::getUser();
        
        //get id
        $query->select("id");
        $query->from("#__users");
        $query->where('id='.$user->id);
        
        //return id
        $db->setQuery($query);
        return $db->loadResult();
        
    }
    
    //return user role
    function getRole($user_id=null){
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        //logged in user
        if ( !$user_id ){
            $user_id = JFactory::getUser()->id;
        }
        
        //get id
        $query->select("role_type");
        $query->from("#__users");
        $query->where('id='.$user_id);
        
        //return id
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    //return user team id
    function getTeamId($user_id=null){
       //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        $user_id = $user_id ? $user_id : CobaltHelperUsers::getUserId();
        
        //get id
        $query->select("team_id");
        $query->from("#__users");
        $query->where('id='.$user_id);
        
        //return id
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;

    }
    
    //return teams to execs
    function getTeams($id=null){
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        //query
        $query->select("t.*,u.first_name,u.last_name,IF(t.name!='',t.name,CONCAT(u.first_name,' ',u.last_name)) AS team_name");
        $query->from("#__teams AS t");
        $query->leftJoin("#__users AS u ON u.id = t.leader_id AND u.published=1");
        
        //search for specific team
        if ( $id ){
            $query->where("t.team_id=$id");
        }

        $user_role = CobaltHelperUsers::getRole();
        $user_id = CobaltHelperUsers::getUserId();
        if ( $user_role == 'manager' ){
            $team_id = CobaltHelperUsers::getTeamId();
            $query->where('t.team_id='.$team_id);
        }
        
        //return results
        $db->setQuery($query);
        $teams = $db->loadAssocList();

        return $teams;
    }
    /**
     * Get users associated with a specific team
     * @param int $id specific team id requested
     * @return mixed $results results from database
     */
    function getTeamUsers($id=null,$idsOnly=FALSE){
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        if ( $idsOnly ){
            $select = "u.id";
        }else{
            $select = "u.*";
        }

        $id = $id ? $id : CobaltHelperUsers::getTeamId();

        //query
        $query->select($select)->from("#__users AS u")->where("u.team_id=$id AND u.published=1");
        
        //return results
        $db->setQuery($query);


        if ( $idsOnly ){
            $users = $db->loadColumn();
        }else{
            $users = $db->loadAssocList();
        }

        return $users;
    }

    function getAllSharedUsers(){

        $db =& JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("id AS value,CONCAT(first_name,' ',last_name) AS label")
            ->from("#__users");

        $role = CobaltHelperUsers::getRole();

        switch ( $role ){
            case "manager":
            case "basic":
                $query->where("team_id=".CobaltHelperUsers::getTeamId());
            break;  
        }

        $db->setQuery($query);
        $users = $db->loadObjectList();

        return $users;

    }

    function getItemSharedUsers($itemId,$itemType){

        $db =& JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("s.user_id AS value,CONCAT(u.first_name,' ',u.last_name) AS label")
            ->from("#__shared AS s")
            ->leftJoin("#__users AS u ON u.id = s.user_id");

        $query->where("s.item_id=".$itemId);
        $query->where("s.item_type=".$db->Quote($itemType));

        $db->setQuery($query);
        $users = $db->loadObjectList();

        return $users;

    }
    
    /**
     * Get deal count associated with users
     * @param $id int User Id to filter for
     * @param $team int Team Id associated to user
     * @param $role String User role to filter for
     * @return int Count of deals returned from database
     */
    function getDealCount($id,$team,$role){
        
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        //query
        $query->select('count(*)');
        $query->from('#__deals AS d');
        $query->leftJoin("#__users AS u ON u.id = d.owner_id AND u.published=1");
        
        //filter based on id and role
        if  ( $role != 'exec' ){
            if ( $role == 'manager' ){
                $query->where("u.team_id=$team");
            }else{
                $query->where("d.owner_id=$id");
            }
        }
        $query->where("d.published=1");
        
        //return results
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    /**
     * Get people count associated with users
     * @param $id int User Id to filter for
     * @param $team int Team Id associated to user
     * @param $role String User role to filter for
     * @return int Count of people returned from database
     */
    function getPeopleCount($id=null,$team=null,$role=null){
        
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        if ( !$id ){
            $id = CobaltHelperUsers::getUserId();
        }
        if ( !$team ){
            $team = CobaltHelperUsers::getTeamId();
        }
        if ( !$role ){
            $role = CobaltHelperUsers::getRole();
        }
        
        //query
        $query->select('count(*)');
        $query->from('#__people AS p');
        $query->leftJoin("#__users AS u ON ( u.id = p.owner_id OR u.id = p.assignee_id ) AND u.published=1");
        
        //filter based on id and role
        if  ( $role != 'exec' ){
            if ( $role == 'manager' ){
                $query->where("u.team_id=$team");
            }else{
                $query->where("( p.owner_id=$id OR p.assignee_id=$id )");
            }
        }

        $query->where("p.published=1");
        
        //return results
        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * Get people emails associated with users
     * @param $id int User Id to filter for
     * @param $team int Team Id associated to user
     * @param $role String User role to filter for
     * @return int Count of people returned from database
     */
    function getPeopleEmails($id=null){
        
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        if ( !$id ){
            $id = CobaltHelperUsers::getUserId();
        }
        
        //query
        $query->select('p.id,p.email');
        $query->from('#__people AS p');
        $query->where("p.owner_id=".$id);
        
        //return results
        $db->setQuery($query);
        $results = $db->loadAssocList();

        //clean results
        $return = array();
        if ( $results ){
            foreach ( $results as $key=>$user ){
                $return[$user['id']] = $user['email'];
            }
        }

        return $return;
    }
    
    /**
     * Get company count associated with users
     * @param $id int User Id to filter for
     * @param $team int Team Id associated to user
     * @param $role String User role to filter for
     * @return int Count of companies returned from database
     */
    function getCompanyCount($id,$team,$role){
        
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        //query
        $query->select('count(*)');
        $query->from('#__companies AS c');
        $query->leftJoin("#__users AS u ON u.id = c.owner_id AND u.published=1");
        
        //filter based on id and role
        /**
        if  ( $role != 'exec' ){
            if ( $role == 'manager' ){
                $query->where("u.team_id=$team");
            }else{
                $query->where(array("c.owner_id=$id"));
            }
        }
         **/

        $query->where("c.published=1");
        
        //return results
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    /**
     * Get commission rates for users
     * @param int $id user id requested else logged in user id is used
     * @return int commission rate
     */
    function getCommissionRate($id=null){
       //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        //logged in user
        if ( $id == null ){
            $user = JFactory::getUser();
            $user_id = CobaltHelperUsers::getUserId();
        }else{
            $user_id = $id;
        }
        
        //get id
        $query->select("commission_rate");
        $query->from("#__users");
        $query->where('id='.$user_id);
        $query->where("published=1");
        
        //return id
        $db->setQuery($query);
        return $db->loadResult();
    }


    function isFullscreen() {
        
        return true;

    }

    function getDateFormat($php=TRUE){

        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        //logged in user
        $user = JFactory::getUser();
        $user_id = CobaltHelperUsers::getUserId();
        
        //get id
        $query->select("date_format");
        $query->from("#__users");
        $query->where('id='.$user_id);
        
        //return id
        $db->setQuery($query);
        $format = $db->loadResult();

        if ( !$php ){
            $format = str_replace("m","mm",$format);
            $format = str_replace("d","dd",$format);
            $format = str_replace("y","yy",$format);
        }

        return $format;

    }

    function getTimeFormat($id=null){

        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        //logged in user
        if ( $id == null ){
            $user = JFactory::getUser();
            $user_id = CobaltHelperUsers::getUserId();
        }else{
            $user_id = $id;
        }
        
        //get id
        $query->select("time_format");
        $query->from("#__users");
        $query->where('id='.$user_id);
        
        //return id
        $db->setQuery($query);
        return $db->loadResult();

    }

    function getTimezone($id=null){

        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        
        //logged in user
        if ( $id == null ){
            $user = JFactory::getUser();
            $user_id = CobaltHelperUsers::getUserId();
        }else{
            $user_id = $id;
        }
        
        //get id
        $query->select("time_zone");
        $query->from("#__users");
        $query->where('id='.$user_id);
        
        //return id
        $db->setQuery($query);
        return $db->loadResult();

    }

    function getLoggedInUser()
    {
        $baseUser = JFactory::getUser();
        $user_id = $baseUser->get('id');

        if($user_id > 0) {
            $db = JFactory::getDBO();

            $query = $db->getQuery(true);
            $query->select('c.*,u.email');
            $query->from('#__users AS c');
            $query->where('c.id = '.$db->Quote($user_id));
            $query->leftJoin("#__users AS u ON u.id=c.id");
            $db->setQuery($query);

            $user = $db->loadObject();

            $user->emails = CobaltHelperUsers::getEmails($user->id);
        } else {
           return false;
        }

        return $user;
    }

    function getUser($user_id,$array=FALSE)
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('c.*');
        $query->from('#__users AS c');
        $query->where('c.id = '.$db->Quote($user_id));
        $db->setQuery($query);

        if ( !$array ){
            $user = $db->loadObject();
        }else{
            $user = $db->loadColumn();
        }

        return $user;
    }

    /** Determine if logged in user ( or specified user ) is an administrator **/
    function isAdmin($user_id=null){

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $user_id = $user_id ? $user_id : CobaltHelperUsers::getUserId();

        $query->select('c.admin');
        $query->from('#__users AS c');
        $query->where('c.id = '.$db->Quote($user_id));
        $db->setQuery($query);
        $user = $db->loadObject();

        $query = $db->getQuery(true);
        if ( $user ){
            return $user->admin == 1;
        }else{
            return false;
        }

    }

    /** Determine if logged in user ( or specified user ) can delete items **/
    function canDelete($user_id=null){

        $db = JFactory::getDBO();

        $user_id = $user_id ? $user_id : CobaltHelperUsers::getUserId();

        $query = $db->getQuery(true);
        $query->select('c.admin,c.can_delete');
        $query->from('#__users AS c');
        $query->where('c.id = '.$db->Quote($user_id));
        $db->setQuery($query);
        $user = $db->loadObject();

        return ( $user->admin == 1 || $user->can_delete == 1 );

    }

     /** Determine if logged in user ( or specified user ) can export items **/
    function canExport($user_id=null){

        $db = JFactory::getDBO();

        $user_id = $user_id ? $user_id : CobaltHelperUsers::getUserId();

        $query = $db->getQuery(true);
        $query->select('c.exports,c.admin');
        $query->from('#__users AS c');
        $query->where('c.id = '.$db->Quote($user_id));
        $db->setQuery($query);
        $user = $db->loadObject();

        return ( $user->exports == 1 || $user->admin == 1 );

    }

    function authenticateAdmin(){
        if (!self::isAdmin()){
            $app =& JFactory::getApplication();
            $app->redirect('index.php');
        }
    }

    //get assigned language for users from database
    function getLanguage(){
        $userId = CobaltHelperUsers::getUserId();

        if ( $userId > 0 ){

            $db =& JFactory::getDBO();
            $query = $db->getQuery(true);

            $query->select("language")->from("#__users")->where('id='.$userId);
            $db->setQuery($query);
            $lang = $db->loadResult();

            return ( $lang != "" && $lang != null ) ? $lang : JFactory::getConfig()->get('language');

        } else { 

            return JFactory::getConfig()->get('language');

        }
    }

    //load assigned language for users into joomla
    function loadLanguage(){
        $lng = self::getLanguage();
        $lang =& JFactory::getLanguage();
        $lang->load("joomla",JPATH_BASE,$lng);
        $lang->setDefault($lng);
    }

 }