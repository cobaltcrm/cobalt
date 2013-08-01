<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Users;

use Joomla\View\AbstractHtmlView;
use JUri;
use JFactory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\DropdownHelper;
use Cobalt\Helper\ToolbarHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Model\Users as UsersModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //application
        $app = JFactory::getApplication();

        //display title
        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'libraries/crm/media/js/cobalt-admin.js');

        //load model
        $layout = $this->getLayout();
        $model = new UsersModel;
        $model->set("_layout",$layout);

        //add toolbar buttons to manage users
        if ($layout == 'default') {

            //buttons
            ToolbarHelper::addNew('edit');
            ToolbarHelper::editList('edit');
            ToolbarHelper::deleteList(TextHelper::_('COBALT_CONFIRMATION'),'delete');

            //get users
            $users = $model->getUsers();

            // Initialise variables.
            $this->state = $model->getState();

            //assign refs
            $this->users = $users;
            $this->listOrder = $this->state->get('Users.filter_order');
            $this->listDirn   = $this->state->get('Users.filter_order_Dir');

        } elseif ( $this->getLayout() == 'edit' ) {

            //buttons
            ToolbarHelper::cancel('cancel');
            ToolbarHelper::save('save');

            //get id
            $id = $app->input->get('cid',null,'array');
            if ( is_array($id) && array_key_exists(0,$id) ) {
                $id = $id[0];
            } else {
                $id = null;
            }

            //plugins
            $app->triggerEvent('onBeforeCRMUserEdit', array(&$id));

            //get user
            $this->user = $model->getUser($id);
            $this->users = $model->getUsers();

            //view data
            $roles = DropdownHelper::getMemberRoles();
            $teamId = UsersHelper::getTeamId($id);
            $teams = UsersHelper::getTeams($teamId);
            $managers = DropdownHelper::getManagers($id);
            $this->member_roles = $roles;
            $this->teams = $teams;
            $this->managers = $managers;
        }

        //javascripts
        $document->addScript(JURI::base().'libraries/crm/media/js/bootstrap-colorpicker.js');

        //stylesheets
        $document->addStylesheet(JURI::base().'libraries/crm/media/css/bootstrap-colorpicker.css');

        /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //display
        return parent::render();
    }

}
