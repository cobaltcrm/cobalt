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

class CobaltModelReport extends CobaltModelDefault
{

    public $published = 1;

    /**
     * Method to store a record
     * @param $_POST data
     * @return boolean True on success
     */
    public function store()
    {
        $app = JFactory::getApplication();

        //Load Tables
        $row = JTable::getInstance('report','Table');
        $oldRow = JTable::getInstance('report','Table');

        $data = $app->input->getRequest('post');

        //date generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));
        if ( !array_key_exists('id',$data) ) {
            $data['created'] = $date;
            $status = "created";
        } else {
            $row->load($data['id']);
            $oldRow->load($data['id']);
            $status = "updated";
        }

        //modified date
        $data['modified'] = $date;

        //assign owner id
        $data['owner_id'] = UsersHelper::getUserId();

        //insert custom field data
        $data['fields'] = serialize($data['fields']);

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

        return true;
    }

    /**
     * Get Custom Reports
     * @param  int   $id specific report to search for
     * @return mixed $results reports matched
     */
    public function getCustomReports($id=null)
    {
        $app = JFactory::getApplication();

        //load database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //gen query string
        $query->select("report.*");
        $query->from("#__reports as report");
        $query->leftJoin("#__users AS u ON u.id = report.owner_id");

        //search for reports
        if ($id != null) {
            $query->where("report.id=$id");
        }

        //filter based on member access roles
        $user_id = UsersHelper::getUserId();
        $member_role = UsersHelper::getRole();
        $team_id = UsersHelper::getTeamId();

        if ($member_role != 'exec') {

            if ($member_role == 'manager') {
                $query->where("u.team_id=$team_id");
            } else {
                $query->where("(report.owner_id=$user_id)");
            }

        }

        /**
         * Set our sorting direction if set via post
         */
        $layout = str_replace("_filter","",$app->input->get('layout'));
        //default deals view
        if ($layout == "custom_reports") {

            $query->order($this->getState('Report.filter_order') . ' ' . $this->getState('Report.filter_order_Dir'));
        }

        //return results
        $db->setQuery($query);
        $results = $db->loadAssocList();

        return $results;
    }

    /**
     * Get data for custom reports
     * @param  int   $id custom id data to retrieve
     * @return mixed $results
     */
    public function getCustomReportData($id=null)
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $app = JFactory::getApplication();

        //get the custom report so we know what data to filter and select
        $custom_report = $this->getCustomReports($id);
        $custom_report = $custom_report[0];
        $custom_report_fields = unserialize($custom_report['fields']);

        //gen query
        //construct query string
        $queryString  = 'd.*,SUM(d.amount) AS filtered_total,';
        $queryString .= 'c.name as company_name,';
        $queryString .= 'stat.name as status_name,';
        $queryString .= 'source.name as source_name,';
        $queryString .= 'stage.name as stage_name,stage.percent,';
        $queryString .= 'user.first_name, user.last_name,';
        $queryString .= 'p.first_name as primary_contact_first_name,p.last_name as primary_contact_last_name,';
        $queryString .= "p.email as primary_contact_email,p.phone as primary_contact_phone,";
        $queryString .= "pc.name as primary_contact_company_name";

        //select
        $query->select($queryString);
        $query->from("#__deals AS d");

        //left join
        $query->leftJoin('#__companies AS c ON c.id = d.company_id AND c.published>0');
        $query->leftJoin('#__deal_status AS stat ON stat.id = d.status_id');
        $query->leftJoin('#__sources AS source ON source.id = d.source_id');
        $query->leftJoin('#__stages AS stage on stage.id = d.stage_id');
        $query->leftJoin('#__users AS user ON user.id = d.owner_id');
        $query->leftJoin("#__people AS p ON p.id = d.primary_contact_id AND p.published>0");
        $query->leftJoin("#__companies AS pc ON pc.id = p.company_id AND pc.published>0");

        //group results
        $query->group("d.id");

        //filter data with user state requests
        $layout = str_replace("_filter","",$app->input->get('layout'));
        $view = $app->input->get('view');

        if ($view == "print") {
            $layout = "custom_report";
            $id = $app->input->get('custom_report');
        }

        $filter_order = $this->getState('Report.'.$id.'_'.$layout.'_filter_order');
        $filter_order_Dir = $this->getState('Report.'.$id.'_'.$layout.'_filter_order_Dir');
        $filter_order = ( strstr($filter_order,"custom_") ) ? str_replace("d.","",$filter_order) : $filter_order;
        $query->order($filter_order . ' ' . $filter_order_Dir );

        //assign defaults
        $close = null;
        $modified = null;
        $created = null;
        $status = null;
        $source = null;
        $stage = null;

        //filter by deal names
        $deal_filter = $this->getState('Report.'.$id.'_'.$layout.'_name');
        if ($deal_filter != null) {
            $query->where("d.name LIKE '%".$deal_filter."%'");
        }

        //owner
        $owner_filter = $this->getState('Report.'.$id.'_'.$layout.'_owner_id');
        if ($owner_filter != null AND $owner_filter != 'all') {
            $owner_type = $this->getState('Report.'.$id.'_'.$layout.'_owner_type');
            if ($owner_type == 'member') {
                $query->where("d.owner_id=".$owner_filter);

            }
            if ($owner_type == 'team') {
                //get team members
                $team_members = UsersHelper::getTeamUsers($owner_filter);
                //filter by results having team ids
                $ids = "";
                for ($i=0;$i<count($team_members);$i++) {
                    $member = $team_members[$i];
                    $ids .= $member['id'].",";
                }
                $ids = substr($ids,0,-1);
                $query->where("d.owner_id IN(".$ids.")");
            }
        }

        //amount
        $amount_filter = $this->getState('Report.'.$id.'_'.$layout.'_amount');
        if ($amount_filter != null AND $amount_filter != 'all') {
            if ( $amount_filter == 'small' ) $query->where("d.amount <= 50");
            if ( $amount_filter == 'medium' ) $query->where("d.amount > 50 AND d.amount <= 400");
            if ( $amount_filter == 'large' ) $query->where("d.amount > 400");
        }
        //source
        $source_filter = $this->getState('Report.'.$id.'_'.$layout.'_source_id');
        if ($source_filter != null AND $source_filter != 'all') {
            $source = $source_filter;
        }
        //stage
        $stage_filter = $this->getState('Report.'.$id.'_'.$layout.'_stage_id');
        if ($stage_filter != null AND $stage_filter != 'all') {
            $stage = $stage_filter;
        }
        //status
        $status_filter = $this->getState('Report.'.$id.'_'.$layout.'_status_id');
        if ($status_filter != null AND $status_filter != 'all') {
            $status = $status_filter;
        }
        //expected close
        $expected_close_filter = $this->getState('Report.'.$id.'_'.$layout.'_expected_close');
        if ($expected_close_filter != null AND $expected_close_filter != 'all') {
            $close = $expected_close_filter;
        }
        //modified
        $modified_filter = $this->getState('Report.'.$id.'_'.$layout.'_modified');
        if ($modified_filter != null AND $modified_filter != 'all') {
            $modified = $modified_filter;
        }
        //created
        $created_filter = $this->getState('Report.'.$id.'_'.$layout.'_created');
        if ($created_filter != null AND $created_filter != 'all') {
            $created = $created_filter;
        }
        //filter by primary contact name
        $primary_contact_name = $this->getState('Report.'.$id.'_'.$layout.'_primary_contact_name');
        if ($primary_contact_name != null) {
            $query->where("(p.first_name LIKE '%".$primary_contact_name."%' OR p.last_name LIKE '%".$primary_contact_name."%')");
        }

        //filter by primary contact email
        $primary_contact_email = $this->getState('Report.'.$id.'_'.$layout.'_primary_contact_email');
        if ($primary_contact_email != null) {
            $query->where("p.email LIKE '%".$primary_contact_email."%'");
        }

        //filter by primary contact phone
        $primary_contact_phone = $this->getState('Report.'.$id.'_'.$layout.'_primary_contact_phone');
        if ($primary_contact_phone != null) {
            $query->where("p.phone LIKE '%".$primary_contact_phone."%'");
        }

        //get current date to use for all date filtering
        $date = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));

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
                $next_week = date('Y-m-d 00:00:00', strtotime(DateHelper::formatDBDate(date("Y-m-d", strtotime($date))) . "+7 days"));
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
                $last_week = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date) . "-7 days")));
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
                $last_month = date('Y-m-1 00:00:00', strtotime(date("Y-m-d", strtotime($date) . "-1 month")));
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
        if ($stage != null && $stage != 'all') {
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
                // else filter by the stage id
                $query->where("d.stage_id='".$stage."'");
            }
        }

        /** ---------------------------------------------------------------------------------------------------------------
         * Field for custom field user states
         */
        //Get custom filters
        $custom_fields = DealHelper::getUserCustomFields();
        //If the user has defined any custom fields we will left join the associated data here
        if ( count($custom_fields) > 0 ) {
            foreach ($custom_fields as $row) {
                    //Join different data based on type
                    switch ($row['type']) {
                        //If the type is forecast we want to calculate the amount
                        case "forecast":
                            $query->select("( d.amount * ( d.probability / 100 )) as custom_".$row['id']);
                            break;
                        //Else join the associated value from the database
                        default :
                            $query->select("custom_".$row['id'].".value as custom_".$row['id']);
                            $query->leftJoin("#__deal_custom_cf as custom_".$row['id']." on ".
                                             "custom_".$row['id'].".deal_id = d.id AND ".
                                             "custom_".$row['id'].".custom_field_id = ".$row['id']);
                            break;
                    }
                    //If the user has any associated user state requests set in the model we set the filters here
                    $custom_field_filter = $this->getState('Report.'.$id.'_'.$layout.'_'.$row['id']);
                    if ($custom_field_filter != null AND $custom_field_filter != 'all') {
                        switch ($row['type']) {
                            case "forecast":
                                $query->where("( d.amount * ( d.probability / 100 )) LIKE '%".$custom_field_filter."%'");
                                break;
                            case "date":

                                    if ($custom_field_filter == "this_week") {
                                        $this_week = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));
                                        $next_week = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+7 days"));
                                        $query->where("custom_".$row['id'].".value >= '$this_week'");
                                        $query->where("custom_".$row['id'].".value < '$next_week'");
                                    }

                                    if ($custom_field_filter == "next_week") {
                                        $next_week = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+7 days"));
                                        $week_after_next = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+14 days"));
                                        $query->where("custom_".$row['id'].".value >= '$next_week'");
                                        $query->where("custom_".$row['id'].".value < '$week_after_next'");
                                    }

                                    if ($custom_field_filter == "this_month") {
                                        $this_month = DateHelper::formatDBDate(date('Y-m-0 00:00:00'));
                                        $next_month = date('Y-m-0 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+1 month"));
                                        $query->where("custom_".$row['id'].".value >= '$this_month'");
                                        $query->where("custom_".$row['id'].".value < '$next_month'");
                                    }

                                    if ($custom_field_filter == "next_month") {
                                        $next_month = date("Y-m-0 00:00:00", strtotime(date("Y-m-d 00:00:00", strtotime($date)) . "+1 month"));
                                        $next_next_month = date("Y-m-0 00:00:00", strtotime(date("Y-m-d 00:00:00", strtotime($date)) . "+2 months"));
                                        $query->where("custom_".$row['id'].".value >= '$next_month'");
                                        $query->where("custom_".$row['id'].".value < '$next_next_month'");
                                    }

                                break;
                            default:
                                $query->where("custom_".$row['id'].".value LIKE '%".$custom_field_filter."%'");
                                break;
                        }
                    }
            }
        }

        //filter based on member access roles
        $user_id = UsersHelper::getUserId();
        $member_role = UsersHelper::getRole();
        $team_id = UsersHelper::getTeamId();

        if ($member_role != 'exec') {

            if ($member_role == 'manager') {
                $query->where("user.team_id=$team_id");
            } else {
                $query->where("(d.owner_id=$user_id)");
            }

        }

        $query->where("d.published=".$this->published);
        $query->where("d.archived=0");

        //return results
        $db->setQuery($query);
        $results = $db->loadAssocList();

        return $results;
    }

        /**
         * Method to delete a record
         * @param int $id document id to delete
         * @return boolean True on success
         */
        function deleteReport($id)
        {
            //get dbo
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            $report = $this->getCustomReports($id);
            if ( count($report) > 0 ) {
                $query->delete("#__reports");
                $query->where("id=".$id);
                $db->setQuery($query);
                $db->query();
            }

            //return
            return true;
        }

        /**
         * Populate user state requests
         */
        function populateState()
        {
            //get states
            $app = JFactory::getApplication();

            //determine view so we set correct states
            $view = $app->input->get('view');
            $layout = str_replace("_filter","",$app->input->get('layout'));
            $id = $app->input->get('id') ? $app->input->get('id') : $app->input->get('custom_report');
            //set layout for filter pages

            if ($view == "print") {
                $id = $app->input->get('custom_report');
                $layout = "custom_report";
            }

            /** --------------------------------------
             * Filter data for different views
             */
            switch ($layout) {

                case "custom_reports"    :
                    //set default filter states for reports
                    $filter_order = $app->getUserStateFromRequest('Report.filter_order','filter_order','report.name');
                    $filter_order_Dir = $app->getUserStateFromRequest('Report.filter_order_Dir','filter_order_Dir','asc');
                    //set states for reports
                    $this->state->set('Report.filter_order',$filter_order);
                    $this->state->set('Report.filter_order_Dir',$filter_order_Dir);
                break;

                case "custom_report"    :

                    //set default filter states for reports
                    $filter_order           = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_filter_order','filter_order','d.name');
                    $filter_order_Dir       = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_filter_order_Dir','filter_order_Dir','asc');
                    $deal_filter            = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_name','deal_name',null);
                    $owner_filter           = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_owner_id','owner_id',UsersHelper::getUserId());
                    $owner_type_filter      = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_owner_type','owner_type','member');
                    $amount_filter          = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_amount','deal_amount',null);
                    $source_filter          = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_source_id','source_id',null);
                    $stage_filter           = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_stage_id','stage_id',null);
                    $status_filter          = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_status_id','status_id',null);
                    $expected_close_filter  = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_expected_close','expected_close',null);
                    $modified_filter        = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_modified','modified',null);
                    $created_filter         = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_created','created',null);
                    $primary_contact_name   = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_primary_contact_name','primary_contact_name',null);
                    $primary_contact_phone  = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_primary_contact_phone','primary_contact_phone',null);
                    $primary_contact_email  = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'primary_contact_email','primary_contact_email',null);

                    //get custom filters
                    $custom_fields = DealHelper::getUserCustomFields();
                    $post_data = $app->input->post;
                    if ( count($custom_fields) > 0 ) {
                        foreach ($custom_fields as $row) {
                                $custom_field_value = $app->getUserStateFromRequest('Report.'.$id.'_'.$layout.'_'.$row['id'],'custom_'.$row['id'],null);
                                $this->state->set('Report.'.$id.'_'.$layout.'_'.$row['id'],$custom_field_value);
                        }
                    }

                    //set states for reports
                    $this->state->set('Report.'.$id.'_'.$layout.'_filter_order',$filter_order);
                    $this->state->set('Report.'.$id.'_'.$layout.'_filter_order_Dir',$filter_order_Dir);
                    $this->state->set('Report.'.$id.'_'.$layout.'_name',$deal_filter);
                    $this->state->set('Report.'.$id.'_'.$layout.'_owner_id',$owner_filter);
                    $this->state->set('Report.'.$id.'_'.$layout.'_owner_type',$owner_type_filter);
                    $this->state->set('Report.'.$id.'_'.$layout.'_amount',$amount_filter);
                    $this->state->set('Report.'.$id.'_'.$layout.'_source_id',$source_filter);
                    $this->state->set('Report.'.$id.'_'.$layout.'_stage_id',$stage_filter);
                    $this->state->set('Report.'.$id.'_'.$layout.'_status_id',$status_filter);
                    $this->state->set('Report.'.$id.'_'.$layout.'_expected_close',$expected_close_filter);
                    $this->state->set('Report.'.$id.'_'.$layout.'_modified',$modified_filter);
                    $this->state->set('Report.'.$id.'_'.$layout.'_created',$created_filter);
                    $this->state->set('Report.'.$id.'_'.$layout.'_primary_contact_phone',$primary_contact_phone);
                    $this->state->set('Report.'.$id.'_'.$layout.'_primary_contact_name',$primary_contact_name);
                    $this->state->set('Report.'.$id.'_'.$layout.'_primary_contact_phone',$primary_contact_phone);
                    $this->state->set('Report.'.$id.'_'.$layout.'_primary_contact_email',$primary_contact_email);
                break;

            }
        }
}
