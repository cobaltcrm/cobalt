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

class CobaltViewFormwizardHtml extends JViewHtml
{

    public function render()
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //load model
        $layout = $this->getLayout();
        $model = new CobaltModelFormwizard();
        $model->set("_layout",$layout);

        //document
        $document = JFactory::getDocument();

        //add toolbar buttons to manage users
        if ($layout == 'default') {

            //buttons
            ToolbarHelper::addNew('edit');
            ToolbarHelper::editList('edit');
            ToolbarHelper::deleteList(JText::_('COBALT_CONFIRMATION'),'remove');

            // Initialise variables.
            $this->state = $model->getState();;
            $this->forms = $model->getForms();

            $this->listOrder = $this->state->get('Formwizard.filter_order');
            $this->listDirn   = $this->state->get('Formwizard.filter_order_Dir');

        } elseif ($layout == 'edit') {

            //buttons
            ToolbarHelper::cancel('cancel');
            ToolbarHelper::save('save');

            //form
            $form_id = $model->getTempFormId();
            $this->form_id = $form_id;
            $this->form = $model->getForm();

            //form types
            $this->form_types = DropdownHelper::getFormTypes($this->form['type']);
            $fields = array(
                    'lead'=>DropdownHelper::getFormFields('people'),
                    'contact'=>DropdownHelper::getFormFields('people'),
                    'deal'=>DropdownHelper::getFormFields('deal'),
                    'company'=>DropdownHelper::getFormFields('company')
                );
            $this->fields = $fields;
            $document->addScriptDeclaration('var fields='.json_encode($fields));

            //get joomla users to add
            $model = new CobaltModelUsers();
            $user_list = $model->getUsers();
            $document->addScriptDeclaration('var user_list='.json_encode($user_list).';');

        }

        //javascripts
        $document->addScript(JURI::base().'libraries/crm/media/js/jquery.base64.js');
        $document->addScript(JURI::base().'libraries/crm/media/js/formwizard.js');
        $document->addScript(JURI::base().'libraries/crm/media/js/cobalt-admin.js');

        /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //display
        return parent::render();
    }

}