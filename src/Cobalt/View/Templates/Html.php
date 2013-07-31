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
defined( '_CEXEC' ) or die( 'Restricted access' );

class CobaltViewTemplatesHtml extends JViewHtml
{

    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //javascripts
        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'libraries/crm/media/js/cobalt-admin.js');

        /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //gather information for view
        $model = new CobaltModelTemplates();

        //get layout
        $layout = $this->getLayout();
        $model->set("_layout",$layout);

        //filter for layout type
        if ($layout == "edit") {

             //toolbar buttons
            ToolbarHelper::cancel('cancel');
            ToolbarHelper::save('save');

            //javascripts
            $document->addScript(JURI::base().'libraries/crm/media/js/template_manager.js');

            //assign view data
            $this->template_types = DropdownHelper::getTemplateTypes();
            $this->template =  $model->getTemplate();

        } else {

            //buttons
            ToolbarHelper::addNew('edit');
            ToolbarHelper::editList('edit');
            ToolbarHelper::deleteList(JText::_('COBALT_CONFIRMATION'),'delete');

            $templates = $model->getTemplates();
            $this->templates = $templates;

            // Initialise state variables.
            $state = $model->getState();
            $this->state = $state;

            $this->listOrder = $this->state->get('Templates.filter_order');
            $this->listDirn   = $this->state->get('Templates.filter_order_Dir');

        }

        //display
        return parent::render();
    }
}
