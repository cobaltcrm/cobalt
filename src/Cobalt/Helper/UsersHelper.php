<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Helper;

use JFactory;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class UsersHelper
{

    //get users depending on logged in member type
    public static function getUsers($id=null,$idsOnly=FALSE)
    {
        //filter based on current logged in user
        $user = UsersHelper::getUserId();
        $user_role = UsersHelper::getRole();
        $results = array();

        //user role filters
        if ($user_role != 'basic') {

            //get db
            $db = \Cobalt\Container::fetch('db');
            $query = $db->getQuery(true);

            $select = ( $idsOnly ) ? "id AS value,CONCAT(first_name,' ',last_name) AS label" : "*";

            //get users
            $query->select($select);
            $query->from("#__users");

            //exec
            if ($id) {
                $query->where("id=$id");
            } elseif ($user_role == 'exec') {
                $query->where("id<>".$user);
            //manager
            } elseif ($user_role == 'manager') {
                $team_id = UsersHelper::getTeamId();
                $query->where('team_id='.$team_id.' AND id <> '.$user);
            }

            //load results
            $query->where("published=1");
            $db->setQuery($query);
            $results = $db->loadAssocList();

        }

        //assign other user info
        if (!$idsOnly) {
            if ( count($results) > 0 ) {
                foreach ($results as $key=>$user) {
                    $results[$key]['emails'] = UsersHelper::getEmails($user['id']);
                }
            }
        }

        //return
        return $results;
    }

    public static function getEventCount($id,$team,$role)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //query
        $query->select('count(*)');
        $query->from('#__events AS e');
        $query->leftJoin("#__users AS u ON u.id = e.owner_id AND u.published=1");

        //filter based on id and role
        if ($role != 'exec') {
            if ($role == 'manager') {
                $query->where("u.team_id=$team");
            } else {
                $query->where("e.owner_id=$id");
            }
        }
        $query->where("e.published=1");


        //return results
        $db->setQuery($query);

        return $db->loadResult();
    }

    public static function getFirstName($id=null)
    {
        $id = $id ? $id : self::getLoggedInUser()->id;

        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);
        $query->clear()->select("first_name")->from("#__users")->where("id=".$id);
        $db->setQuery($query);

        return $db->loadResult();

    }

    //get all company users
    public static function getCompanyUsers($id=null)
    {
        //filter based on current logged in user
        $app = \Cobalt\Container::fetch('app');
        $user = $app->getUser();
        $user_role = UsersHelper::getRole();
        $results = array();

        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //get users
        $query->select("*");
        $query->from("#__users");

        //load results
        $query->where("published=1");
        $db->setQuery($query);
        $results = $db->loadAssocList();

        //assign other user info
        foreach ($results as $key=>$user) {
            $results[$key]['emails'] = UsersHelper::getEmails($user['id']);
        }

        //return
        return $results;
    }

    /**
     * Get user email address for a user
     * @param  int   $id user id to get emails for
     * @return mixed $results db results
     */
    public static function getEmails($id = null)
    {
        //Cobalt User ID
        if (!$id)
        {
            $id = UsersHelper::getUserId();
        }

        //get dbo
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //query
        $query->select("*")->from("#__users_email_cf")->where("member_id=" . (int) $id);

        //load and return results
        $db->setQuery($query);
        $email_cf = $db->loadAssoc();

        $query->clear()
                ->select("j.email,u.id AS member_id")
                ->from("#__users AS u")
                ->leftJoin("#__users AS j ON j.id=u.id")
                ->where("u.id=" . (int) $id);

        $db->setQuery($query);
        $emails = $db->loadAssoc();

        if (is_array($email_cf))
        {
            $emails = array_merge($email_cf, $emails);
        }

        return $emails;

    }

    //return current logged in Cobalt user ID based on Joomla Id
    public static function getUserId()
    {
        $app = \Cobalt\Container::fetch('app');
        return $app->getUser()->get('id');
    }

    //return user role
    public static function getRole($user_id=null)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //logged in user
        $app = \Cobalt\Container::fetch('app');
        $user = $app->getUser();
        if (!$user_id) {
            $user_id = $user->get('id');
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
    public static function getTeamId($user_id=null)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        $user_id = $user_id ?: UsersHelper::getUserId();

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
    public static function getTeams($id=null)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //query
        $query->select("t.*,u.first_name,u.last_name,(CASE WHEN (t.name IS NOT NULL) THEN t.name ELSE CONCAT(u.first_name,NULL,u.last_name) END) AS team_name");
        $query->from("#__teams AS t");
        $query->leftJoin("#__users AS u ON u.id = t.leader_id AND u.published=1");

        //search for specific team
        if ($id)
        {
            $query->where("t.team_id = $id");
        }

        $user_role = UsersHelper::getRole();
        $user_id = UsersHelper::getUserId();

        if ($user_role == 'manager')
        {
            $team_id = UsersHelper::getTeamId();
            $query->where('t.team_id=' . $team_id);
        }

        //return results
        $db->setQuery($query);
        $teams = $db->loadAssocList();

        return $teams;
    }
    /**
     * Get users associated with a specific team
     * @param  int   $id specific team id requested
     * @return mixed $results results from database
     */
    public static function getTeamUsers($id=null,$idsOnly=FALSE)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        if ($idsOnly) {
            $select = "u.id";
        } else {
            $select = "u.*";
        }

        $id = $id ? $id : UsersHelper::getTeamId();

        //query
        $query->select($select)->from("#__users AS u")->where("u.team_id=$id AND u.published=1");

        //return results
        $db->setQuery($query);

        if ($idsOnly) {
            $users = $db->loadColumn();
        } else {
            $users = $db->loadAssocList();
        }

        return $users;
    }

    public static function getAllSharedUsers()
    {
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        $query->select("id AS value,CONCAT(first_name,' ',last_name) AS label")
            ->from("#__users");

        $role = UsersHelper::getRole();

        switch ($role) {
            case "manager":
            case "basic":
                $query->where("team_id=".UsersHelper::getTeamId());
            break;
        }

        $db->setQuery($query);
        $users = $db->loadObjectList();

        return $users;

    }

    public static function getItemSharedUsers($itemId, $itemType)
    {
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        $query->select("s.user_id AS value, CONCAT(u.first_name, ' ', u.last_name) AS label")
            ->from("#__shared AS s")
            ->leftJoin("#__users AS u ON u.id = s.user_id")
            ->where("s.item_id=" . (int) $itemId)
            ->where("s.item_type=" . $db->q($itemType));

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
    public static function getDealCount($id,$team,$role)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //query
        $query->select('count(*)');
        $query->from('#__deals AS d');
        $query->leftJoin("#__users AS u ON u.id = d.owner_id AND u.published=1");

        //filter based on id and role
        if ($role != 'exec') {
            if ($role == 'manager') {
                $query->where("u.team_id=$team");
            } else {
                $query->where("d.owner_id=$id");
            }
        }
        $query->where("d.published=1");

        //return results
        $db->setQuery($query);

        return $db->loadResult();
    }

    /**
     * Get documents count associated with users
     * @param $id int User Id to filter for
     * @param $team int Team Id associated to user
     * @param $role String User role to filter for
     * @return int Count of documents returned from database
     */
    public static function getDocumentCount($id,$team,$role)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //query
        $query->select('count(*)');
        $query->from('#__documents AS d');
        $query->leftJoin("#__users AS u ON u.id = d.owner_id AND u.published=1");

        //filter based on id and role
        if ($role != 'exec') {
            if ($role == 'manager') {
                $query->where("u.team_id=$team");
            } else {
                $query->where("d.owner_id=$id");
            }
        }
        $query->where("d.shared=1");

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
    public static function getPeopleCount($id = null, $team = null, $role = null)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        if (!$id)
        {
            $id = UsersHelper::getUserId();
        }

        if (!$team)
        {
            $team = UsersHelper::getTeamId();
        }

        if (!$role)
        {
            $role = UsersHelper::getRole();
        }

        //query
        $query->select('count(*)');
        $query->from('#__people AS p');
        $query->leftJoin("#__users AS u ON ( u.id = p.owner_id OR u.id = p.assignee_id ) AND u.published = 1");

        //filter based on id and role
        if ($role != 'exec')
        {
            if ($role == 'manager')
            {
                $query->where("u.team_id = $team");
            }
            else
            {
                $query->where("( p.owner_id = $id OR p.assignee_id = $id )");
            }
        }

        $query->where("p.published = 1");

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
    public static function getUsersCount($id = null, $team = null, $role = null)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        if (!$id)
        {
            $id = UsersHelper::getUserId();
        }

        if (!$team)
        {
            $team = UsersHelper::getTeamId();
        }

        if (!$role)
        {
            $role = UsersHelper::getRole();
        }

        //query
        $query->select('count(*)');
        $query->from("#__users AS u");
        $query->where("u.published = 1");

        //filter based on id and role
        if ($role != 'exec')
        {
            if ($role == 'manager')
            {
                $query->where("u.team_id = $team");
            }
            else
            {
                $query->where("( p.owner_id = $id OR p.assignee_id = $id )");
            }
        }

        //return results
        $db->setQuery($query);

        return $db->loadResult();
    }

    /**
     * Get deal_custom count associated with users
     * @return int Count of deal_custom returned from database
     */
    public static function getDealcustomCount()
    {
        return self::getItemsCount('deal_custom');
    }

    /**
     * Get people_custom count associated with users
     * @return int Count of people_custom returned from database
     */
    public static function getPeoplecustomCount()
    {
        return self::getItemsCount('people_custom');
    }

    /**
     * Get companycustom count associated with users
     * @return int Count of companycustom returned from database
     */
    public static function getCompanycustomCount()
    {
        return self::getItemsCount('company_custom');
    }

    /**
     * Get sources count associated with users
     * @return int Count of sources returned from database
     */
    public static function getSourcesCount()
    {
        return self::getItemsCount('stages');
    }

    /**
     * Get stages count associated with users
     * @return int Count of stages returned from database
     */
    public static function getStagesCount()
    {
        return self::getItemsCount('stages');
    }

    /**
     * Get categories count associated with users
     * @return int Count of categories returned from database
     */
    public static function getCategoriesCount()
    {
        return self::getItemsCount('notes_categories');
    }

    /**
     * Get an item count associated with users
     * @param $item string name of the item
     * @return int Count of items returned from database
     */
    public static function getItemsCount($item = null)
    {
        if (!$item || !UsersHelper::isAdmin())
        {
            return false;
        }

        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //query
        $query->select('count(*)');
        $query->from('#__' . $item);

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
    public static function getPeopleEmails($id=null)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        if (!$id) {
            $id = UsersHelper::getUserId();
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
        if ($results) {
            foreach ($results as $key=>$user) {
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
    public static function getCompanyCount($id,$team,$role)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //query
        $query->select('count(*)');
        $query->from('#__companies AS c');
        $query->leftJoin("#__users AS u ON u.id = c.owner_id AND u.published=1");

        //filter based on id and role
        /**
        if ($role != 'exec') {
            if ($role == 'manager') {
                $query->where("u.team_id=$team");
            } else {
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
     * @param  int $id user id requested else logged in user id is used
     * @return int commission rate
     */
    public static function getCommissionRate($id=null)
    {
       //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //logged in user
        if ($id == null) {
            $app = \Cobalt\Container::fetch('app');
            $user = $app->getUser();
            $user_id = $user->get('id');
        } else {
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

    public static function isFullscreen()
    {
        return true;

    }

    public static function getDateFormat($php=TRUE)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //logged in user
        $app = \Cobalt\Container::fetch('app');
        $user = $app->getUser();
        $user_id = $user->get('id');

        //get id
        $query->select("date_format");
        $query->from("#__users");
        $query->where('id='.$user_id);

        //return id
        $db->setQuery($query);
        $format = $db->loadResult();

        if (!$php) {
            $format = str_replace("m","mm",$format);
            $format = str_replace("d","dd",$format);
            $format = str_replace("y","yy",$format);
        }

        return $format;

    }

    public static function getTimeFormat($id=null)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //logged in user
        if ($id == null) {
            $app = \Cobalt\Container::fetch('app');
            $user = $app->getUser();
            $user_id = $user->get('id');
        } else {
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

    public static function getTimezone($id=null)
    {
        //get db
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        //logged in user
        if ($id == null) {
            $app = \Cobalt\Container::fetch('app');
            $user = $app->getUser();
            $user_id = $user->get('id');
        } else {
            $user_id = $id;
        }

        if ($user_id)
        {
            $query->select("time_zone");
            $query->from("#__users");
            $query->where('id='.$user_id);

            return $db->setQuery($query)->loadResult();
        }

        return null;
    }

    public static function getLoggedInUser()
    {
        $app = \Cobalt\Container::fetch('app');
        $user = $app->getUser();

        if ($user->get('id'))
        {
            $user->emails = UsersHelper::getEmails($user->get('id'));
            return $user;
        }

        return false;
    }

    public static function getUser($user_id,$array=FALSE)
    {
        $db = \Cobalt\Container::fetch('db');

        $query = $db->getQuery(true);
        $query->select('c.*');
        $query->from('#__users AS c');
        $query->where('c.id = '.$db->Quote($user_id));
        $db->setQuery($query);

        if (!$array) {
            $user = $db->loadObject();
        } else {
            $user = $db->loadColumn();
        }

        return $user;
    }

    /** Determine if logged in user ( or specified user ) is an administrator **/
    public static function isAdmin($user_id=null)
    {
        $db = \Cobalt\Container::fetch('db');
        $query = $db->getQuery(true);

        $user_id = $user_id ? $user_id : UsersHelper::getUserId();

        $query->select('c.admin');
        $query->from('#__users AS c');
        $query->where('c.id = '.$db->Quote($user_id));
        $db->setQuery($query);
        $user = $db->loadObject();

        $query = $db->getQuery(true);
        if ($user) {
            return $user->admin == 1;
        } else {
            return false;
        }

    }

    /** Determine if logged in user ( or specified user ) can delete items **/
    public static function canDelete($user_id=null)
    {
        $app = \Cobalt\Container::fetch('app');
        $user = $app->getUser($user_id);

        return ( $user->admin == 1 || $user->can_delete == 1 );

    }

     /** Determine if logged in user ( or specified user ) can export items **/
    public static function canExport($user_id = null)
    {
        $app = \Cobalt\Container::fetch('app');
        $user = $app->getUser($user_id);

        return ( $user->exports == 1 || $user->admin == 1 );

    }

    public static function authenticateAdmin()
    {
        if (!self::isAdmin()) {
            $app = \Cobalt\Container::fetch('app');
            $app->redirect('index.php');
        }
    }

    //get assigned language for users from database
    public static function getLanguage()
    {
        $userId = UsersHelper::getUserId();

        if ($userId > 0) {

            $db = \Cobalt\Container::fetch('db');
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
    public static function loadLanguage()
    {
        $lng = self::getLanguage();
        $lang = JFactory::getLanguage();
        $lang->load("joomla",JPATH_BASE,$lng);
        $lang->setDefault($lng);
    }

	/**
	 * Hashes a password using the current encryption.
	 *
	 * @param   string  $password  The plaintext password to encrypt.
	 *
	 * @return  string  The encrypted password.
	 *
	 * @since   1.0
	 */
	public static function hashPassword($password)
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}
 }
