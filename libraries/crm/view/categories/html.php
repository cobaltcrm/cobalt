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

class CobaltViewCategoriesHtml extends JViewHtml
{

    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        CobaltHelperUsers::authenticateAdmin();

        /** Menu Links **/
        $menu = CobaltHelperMenu::getMenuModules();
        $this->menu = $menu;

        //site document
        $document = JFactory::getDocument();
        $document->addScript(JURI::base()."/libraries/crm/media/js/cobalt-admin.js");

         //gather information for view
        $model = new CobaltModelCategories();

        $layout = $this->getLayout();
        $model->set("_layout",$layout);

        if ($layout && $layout == 'edit') {

            CRMToolbarHelper::cancel('cancel');
            CRMToolbarHelper::save('save');

            $this->category = $model->getCategory();

        } else {

            //buttons
            CRMToolbarHelper::addNew('edit');
            CRMToolbarHelper::editList('edit');
            CRMToolbarHelper::deleteList(JText::_('COBALT_CONFIRMATION'),'remove');

            //view references
            $categories = $model->getCategories();
            $this->categories = $categories;

            // Initialise state variables.
            $state = $model->getState();
            $this->state = $state;

            $this->listOrder  = $state->get('Categories.filter_order');
            $this->listDirn   = $state->get('Categories.filter_order_Dir');
        }

        //display
        return parent::render();
    }
}
