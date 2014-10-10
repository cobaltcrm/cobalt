<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Deals;

use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\LinkHelper;
use Cobalt\Model\Deal as DealModel;
use Cobalt\Model\Event as EventModel;
use Cobalt\Model\Company as CompanyModel;
use Cobalt\Model\People as PeopleModel;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\TemplateHelper;
use Cobalt\Helper\CompanyHelper;
use Cobalt\Helper\DealHelper;
use Cobalt\Helper\PeopleHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\ViewHelper;
use Joomla\View\AbstractHtmlView;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{

    public function render()
    {
        $app = \Cobalt\Container::fetch('app');

        //retrieve deal list from model
        $model = new DealModel;
        $state = $model->getState();
        $dealList = array();
        $deal = array();
        $doc = $app->getDocument();

        //session info
        $session = \Cobalt\Container::fetch('app')->getSession();
        $member_role = UsersHelper::getRole();
        $user_id = UsersHelper::getUserId();
        $team_id = UsersHelper::getTeamId();

        //determine if we are requesting a specific deal or all deals
        //if id requested
        if ( $app->input->get('id') ) {
            $model->set('_id',$app->input->get('id'));
            $dealList = $model->getDeals();
            if ( is_null($dealList[0]->id) ) {
                $app->redirect(RouteHelper::_('index.php?view=deals'),TextHelper::_('COBALT_NOT_AUTHORIZED'));
            }
            //display remove and assign primary contact to deal
            $app->input->set('loc', 'deal');
        } else {
        //else load all deals
            if ( $app->input->get('layout') != 'edit' ) {

                if (TemplateHelper::isMobile()) {
                    $model->set('ordering','d.name ASC');
                }

                $dealList = $model->getDeals();
            }
        }

        //determine if we are editing an existing deal entry
        if ( count($dealList) == 1 ) {
            //grab deal object
            $deal = $dealList[0];
            $deal->header = ucwords(TextHelper::_('COBALT_DEAL_EDIT'));
        } else {
            //else we are creating a new entry
            $deal = new \stdClass;
            $deal->name = "";
            $deal->summary = "";
            $deal->company_id = ( $app->input->get('company_id') ) ? $app->input->get('company_id') : null;
            $deal->person_id = ( $app->input->get('person_id') ) ? $app->input->get('person_id') : null;

            //get company name to prefill data and hidden fields
            if ($deal->company_id) {
                $company = CompanyHelper::getCompany($deal->company_id);
                $deal->company_name = $company[0]['name'];
                $deal->company_id = $company[0]['id'];
            }

            //if a person is specified prefill data
            if ($deal->person_id) {
                $person = PeopleHelper::getPerson($deal->person_id);

                $deal->person_name = $person[0]['last_name'] . ', ' . $person[0]['first_name'];
                $deal->person_id = $person[0]['id'];

                //assign company if person is associated with company
                if ($person[0]['company_id']) {
                    $deal->company_id = $person[0]['company_id'];
                    $deal->company_name = $person[0]['company_name'];
                }

            }

            //assign rest of null data
            $deal->amount = "";
            $deal->stage_id = 0;
            $deal->source_id = 0;
            $deal->probability = 0;
            $deal->status_id = 0;
            $deal->expected_close = "";
            $deal->header = ucwords(TextHelper::_('COBALT_DEAL_HEADER'));
        }

        //load javalibs
        if (!TemplateHelper::isMobile()) {
            $doc->addScript( $app->get('uri.media.full').'js/deal_manager.js' );
        }

        //dropdown info
        //get deal type filters
        $deal_types = DealHelper::getDealTypes();
        $deal_type_name = $session->get('deal_type_filter');
        $deal_type_name = array_key_exists($deal_type_name, $deal_types) ? $deal_types[$deal_type_name] : $deal_types[''];

        //get column filters
        $column_filters = DealHelper::getColumnFilters();
        $selected_columns = DealHelper::getSelectedColumnFilters();

        //get member access info
        $teams = UsersHelper::getTeams();
        $users = UsersHelper::getUsers();
        $stages = DealHelper::getStages();

        //get deal stage filters
        $stage_name = $session->get('deal_stage_filter');
        $stage_name = ( $stage_name ) ? $stages[$stage_name] : $stages['all'];

        //get session data to prefill filters
        $user_filter = $session->get('deal_user_filter');
        $team_filter = $session->get('deal_team_filter');
        if ($user_filter == "all" && $user_filter != $user_id) {
            $user_name = TextHelper::_('COBALT_ALL_USERS');
        } elseif ($user_filter == "all") {
            $user_name = TextHelper::_('COBALT_ME');
        } elseif ($user_filter AND $user_filter != $user_id AND $user_filter != 'all') {
            $user_info = UsersHelper::getUsers($user_filter);
            $user_info = $user_info[0];
            $user_name = $user_info['first_name'] . " " . $user_info['last_name'];
        } elseif ($team_filter) {
            $team_info = UsersHelper::getTeams($team_filter);
            $team = $team_info[0];
            $user_name = $team['team_name'].TextHelper::_('COBALT_TEAM_APPEND');
        } else {
            $user_name = TextHelper::_('COBALT_ME');
        }

        //get closing time filters
        $closing_names = DealHelper::getClosing();
        $closing_name = $session->get('deal_close_filter');
        $closing_name = ( $closing_name ) ? $closing_names[$closing_name] : $closing_names['all'];

        //get total deals associated with user
        $total_deals = UsersHelper::getDealCount($user_id,$team_id,$member_role);

        //Load Events & Tasks for person
        $layout = $this->getLayout();
        if ($layout == "deal") {
            $model = new EventModel;
            $events = $model->getEvents("deal",null,$app->input->get('id'));
            $pagination = $model->getPagination();
            $total = $model->getTotal();
            $this->event_dock = ViewHelper::getView('events','event_dock','phtml', array('events'=>$events));

            $primary_contact_id = DealHelper::getPrimaryContact($dealList[0]->id);
            $this->contact_info = ViewHelper::getView('contacts','default','phtml',array('contacts'=>$dealList[0]->people,'primary_contact_id'=>$primary_contact_id));

            $this->document_list = ViewHelper::getView('documents','list','phtml',array('documents' => $deal->documents,'total'=> $total,'pagination'=> $pagination));
            //$this->document_list = ViewHelper::getView('documents','document_row','phtml',array('documents'=>$deal->documents));
            $this->custom_fields_view = ViewHelper::getView('custom','default','phtml',array('type'=>'deal','item'=>$dealList[0]));
        }

        if ($layout == "default") {
            $this->dataTableColumns = $model->getDataTableColumns();
            $pagination = $model->getPagination();
            $total = $model->getTotal();
            $this->deal_list = ViewHelper::getView('deals','list','phtml',array('dealList'=>$dealList,'total'=>$total,'pagination'=>$pagination));
            $this->state = $state;
            $doc->addScriptDeclaration("
            loc = 'deals';
            order_url = 'index.php?view=deals&layout=list&format=raw&tmpl=component';
            order_dir = '".$state->get('Deal.filter_order_Dir')."';
            order_col = '".$state->get('Deal.filter_order')."';
            var dataTableColumns = " . json_encode($this->dataTableColumns) . ";");

            $deal_name = $state->get('Deal.deals_name');
            $this->deal_filter = $deal_name;
        }

        if ( TemplateHelper::isMobile() ) {
            $this->add_note = ViewHelper::getView('note','edit','phtml');
            $this->add_task = ViewHelper::getView('events','edit_task','phtml',array('association_type'=>'deal','assocation_id'=>$app->input->get('id')));
        }

        if ($layout == "edit") {
            $item = $app->input->get('id') && array_key_exists(0,$dealList) ? $dealList[0] : array('id'=>'');
            $this->edit_custom_fields_view = ViewHelper::getView('custom','edit','phtml',array('type'=>'deal','item'=>$item));

            $json = TRUE;

            $companyModel = new CompanyModel;
            $companyNames = $companyModel->getCompanyNames($json);
            $doc->addScriptDeclaration("var company_names=".$companyNames.";");

            $peopleModel = new PeopleModel;
            $peopleNames = $peopleModel->getPeopleNames($json);
            $doc->addScriptDeclaration("var people_names=".$peopleNames.";");
        }

        $closed_stages = DealHelper::getClosedStages();

        //assign results to view
        $this->closed_stages = $closed_stages;
        $this->dealList = $dealList;
        $this->totalDeals = $total_deals;
        $this->deal = $deal;
        $this->deal_types = $deal_types;
        $this->deal_type_name = $deal_type_name;
        $this->user_id = $user_id;
        $this->member_role = $member_role;
        $this->teams = $teams;
        $this->users = $users;
        $this->stages = $stages;
        $this->stage_name = $stage_name;
        $this->user_name = $user_name;
        $this->closing_names = $closing_names;
        $this->closing_name = $closing_name;
        $this->state = $state;
        $this->column_filters = $column_filters;
        $this->selected_columns = $selected_columns;

        //display
        return parent::render();
    }

}
