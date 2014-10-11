<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Cobalt\View\Dashboard;

use Cobalt\Factory;
use Cobalt\Helper\TemplateHelper;
use Cobalt\Helper\ActivityHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\View\AbstractHtmlView;

defined('_CEXEC') or die;

/**
 * HTML view class for the login view
 *
 * @since  1.0
 */
class Html extends AbstractHtmlView
{
	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function render()
	{
		$viewData = array();

		/** @var \Cobalt\Model\Event $model */
		$model = Factory::getModel('Event');

		if (TemplateHelper::isMobile())
		{
			$model->set('current_events', true);
		}

		$events = $model->getEvents();

		/** @var \Cobalt\Model\Deal $dealModel */
		$dealModel = Factory::getModel('Deal');
		$dealModel->set('_view', 'dashboard');
		$dealModel->set('recent', true);
		$dealModel->set('archived', 0);
		$recentDeals = $dealModel->getDeals();

		//get data for sales graphs
		/** @var \Cobalt\Model\Graphs $model */
		$model      = Factory::getModel('Graphs');
		$graph_data = $model->getGraphData();

		$activityHelper = new ActivityHelper;
		$activity       = $activityHelper->getActivity();

		// Assign results to view
		$viewData['events']      = $events;
		$viewData['graph_data']  = $graph_data;
		$viewData['recentDeals'] = $recentDeals;
		$viewData['activity']    = $activity;

		$json = true;

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
			$companyModel   = Factory::getModel('Company');
			$totalCompanies = $companyModel->getTotal();

			$user = UsersHelper::getLoggedInUser();

			$viewData['first_name']   = $user->first_name;
			$viewData['numEvents']    = count($events);
			$viewData['numDeals']     = $totalDeals;
			$viewData['numLeads']     = $totalLeads;
			$viewData['numContacts']  = $totalContacts;
			$viewData['numCompanies'] = $totalCompanies;
		}

		$viewData['peopleNames'] = $peopleModel->getPeopleNames($json);

		/** @var \Cobalt\Model\Deal $dealModel */
		$dealModel = Factory::getModel('Deal');
		$viewData['dealNames'] = $dealModel->getDealNames($json);

		$this->setData($viewData);

		return parent::render();
	}
}
