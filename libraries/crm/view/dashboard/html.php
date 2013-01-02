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

class CobaltViewDashboardHtml extends JViewHtml
{
	function render($tpl = null)
	{

		//get model and retrieve info
		$model = new CobaltModelEvent();

		if(CobaltHelperTemplate::isMobile()) {
			$model->set('current_events',true);
		}

		$events = $model->getEvents();		
		$eventDock = CobaltHelperView::getView('events','dashboard_event_dock','phtml', array('events'=>$events));

		$dealModel = new CobaltModelDeal();
		$dealModel->set('recent',true);
		$dealModel->set('archived',0);
		$recentDeals = $dealModel->getDeals();
        
		// load java libs
		$doc = JFactory::getDocument();
        $doc->addScript( JURI::base().'libraries/crm/media/js/highcharts.js' );
        $doc->addScript( JURI::base().'libraries/crm/media/js/dashboard.js' );
		
        //get data for sales graphs
        $model = new CobaltModelGraphs();
        $graph_data = $model->getGraphData();

        $activityHelper = new CobaltHelperActivity;
        $activity = $activityHelper->getActivity();


		//assign results to view
		$this->eventDock 	= $eventDock;
        $this->graph_data 	= $graph_data;
        $this->recentDeals 	= $recentDeals;
        $this->activity 	= $activity;

        $json = TRUE;
        $peopleModel = new CobaltModelPeople();

        if(CobaltHelperTemplate::isMobile()) {

        	$dealModel->set('recent',false);
	        $totalDeals = $dealModel->getTotal();

	        $peopleModel->set('type','leads');
	        $totalLeads = $peopleModel->getTotal();

	        $peopleModel->set('type','not_leads');
	        $totalContacts = $peopleModel->getTotal();

	        $companyModel = new CobaltModelCompany();
	        $totalCompanies = $companyModel->getTotal();

	        $user = CobaltHelperUsers::getLoggedInUser();

	        $this->first_name 	= $user->first_name;
	        $this->numEvents 	= count($events);
	        $this->numDeals 	= $totalDeals;
	        $this->numLeads  	= $totalLeads;
	        $this->numContacts 	= $totalContacts;
	        $this->numCompanies = $totalCompanies;

        }

        $peopleNames = $peopleModel->getPeopleNames($json);
        $doc->addScriptDeclaration("var people_names=".$peopleNames.";");

        $dealModel = new CobaltModelDeal();
        $dealNames = $dealModel->getDealNames($json);
        $doc->addScriptDeclaration("var deal_names=".$dealNames.";");

         /** get latest activities **/
        $this->latest_activities = CobaltHelperView::getView('dashboard','latest_activities','phtml');
        $this->latest_activities->activity = $activity;
        $activityHelper = new CobaltHelperActivity;
        $activity = $activityHelper->getActivity();
		
		//display
		return parent::render();
	}
	
}