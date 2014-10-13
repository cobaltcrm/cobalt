<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Documents;

use Cobalt\Factory;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\DocumentHelper;
use Joomla\View\AbstractHtmlView;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render()
    {
        $layout = $this->getLayout();

        //get model
        $model = Factory::getModel('Document');

        $documents = $model->getDocuments();
        $state = $model->getState();

        //add js
	    $app = Factory::getApplication();
        $document = $app->getDocument();
        $document->addScript( $app->get('uri.media.full').'js/document_manager.js' );

        //session data
        $session = $app->getSession();
        $member_role = UsersHelper::getRole();
        $user_id = UsersHelper::getUserId();

        //associations
        $assoc = $session->get('document_assoc_filter');
        $assoc_names = DocumentHelper::getAssocTypes();
        $assoc_name = ( $assoc ) ? $assoc_names[$assoc] : $assoc_names['all'];

        //users
        $user_id = UsersHelper::getUserId();
        $user = $session->get('document_user_filter');
        $team = $session->get('document_team_filter');

        if ($user == "all")
        {
            $user_name = TextHelper::_('COBALT_ALL_USERS');
        }
        elseif ($user && $user != $user_id)
        {
            $user_info = UsersHelper::getUser($user);
            $user_name = $user_info->first_name . " " . $user_info->last_name;
        }
        elseif ($team)
        {
            $team_info = UsersHelper::getTeams($team);
            $team_info = $team_info[0];
            $user_name = $team_info['team_name'] . TextHelper::_('COBALT_TEAM_APPEND');
        }
        else
        {
            $user_name = TextHelper::_('COBALT_ME');
        }

        if ($layout == 'default')
        {
            //get model
            $model = Factory::getModel('Documents');
            $total = $model->getTotal();
            $pagination = $model->getPagination();
            $this->dataTableColumns = $model->getDataTableColumns();
            $document->addScriptDeclaration("
            var loc = 'documents';
            var order_dir = '".$state->get('People.filter_order_Dir')."';
            var order_col = '".$state->get('People.filter_order')."';
            var dataTableColumns = " . json_encode($this->dataTableColumns) . ";");
        }

        //type
        $type = $session->get('document_type_filter');
        $type_names = DocumentHelper::getDocTypes();
        $type_name = ( $type && array_key_exists($type,$type_names) ) ? $type_names[$type] : $type_names['all'];

        //teams
        $teams = UsersHelper::getTeams();
        //users
        $users = UsersHelper::getUsers();

        //list view
        $document_list = Factory::getView('documents','list','phtml',array('documents'=>$documents,'state'=>$state, 'total'=>$total,'pagination'=>$pagination));

        if ($layout == "download") {
            DealHelper::downloadDocument();
        }

        //assign ref
        $this->state            = $state;
        $this->document_list    = $document_list;
        $this->assoc_names      = $assoc_names;
        $this->assoc_name       = $assoc_name;
        $this->user_name        = $user_name;
        $this->type_names       = $type_names;
        $this->type_name        = $type_name;
        $this->member_role      = $member_role;
        $this->user_id          = $user_id;
        $this->teams            = $teams;
        $this->users            = $users;

        //display
        return parent::render();
    }

}
