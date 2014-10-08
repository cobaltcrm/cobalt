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
use Cobalt\Table\UserTable;
use Joomla\Registry\Registry;
use Cobalt\Helper\ConfigHelper;
use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\DateHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Users extends DefaultModel
{
    public function store()
    {
        //Load Tables
        $row = $this->getTable('User');
        $data = $this->app->input->post->getArray();

        //$this->app->triggerEvent('onBeforeCRMUserSave', array(&$data));

        //date generation
        $date = date('Y-m-d H:i:s');

        if ( !array_key_exists('id',$data) ) {
            $data['created'] = $date;
            $data['time_zone'] = ConfigHelper::getConfigValue('timezone');
            $data['time_format'] = ConfigHelper::getConfigValue('time_format');

            $data['block'] = 0;
            $data['registerDate'] = $date;
            $data['activation'] = 0;
            $data['params'] = "";
        }

        if ( array_key_exists('password',$data) && $data['password'] != "" ) {
	        $data['password'] = UsersHelper::hashPassword($data['password']);
        } else {
            unset($data['password']);
        }

        //generate team data
        $model = new Teams;
        if ( array_key_exists('id',$data) && $data['id'] > 0 ) {
            $teamId = $this->getTeamId($data['id']);
        }

        //assign user priviliges
        $data['modified'] = $date;
        $data['admin'] = ( array_key_exists ('admin',$data) && $data['admin'] == '1' ) ? 1 : 0;
        $data['exports'] = ( array_key_exists ('exports',$data) && $data['exports'] == 'on' ) ? 1 : 0;
        $data['can_delete'] = ( array_key_exists ('can_delete',$data) && $data['can_delete'] == 'on' ) ? 1 : 0;

        //republish / register users
        if ( array_key_exists('id',$data) && $data['id'] != "" ) {
            $query = $this->db->getQuery(true);
            $query->clear()->select("id")->from("#__users")->where("id=".$data['id']);
            $this->db->setQuery($query);
            $id = $this->db->loadResult();
            if ($id) {
                $data['id'] = $id;
                $data['published'] = 1;
            }
        }

        if ( array_key_exists('team_id',$data) && $data['team_id'] == "" ) {
            unset($data['team_id']);
        }

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        if ( array_key_exists('role_type',$data) && $data['role_type'] == "manager"  ) {
            $teamModel = new Teams;
            $teamName = array_key_exists('team_name',$data) ? $data['team_name'] : "";
            $teamModel->createTeam($row->id,$teamName);
        }

        //if we are downgrading a users priviliges
        if ( array_key_exists('manager_assignment',$data) && $data['manager_assignment'] != null && $data['manager_assignment'] != "" ) {
            $newTeamId = $this->getTeamId($data['manager_assignment']);
            $model->updateTeam($teamId,$newTeamId);
        }

        $row->id = ( array_key_exists('id',$data) && $data['id'] > 0 ) ? $data['id'] : $this->db->insertId();
        $this->updateUserMap($row);

        //$this->app->triggerEvent('onAfterCRMUserSave', array(&$data));

        return true;
    }

    public function updateUserMap($user)
    {
        $query = $this->db->getQuery(true);

        $query->delete("#__user_usergroup_map")->where("user_id=".$user->id);
        $this->db->setQuery($query);
        $this->db->execute();

        $groupId = $user->admin == 1 ? "2" : "2";
        $query->clear();
        $query->insert("#__user_usergroup_map")->columns(array($this->db->quoteName('user_id'),$this->db->quoteName('group_id')))->values($this->db->quote($user->id).', '.$this->db->quote($groupId));
        $this->db->setQuery($query);
        $this->db->execute();

    }

    public function _buildQuery()
    {
         //get dbo
        $query = $this->db->getQuery(true);

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

    public function getUsers($id = null)
    {
        //get dbo
        $query = $this->_buildQuery();

        //sort
        $query->order($this->getState('Users.filter_order') . ' ' . $this->getState('Users.filter_order_Dir'));
        
        if ($id)
        {
            $query->where("u.id = $id");
        }

        $query->where("u.published = 1");

        /** ------------------------------------------
         * Set query limits/ordering and load results
         */
        $limit = $this->getState($this->_view . '_limit');
        $limitStart = $this->getState($this->_view . '_limitstart');

        if ($limit != 0)
        {
            $query->order($this->getState('Users.filter_order') . ' ' . $this->getState('Users.filter_order_Dir'));

            if ($limitStart >= $this->getTotal())
            {
                $limitStart = 0;
                $limit = 10;
                $limitStart = ($limit != 0) ? (floor($limitStart / $limit) * $limit) : 0;
                $this->state->set($this->_view . '_limit', $limit);
                $this->state->set($this->_view . '_limitstart', $limitStart);
            }

            $query .= " LIMIT ".($limit)." OFFSET ".($limitStart);
        }

        //return results
        $this->db->setQuery($query);

        return $this->db->loadAssocList();
    }

    public function getUser($id=null)
    {
        $this->app = \Cobalt\Container::fetch('app');
        $id = $id ? $id : $this->app->input->get("id");

        if ($id > 0) {

            $query = $this->_buildQuery();

            if ($id) {
                $query->where("u.id=$id");
            }

            $this->db->setQuery($query);

            return $this->db->loadAssoc();

        } else {
            return (array) $this->getTable('User');
        }

    }

    public function populateState()
    {
        //get states
        $this->app = \Cobalt\Container::fetch('app');
        $filter_order = $this->app->getUserStateFromRequest('Users.filter_order', 'filter_order', 'u.last_name');
        $filter_order_Dir = $this->app->getUserStateFromRequest('Users.filter_order_Dir', 'filter_order_Dir', 'asc');

        $state = new Registry;

        //set states
        $state->set('Users.filter_order', $filter_order);
        $state->set('Users.filter_order_Dir', $filter_order_Dir);

        // Get pagination request variables
        $limit = $this->app->getUserStateFromRequest($this->_view . '_limit', 'limit', 10);
        $limitstart = $this->app->getUserStateFromRequest($this->_view . '_limitstart', 'limitstart', 0);

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $state->set($this->_view . '_limit', $limit);
        $state->set($this->_view . '_limitstart', $limitstart);

        $this->setState($state);
    }

    public function getJoomlaUsersToAdd()
    {
        //get dbo
        $query = $this->db->getQuery(true);

        //select
        $query->select("ju.id,ju.name,ju.username,ju.email,cu.id as cid,cu.published");
        $query->from("#__users AS ju");

        //left join essential data
        $query->leftJoin("#__users AS cu ON ju.id = cu.id");

        //return results
        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();
        $users = array();
        foreach ($results as $key => $user) {
            if (!$user['cid'] || $user['published'] == -1) {
                $name = explode(" ",$user['name']);
                $user['first_name'] = array_key_exists(0,$name) ? $name[0] : "";
                $user['last_name'] = array_key_exists(1,$name) ? $name[1] : "";
                $users[$user['id']] = $user;
            }
        }

        return $users;
    }

    public function getCobaltUsers($idsOnly=FALSE)
    {
        //get dbo
        $query = $this->db->getQuery(true);

        //select
        $query->select("u.id AS value, CONCAT(u.first_name, ' ', u.last_name) AS label");
        $query->from("#__users AS u");
        $query->where("u.published=1");

        //return results
        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        return $results;
    }

    public function getJoomlaUsersToAddList($namesOnly=FALSE)
    {
        //get dbo
        $query = $this->db->getQuery(true);

        //select
        $query->select("ju.id,ju.name,ju.username,cu.id as cid,cu.published");
        $query->from("#__users AS ju");

        //left join essential data
        $query->leftJoin("#__users AS cu ON ju.id = cu.id");

        //return results
        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        $users = array();
        foreach ($results as $key=>$user) {
            if (!$user['cid'] || $user['published'] == -1) {
                if ($namesOnly) {
                    $users[] = $user['name'];
                } else {
                    $users[$user['id']] = $user['name'];
                }
            }
        }

        return $users;
    }

    //return user team id
    public function getTeamId($user_id)
    {
        //get db
        $query = $this->db->getQuery(true);

        //get id
        $query->select("team_id");
        $query->from("#__users");
        $query->where('id='.$user_id);

        //return id
        $this->db->setQuery($query);

        return $this->db->loadResult();
    }

    public function delete($ids)
    {
        //get db
        $query = $this->db->getQuery(true);

        //$this->app->triggerEvent('onBeforeCRMUserDelete', array(&$ids));

        $query->update("#__users");

        if (is_array($ids))
        {
            $query->where("id IN(".implode(',',$ids).")");
        }
        else
        {
            $query->where("id=".$ids);
        }

        $query->set("published=-1");
        $this->db->setQuery($query);

        if ( $this->db->execute() )
        {
            //$this->app->trigger('onAfterCRMUserDelete', array(&$ids));

            return true;
        }
        else
        {
            return false;
        }

    }

    /**
     * Describe and configure columns for jQuery dataTables here.
     *
     * 'data'       ... column id
     * 'orderable'  ... if the column can be ordered by user or not
     * 'ordering'   ... name of the column in SQL query with table prefix
     * 'sClass'     ... CSS class applied to the column
     * (other settings can be found at dataTable documentation)
     *
     * @return array
     */
    public function getDataTableColumns()
    {
        $columns = array();
        $columns[] = array('data' => 'id', 'orderable' => false, 'sClass' => 'text-center');
        $columns[] = array('data' => 'name', 'ordering' => 'u.last_name');
        $columns[] = array('data' => 'username', 'ordering' => 'ju.username');
        $columns[] = array('data' => 'team_id', 'ordering' => 'u.team_id');
        $columns[] = array('data' => 'email', 'ordering' => 'ju.email');
        $columns[] = array('data' => 'role_type', 'ordering' => 'u.role_type');
        $columns[] = array('data' => 'last_login', 'ordering' => 'ju.lastvisitDate');

        return $columns;
    }

    /**
     * Method transforms items to the format jQuery dataTables needs.
     * Algorithm is available in parent method, just pass items array.
     *
     * @param   array of object of items from the database
     * @return  array in format dataTables requires
     */
    public function getDataTableItems($items = array())
    {
        if (!$items)
        {
            $items = $this->getUsers();
        }

        return parent::getDataTableItems($items);
    }

    /**
     * Prepare HTML field templates for each dataTable column.
     *
     * @param   string column name
     * @param   object of item
     * @return  string HTML template for propper field
     */
    public function getDataTableFieldTemplate($column, $item)
    {
        $template = '';

        switch ($column)
        {
            case 'id':
                $template .= '<input type="checkbox" class="export" name="ids[]" value="' . $item->id . '" />';
                break;
            case 'name':
                $template .= '<a href="'.RouteHelper::_('index.php?view=users&layout=edit&id='.$item->id).'">'.$item->first_name.' '.$item->last_name.'</a>';
                break;
            case 'team_id':
                if (isset($item->team_id) && $item->team_id)
                {
                    $template .= $item->team_name . TextHelper::_("COBALT_TEAM_APPEND");
                }
                break;
            case 'role_type':
                $template .= ucwords($item->role_type);
                break;
            case 'last_login':
                $template = DateHelper::formatDate($item->last_login);
                break;
            default:
                if (isset($column) && isset($item->{$column}))
                {
                    $template = $item->{$column};
                }
                else
                {
                    $template = '';
                }
                break;
        }

        return $template;
    }

}
