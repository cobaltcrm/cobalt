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
use Cobalt\Factory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\DropdownHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Helper\MenuHelper;
use Cobalt\Model\Users as UsersModel;
use Cobalt\Model\User as UserModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //application
        $app = Factory::getApplication();

        //display title
        $document = $app->getDocument();

        //load model
        $layout = $this->getLayout();
        $model = new UsersModel;
        $model->set("_layout", $layout);

        //add toolbar buttons to manage users
        if ($layout == 'default')
        {
            $this->toolbar = new Toolbar;
            $this->toolbar->addNew();
            $this->toolbar->addTrashRow();

            //get users
            $users = $model->getUsers();

            // Initialise variables.
            $this->state = $model->getState();

            //assign refs
            $this->users = $users;
            $this->listOrder = $this->state->get('Users.filter_order');
            $this->listDirn   = $this->state->get('Users.filter_order_Dir');

            $app->getDocument()->addScriptDeclaration("
                var loc = 'users';
                var order_dir = '" . $this->state->get('Users.filter_order_Dir') . "';
                var order_col = '" . $this->state->get('Users.filter_order') . "';
                var dataTableColumns = " . json_encode($model->getDataTableColumns()) . ";");

        }
        elseif ($this->getLayout() == 'edit')
        {
            $model = new UserModel;
            $model->set("_layout", $layout);
            $this->toolbar = new Toolbar;
            $this->toolbar->save();
            $this->toolbar->cancel();

            //get id
            $id = $app->input->getInt('id', null);

            //plugins
            // $app->triggerEvent('onBeforeCRMUserEdit', array(&$id));

            //get user
            $this->user = $model->getUser($id);

            $this->team = $this->user->getTeamByLeader();

            //view data
            $roles = DropdownHelper::getMemberRoles();
            $teamId = UsersHelper::getTeamId($id);
            $teams = UsersHelper::getTeams($teamId);
            $managers = DropdownHelper::getManagers($id);
            $this->member_roles = $roles;
            $this->teams = $teams;
            $this->managers = $managers;
        }

        /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //display
        return parent::render();
    }
}
