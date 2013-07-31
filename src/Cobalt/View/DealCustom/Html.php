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

class CobaltViewDealcustomHtml extends JViewHtml
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //document
        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'libraries/crm/media/js/cobalt-admin.js');
        $document->addScript(JURI::base().'libraries/crm/media/js/custom_manager.js');

        /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //model
        $model = new CobaltModelDealcustom();

        //gather information for view
        $layout = $this->getLayout();
        $model->set("_layout",$layout);
        $this->pagination   = $model->getPagination();

        if ($layout && $layout == 'edit') {

            //toolbar
            ToolbarHelper::cancel('cancel');
            ToolbarHelper::save('save');

            //assign view info
            $this->custom_types = DropdownHelper::getCustomTypes('deal');
            $this->custom = $model->getItem();
            if ($this->custom['type'] != null) {
                    $document->addScriptDeclaration('var type = "'.$this->custom['type'].'";');
            }

        } else {

            //buttons
            ToolbarHelper::addNew('edit');
            ToolbarHelper::editList('edit');
            ToolbarHelper::deleteList(JText::_('COBALT_CONFIRMATION'),'delete');

            //assign view info
            $custom = $model->getCustom();
            $this->custom_fields = $custom;

            // Initialise state variables.
            $state = $model->getState();
            $this->state = $state;

            $this->listOrder  = $this->state->get('Dealcustom.filter_order');
            $this->listDirn   = $this->state->get('Dealcustom.filter_order_Dir');
            $this->saveOrder  = $this->listOrder == 'c.ordering';
        }

        //display
        return parent::render();
    }
}
