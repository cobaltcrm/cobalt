<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Companies;

use Cobalt\Factory;
use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\TemplateHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\CompanyHelper;
use Cobalt\Model\Company as CompanyModel;
use Cobalt\Model\Event as EventModel;

use Joomla\View\AbstractHtmlView;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render()
    {
        $app = Factory::getApplication();
        $app->input->set('view', 'companies');
        $app->input->def('layout', 'default');

        $id = $app->input->getInt('id', $app->input->getInt('company_id'));

        //get model
        $model = new CompanyModel;
        $state = $model->getState();

        //session data
        $session = $app->getSession();
        $member_role = UsersHelper::getRole();
        $user_id = UsersHelper::getUserId();
        $team_id = UsersHelper::getTeamId();
        $company = $session->get('company_type_filter');
        $user = $session->get('company_user_filter');
        $team = $session->get('company_team_filter');

        //load java libs
        $doc = $app->getDocument();
        $doc->addScript( $app->get('uri.media.full').'js/company_manager.js' );

        //determine if we are requesting a specific company or all companies
        //if id requested
        if ($id)
        {
            $companies = $model->getCompanies($id);

            if (is_null($companies[0]->id))
            {
                $app->redirect(RouteHelper::_('index.php?view=companies'), TextHelper::_('COBALT_NOT_AUTHORIZED'));
            }
        }
        else
        {
            //else load all companies
            if ($app->input->get('layout') != 'edit')
            {
                $companies = $model->getCompanies();
            }
        }

        //assign pagination
        $pagination = $model->getPagination();
        $this->pagination = $pagination;

        //get company type filters
        $company_types = CompanyHelper::getTypes();
        $company_type = ( $company ) ? $company_types[$company] : $company_types['all'];

        //get user filter
        if ($user AND $user != $user_id AND $user != 'all')
        {
            $user_info = UsersHelper::getUsers($user);
            $user_info = $user_info[0];
            $user_name = $user_info['first_name'] . " " . $user_info['last_name'];
        }
        elseif ($team)
        {
            $team_info = UsersHelper::getTeams($team);
            $team_info = $team_info[0];
            $user_name = $team_info['team_name'].TextHelper::_('COBALT_TEAM_APPEND');
        }
        elseif ($user == 'all' || $user == "")
        {
            $user_name = TextHelper::_('COBALT_ALL_USERS');
        }
        else
        {
            $user_name = TextHelper::_('COBALT_ME');
        }

        //get associated members and teams
        $teams = UsersHelper::getTeams();
        $users = UsersHelper::getUsers();

        //get total associated companies for count display
        $company_count = UsersHelper::getCompanyCount($user_id, $team_id, $member_role);

        //Load Events & Tasks for person
        $layout = $app->input->get('layout');

        switch ($layout)
        {
            case 'company':

                $model = new EventModel;
                $events = $model->getEvents("company", null, $app->input->get('id'));

                $this->event_dock = Factory::getView('events', 'event_dock', 'phtml', array('events' => $events));
                $this->deal_dock = Factory::getView('deals', 'deal_dock', 'phtml', array('deals' => $companies[0]->deals));
                $this->document_list = Factory::getView('documents', 'document_row', 'phtml', array('documents' => $companies[0]->documents));
                $this->people_dock = Factory::getView('people', 'people_dock', 'html', array('people' => $companies[0]->people));
                $this->custom_fields_view = Factory::getView('custom', 'default', 'html', array('type' => 'company', 'item' => $companies[0]));

                if (TemplateHelper::isMobile())
                {
                    $add_note = Factory::getView('note','edit','html');
                    $this->add_note = $add_note;
                }

            break;
            case 'default':
            default:

                //get column filters
                $this->column_filters = CompanyHelper::getColumnFilters();
                $this->selected_columns = CompanyHelper::getSelectedColumnFilters();
                $this->company_list = Factory::getView('companies', 'list', 'html', array('companies' => $companies));
                $this->company_filter = $state->get('Company.companies_name');
                $this->dataTableColumns = $model->getDataTableColumns();

                $doc->addScriptDeclaration("
                var loc = 'companies';
                var order_dir = '".$state->get('Company.filter_order_Dir')."';
                var order_col = '".$state->get('Company.filter_order')."';
                var dataTableColumns = " . json_encode($this->dataTableColumns) . ";");
            break;

            case 'edit':
                $item = $app->input->get('id') && array_key_exists(0,$companies) ? $companies[0] : array('id'=>'');
                $this->edit_custom_fields_view = Factory::getView('custom','edit','html', array('type' => 'company', 'item' => $item));
            break;

        }

        //ref assignments
        $this->companies=$companies;
        $this->user_id=$user_id;
        $this->member_role=$member_role;
        $this->teams=$teams;
        $this->users=$users;
        $this->company_types=$company_types;
        $this->company_type=$company_type;
        $this->user_name=$user_name;
        $this->company_count=$company_count;
        $this->state=$state;

        //display
        return parent::render();
    }

}
