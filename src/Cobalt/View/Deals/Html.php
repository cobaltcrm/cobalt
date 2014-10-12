<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Cobalt\View\Deals;

use Cobalt\Factory;
use Cobalt\Helper\CompanyHelper;
use Cobalt\Helper\DealHelper;
use Cobalt\Helper\PeopleHelper;
use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\TemplateHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\View\AbstractHtmlView;

defined('_CEXEC') or die('Restricted access');

/**
 * HTML view class for the deals view
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
		$app = Factory::getApplication();

		// Retrieve deal list from model
		/** @var \Cobalt\Model\Deal $model */
		$model     = Factory::getModel('Deal');
		$state     = $model->getState();
		$dealList  = array();
		$deal      = array();
		$extraData = array();

		// Session info
		$session     = $app->getSession();
		$member_role = UsersHelper::getRole();
		$user_id     = UsersHelper::getUserId();
		$team_id     = UsersHelper::getTeamId();

		// Determine if we are requesting a specific deal or all deals
		if ($app->input->get('id'))
		{
			$model->set('_id', $app->input->get('id'));
			$dealList = $model->getDeals();

			if (is_null($dealList[0]->id))
			{
				$app->redirect(RouteHelper::_('index.php?view=deals'), TextHelper::_('COBALT_NOT_AUTHORIZED'));
			}

			// Display remove and assign primary contact to deal
			$app->input->set('loc', 'deal');
		}
		else
		{
			if ($app->input->get('layout') != 'edit')
			{
				if (TemplateHelper::isMobile())
				{
					$model->set('ordering', 'd.name ASC');
				}

				$dealList = $model->getDeals();
			}
		}

		// Determine if we are editing an existing deal entry
		if (count($dealList) == 1)
		{
			// Grab deal object
			$deal         = $dealList[0];
			$deal->header = ucwords(TextHelper::_('COBALT_DEAL_EDIT'));
		}
		else
		{
			// Else we are creating a new entry
			$deal             = new \stdClass;
			$deal->name       = '';
			$deal->summary    = '';
			$deal->company_id = $app->input->get('company_id', null);
			$deal->person_id  = $app->input->get('person_id', null);

			// Get company name to prefill data and hidden fields
			if ($deal->company_id)
			{
				$company            = CompanyHelper::getCompany($deal->company_id);
				$deal->company_name = $company[0]['name'];
				$deal->company_id   = $company[0]['id'];
			}

			// If a person is specified prefill data
			if ($deal->person_id)
			{
				$person = PeopleHelper::getPerson($deal->person_id);

				$deal->person_name = $person[0]['last_name'] . ', ' . $person[0]['first_name'];
				$deal->person_id   = $person[0]['id'];

				// Assign company if person is associated with company
				if ($person[0]['company_id'])
				{
					$deal->company_id   = $person[0]['company_id'];
					$deal->company_name = $person[0]['company_name'];
				}
			}

			// Assign rest of null data
			$deal->amount         = '';
			$deal->stage_id       = 0;
			$deal->source_id      = 0;
			$deal->probability    = 0;
			$deal->status_id      = 0;
			$deal->expected_close = "";
			$deal->header         = ucwords(TextHelper::_('COBALT_DEAL_HEADER'));
		}

		// Load javalibs
		if (!TemplateHelper::isMobile())
		{
			//$doc->addScript($app->get('uri.media.full') . 'js/deal_manager.js');
		}

		// Dropdown info
		// Get deal type filters
		$deal_types     = DealHelper::getDealTypes();
		$deal_type_name = $session->get('deal_type_filter');
		$deal_type_name = array_key_exists($deal_type_name, $deal_types) ? $deal_types[$deal_type_name] : $deal_types[''];

		// Get column filters
		$column_filters   = DealHelper::getColumnFilters();
		$selected_columns = DealHelper::getSelectedColumnFilters();

		// Get member access info
		$teams  = UsersHelper::getTeams();
		$users  = UsersHelper::getUsers();
		$stages = DealHelper::getStages();

		// Get deal stage filters
		$stage_name = $session->get('deal_stage_filter');
		$stage_name = ($stage_name) ? $stages[$stage_name] : $stages['all'];

		// Get session data to prefill filters
		$user_filter = $session->get('deal_user_filter');
		$team_filter = $session->get('deal_team_filter');

		if ($user_filter == 'all' && $user_filter != $user_id)
		{
			$user_name = TextHelper::_('COBALT_ALL_USERS');
		}
		elseif ($user_filter == 'all')
		{
			$user_name = TextHelper::_('COBALT_ME');
		}
		elseif ($user_filter && $user_filter != $user_id && $user_filter != 'all')
		{
			$user_info = UsersHelper::getUsers($user_filter);
			$user_info = $user_info[0];
			$user_name = $user_info['first_name'] . ' ' . $user_info['last_name'];
		}
		elseif ($team_filter)
		{
			$team_info = UsersHelper::getTeams($team_filter);
			$team      = $team_info[0];
			$user_name = $team['team_name'] . TextHelper::_('COBALT_TEAM_APPEND');
		}
		else
		{
			$user_name = TextHelper::_('COBALT_ME');
		}

		// Get closing time filters
		$closing_names = DealHelper::getClosing();
		$closing_name  = $session->get('deal_close_filter');
		$closing_name  = ($closing_name) ? $closing_names[$closing_name] : $closing_names['all'];

		// Get total deals associated with user
		$total_deals = UsersHelper::getDealCount($user_id, $team_id, $member_role);

		//Load Events & Tasks for person
		$layout = $this->getLayout();

		if ($layout == 'deal')
		{
			$model            = Factory::getModel('Event');
			$events           = $model->getEvents('deal', null, $app->input->get('id'));
			$pagination       = $model->getPagination();
			$total            = $model->getTotal();
			//$this->event_dock = Factory::getView('events', 'event_dock', 'phtml', array('events' => $events));

			$primary_contact_id = DealHelper::getPrimaryContact($dealList[0]->id);
			//$this->contact_info = Factory::getView('contacts', 'default', 'phtml', array('contacts' => $dealList[0]->people, 'primary_contact_id' => $primary_contact_id));

			//$this->document_list = Factory::getView('documents', 'list', 'phtml', array('documents' => $deal->documents, 'total' => $total, 'pagination' => $pagination));
			//$this->custom_fields_view = Factory::getView('custom', 'default', 'phtml', array('type' => 'deal', 'item' => $dealList[0]));
		}

		if ($layout == 'default')
		{
			$extraData['dataTableColumns'] = $model->getDataTableColumns();
			$extraData['deal_filter']      = $state->get('Deal.deals_name');
			//$this->deal_list        = Factory::getView('deals', 'list', 'phtml', array('dealList' => $dealList, 'total' => $total, 'pagination' => $pagination));

		}

		if (TemplateHelper::isMobile())
		{
			//$this->add_note = Factory::getView('note', 'edit', 'phtml');
			//$this->add_task = Factory::getView('events', 'edit_task', 'phtml', array('association_type' => 'deal', 'assocation_id' => $app->input->get('id')));
		}

		if ($layout == 'edit')
		{
			$item                          = $app->input->get('id') && array_key_exists(0, $dealList) ? $dealList[0] : array('id' => '');
			//$this->edit_custom_fields_view = Factory::getView('custom', 'edit', 'phtml', array('type' => 'deal', 'item' => $item));

			$json = true;

			$companyModel = Factory::getModel('Company');
			$companyNames = $companyModel->getCompanyNames($json);
			//$doc->addScriptDeclaration("var company_names=" . $companyNames . ";");

			$peopleModel = Factory::getModel('People');
			$peopleNames = $peopleModel->getPeopleNames($json);
			//$doc->addScriptDeclaration("var people_names=" . $peopleNames . ";");
		}

		$closed_stages = DealHelper::getClosedStages();

		// Assign results to view
		$viewData = array(
			'closed_stages'    => $closed_stages,
		    'dealList'         => $dealList,
		    'totalDeals'       => $total_deals,
		    'deal'             => $deal,
		    'deal_types'       => $deal_types,
		    'deal_type_name'   => $deal_type_name,
		    'user_id'          => $user_id,
		    'member_role'      => $member_role,
			'teams'            => $teams,
			'users'            => $users,
			'stages'           => $stages,
			'stage_name'       => $stage_name,
			'user_name'        => $user_name,
			'closing_names'    => $closing_names,
			'closing_name'     => $closing_name,
			'state'            => $state,
			'column_filters'   => $column_filters,
			'selected_columns' => $selected_columns
		);

		$this->setData(array_merge($viewData, $extraData));

		return parent::render();
	}
}
