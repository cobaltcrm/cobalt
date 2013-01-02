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

class CobaltViewFormwizardHtml extends JViewHtml
{

	function render()
	{
        //authenticate the current user to make sure they are an admin
        CobaltHelperUsers::authenticateAdmin();

        //load model
        $layout = $this->getLayout();
        $model = new CobaltModelFormwizard();
        $model->set("_layout",$layout);
        
        //document
        $document =& JFactory::getDocument();
        
        //add toolbar buttons to manage users
        if ( $layout == 'default' ){

            //buttons
            CRMToolbarHelper::addNew('edit');
            CRMToolbarHelper::editList('edit');
            CRMToolbarHelper::deleteList(JText::_('COBALT_CONFIRMATION'),'remove');
            
            // Initialise variables.
            $this->state = $model->getState();;
            $this->forms = $model->getForms();

            $this->listOrder = $this->state->get('Formwizard.filter_order');
            $this->listDirn   = $this->state->get('Formwizard.filter_order_Dir');
            
        }else if ( $layout == 'edit' ){

            //buttons
            CRMToolbarHelper::cancel('cancel');
            CRMToolbarHelper::save('save');

            //form
            $form_id = $model->getTempFormId();
            $this->form_id = $form_id;
            $this->form = $model->getForm();

            //form types
            $this->form_types = CobaltHelperDropdown::getFormTypes($this->form['type']);
            $fields = array(
                    'lead'=>CobaltHelperDropdown::getFormFields('people'),
                    'contact'=>CobaltHelperDropdown::getFormFields('people'),
                    'deal'=>CobaltHelperDropdown::getFormFields('deal'),
                    'company'=>CobaltHelperDropdown::getFormFields('company') 
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
        $menu = CobaltHelperMenu::getMenuModules();
        $this->menu = $menu;
	    
	    //display
		return parent::render();
	}
    
}