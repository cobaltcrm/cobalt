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

class CobaltViewReportsHtml extends JViewHtml
{
    public function render()
    {
        //app
        $this->app = JFactory::getApplication();

        //load reports menu bar
        $this->menu = TemplateHelper::loadReportMenu();

        //get document
        $this->document = JFactory::getDocument();

        //determine view layout
        $this->layout = $this->getLayout();
        $func = "_display_".$this->layout;
        if ( method_exists($this, $func) ) {
            $this->$func();
        }

        //assign user filter priviliges
        $this->member_role = UsersHelper::getRole();
        $this->user_id = UsersHelper::getUserId();
        $this->team_id = UsersHelper::getTeamId();

        //if the user is not basic then they are able to filter through company/team/user data
        if ($this->member_role != 'basic') {

            //exec can see teams
            if ($this->member_role == 'exec') {
                $this->teams = UsersHelper::getTeams();
            }

            //manager and exec users
            $this->users = UsersHelper::getUsers();
        }

        //assign team names for drop downs
        $this->team_names = DropdownHelper::getTeamNames();

        //assign user names for drop downs
        $this->user_names = DropdownHelper::getUserNames();

        //assign categories for drop downs
        $this->categories = NoteHelper::getCategories();

        //display
        return parent::render();
    }

    public function _display_default()
    {
        //load javascripts
        $this->document->addScript( JURI::base().'libraries/crm/media/js/highcharts.js' );
        $this->document->addScript( JURI::base().'libraries/crm/media/js/sales_dashboard.js' );

        //get data for sales graphs
        $graphModel = new CobaltModelGraphs();
        $this->graph_data = $graphModel->getGraphData();
    }

    public function _display_edit_custom_report()
    {
        //load javascripts
        $this->document->addScript( JURI::base().'libraries/crm/media/js/custom_reports.js' );

        //if we are editing an existing entry
        $id = $this->app->input->get('id');
        if ($id != null) {
            $reportModel = new CobaltModelReport();
            $this->report = $reportModel->getCustomReports($id);
        }

        //assign references
        $this->columns = CobaltHelperDeal::getAllCustomFields($id);
    }

    public function _display_custom_report()
    {
        //get model
        $reportModel = new CobaltModelReport();

        //get report
        $this->report = $reportModel->getCustomReports($this->app->input->get('id'));
        $this->report_data = $reportModel->getCustomReportData($this->app->input->get('id'));
        $state = $reportModel->getState();

        //info for dropdowns
        $deal_amounts = CobaltHelperDeal::getAmounts();
        $deal_sources = CobaltHelperDeal::getSources();
        $deal_stages  = CobaltHelperDeal::getSourceStages();
        $deal_statuses = CobaltHelperDeal::getStatuses();
        $deal_close_dates = CobaltHelperDeal::getClosing();
        $modified_dates = CobaltHelperDeal::getModified();

        $custom_report_header = ViewHelper::getView('reports','custom_report_header','phtml',array('report_data'=>$this->report_data,'report'=>$this->report,'state'=>$state));
        $custom_report_list = ViewHelper::getView('reports','custom_report_filter','phtml',array('report_data'=>$this->report_data,'report'=>$this->report,'state'=>$state));
        $custom_report_footer = ViewHelper::getView('reports','custom_report_footer','phtml');

        $custom_report_header->deal_amounts = $deal_amounts;
        $custom_report_header->deal_sources = $deal_sources;
        $custom_report_header->deal_stages = $deal_stages;
        $custom_report_header->deal_statuses = $deal_statuses;
        $custom_report_header->deal_close_dates = $deal_close_dates;
        $custom_report_header->modified_dates = $modified_dates;
        $custom_report_header->created_dates = CobaltHelperDate::getCreatedDates();
        $custom_report_header->team_names = DropdownHelper::getTeamNames();
        $custom_report_header->user_names = DropdownHelper::getUserNames();
        $custom_report_header->state = $state;

        //assign refs to view
        $this->custom_report_header = $custom_report_header;
        $this->custom_report_list = $custom_report_list;
        $this->custom_report_footer = $custom_report_footer;
        $this->state = $state;
    }

    public function _display_custom_reports()
    {
        //load javascripts
        $this->document->addScript( JURI::base().'libraries/crm/media/js/custom_reports.js' );

        //get info from model
        $reportModel = new CobaltModelReport();
        $reports = $reportModel->getCustomReports();
        $state = $reportModel->getState();

        //list view
        $custom_reports_list = ViewHelper::getView('reports','custom_reports_filter','phtml', array('reports'=>$reports));

        //assign references
        $this->custom_reports_list = $custom_reports_list;
        $this->reports = $reports;
        $this->state = $state;
    }

    public function _display_notes()
    {
        //get model for reports
        $noteModel = new CobaltModelNote();
        $note_entries = $noteModel->getNotes(NULL,NULL,FALSE);
        $this->notes_list = ViewHelper::getView('reports','notes_filter','phtml', array('note_entries'=>$note_entries));

        $state = $noteModel->getState();
        $notes_header = ViewHelper::getView('reports','notes_header','phtml', array('note_entries'=>$note_entries,'state'=>$state));
        $notes_footer = ViewHelper::getView('reports','notes_footer','phtml');
        $notes_header->team_names   = DropdownHelper::getTeamNames();
        $notes_header->user_names   = DropdownHelper::getUserNames();
        $notes_header->state        = $state;

        $categories = $noteModel->getNoteCategories();
        $notes_header->categories       = $categories;
        $notes_header->created_dates    = CobaltHelperDate::getCreatedDates();

         // Initialise state variables.
        $state = $noteModel->getState();

        //assign refs to view
        $this->notes_header = $notes_header;
        $this->notes_footer = $notes_footer;
        $this->state = $state;
        $this->note_entries = $note_entries;
    }

    public function _display_deal_milestones()
    {
        //get deals for reports
        $dealModel = new CobaltModelDeal();
        $dealModel->set('archived',0);
        $deals = $dealModel->getDeals();

        //pagination
        $this->pagination = $dealModel->getPagination();

         // Initialise state variables.
        $this->state = $dealModel->getState();

        //list view
        $this->deal_milestone_list = ViewHelper::getView('reports','deal_milestones_filter','phtml', array('deals'=>$deals));
        $this->deals = $deals;
    }

    public function _display_roi_report()
    {
        //get sources for reports
        $sourceModel = new CobaltModelSource();
        $sources = $sourceModel->getRoiSources();

         // Initialise state variables.
        $state = $sourceModel->getState();

        //list view
        $roi_report_header = ViewHelper::getView('reports','roi_report_header','phtml',array('state'=>$state));
        $roi_report_list = ViewHelper::getView('reports','roi_report_filter','phtml',array('sources'=>$sources));
        $roi_report_footer = ViewHelper::getView('reports','roi_report_footer','phtml');

        //assign rfs to view
        $this->roi_report_header = $roi_report_header;
        $this->roi_report_list = $roi_report_list;
        $this->roi_report_footer = $roi_report_footer;
        $this->state = $state;
        $this->sources = $sources;
    }

    public function _display_source_report()
    {
        //get deals for reports
        $dealModel = new CobaltModelDeal();
        $dealModel->set('archived',0);
        $dealModel->set('limit',0);
        $reports = $dealModel->getDeals();

        //get model state
        $state = $dealModel->getState();

        //info for dropdowns
        $deal_amounts = CobaltHelperDeal::getAmounts();
        $deal_sources = CobaltHelperDeal::getSources();
        $deal_stages  = CobaltHelperDeal::getStages();
        $deal_statuses = CobaltHelperDeal::getStatuses();
        $deal_close_dates = CobaltHelperDeal::getClosing();
        $modified_dates = CobaltHelperDeal::getModified();

         //list view
        $source_report_header  = ViewHelper::getView('reports','source_report_header','phtml', array('state'=>$state,'reports'=>$reports));
        $source_report_list    = ViewHelper::getView('reports','source_report_filter','phtml', array('reports'=>$reports));
        $source_report_footer  = ViewHelper::getView('reports','source_report_footer','phtml');

        $source_report_header->deal_amounts = $deal_amounts;
        $source_report_header->deal_stages = $deal_stages;
        $source_report_header->deal_statuses = $deal_statuses;
        $source_report_header->deal_close_dates = $deal_close_dates;
        $source_report_header->modified_dates = $modified_dates;
        $source_report_header->created_dates = CobaltHelperDate::getCreatedDates();
        $source_report_header->team_names = DropdownHelper::getTeamNames();
        $source_report_header->user_names = DropdownHelper::getUserNames();
        $source_report_header->state = $state;

        //assign refs to view
        $this->source_report_header = $source_report_header;
        $this->source_report_list = $source_report_list;
        $this->source_report_footer = $source_report_footer;
        $this->state = $state;
        $this->reports = $reports;
    }

    public function _display_sales_pipeline()
    {
         //get deals for reports
        $dealModel = new CobaltModelDeal();
        $dealModel->set('archived',0);
        $dealModel->set('limit',0);
        $reports = $dealModel->getReportDeals();

         // Initialise state variables.
        $state = $dealModel->getState();

        //info for dropdowns
        $deal_amounts       = CobaltHelperDeal::getAmounts();
        $deal_stages        = CobaltHelperDeal::getActiveStages(TRUE);
        $deal_statuses      = CobaltHelperDeal::getStatuses();
        $deal_close_dates   = CobaltHelperDeal::getClosing();
        $modified_dates     = CobaltHelperDeal::getModified();

        //list view
        $sales_pipeline_header  = ViewHelper::getView('reports','sales_pipeline_header','phtml', array('state'=>$state,'reports'=>$reports));
        $sales_pipeline_list    = ViewHelper::getView('reports','sales_pipeline_filter','phtml', array('reports'=>$reports));
        $sales_pipeline_footer  = ViewHelper::getView('reports','sales_pipeline_footer','phtml');

        $sales_pipeline_header->deal_amounts = $deal_amounts;
        $sales_pipeline_header->deal_stages = $deal_stages;
        $sales_pipeline_header->deal_statuses = $deal_statuses;
        $sales_pipeline_header->deal_close_dates = $deal_close_dates;
        $sales_pipeline_header->modified_dates = $modified_dates;
        $sales_pipeline_header->created_dates = CobaltHelperDate::getCreatedDates();
        $sales_pipeline_header->team_names = DropdownHelper::getTeamNames();
        $sales_pipeline_header->user_names = DropdownHelper::getUserNames();
        $sales_pipeline_header->state = $state;

        //assign refs to view
        $this->sales_pipeline_header = $sales_pipeline_header;
        $this->sales_pipeline_list = $sales_pipeline_list;
        $this->sales_pipeline_footer = $sales_pipeline_footer;
        $this->state = $state;
        $this->reports = $reports;

    }

}
