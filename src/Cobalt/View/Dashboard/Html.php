<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Dashboard;

use JUri;
use JFactory;
use Joomla\View\AbstractHtmlView;
use Cobalt\Model\Event as EventModel;
use Cobalt\Model\Graphs as GraphsModel;
use Cobalt\Model\Deal as DealModel;
use Cobalt\Model\Company as CompanyModel;
use Cobalt\Model\People as PeopleModel;
use Cobalt\Helper\TemplateHelper;
use Cobalt\Helper\ViewHelper;
use Cobalt\Helper\ActivityHelper;
use Cobalt\Helper\UsersHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {

        //get model and retrieve info
        $model = new EventModel;

        if (TemplateHelper::isMobile()) {
            $model->set('current_events',true);
        }

        $events = $model->getEvents();
        $eventDock = ViewHelper::getView('events','dashboard_event_dock','phtml', array('events'=>$events));

        $dealModel = new DealModel;
		$dealModel->set('_view', 'dashboard');

        $dealModel->set('recent',true);
        $dealModel->set('archived',0);
        $recentDeals = $dealModel->getDeals();

        // load java libs
        $doc = JFactory::getDocument();
        $doc->addScript( JURI::base().'src/Cobalt/media/js/highcharts.js' );
        $doc->addScript( JURI::base().'src/Cobalt/media/js/dashboard.js' );

        //get data for sales graphs
        $model = new GraphsModel;
        $graph_data = $model->getGraphData();

        $activityHelper = new ActivityHelper;
        $activity = $activityHelper->getActivity();

        //assign results to view
        $this->eventDock 	= $eventDock;
        $this->graph_data 	= $graph_data;
        $this->recentDeals 	= $recentDeals;
        $this->activity 	= $activity;

        $json = TRUE;
        $peopleModel = new PeopleModel;

        if (TemplateHelper::isMobile()) {

            $dealModel->set('recent',false);
            $totalDeals = $dealModel->getTotal();

            $peopleModel->set('type','leads');
            $totalLeads = $peopleModel->getTotal();

            $peopleModel->set('type','not_leads');
            $totalContacts = $peopleModel->getTotal();

            $companyModel = new CompanyModel;
            $totalCompanies = $companyModel->getTotal();

            $user = UsersHelper::getLoggedInUser();

            $this->first_name 	= $user->first_name;
            $this->numEvents 	= count($events);
            $this->numDeals 	= $totalDeals;
            $this->numLeads  	= $totalLeads;
            $this->numContacts 	= $totalContacts;
            $this->numCompanies = $totalCompanies;

        }

        $peopleNames = $peopleModel->getPeopleNames($json);
        $doc->addScriptDeclaration("var people_names=".$peopleNames.";");

        $dealModel = new DealModel;
        $dealNames = $dealModel->getDealNames($json);
        $doc->addScriptDeclaration("var deal_names=".$dealNames.";");

         /** get latest activities **/
        $this->latest_activities = ViewHelper::getView('dashboard','latest_activities','phtml');
        $this->latest_activities->activity = $activity;

        $activityHelper = new ActivityHelper;
        $activity = $activityHelper->getActivity();

        //display
        return parent::render();
    }

}
