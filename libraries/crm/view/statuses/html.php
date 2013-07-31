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

class CobaltViewStatusesHtml extends JViewHtml
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        CobaltHelperUsers::authenticateAdmin();

        //document
        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'libraries/crm/media/js/cobalt-admin.js');

         /** Menu Links **/
        $menu = CobaltHelperMenu::getMenuModules();
        $this->menu = $menu;

        $layout = $this->getLayout();

        //gather information for view
        $model = new CobaltModelStatuses();
        $model->set("_layout",$layout);
        $this->pagination   = $model->getPagination();

        if ($layout && $layout == 'edit') {

            //toolbar buttons
            CRMToolbarHelper::cancel('cancel');
            CRMToolbarHelper::save('save');

            //javascripts
            $document->addScript(JURI::base().'libraries/crm/media/js/bootstrap-colorpicker.js');

            //stylesheets
            $document->addStylesheet(JURI::base().'libraries/crm/media/css/bootstrap-colorpicker.css');

            //get status
            $this->status = $model->getStatus();

            //script declarations
            if ($this->status['color'] != null) {
                $document->addScriptDeclaration('var status_color = "'.$this->status['color'].'";');
            } else {
                $document->addScriptDeclaration('var status_color = "ff0000";');
            }

        } else {

            //buttons
            CRMToolbarHelper::addNew('edit');
            CRMToolbarHelper::editList('edit');
            CRMToolbarHelper::deleteList(JText::_('COBALT_CONFIRMATION'),'delete');

            //statuses
            $statuses = $model->getStatuses();
            $this->statuses  = $statuses;

            // Initialise state variables.
            $state = $model->getState();
            $this->state = $state;

            $this->listOrder  = $this->state->get('Statuses.filter_order');
            $this->listDirn   = $this->state->get('Statuses.filter_order_Dir');
            $this->saveOrder  = $this->listOrder == 'c.ordering';

        }

        //display
        return parent::render();
    }
}
