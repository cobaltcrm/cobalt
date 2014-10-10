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

use Cobalt\Factory;
use Joomla\View\AbstractHtmlView;
use Cobalt\Helper\TemplateHelper;
use Cobalt\Helper\ActivityHelper;
use Cobalt\Helper\UsersHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {

        //get model and retrieve info
	    /** @var \Cobalt\Model\Event $model */
        $model = Factory::getModel('Event');

        if (TemplateHelper::isMobile())
        {
            $model->set('current_events', true);
        }

        $events = $model->getEvents();
        $eventDock = Factory::getView('events','dashboard_event_dock','phtml', array('events'=>$events));

	    /** @var \Cobalt\Model\Deal $dealModel */
	    $dealModel = Factory::getModel('Deal');
		$dealModel->set('_view', 'dashboard');

        $dealModel->set('recent',true);
        $dealModel->set('archived',0);
        $recentDeals = $dealModel->getDeals();

        $doc = Factory::getApplication()->getDocument();

        //get data for sales graphs
	    /** @var \Cobalt\Model\Graphs $model */
	    $model = Factory::getModel('Graphs');
        $graph_data = $model->getGraphData();

        $activityHelper = new ActivityHelper;
        $activity = $activityHelper->getActivity();

        //assign results to view
        $this->eventDock 	= $eventDock;
        $this->graph_data 	= $graph_data;
        $this->recentDeals 	= $recentDeals;
        $this->activity 	= $activity;

        $json = TRUE;

	    /** @var \Cobalt\Model\People $peopleModel */
	    $peopleModel = Factory::getModel('People');

        if (TemplateHelper::isMobile())
        {

            $dealModel->set('recent', false);
            $totalDeals = $dealModel->getTotal();

            $peopleModel->set('type', 'leads');
            $totalLeads = $peopleModel->getTotal();

            $peopleModel->set('type', 'not_leads');
            $totalContacts = $peopleModel->getTotal();

	        /** @var \Cobalt\Model\Company $companyModel */
	        $companyModel = Factory::getModel('Company');
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

	    /** @var \Cobalt\Model\Deal $dealModel */
	    $dealModel = Factory::getModel('Deal');
        $dealNames = $dealModel->getDealNames($json);
        $doc->addScriptDeclaration("var deal_names=".$dealNames.";");

         /** get latest activities **/
        $this->latest_activities = Factory::getView('dashboard','latest_activities','phtml',array('activity' => $activity));

        //display
        return parent::render();
    }
}
