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

use Joomla\Filter\OutputFilter;
use Cobalt\Table\PeopleTable;
use JUri;
use Cobalt\Helper\RouteHelper;
use JFactory;
use Cobalt\Helper\CobaltHelper;
use Cobalt\Helper\ActivityHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\DropdownHelper;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\TweetsHelper;
use Cobalt\Helper\PeopleHelper;
use Cobalt\Helper\DealHelper;
use Cobalt\Pagination;


// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class People extends DefaultModel
{
    public $_view = null;
    public $_layout = null;
    public $_id = null;
    public $person = null;
    public $recent = false;
    public $published = 1;
    public $company_id = null;
    public $event_id = null;
    public $type = null;
    public $app = null;
    public $person_id = null;
    public $deal_id = null;
    public $query = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->app = \Cobalt\Container::fetch('app');
        $this->_view = $this->app->input->get('view');
        $this->_layout = str_replace('_filter','',$this->app->input->get('layout'));
        $this->_id = $this->app->input->get('id');
    }

    /**
     * Method to store a record
     *
     * @return boolean True on success
     */
    public function store($data=null)
    {
        //Load Tables
        $row = new PeopleTable;
        $oldRow = new PeopleTable;
        if ($data == null) {
            $data = $this->app->input->getArray(array(
                'id' => 'int',
                'first_name' => 'string',
                'last_name' => 'string',
                'company' => 'string',
                'company_id' => 'int',
                'position' => 'string',
                'phone' => 'string',
                'email' => 'email',
                'source_id' => 'int',
                'status_id' => 'int',
                'deal_id' => 'int',
                'type' => 'string',
                'home_address_1' => 'string',
                'home_address_2' => 'string',
                'home_city' => 'string',
                'home_state' => 'string',
                'home_zip' => 'string',
                'home_country' => 'string',
                'work_address_1' => 'string',
                'work_address_2' => 'string',
                'work_city' => 'string',
                'work_country' => 'string',
                'work_state' => 'string',
                'work_zip' => 'string',
                'assignee_name' => 'string',
                'assignee_id' => 'int',
                'assignment_note' => 'string',
                'mobile_phone' => 'string',
                'home_email' => 'email',
                'other_email' => 'email',
                'home_phone' => 'string',
                'fax' => 'string',
                'website' => 'string',
                'facebook_url' => 'string',
                'twitter_user' => 'string',
                'linkedin_url' => 'string',
                'aim' => 'string'
            ));
        }

        //date generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));

        if ( !array_key_exists('id',$data) || ( array_key_exists('id',$data) && $data['id'] <= 0 ) ) {
            $data['created'] = $date;
            $data['owner_id'] = array_key_exists('owner_id',$data) ? $data['owner_id'] : UsersHelper::getUserId();
            $status = "created";
        } else {
            $row->load($data['id']);
            $oldRow->load($data['id']);
            $status = "updated";
        }

        $data['modified'] = $date;

        //generate custom field string
        $customArray = array();
        foreach ($data as $name => $value) {
            if ( strstr($name,'custom_') && !strstr($name,'_input') && !strstr($name,"_hidden") ) {
                $id = str_replace('custom_','',$name);
                $customArray[] = array('custom_field_id'=>$id,'custom_field_value'=>$value);
                unset($data[$name]);
            }
        }

        if ((array_key_exists('company_name',$data) && $data['company_name']!="")  || (array_key_exists('company',$data) && $data['company'] != "")) {

            $company_name = array_key_exists('company_name',$data) ? $data['company_name'] : $data['company'];

            $companyModel = new Company;
            $existingCompany = $companyModel->checkCompanyName($company_name);

            if ($existingCompany=="") {
                $cdata = array();
                $cdata['name'] = $company_name;
                $data['company_id'] = $companyModel->store($cdata)->id;
            } else {
                $data['company_id'] = $existingCompany;
            }
        }

        if ( array_key_exists('company_id',$data) && is_array($data['company_id']) ) {
            $company_name = $data['company_id']['value'];
            $companyModel = new Company;
            $existingCompany = $companyModel->checkCompanyName($company_name);
            if ($existingCompany=="") {
                $cdata = array();
                $cdata['name'] = $company_name;
                $data['company_id'] = $companyModel->store($cdata)->id;
            } else {
                $data['company_id'] = $existingCompany;
            }
        }

        /** retrieving joomla user id **/
        if ( array_key_exists('email',$data) ) {
            $data['id'] = self::associateJoomlaUser($data['email']);
        }

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        $app = \Cobalt\Container::fetch('app');
        $app->triggerEvent('onBeforePersonSave', array(&$row));

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

        $person_id = isset($data['id']) ? $data['id'] : $this->db->insertId();

        /** Updating the joomla user **/
        if ( array_key_exists('id',$data) && $data['id'] != "" ) {
            self::updateJoomlaUser($data);
        }

        ActivityHelper::saveActivity($oldRow, $row,'person', $status);

        //if we receive no custom post data do not modify the custom fields
        if ( count($customArray) > 0 ) {
            CobaltHelper::storeCustomCf($person_id,$customArray,'people');
        }

        //bind to cf tables for deal & person association
        if (isset($data['deal_id']) && $data['deal_id']) 
        {
            $deal = array (
                    'association_id = '.$data['deal_id'],
                    'association_type="deal"',
                    'person_id = '.$row->id,
                    "created = '$date'"
                );
            
            if (!$this->dealsPeople($deal))
            {
                return false;
            }
        }

        //Pass Status to plugin & form ID if available
        $row->status    = $status;
        if ( isset($data) && is_array($data) && array_key_exists('form_id',$data) ) {
            $row->form_id   =  $data['form_id'];
        } else {
            $row->form_id   = '';
        }

        $app->triggerEvent('onAfterPersonSave', array(&$row));

        return $person_id;
    }

    public function updateJoomlaUser($data)
    {
        $name = $data['first_name'].' '.$data['last_name'];

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update("#__users")->set(array("email=".$db->Quote($data['email']),"name=".$db->Quote($name)))->where("id=".$data['id']);
        $db->setQuery($query);
        $db->query();
    }

    public function associateJoomlaUser($email)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select("id")->from("#__users")->where("email=".$db->Quote($email));
        $db->setQuery($query);

        return $db->loadResult();
    }

    /*
     * Method to link deals and people in cf tables
     */

    public function dealsPeople($cfdata)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->insert('#__people_cf');
        $query->set($cfdata);
        $db->setQuery($query);

        if ($db->query()) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Build our query
     */
    public function _buildQuery()
    {
        /** Large SQL Selections **/
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $db->setQuery("SET SQL_BIG_SELECTS=1");
        $db->query();

        $view = $this->app->input->get('view');
        $layout = $this->app->input->get('layout');
        //retrieve person id
        if (!$this->_id) {

            //get filters
            $type   = $this->app->input->get('type') ? $this->app->input->get('type') : $this->type;
            $user   = $this->app->input->get('user');
            $stage  = $this->app->input->get('stage');
            $tag    = $this->app->input->get('tag');
            $status = $this->app->input->get('status');
            $team   = $this->app->input->get('team_id');

            //get session data
            $session    = JFactory::getSession();

            //set user session data
            if ($tag != null) {
                $session->set('people_tag_filter',$tag);
            } else {
                $sess_tag   = $session->get('people_tag_filter');
                $tag = $sess_tag;
            }
        }

        //TODO specific user id, access roles
        $db = JFactory::getDBO();
        //generate query

        $query = $db->getQuery(true);

        $export = $this->app->input->get('export');

        if ($export) {

            $query->select('p.first_name,p.last_name,p.position,p.phone,p.email,p.home_address_1,p.home_address_2,'.
                        'p.home_city,p.home_state,p.home_zip,p.home_country,p.fax,p.website,p.facebook_url,p.twitter_user,'.
                        'p.linkedin_url,p.created,p.tags,p.type,p.info,p.modified,p.work_address_1,p.work_address_2,'.
                        'p.work_city,p.work_state,p.work_zip,p.work_country,p.assignment_note,p.mobile_phone,p.home_email,'.
                        'p.other_email,p.home_phone,c.name as company_name, CONCAT(u2.first_name," ",u2.last_name) AS assignee_name,'.
                        'u.first_name AS owner_first_name,'.
                        'u.last_name AS owner_last_name, stat.name as status_name,'.
                        'source.name as source_name');
            $query->from('#__people AS p');
            $query->leftJoin('#__companies AS c ON c.id = p.company_id');
            $query->leftJoin('#__people_status AS stat ON stat.id = p.status_id');
            $query->leftJoin('#__sources AS source ON source.id = p.source_id');
            $query->leftJoin("#__users AS u ON u.id = p.owner_id");
            $query->leftJoin("#__users AS u2 ON u2.id = p.assignee_id");

        } else {

            $query->select('p.*,c.name as company_name, CONCAT(u2.first_name," ",u2.last_name) AS assignee_name,u.first_name AS owner_first_name,
                        u.last_name AS owner_last_name, stat.name as status_name,stat.color as status_color,
                        source.name as source_name,event.id as event_id,
                        event.name as event_name, event.type as event_type,
                        event.due_date as event_due_date,event.description as event_description');
            $query->from('#__people AS p');
            $query->leftJoin('#__companies AS c ON c.id = p.company_id');
            $query->leftJoin('#__people_status AS stat ON stat.id = p.status_id');
            $query->leftJoin('#__sources AS source ON source.id = p.source_id');
            $query->leftJoin("#__users AS u ON u.id = p.owner_id");
            $query->leftJoin("#__users AS u2 ON u2.id = p.assignee_id");

            //join tasks
            $query->leftJoin("#__events_cf as event_person_cf on event_person_cf.association_id = p.id AND event_person_cf.association_type ='person'");
            $query->leftJoin("#__events as event on event.id = event_person_cf.event_id");

        }

        // group ids
        $query->group("p.id");

        /** ---------------------------------------------------------------
         * Filter data using member role permissions
         */
        $member_id = UsersHelper::getUserId();
        $member_role = UsersHelper::getRole();
        $team_id = UsersHelper::getTeamId();
        $owner_filter = $this->state->get('People.owner_id_filter');
        $owner_filter_team = $this->state->get('People.owner_id_filter', $team_id);
        $owner_filter_member = $this->state->get('People.owner_id_filter', $member_id);
        $owner_type_filter = $this->state->get('People.owner_type_filter');

        if ($owner_filter && $owner_filter == "all")
        {
            if ($member_role != 'exec')
            {
                if ($member_role == 'manager')
                {
                    $query->where("(u.team_id=$owner_filter_team OR u2.team_id=$owner_filter_team)");
                }
                else
                {
                    $query->where("(p.owner_id=$owner_filter_member OR p.assignee_id=$owner_filter_member)");
                }
            }
        }
        elseif ($owner_type_filter == 'team')
        {
            $query->where("(u.team_id=$owner_filter_team OR u2.team_id=$owner_filter_team)");
        }
        elseif ($owner_type_filter == 'member' )
        {
            $query->where("(p.owner_id=$owner_filter_member OR p.assignee_id=$owner_filter_member)");
        }
        else
        {
            if ( !(isset($owner_filter)) )
            {
                if ($this->_id)
                {
                    if ($member_role == "basic")
                    {
                        $query->where("(p.owner_id=$member_id OR p.assignee_id=$member_id)");
                    }
                    if ($member_role == "manager") {
                        $team_members = UsersHelper::getTeamUsers($team_id,TRUE);
                        $team_members = array_merge($team_members,array(0=>$member_id));
                        $query->where("(p.owner_id IN(".implode(',',$team_members).") OR p.assignee_id IN(".implode(',',$team_members)."))");
                    }
                }
                else
                {
                    $query->where("(p.owner_id=$member_id OR p.assignee_id=$member_id)");
                }
            }
        }

        //searching for specific person
        if ($this->_id)
        {
            if ( is_array($this->_id) )
            {
                $query->where("p.id IN (".implode(',',$this->_id).")");
            }
            else
            {
                $query->where("p.id=$this->_id");
            }
        }

        if (!$this->_id)
        {
            if (!$export)
            {

                //filter data
                $item_filter = $this->state->get('People.item_filter', $this->app->input->getString('item'));

                if ($item_filter && $item_filter != 'all')
                {
                    switch ($item_filter)
                    {
                        case 'leads':
                            $query->where("p.type='lead'");
                        break;
                        case 'not_leads':
                            $query->where("p.type='contact'");
                        break;
                    }
                }

                //search with status
                $status_filter = $this->state->get('People.item_filter', $status);

                if ($status_filter && $status_filter != 'any')
                {
                    $query->where('p.status_id='.$status_filter);
                }

                //search by tags
                if ($tag) {

                }

                //get current date
                $date = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));
                $stage_filter = $this->state->get('People.stage_filter', $stage);

                //filter for type
                if ($stage != null  && $stage != 'all') {

                    //filter for deals//tasks due today
                    if ($stage == 'today') {
                        $tomorrow = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() + (1*24*60*60)));
                        $query->where("event.due_date >'$date' AND event.due_date < '$tomorrow'");
                    }

                    //filter for deals//tasks due tomorrow
                    if ($stage == "tomorrow") {
                        $tomorrow = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() + (1*24*60*60)));
                        $query->where("event.due_date='".$tomorrow."'");
                    }

                    //filter for people updated in the last 30 days
                    if ($stage == "past_thirty") {
                        $last_thirty_days = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() - (30*24*60*60)));
                        $query->where("p.modified >'$last_thirty_days'");
                    }

                    //filter for recently added people
                    if ($stage == "recently_added") {
                        $last_five_days = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() - (5*24*60*60)));
                        $query->where("p.modified >'$last_five_days'");
                    }

                    //filter for last imported people
                    if ($stage == "last_import") {

                    }

                } else {
                    //get latest task entry

                    if ($this->recent) {
                        $query->where(  "( event.due_date IS NULL OR event.due_date=(SELECT MIN(e2.due_date) FROM #__events_cf e2cf ".
                                        "LEFT JOIN #__events as e2 on e2.id = e2cf.event_id ".
                                        "WHERE e2cf.association_id=p.id AND e2.published>0) )");
                    }
                }

            }

            /** company filter **/
            if ($this->company_id) {
                $query->where("p.company_id=".$this->company_id);
            }

            if ($this->event_id) {
                $query->where("event.id=".$this->event_id);
            }

            /** person name filter **/
            $person_filter = $this->getState('People.person_name');
            if ($person_filter != null) {
                $query->where("( p.first_name LIKE '%".$person_filter."%' OR p.last_name LIKE '%".$person_filter."%' OR CONCAT(p.first_name,' ',p.last_name) LIKE '%".$person_filter."%')");
            }

        }

        $query->where("p.published=".$this->published);

        //return query string
        return $query;
    }

    /*
     * Method to access people
     *
     * @return array
     */
    public function getPeople()
    {
        //Get query
        $db = JFactory::getDBO();
        $query = $this->_buildQuery();

        $view = $this->app->input->get('view');
        $layout = $this->app->input->get('layout');

        /** ------------------------------------------
         * Set query limits/ordering and load results
         */
        $limit = $this->getState($this->_view.'_limit');
        $limitStart = $this->getState($this->_view.'_limitstart');

        if (!$this->_id && $limit != 0)
        {
            $query->order($this->getState('People.filter_order') . ' ' . $this->getState('People.filter_order_Dir'));

            if ( $limitStart >= $this->getTotal() )
            {
                $limitStart = 0;
                $limit = 10;
                $limitStart = ($limit != 0) ? (floor($limitStart / $limit) * $limit) : 0;
                $this->state->set($this->_view.'_limit', $limit);
                $this->state->set($this->_view.'_limitstart', $limitStart);
            }

            $query .= " LIMIT ".($limit)." OFFSET ".($limitStart);
        }

        $db->setQuery($query);
        $people = $db->loadAssocList();

        //generate query to join deals
        if ( count($people) > 0 ) {

            $export = $this->app->input->get('export');

            if (!$export) {

                //generate query to join notes
                foreach ($people as $key => $person) {

                    /* Notes */
                    $notesModel = new Note;
                    $people[$key]['notes'] = $notesModel->getNotes($person['id'],'people');

                    /* Docs */
                    $docsModel = new Document;
                    $docsModel->set('person_id',$person['id']);
                    $people[$key]['documents'] = $docsModel->getDocuments();

                    /* Tweets */
                    if ($person['twitter_user']!="" && $person['twitter_user']!=" ") {
                        $people[$key]['tweets'] = TweetsHelper::getTweets($person['twitter_user']);
                    }

                }

            }
        }

        $this->app->triggerEvent('onPersonLoad', array(&$people));

        //return results
        return $people;

    }

    /*
     * Method to retrieve person
     */
    public function getPerson($id=null)
    {
        $app = \Cobalt\Container::fetch('app');
        $id = $id ? $id : $app->input->get('id');

        if ($id > 0) {

            $db = JFactory::getDBO();
            //generate query
            //
            $query = $db->getQuery(true);
            $query->select('p.*,c.name as company_name,stat.name as status_name,
                            source.name as source_name, owner.name as owner_name, crmery_user.first_name AS owner_first_name, crmery_user.last_name AS owner_last_name');
            $query->from('#__people AS p');
            $query->leftJoin('#__companies AS c ON c.id = p.company_id AND c.published>0');
            $query->leftJoin('#__people_status AS stat ON stat.id = p.status_id');
            $query->leftJoin('#__sources AS source ON source.id = p.source_id');
            $query->leftJoin('#__users AS owner ON p.owner_id = owner.id');
            $query->leftJoin("#__users AS crmery_user ON crmery_user.id = p.owner_id");

            //searching for specific person
            $query->where("p.published=".$this->published);
            $query->where("p.id='".$id."'");

            //run query and grab results
            $db->setQuery($query);
            $person = $db->loadAssoc();

            /* Deals */
            $dealModel = new Deal();
            $dealModel->set('person_id',$person['id']);
            $person['deals'] = $dealModel->getDeals();;

            /* Notes */
            $notesModel = new Note();
            $person['notes'] = $notesModel->getNotes($person['id'],'person');

            /* Docs */
            $docsModel = new Document();
            $docsModel->set('person_id',$person['id']);
            $person['documents'] = $docsModel->getDocuments();

            /* Tweets */
            if ($person['twitter_user']!="" && $person['twitter_user']!=" ") {
                $person['tweets'] = TweetsHelper::getTweets($person['twitter_user']);
            }

             $this->person = $person;

        } else {

             //TODO update things to OBJECTS
            $person = (array) new PeopleTable;
            $this->person = $person;

        }

        $app->triggerEvent('onPersonLoad', array(&$person));

        return $person;
    }

    /*
     * Method to retrieve list of names and ids
     */
    public function getPeopleList()
    {
        //db object
        $db = JFactory::getDBO();
        //gen query
        $query = $db->getQuery(true);
        $query->select("DISTINCT(p.id),p.first_name,p.last_name");
        $query->from("#__people AS p");
        $query->leftJoin("#__users AS u ON u.id = p.owner_id");
        $query->leftJoin("#__people_cf AS dcf ON dcf.person_id = p.id AND dcf.association_type='deal'");

        //filter based on member access roles
        $user_id = UsersHelper::getUserId();
        $member_role = UsersHelper::getRole();
        $team_id = UsersHelper::getTeamId();

        if ($member_role != 'exec') {

            if ($member_role == 'manager') {
                $query->where("u.team_id=$team_id");
            } else {
                $query->where("(p.owner_id=$user_id OR p.assignee_id=$user_id )");
            }

        }

        $query->where("p.published=".$this->published);

        $associationType = $this->app->input->get('association');
        $associationId = $this->app->input->get('association_id');

        if ($associationType == "company") {
            $query->where("p.company_id=".$associationId);
        }
        if ($associationType == "deal") {
            $query->where("dcf.association_id=".$associationId." AND dcf.association_type='deal'");
        }

        //set query
        $db->setQuery($query);

        //load list
        $row = $db->loadAssocList();
        $blank = array(array('first_name'=>TextHelper::_('COBALT_NONE'),'last_name'=>'','id'=>0));
        $return = array_merge($blank,$row);

        //return results
        return $return;

    }

    /**
     * Get total number of rows for pagination
     */
    public function getTotal()
    {
      if ( empty ( $this->_total ) ) {
          $query = $this->_buildQuery();
          $this->_total = $this->_getListCount($query);
      }

      return $this->_total;
   }

    /**
     * Generate pagination
     */
    public function getPagination()
    {
      // Lets load the content if it doesn't already exist

      if (empty($this->_pagination)) {
         jimport('joomla.html.pagination');
         $total = $this->getTotal();
         $total = $total ? $total : 0;
         $this->_pagination = new Pagination( $total, $this->getState($this->_view.'_limitstart'), $this->getState($this->_view.'_limit'),null,RouteHelper::_('index.php?view=people'));
      }

      return $this->_pagination;

    }

    /**
     * Populate user state requests
     */
    public function populateState()
    {
        //get states
        $app = \Cobalt\Container::fetch('app');
        $view = $this->app->input->get('view');

        //TODO add these limits to the switch statement to support multiple pages and layouts
        // Get pagination request variables
        $limit = $app->getUserStateFromRequest($view.'_limit','limit',10);
        $limitstart = $app->getUserStateFromRequest($view.'_limitstart','limitstart',0);

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->state->set($view.'_limit', $limit);
        $this->state->set($view.'_limitstart', $limitstart);

        //set default filter states for reports
        $filter_order       = $app->getUserStateFromRequest('People.filter_order', 'filter_order', 'p.last_name');
        $filter_order_Dir   = $app->getUserStateFromRequest('People.filter_order_Dir', 'filter_order_Dir', 'asc');
        $person_filter      = $app->getUserStateFromRequest('People.person_name', 'people_name', null);
        $item_filter        = $app->input->getString('item', '');
        $stage_filter       = $app->input->getString('stage', '');
        $status_filter       = $app->input->getString('status', '');
        $ownertype_filter   = $app->input->getRaw('ownertype', null);

        //set states for reports
        $this->state->set('People.filter_order', $filter_order);
        $this->state->set('People.filter_order_Dir', $filter_order_Dir);
        $this->state->set('People.filter_order_Dir', $filter_order_Dir);
        $this->state->set('People.person_name', $person_filter);
        $this->state->set('People.item_filter', $item_filter);
        $this->state->set('People.stage_filter', $stage_filter);
        $this->state->set('People.status_filter', $status_filter);

        if ($ownertype_filter)
        {
            if ($ownertype_filter != 'all')
            {
                $owner_filters = explode(':', $ownertype_filter);
                $this->state->set('People.owner_id_filter', $owner_filters[1]);
                $this->state->set('People.owner_type_filter', $owner_filters[0]);
            }
            else
            {
                $this->state->set('People.owner_id_filter', $ownertype_filter);
            }
        }

    }

    public function getDropdowns()
    {
        $dropdowns['person_type'] = DropdownHelper::generateDropdown('person_type',$this->person['type']);

        return $dropdowns;
    }

    public function searchForContact($contact_name,$idsOnly=TRUE)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $select = $idsOnly ? "CONCAT(first_name,' ',last_name) AS label,id AS value" : "*";

        $query->select($select)
            ->from("#__people")
            ->where("published=".$this->published)
            ->where("LOWER(first_name) LIKE '%".ucwords($contact_name)."%' OR LOWER(last_name) LIKE '%".ucwords($contact_name)."%'");

        $db->setQuery($query);

        $return = $db->loadObjectList();

        return $return;

    }

     /**
     * Checks for existing company by name
     * @param  [var] $name company name to check
     * @return [int] ID of existing company
     */
    public function checkPersonName($name)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('p.id');
        $query->from('#__people AS p');
        $query->where('LOWER(CONCAT(p.first_name," ",p.last_name)) = "'.strtolower($name).'"');
        $db->setQuery($query);
        $existingPerson = $db->loadResult();

        return $existingPerson;
    }

    public function getPeopleNames($json=FALSE)
    {
        $names = $this->getPeopleList();
        $return = array();
        if ( count($names) > 0 ) {
            foreach ($names as $key => $person) {
                $personName = "";
                $personName .= array_key_exists('first_name',$person) ? $person['first_name'] : "";
                $personName .= array_key_exists('last_name',$person) ? " ".$person['last_name'] : "";
                $return[] = array('label'=>$personName,'value'=>$person['id']);
            }
        }

        return $json ? json_encode($return) : $return;
    }

    public function getEmail($person_id)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('p.email');
        $query->from('#__people AS p');
        $query->where('p.id='.$person_id);
        $db->setQuery($query);
        $id = $db->loadResult();

        return $id;

    }

    /** Functions for contact information **/

    public function buildSelect()
    {
    $this->query->select("DISTINCT(p.id),p.*,
                        u.first_name as owner_first_name,u.last_name as owner_last_name,
                        c.id as company_id,c.name as company_name,IF(p.id=d2.primary_contact_id, 1, 0) AS is_primary_contact");

    $this->query->from("#__people AS p");
    $this->query->leftJoin("#__people_cf as cf ON cf.person_id = p.id");
    $this->query->leftJoin("#__users AS u ON u.id = p.owner_id");
    $this->query->leftJoin("#__companies AS c ON c.id = p.company_id");
    $this->query->leftJoin("#__deals AS d ON d.id = cf.association_id AND cf.association_type = 'deal'");
    $this->query->leftJoin("#__deals AS d2 ON d2.primary_contact_id = p.id");

    }

    public function buildWhere()
    {
        if ($this->deal_id) {
            $this->query->where("(cf.association_id = ".$this->deal_id." OR d2.id = ".$this->deal_id.")");
        }

        if ($this->event_id) {
            $event_model = new CobaltModelEvent();
            $event = $event_model->getEvent($this->event_id);
            if ( array_key_exists('association_type',$event) && $event['association_type'] != null ) {
                switch ($event['association_type']) {
                    case "person":
                        $this->query->where("p.id=".$event['association_id']);
                    break;
                    case "deal":
                        $this->query->where("cf.association_id=".$event['association_id']);
                        $this->query->where("cf.association_type='deal'");
                    break;
                    case "company":
                        $this->query->where("p.company_id=".$event['association_id']);
                    break;
                }
            } else {
                return false;
            }
        }

        if ($this->company_id) {
            $this->query->where("p.company_id=".$this->company_id);
        }

        //filter based on member access roles
        $user_id = UsersHelper::getUserId();
        $member_role = UsersHelper::getRole();
        $team_id = UsersHelper::getTeamId();

        if ($member_role != 'exec') {

            if ($member_role == 'manager') {
                $this->query->where("u.team_id=$team_id");
            } else {
                $this->query->where("p.owner_id=$user_id");
            }

        }

        $this->query->where("p.published>0");

        return true;
    }

    public function buildOrder()
    {
        $this->query->order("is_primary_contact DESC");
    }

    public function getContacts()
    {
        $db = JFactory::getDBO();
        $this->query = $db->getQuery(true);

        $this->buildSelect();
        if ( !$this->buildWhere() ) {
            return false;
        }

        $this->buildOrder();

        $db->setQuery($this->query);
        $people = $db->loadAssocList();

        $default_image = JURI::base().'libraries/crm/media/images/person.png';

        $n = count($people);
        for ($i=0;$i<$n;$i++) {
            if ($people[$i]['avatar']=="" && $people[$i]['email']!="") {
                $people[$i]['avatar'] = CobaltHelper::getGravatar($people[$i]['email'],null,false,$default_image);
            } elseif ($people[$i]['avatar']=="") {
                $people[$i]['avatar'] = $default_image;
            }
        }

        return $people;

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
        $columns[] = array('data' => 'avatar', 'ordering' => 'p.avatar');
        $columns[] = array('data' => 'name', 'ordering' => 'p.last_name');
        $columns[] = array('data' => 'company_name', 'ordering' => 'c.name');
        $columns[] = array('data' => 'owner', 'ordering' => 'u.last_name');
        $columns[] = array('data' => 'email', 'ordering' => 'p.email');
        $columns[] = array('data' => 'phone', 'ordering' => 'p.phone');
        $columns[] = array('data' => 'status_name', 'ordering' => 'stat.name');
        $columns[] = array('data' => 'source_name', 'ordering' => 'source.name');
        $columns[] = array('data' => 'type', 'ordering' => 'p.type');
        $columns[] = array('data' => 'notes', 'orderable' => false);
        $columns[] = array('data' => 'address', 'orderable' => false);
        $columns[] = array('data' => 'created', 'ordering' => 'p.created');
        $columns[] = array('data' => 'modified', 'ordering' => 'p.modified');

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
            $items = $this->getPeople();
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

        switch ($column)
        {
            case 'id':
                $template = '<input type="checkbox" class="export" name="ids[]" value="'.$item->id.'" />';
                break;
            case 'avatar':
                if (isset($item->avatar) && $item->avatar)
                {
                    $template = '<img id="avatar_img_'.$item->id.'" data-item-type="people" data-item-id="'.$item->id.'" class="avatar" src="'.JURI::base().'src/Cobalt/media/avatars/'.$item->avatar.'"/>';
                }
                else
                {
                    $template = '<img id="avatar_img_'.$item->id.'" data-item-type="people" data-item-id="'.$item->id.'" class="avatar" src="'.JURI::base().'src/Cobalt/media/images/person.png'.'"/>';
                }
                break;
            case 'name':
                $template = '<a href="'.RouteHelper::_('index.php?view=people&layout=person&id='.$item->id).'">'.$item->first_name.' '.$item->last_name.'</a>';
                break;
            case 'company_name':
                $template = '<a href="'.RouteHelper::_('index.php?view=companies&layout=company&id='.$item->company_id).'">'.$item->company_name.'</a>';
                break;
            case 'owner':
                if (!isset($item->owner_last_name) || !$item->owner_last_name)
                {
                    $item->status_name = TextHelper::_('COBALT_CLICK_TO_EDIT');
                }

                $me = array(array('label' => TextHelper::_('COBALT_ME'), 'value' => UsersHelper::getLoggedInUser()->id));
                $users = UsersHelper::getUsers(null, true);
                $users = array_merge($me, $users);

                $template = '<div class="dropdown">';
                $template .= ' <a href="#" class="dropdown-toggle update-toggle-html" role="button" data-toggle="dropdown" id="oerson_owner_'.$item->id.'_link">';
                $template .=    $item->owner_first_name . ' ' . $item->owner_last_name;
                $template .= ' </a>';
                $template .= ' <ul class="dropdown-menu" aria-labelledby="deal_status_'.$item->id.'" role="menu">';

                if (isset($users) && count($users))
                {
                    foreach ($users as $id => $user)
                    {
                        $template .= '  <li>';
                        $template .= '   <a href="#" class="owner_select dropdown_item" data-field="owner_id" data-item="person" data-item-id="'.$item->id.'" data-value="'.$user['value'].'">';
                        $template .= '    <span class="person-owner-'.OutputFilter::stringURLUnicodeSlug($user['value']).'">'.$user['label'].'</span>';
                        $template .= '   </a>';
                        $template .= '  </li>';
                    }
                }

                $template .= '  </ul>';
                $template .= ' </div>';
                break;
            case 'status_name':
                if (!isset($item->status_id) || !$item->status_id)
                {
                    $item->status_name = TextHelper::_('COBALT_CLICK_TO_EDIT');
                }

                $statuses = PeopleHelper::getStatusList();
                $template = '<div class="dropdown">';
                $template .= ' <a href="#" class="dropdown-toggle update-toggle-html" role="button" data-toggle="dropdown" id="deal_stage_'.$item->id.'_link">';
                $template .= '  <span class="person-status-'.$item->status_name.'">'.$item->status_name.'</span>';
                $template .= ' </a>';
                $template .= ' <ul class="dropdown-menu" aria-labelledby="deal_stage_'.$item->id.'" role="menu">';

                if (isset($statuses) && count($statuses))
                {
                    foreach ($statuses as $id => $status)
                    {
                        $template .= '  <li>';
                        $template .= '   <a href="#" class="status_select dropdown_item" data-field="status_id" data-item="people" data-item-id="'.$item->id.'" data-value="'.$status['id'].'">';
                        $template .= '    <span class="person-status-'.OutputFilter::stringURLUnicodeSlug($status['id']).'">'.$status['name'].'</span>';
                        $template .= '   </a>';
                        $template .= '  </li>';
                    }
                }

                $template .= '  </ul>';
                $template .= ' </div>';
                break;
            case 'source_name':
                if (!isset($item->source_id) || !$item->source_id)
                {
                    $item->source_name = TextHelper::_('COBALT_CLICK_TO_EDIT');
                }

                $sources = DealHelper::getSources(null, true);
                $template = '<div class="dropdown">';
                $template .= ' <a href="#" class="dropdown-toggle update-toggle-html" role="button" data-toggle="dropdown" id="person_source_'.$item->id.'_link">';
                $template .= '  <span class="person-source-'.$item->source_name.'">'.$item->source_name.'</span>';
                $template .= ' </a>';
                $template .= ' <ul class="dropdown-menu" aria-labelledby="person_source_'.$item->id.'" role="menu">';

                if (isset($sources) && count($sources))
                {
                    foreach ($sources as $id => $name)
                    {
                        $template .= '  <li>';
                        $template .= '   <a href="#" class="source_select dropdown_item" data-field="source_id" data-item="people" data-item-id="'.$item->id.'" data-value="'.$id.'">';
                        $template .= '    <span class="person-source-'.OutputFilter::stringURLUnicodeSlug($name).'">'.$name.'</span>';
                        $template .= '   </a>';
                        $template .= '  </li>';
                    }
                }

                $template .= '  </ul>';
                $template .= ' </div>';
                break;
            case 'type':
                if (!isset($item->type) || !$item->type)
                {
                    $item->type = TextHelper::_('COBALT_CLICK_TO_EDIT');
                }

                $types = PeopleHelper::getPeopleTypes(false);
                $template = '<div class="dropdown">';
                $template .= ' <a href="#" class="dropdown-toggle update-toggle-html" role="button" data-toggle="dropdown" id="person_type_'.$item->id.'_link">';
                $template .=    $item->type;
                $template .= ' </a>';
                $template .= ' <ul class="dropdown-menu" aria-labelledby="person_type_'.$item->id.'" role="menu">';

                if (isset($types) && count($types))
                {
                    foreach ($types as $id => $name)
                    {
                        $template .= '  <li>';
                        $template .= '   <a href="#" class="type_select dropdown_item" data-field="type" data-item="people" data-item-id="'.$item->id.'" data-value="'.$id.'">';
                        $template .= '    <span class="person-type-'.OutputFilter::stringURLUnicodeSlug($name).'">'.$name.'</span>';
                        $template .= '   </a>';
                        $template .= '  </li>';
                    }
                }

                $template .= '  </ul>';
                $template .= ' </div>';
                break;
            case 'notes':
                $template = '<a rel="tooltip" title="'.TextHelper::_('COBALT_VIEW_NOTES').'" data-placement="bottom" class="btn" href="#" onclick="Cobalt.openNoteModal('.$item->id.', \'people\');"><i class="glyphicon glyphicon-file"></i></a>';
                break;
            case 'address':
                $template = $item->work_city.'<br>'.$item->work_state.'<br>'.$item->work_zip.'<br>'.$item->work_country;
                break;
            case 'created':
                $template = DateHelper::formatDate($item->created);
                break;
            case 'modified':
                $template = DateHelper::formatDate($item->modified);
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
