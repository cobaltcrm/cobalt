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

class CobaltViewReportsRaw extends JViewHtml
{
    public function render()
    {
        //application
        $app = JFactory::getApplication();

        //get layout requested to load correct data and pass references
        $layout = $this->getLayout();
        $func = "_display_".$layout;
        if ( function_exists($this->$func) ) {
            $this->$func();
        }

        //display
        echo parent::render();
    }

    //notes report page
    public function _display_notes_filter()
    {
        //get model for reports
        $noteModel = new CobaltModalNote();
        $this->note_entries = $noteModel->getNotes(NULL,NULL,FALSE);
    }

    //deal_milestones page
    public function _display_deal_milestones_filter()
    {
        //get deals for reports
        $dealModel = new CobaltModelDeal();
        $dealModel->set('archived',0);
        $this->deals = $dealModel->getDeals();
    }

    //roi report page
    public function _display_roi_report_filter()
    {
        //get sources for reports
        $sourceModel = new CobaltModelSource();
        $this->sources = $sourceModel->getRoiSources();
    }

    //sales pipeline page
    public function _display_sales_pipeline_filter()
    {
        //get deals for reports
        $dealModel = new CobaltModelDeal();

        $dealModel->set('archived',0);
        $dealModel->set('limit',0);
        $this->reports = $dealModel->getReportDeals();
    }

     //source report page
    public function _display_source_report_filter()
    {
        //get deals for reports
        $dealModel = new CobaltModelDeal();
        $dealModel->set('archived',0);
        $dealModel->set('limit',0);
        $this->reports = $dealModel->getDeals();
    }

    //custom reports default page
    public function _display_custom_reports_filter()
    {
        //get model
        $reportModel = new CobaltModelReport();
        $this->reports = $reportModel->getCustomReports();
        $this->state = $reportModel->getState();
    }

    //individual custom reports
    public function _display_custom_report_filter()
    {
        //get report
        $reportModel = new CobaltModelReport();
        $this->report = $reportModel->getCustomReports($app->input->get('id'));
        $this->report_data = $reportModel->getCustomReportData($app->input->get('id'));
        $this->state = $reportModel->getState();
     }

}
