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

class CobaltViewDealsHtml extends JViewHTML
{

    public function render()
    {
        $app = JFactory::getApplication();

        //retrieve deal list from model
        $model = new CobaltModelDeal();
        $state = $model->getState();
        $dealList = array();
        $deal = array();
        $doc = JFactory::getDocument();

        //session info
        $session = JFactory::getSession();
        $member_role = CobaltHelperUsers::getRole();
        $user_id = CobaltHelperUsers::getUserId();
        $team_id = CobaltHelperUsers::getTeamId();

        //determine if we are requesting a specific deal or all deals
        //if id requested
        if ( $app->input->get('id') ) {
            $model->set('_id',$app->input->get('id'));
            $dealList = $model->getDeals();
            if ( is_null($dealList[0]['id']) ) {
                $app->redirect(JRoute::_('index.php?view=deals'),CRMText::_('COBALT_NOT_AUTHORIZED'));
            }
        } else {
        //else load all deals
            if ( $app->input->get('layout') != 'edit' ) {

                if (CobaltHelperTemplate::isMobile()) {
                    $model->set('ordering','d.name ASC');
                }

                $dealList = $model->getDeals();
            }
        }

        //determine if we are editing an existing deal entry
        if ( count($dealList) == 1 ) {
            //grab deal object
            $deal = $dealList[0];
            $deal['header'] = ucwords(CRMText::_('COBALT_DEAL_EDIT'));
        } else {
            //else we are creating a new entry
            $deal = array();
            $deal['name'] = "";
            $deal['summary'] = "";
            $deal['company_id'] = ( $app->input->get('company_id') ) ? $app->input->get('company_id') : null;
            $deal['person_id'] = ( $app->input->get('person_id') ) ? $app->input->get('person_id') : null;

            //get company name to prefill data and hidden fields
            if ($deal['company_id']) {
                $company = CobaltHelperCompany::getCompany($deal['company_id']);
                $deal['company_name'] = $company[0]['name'];
                $deal['company_id'] = $company[0]['id'];
            }

            //if a person is specified prefill data
            if ($deal['person_id']) {
                $person = CobaltHelperPeople::getPerson($deal['person_id']);

                $deal['person_name'] = $person[0]['last_name'] . ', ' . $person[0]['first_name'];
                $deal['person_id'] = $person[0]['id'];

                //assign company if person is associated with company
                if ($person[0]['company_id']) {
                    $deal['company_id'] = $person[0]['company_id'];
                    $deal['company_name'] = $person[0]['company_name'];
                }

            }

            //assign rest of null data
            $deal['amount'] = "";
            $deal['stage_id'] = 0;
            $deal['source_id'] = 0;
            $deal['probability'] = 0;
            $deal['status_id'] = 0;
            $deal['expected_close'] = "";
            $deal['header'] = ucwords(CRMText::_('COBALT_DEAL_HEADER'));
        }

        //load javalibs
        if (!CobaltHelperTemplate::isMobile()) {
            $doc->addScript( JURI::base().'libraries/crm/media/js/deal_manager.js' );
        }

        //dropdown info
        //get deal type filters
        $deal_types = CobaltHelperDeal::getDealTypes();
        $deal_type_name = $session->get('deal_type_filter');
        $deal_type_name = ( $deal_type_name ) ? $deal_types[$deal_type_name] : $deal_types['all'];

        //get column filters
        $column_filters = CobaltHelperDeal::getColumnFilters();
        $selected_columns = CobaltHelperDeal::getSelectedColumnFilters();

        //get member access info
        $teams = CobaltHelperUsers::getTeams();
        $users = CobaltHelperUsers::getUsers();
        $stages = CobaltHelperDeal::getStages();

        //get deal stage filters
        $stage_name = $session->get('deal_stage_filter');
        $stage_name = ( $stage_name ) ? $stages[$stage_name] : $stages['all'];

        //get session data to prefill filters
        $user_filter = $session->get('deal_user_filter');
        $team_filter = $session->get('deal_team_filter');
        if ($user_filter == "all" && $user_filter != $user_id) {
            $user_name = CRMText::_('COBALT_ALL_USERS');
        } elseif ($user_filter == "all") {
            $user_name = CRMText::_('COBALT_ME');
        } elseif ($user_filter AND $user_filter != $user_id AND $user_filter != 'all') {
            $user_info = CobaltHelperUsers::getUsers($user_filter);
            $user_info = $user_info[0];
            $user_name = $user_info['first_name'] . " " . $user_info['last_name'];
        } elseif ($team_filter) {
            $team_info = CobaltHelperUsers::getTeams($team_filter);
            $team = $team_info[0];
            $user_name = $team['team_name'].CRMText::_('COBALT_TEAM_APPEND');
        } else {
            $user_name = CRMText::_('COBALT_ME');
        }

        //get closing time filters
        $closing_names = CobaltHelperDeal::getClosing();
        $closing_name = $session->get('deal_close_filter');
        $closing_name = ( $closing_name ) ? $closing_names[$closing_name] : $closing_names['all'];

        //get total deals associated with user
        $total_deals = CobaltHelperUsers::getDealCount($user_id,$team_id,$member_role);

        //Load Events & Tasks for person
        $layout = $this->getLayout();
        if ($layout == "deal") {
                $model = new CobaltModelEvent();
                $events = $model->getEvents("deal",null,$app->input->get('id'));
                $this->event_dock = CobaltHelperView::getView('events','event_dock','phtml', array('events'=>$events));

                $primary_contact_id = CobaltHelperDeal::getPrimaryContact($dealList[0]['id']);
                $this->contact_info = CobaltHelperView::getView('contacts','default','phtml',array('contacts'=>$dealList[0]['people'],'primary_contact_id'=>$primary_contact_id));

                $this->document_list = CobaltHelperView::getView('documents','document_row','phtml',array('documents'=>$deal['documents']));
                $this->custom_fields_view = CobaltHelperView::getView('custom','default','phtml',array('type'=>'deal','item'=>$dealList[0]));

                if ( CobaltHelperBanter::hasBanter() ) {
                    $room_list = new CobaltHelperTranscriptlists();
                    $room_lists = $room_list->getRooms();
                    $transcripts = array();
                    if ( is_array($room_lists) && count($room_lists) > 0 ) {
                        $transcripts = $room_list->getTranscripts($room_lists[0]->id);
                    }
                    $this->banter_dock = CobaltHelperView::getView('banter','default','phtml',array('rooms'=>$room_lists,'transcripts'=>$transcripts));
                }
        }

        if ($layout == "default") {
            $pagination = $model->getPagination();
            $total = $model->getTotal();
            $this->deal_list = CobaltHelperView::getView('deals','list','phtml',array('dealList'=>$dealList,'total'=>$total,'pagination'=>$pagination));
            $this->state = $state;
            $doc->addScriptDeclaration("
            loc = 'deals';
            order_url = 'index.php?view=deals&layout=list&format=raw&tmpl=component';
            order_dir = '".$state->get('Deal.filter_order_Dir')."';
            order_col = '".$state->get('Deal.filter_order')."';");

            $deal_name = $state->get('Deal.deals_name');
            $this->deal_filter = $deal_name;
        }

        if ( CobaltHelperTemplate::isMobile() ) {
            $this->add_note = CobaltHelperView::getView('note','edit','phtml');
            $this->add_task = CobaltHelperView::getView('events','edit_task','phtml',array('association_type'=>'deal','assocation_id'=>$app->input->get('id')));
        }

        if ($layout == "edit") {
            $item = $app->input->get('id') && array_key_exists(0,$dealList) ? $dealList[0] : array('id'=>'');
            $this->edit_custom_fields_view = CobaltHelperView::getView('custom','edit','phtml',array('type'=>'deal','item'=>$item));

            $json = TRUE;

            $companyModel = new CobaltModelCompany();
            $companyNames = $companyModel->getCompanyNames($json);
            $doc->addScriptDeclaration("var company_names=".$companyNames.";");

            $peopleModel = new CobaltModelPeople();
            $peopleNames = $peopleModel->getPeopleNames($json);
            $doc->addScriptDeclaration("var people_names=".$peopleNames.";");
        }

        $closed_stages = CobaltHelperDeal::getClosedStages();

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
