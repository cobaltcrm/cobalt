<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\People;

use Cobalt\Factory;
use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\CompanyHelper;
use Cobalt\Helper\DealHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\PeopleHelper;
use Cobalt\Helper\TemplateHelper;
use Joomla\View\AbstractHtmlView;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render()
    {
        $app = Factory::getApplication();

        ///retrieve task list from model
        $model = Factory::getModel('People');

        $state = $model->getState();

        //session data
        $session = $app->getSession();

        $user_id = UsersHelper::getUserId();
        $team_id = UsersHelper::getTeamId();
        $member_role = UsersHelper::getRole();

        $people_type_name = $session->get('people_type_filter');
        $user = $session->get('people_user_filter');
        $team = $session->get('people_team_filter');
        $stage = $session->get('people_stage_filter');
        $tag = $session->get('people_tag_filter');
        $status = $session->get('people_status_filter');

        //load java
        $document = $app->getDocument();
        $document->addScript( $app->get('uri.media.full').'js/people_manager.js' );

        //get list of people
        $people = $model->getPeople();
        $person = array();

        //Pagination
        $this->pagination = $model->getPagination();

        //determine if we are editing an existing person entry
        if ( $app->input->get('id') ) {
            //grab deal object
            $person = $people[0];
            if (is_null($person['id'])) {
                $app->redirect(RouteHelper::_('index.php?view=people'),TextHelper::_('COBALT_NOT_AUTHORIZED'));
            }
            $person['header'] = TextHelper::_('COBALT_EDIT').' '.$person['first_name'] . ' ' . $person['last_name'];
        } else {
            //else we are creating a new entry
            $person = array();
            $person['id'] = '';
            $person['first_name'] = "";
            $person['last_name'] = "";
            $person['company_id'] = ( $app->input->get('company_id') ) ? $app->input->get('company_id') : null;
            $person['deal_id'] = ( $app->input->get('deal_id') ) ? $app->input->get('deal_id') : null;

            //get company name to prefill data on page and hidden fields
            if ($person['company_id']) {
                $company = CompanyHelper::getCompany($person['company_id']);
                $person['company_name'] = $company[0]['name'];
                $person['company_id'] = $company[0]['id'];
            }

            //get deal name to prefill data on page and hidden fields
            if ($person['deal_id']) {
                $deal = DealHelper::getDeal($person['deal_id']);
                $person['deal_name'] = $deal[0]['name'];
                $person['deal_id'] = $deal[0]['id'];
            }

            $person['position'] = "";
            $person['phone'] = "";
            $person['email'] = "";
            $person['type'] = '';
            $person['source_id'] = null;
            $person['status_id'] = null;
            $person['header'] = TextHelper::_('COBALT_PERSON_ADD');
        }

        //get total people associated with users account
        $total_people = UsersHelper::getPeopleCount($user_id,$team_id,$member_role);

        //get filter types
        $people_types = PeopleHelper::getPeopleTypes();
        $people_type_name = ( $people_type_name && array_key_exists($people_type_name,$people_types) ) ? $people_types[$people_type_name] : $people_types['all'];

        //get column filters
        $column_filters = PeopleHelper::getColumnFilters();
        $selected_columns = PeopleHelper::getSelectedColumnFilters();

        //get user filter
        //get associated users//teams
        $teams = UsersHelper::getTeams();
        $users = UsersHelper::getUsers();

        if ($user AND $user != $user_id AND $user != 'all') {
            $user_info = UsersHelper::getUsers($user);
            $user_info = $user_info[0];
            $user_name = $user_info['first_name'] . " " . $user_info['last_name'];
        } elseif ($team) {
            $team_info = UsersHelper::getTeams($team);
            $team_info = $team_info[0];
            $user_name = $team_info['team_name'].TextHelper::_('COBALT_TEAM_APPEND');
        } elseif ($user == 'all') {
            $user_name = TextHelper::_('COBALT_ALL_USERS');
        } else {
            $user_name = TextHelper::_('COBALT_ME');
        }

        //get stage filter
        $stages = PeopleHelper::getStages();
        $stages_name = ( $stage ) ? $stages[$stage] : $stages['past_thirty'];

        //get tag filter
        $tag_list = PeopleHelper::getTagList();
        for ( $i=0; $i<count($tag_list); $i++ ) {
            if ($tag_list[$i]['id'] == $tag AND $tag != 'any') {
                $tag_name = $tag_list[$i]['name'];
                break;
            }
        }
        $tag_name = ( $tag AND $tag != 'any' ) ? $tag_name : 'all tags';

        //get status filter
        $status_list = PeopleHelper::getStatusList();
        for ( $i=0; $i<count($status_list); $i++ ) {
            if ($status_list[$i]['id'] == $status AND $status != 'any') {
                $status_name = $status_list[$i]['name'];
                break;
            }
        }
        $status_name = ( $status AND $status != 'any' ) ? $status_name : 'any status';

        $dropdowns = $model->getDropdowns();

        //Load Events & Tasks for person
        $layout = $this->getLayout();
        if ($layout == "person") {
            $model = Factory::getModel('Event');
            $events = $model->getEvents("person",null,$app->input->get('id'));
            $this->event_dock = Factory::getView('events','event_dock','phtml',array('events'=>$events));
            $this->deal_dock = Factory::getView('deals','deal_dock','phtml', array('deals' => !empty($person['deals']) ? $person['deals'] : array() ));

            $this->document_list = Factory::getView('documents','document_row','phtml', array('documents'=>$person['documents']));
            $this->custom_fields_view = Factory::getView('custom','default','phtml',array('type'=>'people','item'=>$person));
        }

        if ($layout == "default") {
            $total = $model->getTotal();
            $pagination = $model->getPagination();
            $this->people_list = Factory::getView('people','list','phtml',array('people'=>$people,'total'=>$total,'pagination'=>$pagination));
            $this->people_filter = $state->get('Deal.people_name');
            $this->dataTableColumns = $model->getDataTableColumns();
            $document->addScriptDeclaration("
            var loc = 'people';
            var order_dir = '".$state->get('People.filter_order_Dir')."';
            var order_col = '".$state->get('People.filter_order')."';
            var dataTableColumns = " . json_encode($this->dataTableColumns) . ";");
        }

        if ($layout == "edit") {
            $item = $app->input->get('id') && array_key_exists(0,$people) ? $people[0] : array('id'=>'');
            $this->edit_custom_fields_view = Factory::getView('custom','edit','phtml',array('type'=>'people','item'=>$item));

            $companyModel = Factory::getModel('Company');

            $json = TRUE;
            $companyNames = $companyModel->getCompanyNames($json);
            $document->addScriptDeclaration("var company_names=".$companyNames.";");
        }

        if ( TemplateHelper::isMobile() && $app->input->get('id')) {
            $this->add_note = Factory::getView('note','edit','phtml',array('add_note'=>$add_note));

            $this->add_task = Factory::getView('events','edit_task','phtml',array('association_type'=>'person','assocation_id'=>$app->input->get('id')));
        }

        //assign results to view
        $this->people = $people;
        $this->person = $person;
        $this->totalPeople = $total_people;
        $this->people_type_name = $people_type_name;
        $this->people_types = $people_types;
        $this->user_id = $user_id;
        $this->team_id = $team_id;
        $this->member_role = $member_role;
        $this->user_name = $user_name;
        $this->teams = $teams;
        $this->users = $users;
        $this->stages = $stages;
        $this->stages_name = $stages_name;
        $this->tag_list = $tag_list;
        $this->tag_name = $tag_name;
        $this->status_list = $status_list;
        $this->status_name = $status_name;
        $this->state = $state;
        $this->column_filters = $column_filters;
        $this->selected_columns = $selected_columns;
        $this->dropdown = $dropdowns;

        //display
        return parent::render();
    }

}
