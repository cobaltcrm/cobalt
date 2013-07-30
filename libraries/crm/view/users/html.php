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

class CobaltViewUsersHtml extends JViewHtml
{

    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        CobaltHelperUsers::authenticateAdmin();

        //application
        $app = JFactory::getApplication();

        //display title
        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'libraries/crm/media/js/cobalt-admin.js');

        //load model
        $layout = $this->getLayout();
        $model = new CobaltModelUsers();
        $model->set("_layout",$layout);

        //add toolbar buttons to manage users
        if ($layout == 'default') {

            //buttons
            CRMToolbarHelper::addNew('edit');
            CRMToolbarHelper::editList('edit');
            CRMToolbarHelper::deleteList(JText::_('COBALT_CONFIRMATION'),'delete');

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
            CRMToolbarHelper::cancel('cancel');
            CRMToolbarHelper::save('save');

            //get id
            $id = $app->input->get('cid',null,'array');
            if ( is_array($id) && array_key_exists(0,$id) ) {
                $id = $id[0];
            } else {
                $id = null;
            }

            //plugins
            $dispatcher = JEventDispatcher::getInstance();
            $dispatcher->trigger('onBeforeCRMUserEdit', array(&$id));

            //get user
            $this->user = $model->getUser($id);
            $this->users = $model->getUsers();

            //view data
            $roles = CobaltHelperDropdown::getMemberRoles();
            $teamId = CobaltHelperUsers::getTeamId($id);
            $teams = CobaltHelperUsers::getTeams($teamId);
            $managers = CobaltHelperDropdown::getManagers($id);
            $this->member_roles = $roles;
            $this->teams = $teams;
            $this->managers = $managers;
        }

        //javascripts
        $document->addScript(JURI::base().'libraries/crm/media/js/bootstrap-colorpicker.js');

        //stylesheets
        $document->addStylesheet(JURI::base().'libraries/crm/media/css/bootstrap-colorpicker.css');

        /** Menu Links **/
        $menu = CobaltHelperMenu::getMenuModules();
        $this->menu = $menu;

        //display
        return parent::render();
    }

}
