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

class CobaltViewStagesHtml extends JViewHtml
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        CobaltHelperUsers::authenticateAdmin();

        /** Menu Links **/
        $menu = CobaltHelperMenu::getMenuModules();
        $this->menu = $menu;

        //gather information for view
        $model = new CobaltModelStages();

        $layout = $this->getLayout();
        $model->set("_layout",$layout);
        $this->pagination   = $model->getPagination();
        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'libraries/crm/media/js/cobalt-admin.js');

        if ($layout && $layout == 'edit') {

            CRMToolbarHelper::cancel('cancel');
            CRMToolbarHelper::save('save');

            $document->addScript(JURI::base().'libraries/crm/media/js/stage_manager.js');
            $document->addScript(JURI::base().'libraries/crm/media/js/bootstrap-colorpicker.js');
            //stylesheets
            $document->addStylesheet(JURI::base().'libraries/crm/media/css/bootstrap-colorpicker.css');

            $this->stage = $model->getStage();

        } else {

            //buttons
            CRMToolbarHelper::addNew('edit');
            CRMToolbarHelper::editList('edit');
            CRMToolbarHelper::deleteList(JText::_('COBALT_CONFIRMATION'),'delete');

            $stages = $model->getStages();
            $this->stages = $stages;

            // Initialise state variables.
            $state = $model->getState();
            $this->state = $state;

            $this->listOrder  = $this->state->get('Stages.filter_order');
            $this->listDirn   = $this->state->get('Stages.filter_order_Dir');
            $this->saveOrder  = $this->listOrder == 's.ordering';
        }

        //display
        return parent::render();
    }
}
