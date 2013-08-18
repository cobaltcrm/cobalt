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

use Joomla\Registry\Registry;
use Cobalt\Table\DealTable;
use Cobalt\Helper\DealHelper;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\ActivityHelper;
use Cobalt\Helper\CobaltHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Deal extends DefaultModel
{
    public $_types=null;
    public $_data = null;
    public $_id = null;
    public $_type = null;
    public $_user = null;
    public $_stage = null;
    public $_close = null;
    public $_team = null;
    public $_status = null;
    public $_source = null;
    public $_modified = null;
    public $_created = null;
    public $_session = null;
    public $_user_id = null;
    public $_view = null;
    public $_layout = null;
    public $recent = null;
    public $published = 1;
    public $person_id = null;
    public $company_id = null;
    public $ordering = null;
    public $archived = null;
    public $limit = 1;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $app = \Cobalt\Container::get('app');
        $this->_view = $app->input->get('view');
        $this->_layout = str_replace('_filter','',$app->input->get('layout'));
    }

    /**
     * Method to store a record
     *
     * @return boolean True on success
     */
    public function store($data = null,$returnRow = false)
    {
        /** @var \Cobalt\Application $app */
        $app = \Cobalt\Container::get('app');

        //Load Tables
        $row = new DealTable;
        $oldRow = new DealTable;

        if ($data == null) {
            $data = $app->input->post->getArray();
        }

        //date generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));

        //assign the creation date
        if ( !array_key_exists('id',$data) || ( array_key_exists('id',$data) && $data['id'] <= 0 ) ) {
            $data['created'] = $date;
            $status = "created";
            //assign the owner id
            $data['owner_id'] = array_key_exists('owner_id',$data) ? $data['owner_id'] : UsersHelper::getUserId();
        } else {
            $row->load($data['id']);
            $oldRow->load($data['id']);
            $status = "updated";
        }

        //update our modified date
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

        //deal was closed
        $closedStages = $this->getClosedStages();
        if ( array_key_exists('stage_id',$data) && in_array($data['stage_id'],$closedStages) ) {
            $data['actual_close'] = $date;
        }

        /** check for and automatically associate and create primary contacts or people **/
        if ( array_key_exists('person_name',$data) && $data['person_name'] != "" ) {
            $peopleModel = new People;
            $existingPerson = $peopleModel->checkPersonName($data['person_name']);

            if ($existingPerson=="") {
                $pdata = array();
                $name = explode(" ",$data['person_name']);
                $pdata['first_name'] = $name[0];
                $pdata['last_name'] = array_key_exists(1,$name) ? $name[1] : "";
                if ( array_key_exists('company_id',$data) ) {
                    $pdata['company_id'] = $data['company_id'];
                }
                $data['person_id'] = $peopleModel->store($pdata);
            } else {
                $data['person_id'] = $existingPerson;
            }

        }

        if ( array_key_exists('primary_contact_name',$data) && $data['primary_contact_name'] != "" ) {
            $peopleModel = new People;
            $existingPerson = $peopleModel->checkPersonName($data['primary_contact_name']);

            if ($existingPerson=="") {
                $pdata = array();
                $name = explode(" ",$data['primary_contact_name']);
                $pdata['first_name'] = $name[0];
                $pdata['last_name'] = array_key_exists(1,$name) ? $name[1] : "";
                if ( array_key_exists('company_id',$data) ) {
                    $pdata['company_id'] = $data['company_id'];
                }
                $data['primary_contact_id'] = $peopleModel->store($pdata);
            } else {
                $data['primary_contact_id'] = $existingPerson;
            }

        }

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        $app->triggerEvent('onBeforeDealSave', array(&$row));

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

        $deal_id = array_key_exists('id',$data) && $data['id'] > 0 ? $data['id'] : $row->id;

        ActivityHelper::saveActivity($oldRow, $row, 'deal', $status);

        //if we receive no custom post data do not modify the custom fields
        if ( count($customArray) > 0 ) {
           CobaltHelper::storeCustomCf($deal_id,$customArray,'deal');
        }

        if (!empty($data['primary_contact_id']) || !empty($data['person_id'])) {
            $contactId = array_key_exists('primary_contact_id', $data) ? $data['primary_contact_id'] : $data['person_id'];
            $this->storeContact($deal_id, $contactId);
        }

        $closed_stages = DealHelper::getClosedStages();

        $row->closed = in_array($row->stage_id,$closed_stages) ? TRUE : FALSE;
        $row->actual_close_formatted = isset($row->actual_close) ? DateHelper::formatDate($row->actual_close) : DateHelper::formatDate(date("Y-m-d"));
        $row->expected_close_formatted = isset($row->expected_close) ? DateHelper::formatDate($row->expected_close) : DateHelper::formatDate(date("Y-m-d"));

        $app->triggerEvent('onAfterDealSave', array(&$row));

        //return success
        if ($returnRow) {
            return $row;
        } else {
            return $deal_id;
        }
    }

    public function _buildQuery()
    {
        /** Large SQL Selections **/
        $app = \Cobalt\Container::get('app');
        $this->db->setQuery("SET SQL_BIG_SELECTS=1")->execute();

        //set defaults
        $id = $this->_id;
        $type = $this->_type;
        $user = $this->_user;
        $stage = $this->_stage;
        $close = $this->_close;
        $team = $this->_team;
        $status = $this->_status;
        $source = $this->_source;
        $modified = $this->_modified;
        $created = $this->_created;
        $session = $this->_session;
        $user_id = $this->_user_id;
        $session = $app->getSession();

        //determine which layout is requesting the information
        $view = $this->_view;
        $layout = $this->_layout;

        //determine if we are sorting//searching for a team or user
        if ($team) {
            $session->set('deal_user_filter',null);
        }
        if ($user) {
            $session->set('deal_team_filter',null);
        }

        /** -----------------------------
         * Session data for the default deals page
         */
        //set user session data
        if ($view != "reports") {
            if ($type != null) {
                $session->set('deal_type_filter',$type);
            } else {
                $sess_type = $session->get('deal_type_filter');
                $type = $sess_type;
            }
            if ($user != null) {
                $session->set('deal_user_filter',$user);
            } else {
                $sess_user = $session->get('deal_user_filter');
                $user = $sess_user;
            }
            if ($stage != null) {
                $session->set('deal_stage_filter',$stage);
            } else {
                $sess_stage = $session->get('deal_stage_filter');
                $stage = $sess_stage;
            }
            if ($close != null) {
                $session->set('deal_close_filter',$close);
            } else {
                $sess_close = $session->get('deal_close_filter');
                $close = $sess_close;
            }
            if ($team != null) {
                $session->set('deal_team_filter',$team);
            } else {
                $sess_team = $session->get('deal_team_filter');
                $team = $sess_team;
            }
        }

        $query = $this->db->getQuery(true);

        //construct query string

        $export = $app->input->get('export');

        if ($export) {

            $queryString  = 'd.name,d.summary,d.probability,d.amount,d.actual_close,d.archived,';
            $queryString .= 'd.modified,d.category,d.expected_close,d.created,SUM(d.amount) AS filtered_total,';
            $queryString .= '( d.amount * ( d.probability / 100 )) AS forecast,';
            $queryString .= 'c.name as company_name,';
            $queryString .= 'IF(stat.name != "",stat.name,"none") as status_name,';
            $queryString .= 'source.name as source_name,';
            $queryString .= 'stage.name as stage_name,stage.percent,';
            $queryString .= 'p.first_name as primary_contact_first_name,p.last_name as primary_contact_last_name,';
            $queryString .= "p.email,p.phone";
            $queryString .= ' FROM #__deals AS d';

            $query
                ->select($queryString)
                ->leftJoin('#__companies AS c ON c.id = d.company_id AND c.published>0')
                ->leftJoin('#__deal_status AS stat ON stat.id = d.status_id')
                ->leftJoin('#__sources AS source ON source.id = d.source_id')
                ->leftJoin('#__stages AS stage on stage.id = d.stage_id')
                ->leftJoin("#__people AS p ON p.id = d.primary_contact_id AND p.published>0")
                ->leftJoin("#__shared AS shared ON shared.item_id=d.id AND shared.item_type='deal'");

        } else {

            $queryString  = 'd.*,SUM(d.amount) AS filtered_total,';
            $queryString .= '( d.amount * ( d.probability / 100 )) AS forecast,';
            $queryString .= 'c.name as company_name,';
            $queryString .= 'IF(stat.name != "",stat.name,"none") as status_name,';
            $queryString .= 'source.name as source_name,';
            $queryString .= 'stage.name as stage_name,stage.percent,';
            $queryString .= 'event_cf.*,';
            $queryString .= 'event.id as event_id,event.name as event_name,event.due_date as event_due_date,event.type as event_type,';
            $queryString .= 'user.first_name as owner_first_name, user.last_name as owner_last_name,';
            $queryString .= 'p.first_name as primary_contact_first_name,p.last_name as primary_contact_last_name,';
            $queryString .= "p.email,p.phone";
            $queryString .= ' FROM #__deals AS d';

            $query
                ->select($queryString)
                ->leftJoin('#__companies AS c ON c.id = d.company_id AND c.published>0')
                ->leftJoin('#__deal_status AS stat ON stat.id = d.status_id')
                ->leftJoin('#__sources AS source ON source.id = d.source_id')
                ->leftJoin('#__stages AS stage on stage.id = d.stage_id')
                ->leftJoin("#__events_cf AS event_cf ON event_cf.association_id = d.id AND event_cf.association_type ='deal' ")
                ->leftJoin("#__events AS event ON event.id = event_cf.event_id AND event.due_date IS NULL or event.due_date=(SELECT MIN(e2.due_date) FROM #__events_cf e2cf ".
                             "LEFT JOIN #__events as e2 on e2.id = e2cf.event_id ".
                             "WHERE e2cf.association_id=d.id AND e2cf.association_type='deal') AND event.published>0")
                ->leftJoin('#__users AS user ON user.id = d.owner_id')
                ->leftJoin("#__people AS p ON p.id = d.primary_contact_id AND p.published>0")
                ->leftJoin("#__shared AS shared ON shared.item_id=d.id AND shared.item_type='deal'");
        }

        if (!$id) {

            /** --------------------
             * Sort data for reports pages
             */
            if ($view == 'reports') {
                //name
                $deal_filter = $this->getState('Deal.'.$layout.'_name');
                if ($deal_filter != null) {
                    $query->where("d.name LIKE '%".$deal_filter."%'");
                }
                //owner
                $owner_filter = $this->getState('Deal.'.$layout.'_owner_id');
                if ($owner_filter != null AND $owner_filter != 'all') {
                    $owner_type = $this->getState('Deal.'.$layout.'_owner_type');
                    if ($owner_type == 'member' OR $owner_type == null) {
                        $query->where("d.owner_id=".$owner_filter);

                    }
                    if ($owner_type == 'team') {
                        //get team members
                        $team_members = UsersHelper::getTeamUsers($owner_filter);
                        //filter by results having team ids
                        $ids = "0,";
                        for ($i=0;$i<count($team_members);$i++) {
                            $member = $team_members[$i];
                            $ids .= $member['id'].",";
                        }
                        $ids = substr($ids,0,-1);
                        $query->where("d.owner_id IN(".$ids.")");
                    }
                }
                //amount
                $amount_filter = $this->getState('Deal.'.$layout.'_amount');
                if ($amount_filter != null AND $amount_filter != 'all') {
                    if ( $amount_filter == 'small' ) $query->where("d.amount <= ".TextHelper::_('COBALT_SMALL_DEAL_AMOUNT'));
                    if ( $amount_filter == 'medium' ) $query->where("d.amount > ".TextHelper::_('COBALT_SMALL_DEAL_AMOUNT')." AND d.amount <= ".TextHelper::_('COBALT_MEDIUM_DEAL_AMOUNT'));
                    if ( $amount_filter == 'large' ) $query->where("d.amount > ".TextHelper::_('COBALT_LARGE_DEAL_AMOUNT'));
                }
                //source
                $source_filter = $this->getState('Deal.'.$layout.'_source_id');
                if ($source_filter != null AND $source_filter != 'all') {
                    $source = $source_filter;
                }
                //stage
                $stage_filter = $this->getState('Deal.'.$layout.'_stage_id');
                if ($stage_filter != null AND $stage_filter != 'all') {
                    $stage = $stage_filter;
                }
                //status
                $status_filter = $this->getState('Deal.'.$layout.'_status_id');
                if ($status_filter != null AND $status_filter != 'all') {
                    $status = $status_filter;
                }
                //expected close
                $expected_close_filter = $this->getState('Deal.'.$layout.'_expected_close');
                if ($expected_close_filter != null AND $expected_close_filter != 'all') {
                    $close = $expected_close_filter;
                }
                //modified
                $modified_filter = $this->getState('Deal.'.$layout.'_modified');
                if ($modified_filter != null AND $modified_filter != 'all') {
                    $modified = $modified_filter;
                }
                //created
                $created_filter = $this->getState('Deal.'.$layout.'_created');
                if ($created_filter != null AND $created_filter != 'all') {
                    $created = $created_filter;
                }
            }

             //get current date to use for all date filtering
            $date = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));

            /** ------------------------------------------
             * Here we filter for diferent types of deals
             */
            if ($type != null  && $type != 'all') {

                //filter for deals//tasks due today
                if ($type == 'today') {
                    $tomorrow = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() + (1*24*60*60)));
                    $query->where("event.due_date > '$date' AND event.due_date < '$tomorrow'");
                    $query->where("event.published>0");
                }

                //filter for deals//tasks due tomorrow
                if ($type == "tomorrow") {
                    $tomorrow = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() + (1*24*60*60)));
                    $query->where("event.due_date='".$tomorrow."'");
                    $query->where("event.published>0");
                }

                //filter for deals updated in the last 30 days
                if ($type == "updated_thirty") {
                    $last_thirty_days = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() - (30*24*60*60)));
                    $query->where("d.modified >'$last_thirty_days'");
                }

                //filter for most valuable deals
                if ($type == "valuable") {
                    $query->order('d.amount DESC');
                }

                //filter for past deals
                if ($type == "past") {
                    $query->where("event.due_date < '$date'");
                    $query->where("event.published>0");
                }

                //filter for deals not updated in the last 30 days
                if ($type == "not_updated_thirty") {
                    $last_thirty_days = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() - (30*24*60*60)));
                    $query->where("d.modified < '$last_thirty_days'");
                }

                //filter for shared deals
                if ($type == "shared") {
                    $query->where("shared.item_id IS NOT NULL");
                }

                //filter for archived deals
                if ( $type == "archived" && is_null($this->archived) ) {
                    $query->where("d.archived=1");
                }

            } else {
                $query->where("d.archived=0");
            }

            /** --------------------------------------------
             * Search for closing deal filters
             */
            if ($close != null && $close != "any") {

                if ($close == "this_week") {
                    $this_week = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));
                    $next_week = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+7 days"));
                    $query->where("d.expected_close >= '$this_week'");
                    $query->where("d.expected_close < '$next_week'");
                }

                if ($close == "next_week") {
                    $next_week = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+7 days"));
                    $week_after_next = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+14 days"));
                    $query->where("d.expected_close >= '$next_week'");
                    $query->where("d.expected_close < '$week_after_next'");
                }

                if ($close == "this_month") {
                    $this_month = DateHelper::formatDBDate(date('Y-m-0 00:00:00'));
                    $next_month = date('Y-m-0 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+1 month"));
                    $query->where("d.expected_close >= '$this_month'");
                    $query->where("d.expected_close < '$next_month'");
                }

                if ($close == "next_month") {
                    $next_month = date("Y-m-0 00:00:00", strtotime(date("Y-m-d 00:00:00", strtotime($date)) . "+1 month"));
                    $next_next_month = date("Y-m-0 00:00:00", strtotime(date("Y-m-d 00:00:00", strtotime($date)) . "+2 months"));
                    $query->where("d.expected_close >= '$next_month'");
                    $query->where("d.expected_close < '$next_next_month'");
                }

            }

            /** --------------------------------------------
             * Search for modified deal filters
             */
            if ($modified != null && $modified != "any") {

                if ($modified == "this_week") {
                    $this_week = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));
                    $last_week = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "-7 days"));
                    $query->where("d.modified >= '$last_week'");
                    $query->where("d.modified < '$this_week'");
                }

                if ($modified == "last_week") {
                    $last_week = DateHelper::formatDBDate(date("Y-m-d", strtotime("-7 days")));
                    $week_before_last = DateHelper::formatDBDate(date("Y-m-d", strtotime("-14 days")));
                    $query->where("d.modified >= '$week_before_last'");
                    $query->where("d.modified < '$last_week'");
                }

                if ($modified == "this_month") {
                    $this_month = DateHelper::formatDBDate(date('Y-m-1 00:00:00'));
                    $next_month = date('Y-m-1 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+1 month"));
                    $query->where("d.modified >= '$this_month'");
                    $query->where("d.modified < '$next_month'");
                }

                if ($modified == "last_month") {
                    $this_month = DateHelper::formatDBDate(date('Y-m-1 00:00:00'));
                    $last_month = date('Y-m-1 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "-1 month"));
                    $query->where("d.modified >= '$last_month'");
                    $query->where("d.modified < '$this_month'");
                }

            }

            /** --------------------------------------------
             * Search for created deal filters
             */
            if ($created != null && $created != "any") {

                if ($created == "this_week") {
                    $this_week = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));
                    $last_week = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "-7 days"));
                    $query->where("d.created >= '$last_week'");
                    $query->where("d.created < '$this_week'");
                }

                if ($created == "last_week") {
                    $last_week = DateHelper::formatDBDate(date("Y-m-d", strtotime("-7 days")));
                    $week_before_last = DateHelper::formatDBDate(date("Y-m-d", strtotime("-14 days")));
                    $query->where("d.created >= '$week_before_last'");
                    $query->where("d.created < '$last_week'");
                }

                if ($created == "this_month") {
                    $this_month = DateHelper::formatDBDate(date('Y-m-1 00:00:00'));
                    $next_month = date('Y-m-1 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+1 month"));
                    $query->where("d.created >= '$this_month'");
                    $query->where("d.created < '$next_month'");
                }

                if ($created == "last_month") {
                    $this_month = DateHelper::formatDBDate(date('Y-m-1 00:00:00'));
                    $last_month = date('Y-m-1 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "-1 month"));
                    $query->where("d.created >= '$last_month'");
                    $query->where("d.created < '$this_month'");
                }

                if ($created == "today") {
                    $today = DateHelper::formatDBDate(date("Y-m-d 00:00:00"));
                    $tomorrow = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+1 day"));
                    $query->where("d.created >= '$today'");
                    $query->where("d.created < '$tomorrow'");
                }

                if ($created == "yesterday") {
                    $today = DateHelper::formatDBDate(date("Y-m-d 00:00:00"));
                    $yesterday = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "-1 day"));
                    $query->where("d.created >= '$yesterday'");
                    $query->where("d.created < '$today'");
                }

            }

            /** ------------------------------------------
             * Search for status
             */
            if ($status != null AND $status != 'all') {
                $query->where("d.status_id=".$status);
            }

            /** -------------------------
             * Search for sources
             */
            if ($source != null AND $source != 'all') {
                $query->where('d.source_id='.$source);
            }

            /** ----------------------------------------------------------------
             * Filter for stage id associations
             */
            if ($stage != null && $stage != 'all' and !$id) {
                //if we want active deals we must retrieve the active stage ids to filter by
                if ($stage == 'active') {
                    //get stage ids
                    $stage_ids = DealHelper::getActiveStages();
                    //filter by results having team ids
                    $stages = "";
                    for ($i=0;$i<count($stage_ids);$i++) {
                        $stage = $stage_ids[$i];
                        $stages .= $stage['id'].",";
                    }
                    $stages = substr($stages,0,-1);
                    $query->where("d.stage_id IN(".$stages.")");
                } else {
                    //else filter by the stage id
                    $query->where("d.stage_id='".$stage."'");
                }
            }

            /** --------------------------------------------------------
             * Filter data for the sources page
             */
            //source view
            if ($view == "reports" && $layout == "source_report") {
                //filter by active and closed stages
                $active_and_closed_stages = DealHelper::getNonInactiveStages();
                $query->where("d.stage_id IN(".implode(',',$active_and_closed_stages).")");

                //filter by deals that are associated to sources
                $query->where("d.source_id <> 0");
            }

            $deal_filter = $this->getState('Deal.'.$view.'_name');
            if ($deal_filter != null) {
                $query->where("d.name LIKE '%".$deal_filter."%'");
            }

            /** --------------------
             * Grab only recently accessed deals
             */

            if ($this->recent) {
                $past = DateHelper::formatDBDate(date('Y-m-d H:i:s')." - 30 days");
                $query->where('d.last_viewed > '.$this->db->Quote($past));
            }

            /**---------------------
             * Group by deal id
             */
            $query->group('d.id');

            /**
             * Set our sorting direction if set via post
             */
            //default deals view
            //
            if ($this->ordering) {
                $query->order($this->ordering);
            } elseif ($view == "deals") {
                $orderString = $this->getState('Deal.filter_order') . " " . (String)$this->getState('Deal.filter_order_Dir');
                $query->order($orderString);
            } else
            //reports view
            if ($view == "reports") {
                $query->order($this->getState('Deal.'.$layout.'_filter_order') . ' ' . $this->getState('Deal.'.$layout.'_filter_order_Dir'));
            } else {
                $query->order("d.amount DESC");
            }

        }

        /** ---------------------
         * Filter by id
         */
        if ($id) {
            if ( is_array($id) ) {
                $query->where("d.id IN (".implode(',',$id).")");
            } else {
                $query->where("d.id=$id");
            }
        }
        /** or team **/
        if ($team) {
            $query->where("user.team_id=$team");
        }

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
                    $query->where('( user.team_id = '.$team_id." OR shared.user_id=".$member_id." )");
                } else {
                //basic user filter
                    $query->where(array('(d.owner_id = '.$member_id." OR shared.user_id=".$member_id." )"));
                }
            }
        } elseif ($team) {
            $query->where("user.team_id=$team");
        } elseif ($user && $user != "all") {
            $query->where("(d.owner_id=".$user." OR shared.user_id=".$user.")");
        } else {
            if ( !(isset($owner_filter)) ) {
                $query->where("( d.owner_id=".UsersHelper::getLoggedInUser()->id." OR shared.user_id=".UsersHelper::getLoggedInUser()->id." )");
            }
        }

        /** company **/
        if ($this->company_id) {
            $query->where("d.company_id=".$this->company_id);
        }
        /** people **/
        if ($this->person_id) {
            $query->leftJoin("#__people_cf AS dpcf ON dpcf.association_id = d.id AND dpcf.association_type='deal'");
            $query->where("dpcf.person_id=".$this->person_id);
        }
        /** archived **/
        if ( !is_null($this->archived) ) {
            $query->where("d.archived=".$this->archived);
        }
        /** published **/
        $query->where("d.published=".$this->published);

        //set db query and load object list
        return $query;
    }

    /**
     * Method to access deals
     * @param $id to search for
     * @param $type to filter by
     * @param $user to filter by
     * @param $stage to filter by
     * @param $close date to filter by
     * @param $team to filter by
     * @return $results
     */
    public function getDeals($id = null, $type = null, $user = null, $stage = null, $close = null, $team = null)
    {
        $app = \Cobalt\Container::get('app');

        //set defaults
        $this->_id = ( $this->_id ) ? $this->_id : $id;
        $this->_type = $type;
        $this->_user = $user;
        $this->_stage = $stage;
        $this->_close = $close;
        $this->_team = $team;
        $this->_status = null;
        $this->_source = null;
        $this->_modified = null;
        $this->_created = null;

        //get session data
        $this->_session = $app->getSession();
        $this->_user_id = UsersHelper::getUserId();

        $query = $this->_buildQuery();


        /** ------------------------------------------
         * Set query limits and load results
         */
        $limit = $this->getState($this->_view.'_limit');
        $limitStart = $this->getState($this->_view.'_limitstart');

        if (!$this->_id && $limit != 0 && $this->limit == 1) {
            if ( $limitStart >= $this->getTotal() ) {
                $limitStart = 0;
                $limit = 10;
                $limitStart = ($limit != 0) ? (floor($limitStart / $limit) * $limit) : 0;
                $state = $this->getState();
                $state->set($this->_view.'_limit', $limit);
                $state->set($this->_view.'_limitstart', $limitStart);
            }

            // Todo: should not be string
            $query .= " LIMIT ".($limit)." OFFSET ".($limitStart);
        }

        $deals = $this->db->setQuery($query)->loadObjectList();

        /**------------------------------------------
         * Generate queries to join essential data
         */
        if ( count($deals) > 0 ) {

            $export = $app->input->get('export');

            if (!$export) {

                /** ------------------------------------------
                 *  Get data
                 */
                foreach ($deals as $key => $deal) {

                    self::getDealDetails($deals[$key]);

                /** ------------------------------------------
                 * Update last access for each deal
                 */

                    if ($this->_id) {
                        $now = DateHelper::formatDBDate(date("Y-m-d H:i:s"));
                        $query = $this->db->getQuery(true)
                            ->update("#__deals")
                            ->set("last_viewed=".$this->db->quote($now))
                            ->where("id=".$deal['id']);

                        $this->db->setQuery($query)->execute();
                    }
                }

            }
        }

        /** ------------------------------------------
         *  Return results
         */
        $app->triggerEvent('onDealLoad', array(&$deals));

        // cast to array so it never returns null to view
        return (array) $deals;
    }

    public function getDealDetails(&$deal)
    {
        $closed_stages = DealHelper::getClosedStages();
        $deal->closed = in_array($deal->stage_id,$closed_stages) ? TRUE : FALSE;
        $deal_id = $deal->id;

        /** ------------------------------------------
         *  Join contacts
         */

            $peopleModel = new People;
            $peopleModel->set('deal_id',$deal_id);
            $people = $peopleModel->getContacts();
            //assign results to company
            $deal->people = $people;

        /** ------------------------------------------
         *  Join conversations
         */
            $convoModel = new Conversation;
            $convoModel->set('deal_id',$deal_id);
            $conversations = $convoModel->getConversations();
            $deal->conversations = $conversations;

        /** ------------------------------------------
         *  Join notes
         */

           $notesModel = new Note;
           $deal->notes = $notesModel->getNotes($deal_id, 'deal');

         /** ------------------------------------------
         *  Join documents
         */
            $docModel = new Document;
            $docModel->set('deal_id',$deal_id);
            $deal->documents = $docModel->getDocuments();

        /** ------------------------------------------
         *  Join tasks & events
         */
            $eventModel = new Event;
            $eventModel->set('deal_id',$deal_id);
            $events = $eventModel->getEvents();
            $deal->events = $events;

    }

    public function getDeal($id=null)
    {
        $app = \Cobalt\Container::get('app');
        $id = $id ? $id : $app->input->get('id');

        if ($id > 0) {

            $query = $this->_buildQuery();

            $deal = $this->db->setQuery($query)->loadObjectList();

            self::getDealDetails($deal);

        } else {

            //TODO update things to OBJECTS
            $deal = new DealTable;
        }

        return $deal;
    }

    /**
     * Get deals that are active for the reports page
     * @param none
     * @return mixed $results
     */
    public function getReportDeals()
    {
        //get filter
        $session = \Cobalt\Container::get('session');
        $filter = $session->get('deal_stage_filter');
        //get deals
        $deals = $this->getDeals(null,null,null,'active');
        //reset filter
        $session->set('deal_stage_filter',$filter);
        //return deals
        return $deals;
    }

    /* ---------------------------------
     * Method to get list of deals
     */

    public function getDealList()
    {
        $app = \Cobalt\Container::get('app');

        //gen query
        $query = $this->db->getQuery(true)
            ->select("DISTINCT(d.id),d.name,d.id")
            ->from("#__deals AS d")
            ->leftJoin('#__users AS user ON user.id = d.owner_id')
            ->leftJoin("#__people_cf AS pcf ON pcf.association_id = d.id AND pcf.association_type='deal'");

        /** ---------------------------------------------------------------
         * Filter data using member role permissions
         */
        $member_id = UsersHelper::getUserId();
        $member_role = UsersHelper::getRole();
        $team_id = UsersHelper::getTeamId();
        if ($member_role != 'exec') {
             //manager filter
            if ($member_role == 'manager') {
                $query->where('user.team_id = '.$team_id);
            } else {
            //basic user filter
                $query->where(array('d.owner_id = '.$member_id));
            }
        }

        $query->where("d.published=".$this->published);

        $associationType = $app->input->get('association');
        $associationId = $app->input->get('association_id');

        if ($associationType == "company") {
            $query->where("d.company_id=".$associationId);
        }

        if ($associationType == "person") {
            $query->where("pcf.person_id=".$associationId);
        }

        $row = $this->db->setQuery($query)->loadAssocList();

        if ( count($row) == 0 ) {
            $row = array();
        }

        $blank = array(array('name' => TextHelper::_('COBALT_NONE'), 'id'=>0));
        $return = array_merge($blank, $row);

        return $return;
    }

    /**
     * Method to get graph deal information
     * @param $type type of deal to filter by, values 'stage','status'
     * @param $access_type to search by 'company','team','member'
     * @param $access_id the id of the $member_type to search by
     */
    public function getGraphDeals($type = null, $access_type = null, $access_id = null)
    {
        $query = $this->db->getQuery(true);

        //search by type
        if ($type == 'stage') {
            $query
                ->select("d.stage_id,count(*) AS y, stage.name AS name")
                ->from("#__deals AS d")
                ->leftJoin("#__stages AS stage ON stage.id=d.stage_id");
        }
        if ($type == 'status') {
            $query
                ->select("d.status_id,count(*) AS y,status.name AS name")
                ->from("#__deals AS d")
                ->leftJoin("#__deal_status AS status ON status.id=d.status_id");
        }

        //if user is not an executive then there are limitations
        if ($access_type != 'company') {

            //team sorting
            if ($access_type == 'team') {
                //get team members
                $team_members = UsersHelper::getTeamUsers($access_id);
                $members = array();
                $members[] = 0;
                foreach ($team_members as $key=>$member) {
                    $members[] = $member['id'];
                }
                $query->where("d.owner_id IN (".implode(",",$members).")");
            }

            //member sorting
            if ($access_type == 'member') {
                $query->where("d.owner_id=$access_id");
            }

        }

        //grouping
        if ($type =='stage') {
            $query->where("d.stage_id<>0 AND d.stage_id=stage.id");
            $query->group("d.stage_id");
        }
        if ($type == 'status') {
            $query->where("d.status_id<>0 AND d.status_id=status.id");
            $query->group("d.status_id");
        }

        if ( !is_null($this->archived) ) {
            $query->where("d.archived=".$this->archived);
        }

        $query->where("d.published=".$this->published);

        $results = $this->db->setQuery($query)->loadAssocList();

        //clean results and force datatypes for graph rendering
        if ( count($results) > 0 ) {
            foreach ($results as $key => $stage) {
                $results[$key]['y'] = (int) $stage['y'];
                $results[$key]['data'] = array((int) $stage['y']);
            }
        }

        return $results;
    }

    /**
     * Get lead sources from database where we have associated won deals and grab amount from deals
     * @param $access_type to filter by 'member','team','company'
     * @param $access_id id of $access_type to filter by
     * @return mixed $results
     */
    public function getLeadSources($access_type=null,$access_id=null)
    {
        //get won stage id so we know what stage to filter by for the deals
        $won_stage_ids = DealHelper::getWonStages();

        $query = $this->db->getQuery(true);

        //gen query
        $query->select("s.name,SUM(d.amount) as y");
        $query->from("#__people AS p");

        //left join people that have won deals
        $query->leftJoin("#__sources AS s ON s.id = p.source_id");
        $query->leftJoin("#__people_cf AS dpcf ON dpcf.person_id = p.id AND dpcf.association_type='deal'");
        $query->leftJoin("#__deals AS d ON d.id = dpcf.association_id AND d.published>0");

        //filter where we have associated source ids and the person is a lead contact
        $query->where("p.source_id <> 0");
        $query->where("p.type='lead'");

        //filter by won deals
        $won_stage_ids[]=0;
        $query->where("d.stage_id IN(".implode(',',$won_stage_ids).")");

        $query->where("p.published=".$this->published);

        //filter by access type
        if ($access_type != 'company') {

            //team sorting
            if ($access_type == 'team') {
                //get team members
                $team_members = UsersHelper::getTeamUsers($access_id);
                $query .= " AND d.owner_id IN (";
                //loop to make string
                $query .= "0,";
                foreach ($team_members as $key=>$member) {
                    $query .= "'".$member['id']."',";
                }
                $query  = substr($query,0,-1);
                $query .= ") ";
            }

            //member sorting
            if ($access_type == 'member') {
                $query .= " AND d.owner_id=$access_id ";
            }

        }

        //group by source ids
        $query .= " GROUP BY s.id";

        $results = $this->db->setQuery($query)->loadAssocList();

        if (count($results) > 0) {
            foreach ($results as $key=>$source) {
                $results[$key]['y'] = (int) $source['y'];
                $results[$key]['data'] = array((int) $source['y']);
            }
        }

        return $results;
    }

    /**
     * Populate user state requests
     */
    public function populateState()
    {
        //get states
        $app = \Cobalt\Container::get('app');

        //determine view so we set correct states
        $view = $app->input->get('view');
        $layout = str_replace("_filter","",$app->input->get('layout'));

        $limit = $app->getUserStateFromRequest($view.'_limit','limit',10);
        $limitstart = $app->getUserStateFromRequest($view.'_limitstart','limitstart',0);

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0) ? (floor($limitstart / $limit) * $limit) : 0;

        $state = new Registry;
        $state->set($view.'_limit', $limit);
        $state->set($view.'_limitstart', $limitstart);

        /** --------------------------------------
         * Filter data for different views
         */
        switch ($view) {
            case "reports" :
                //set default filter states for reports
                $filter_order = $app->getUserStateFromRequest('Deal.'.$layout.'_filter_order','filter_order','d.name');
                $filter_order_Dir = $app->getUserStateFromRequest('Deal.'.$layout.'_filter_order_Dir','filter_order_Dir','asc');
                $deal_filter = $app->getUserStateFromRequest('Deal.'.$layout.'_name','deal_name',null);
                $owner_filter = $app->getUserStateFromRequest('Deal.'.$layout.'_owner_id','owner_id','all');
                $owner_type_filter = $app->getUserStateFromRequest('Deal.'.$layout.'_owner_type','owner_type',null);
                $amount_filter = $app->getUserStateFromRequest('Deal.'.$layout.'_amount','deal_amount',null);
                $source_filter = $app->getUserStateFromRequest('Deal.'.$layout.'_source_id','source_id',null);
                $stage_filter = $app->getUserStateFromRequest('Deal.'.$layout.'_stage_id','stage_id','active');
                $status_filter = $app->getUserStateFromRequest('Deal.'.$layout.'_status_id','status_id',null);
                $expected_close_filter = $app->getUserStateFromRequest('Deal.'.$layout.'_expected_close','expected_close',null);
                $modified_filter = $app->getUserStateFromRequest('Deal.'.$layout.'_modified','modified',null);
                $created_filter = $app->getUserStateFromRequest('Deal.'.$layout.'_created','created',null);

                //set states for reports
                $state->set('Deal.'.$layout.'_filter_order',$filter_order);
                $state->set('Deal.'.$layout.'_filter_order_Dir',$filter_order_Dir);
                $state->set('Deal.'.$layout.'_name',$deal_filter);
                $state->set('Deal.'.$layout.'_owner_id',$owner_filter);
                $state->set('Deal.'.$layout.'_owner_type',$owner_type_filter);
                $state->set('Deal.'.$layout.'_amount',$amount_filter);
                $state->set('Deal.'.$layout.'_source_id',$source_filter);
                $state->set('Deal.'.$layout.'_stage_id',$stage_filter);
                $state->set('Deal.'.$layout.'_status_id',$status_filter);
                $state->set('Deal.'.$layout.'_expected_close',$expected_close_filter);
                $state->set('Deal.'.$layout.'_modified',$modified_filter);
                $state->set('Deal.'.$layout.'_created',$created_filter);

                break;
            break;

            case "deals" :
                //set defaults
                $filter_order = $app->getUserStateFromRequest('Deal.filter_order','filter_order','d.name');
                $filter_order_Dir = $app->getUserStateFromRequest('Deal.filter_order_Dir','filter_order_Dir','asc');
                $deal_filter = $app->getUserStateFromRequest('Deal.'.$view.'_name','deal_name',null);

                //set states
                $state->set('Deal.filter_order',$filter_order);
                $state->set('Deal.filter_order_Dir',$filter_order_Dir);
                $state->set('Deal.'.$view.'_name',$deal_filter);
                break;
        }

       $this->setState($state);

    }

    /**
     * Store contacts to CF tables
     * @param  [type] $deal_id    [description]
     * @param  [type] $contact_id [description]
     * @return [type] [description]
     */
    public function storeContact($deal_id, $contact_id)
    {
        $query = $this->db->getQuery(true)
            ->select("COUNT(*)")
            ->from("#__people_cf")
            ->where("association_id=".$deal_id)
            ->where("association_type='deal'")
            ->where("person_id=".$contact_id);

        $contacts = $this->db->setQuery($query)->loadResult();

        if ($contacts == 0) {

            $created = DateHelper::formatDBDate(date('Y-m-d H:i:s'));

            $data = array($deal_id.",'deal',".$contact_id.",'".$created."'");

            $query
                ->clear()
                ->insert('#__people_cf')
                ->columns('association_id, association_type, person_id, created')
                ->values($data);

            $this->db->setQuery($query)->execute();
        }

    }

     /**
     * Checks for existing company by name
     * @param  [var] $name company name to check
     * @return [int] ID of existing company
     */
    public function checkDealName($name)
    {
        $query = $this->db->getQuery(true)
            ->select('d.id')
            ->from('#__deals AS d')
            ->where('LOWER(d.name) = "'.strtolower($name).'"');

        return $this->db->setQuery($query)->loadResult();
    }

    public function getClosedStages()
    {
        $query = $this->db->getQuery(true)
            ->select('s.id')
            ->from('#__stages AS s')
            ->where("s.percent=100");

        return $this->db->setQuery($query)->loadColumn();
    }

    public function getDealNames($json=FALSE)
    {
        $names = $this->getDealList();
        $return = array();
        if ( count($names) > 0 ) {
            foreach ($names as $key => $deal) {
                $return[] = array('label'=>$deal['name'],'value'=>$deal['id']);
            }
        }

        return $json ? json_encode($return) : $return;
    }

}
