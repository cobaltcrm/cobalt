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
defined( '_CEXEC' ) or die( 'Restricted access' );

class CobaltModelPeople extends CobaltModelDefault
{

        var $_view = null;
        var $_layout = null;
        var $_id = null;
        var $person = null;
        var $recent = false;
        var $published = 1;
        var $company_id = null;
        var $event_id = null;
        var $type = null;
        var $app = null;
        var $person_id = null;
        var $deal_id = null;
        var $query = null;

        /**
         * Constructor
         */
        function __construct()
        {
            parent::__construct();

            $this->app = JFactory::getApplication();
            $this->_view = $this->app->input->get('view');
            $this->_layout = str_replace('_filter','',$this->app->input->get('layout'));
            $this->_id = $this->app->input->get('id');
        }

        /**
         * Method to store a record
         *
         * @return    boolean    True on success
         */
        function store($data=null)
        {
            //Load Tables
            $row = JTable::getInstance('people','Table');
            $oldRow = JTable::getInstance('people','Table');
            if ($data == null) {
              $data = $this->app->input->getRequest('post');
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

                $companyModel = new CobaltModelCompany();
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
                $companyModel = new CobaltModelCompany();
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
                $this->setError($this->_db->getErrorMsg());

                return false;
            }

            $dispatcher = JEventDispatcher::getInstance();
            $dispatcher->trigger('onBeforePersonSave', array(&$row));

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

            $person_id = isset($data['id']) ? $data['id'] : $this->_db->insertId();

            /** Updating the joomla user **/
            if ( array_key_exists('id',$data) && $data['id'] != "" ) {
                self::updateJoomlaUser($data);
            }

            CobaltHelperActivity::saveActivity($oldRow, $row,'person', $status);

            //if we receive no custom post data do not modify the custom fields
            if ( count($customArray) > 0 ) {
                CobaltHelperCobalt::storeCustomCf($person_id,$customArray,'people');
            }

            //bind to cf tables for deal & person association
            if ( array_key_exists('deal_id',$data) && $data['deal_id'] != 0 ) {
                $deal = array (
                            'association_id = '.$data['deal_id'],
                            'association_type="deal"',
                            'person_id = '.$row->id,
                            "created = '$date'"
                        );
                if (!$this->dealsPeople($deal)) {
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
            $dispatcher = JEventDispatcher::getInstance();
            $dispatcher->trigger('onAfterPersonSave', array(&$row));

            return $person_id;
        }

        function updateJoomlaUser($data)
        {
            $name = $data['first_name'].' '.$data['last_name'];

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->update("#__users")->set(array("email=".$db->Quote($data['email']),"name=".$db->Quote($name)))->where("id=".$data['id']);
            $db->setQuery($query);
            $db->query();
        }

        function associateJoomlaUser($email)
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

        function dealsPeople($cfdata)
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
        function _buildQuery()
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

                //determine whether we are filtering with a team or user
                if ($team) {
                    $session->set('people_user_filter',null);
                }
                if ($user) {
                    $session->set('people_team_filter',null);
                }

                //set user session data
                if ($type != null) {
                    $session->set('people_type_filter',$type);
                } else {
                    $sess_type  = $session->get('people_type_filter');
                    $type = $sess_type;
                }
                if ($user != null) {
                    $session->set('people_user_filter',$user);
                } else {
                    $sess_user  = $session->get('people_user_filter');
                    $user = $sess_user;
                }
                if ($stage != null) {
                    $session->set('people_stage_filter',$stage);
                } else {
                    $sess_stage = $session->get('people_stage_filter');
                    $stage = $sess_stage;
                }
                if ($tag != null) {
                    $session->set('people_tag_filter',$tag);
                } else {
                    $sess_tag   = $session->get('people_tag_filter');
                    $tag = $sess_tag;
                }
                if ($status != null) {
                    $session->set('people_status_filter',$status);
                } else {
                    $sess_status= $session->get('people_status_filter');
                    $status = $sess_status;
                }
                if ($team != null) {
                    $session->set('people_team_filter',$team);
                } else {
                    $sess_team  = $session->get('people_team_filter');
                    $team = $sess_team;
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
            if ( ( isset($user) && $user == "all" ) || ( isset($owner_filter) && $owner_filter == "all" ) ) {
                if ($member_role != 'exec') {
                     //manager filter
                    if ($member_role == 'manager') {
                        $query->where("(u.team_id=$team_id OR u2.team_id=$team_id)");
                    } else {
                    //basic user filter
                        $query->where("(p.owner_id=$member_id OR p.assignee_id=$member_id)");
                    }
                }
            } elseif ( isset($team) && $team ) {
                $query->where("(u.team_id=$team OR u2.team_id=$team)");
            } elseif ( isset($user) && $user != "all" ) {
                $query->where("(p.owner_id=$user OR p.assignee_id=$user)");
            } else {
                if ( !(isset($owner_filter)) ) {
                    if ($this->_id) {
                        if ($member_role == "basic") {
                            $query->where("(p.owner_id=$member_id OR p.assignee_id=$member_id)");
                        }
                        if ($member_role == "manager") {
                            $team_members = UsersHelper::getTeamUsers($team_id,TRUE);
                            $team_members = array_merge($team_members,array(0=>$member_id));
                            $query->where("(p.owner_id IN(".implode(',',$team_members).") OR p.assignee_id IN(".implode(',',$team_members)."))");
                        }
                    } else {
                        $query->where("(p.owner_id=$member_id OR p.assignee_id=$member_id)");
                    }
                }
            }

            //searching for specific person
            if ($this->_id) {
                if ( is_array($this->_id) ) {
                        $query->where("p.id IN (".implode(',',$this->_id).")");
                } else {
                        $query->where("p.id=$this->_id");
                }
            }

            if (!$this->_id) {

                if (!$export) {

                //filter data
                if ($type AND $type != 'all') {
                    switch ($type) {
                        case 'leads':
                            $query->where("p.type='lead'");
                        break;
                        case 'not_leads':
                            $query->where("p.type='contact'");
                        break;
                    }
                }

                //search with status
                if ($status AND $status != 'any') {
                    $query->where('p.status_id='.$status);
                }

                //search by tags
                if ($tag) {

                }

                //get current date
                $date = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));

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
                $people_filter = $this->getState('People.'.$view.'_name');
                if ($people_filter != null) {
                    $query->where("( p.first_name LIKE '%".$people_filter."%' OR p.last_name LIKE '%".$people_filter."%' OR CONCAT(p.first_name,' ',p.last_name) LIKE '%".$people_filter."%')");
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
        function getPeople()
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
            if (!$this->_id && $limit != 0) {
                $query->order($this->getState('People.filter_order') . ' ' . $this->getState('People.filter_order_Dir'));
                if ( $limitStart >= $this->getTotal() ) {
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

                        /* Deals */
                        $dealModel = new CobaltModelDeal();
                        $dealModel->set('person_id',$person['id']);
                        $people[$key]['deals'] = $dealModel->getDeals();;

                        /* Notes */
                        $notesModel = new CobaltModelNote();
                        $people[$key]['notes'] = $notesModel->getNotes($person['id'],'people');

                        /* Docs */
                        $docsModel = new CobaltModelDocument();
                        $docsModel->set('person_id',$person['id']);
                        $people[$key]['documents'] = $docsModel->getDocuments();

                        /* Tweets */
                        if ($person['twitter_user']!="" && $person['twitter_user']!=" ") {
                            $people[$key]['tweets'] = TweetsHelper::getTweets($person['twitter_user']);
                        }

                    }

                }
            }

            $dispatcher = JEventDispatcher::getInstance();
            $dispatcher->trigger('onPersonLoad', array(&$people));

            //return results
            return $people;

        }

        /*
         * Method to retrieve person
         */
        function getPerson($id=null)
        {
            $app = JFactory::getApplication();
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
                $dealModel = new CobaltModelDeal();
                $dealModel->set('person_id',$person['id']);
                $person['deals'] = $dealModel->getDeals();;

                /* Notes */
                $notesModel = new CobaltModelNote();
                $person['notes'] = $notesModel->getNotes($person['id'],'person');

                /* Docs */
                $docsModel = new CobaltModelDocument();
                $docsModel->set('person_id',$person['id']);
                $person['documents'] = $docsModel->getDocuments();

                /* Tweets */
                if ($person['twitter_user']!="" && $person['twitter_user']!=" ") {
                    $person['tweets'] = TweetsHelper::getTweets($person['twitter_user']);
                }

                 $this->person = $person;

            } else {

                 //TODO update things to OBJECTS
                $person = (array) JTable::getInstance('People','Table');
                $this->person = $person;

            }

            $dispatcher = JEventDispatcher::getInstance();
            $dispatcher->trigger('onPersonLoad', array(&$person));

            return $person;
        }

        /*
         * Method to retrieve list of names and ids
         */
        function getPeopleList()
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
        function getTotal()
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
        function getPagination()
        {
          // Lets load the content if it doesn't already exist

          if (empty($this->_pagination)) {
             jimport('joomla.html.pagination');
             $total = $this->getTotal();
             $total = $total ? $total : 0;
             $this->_pagination = new CobaltPagination( $total, $this->getState($this->_view.'_limitstart'), $this->getState($this->_view.'_limit'),null,JRoute::_('index.php?view=people'));
          }

          return $this->_pagination;

        }

        /**
         * Populate user state requests
         */
        function populateState()
        {
            //get states
            $app = JFactory::getApplication();
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
            $filter_order           = $app->getUserStateFromRequest('People.filter_order','filter_order','p.last_name');
            $filter_order_Dir       = $app->getUserStateFromRequest('People.filter_order_Dir','filter_order_Dir','asc');
            $people_filter          = $app->getUserStateFromRequest('People.'.$view.'_name','name',null);

            //set states for reports
            $this->state->set('People.filter_order',$filter_order);
            $this->state->set('People.filter_order_Dir',$filter_order_Dir);
            $this->state->set('People.filter_order_Dir',$filter_order_Dir);
            $this->state->set('People.'.$view.'_name',$people_filter);

        }

        function getDropdowns()
        {
            $dropdowns['person_type'] = DropdownHelper::generateDropdown('person_type',$this->person['type']);

            return $dropdowns;
        }

        function searchForContact($contact_name,$idsOnly=TRUE)
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
         * @return [int]       ID of existing company
         */
        function checkPersonName($name)
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

        function getPeopleNames($json=FALSE)
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

        function getEmail($person_id)
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
                    $people[$i]['avatar'] = CobaltHelperCobalt::getGravatar($people[$i]['email'],null,false,$default_image);
                } elseif ($people[$i]['avatar']=="") {
                    $people[$i]['avatar'] = $default_image;
                }
            }

            return $people;

        }

}
