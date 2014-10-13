<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\FormWizard;

use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Helper\DropdownHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Model\FormWizard as FormWizardModel;
use Cobalt\Model\Users as UsersModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render()
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //application
        $app = Factory::getApplication();

        //load model
        $layout = $this->getLayout();
        $model = new FormWizardModel;
        $model->set("_layout",$layout);

        //document
        $document = $app->getDocument();

        // Create the toolbar object
        $this->toolbar = new Toolbar;

        //add toolbar buttons to manage users
        if ($layout == 'default')
        {
            //buttons
            $this->toolbar->addNew();
            $this->toolbar->addDeleteRow();

            // ToolbarHelper::editList('edit');

            // Initialise variables.
            $this->state = $model->getState();;
            $this->forms = $model->getForms();

            $document->addScriptDeclaration("
                var loc = 'formwizard';
                var order_dir = '" . $this->state->get('Formwizard.filter_order_Dir') . "';
                var order_col = '" . $this->state->get('Formwizard.filter_order') . "';
                var dataTableColumns = " . json_encode($model->getDataTableColumns()) . ";");

            $document->addScriptDeclaration('jQuery(function() { FormWizard.bind(); });');
        }
        elseif ($layout == 'edit')
        {
            //buttons
            $this->toolbar->save();
            $this->toolbar->cancel();

            //form
            $form_id = $model->getTempFormId();
            $this->form_id = $form_id;
            $this->form = $model->getForm();

            //form types
            $this->form_types = DropdownHelper::getFormTypes($this->form->type);
            $fields = array(
                'lead' => DropdownHelper::getFormFields('people'),
                'contact' => DropdownHelper::getFormFields('people'),
                'deal' => DropdownHelper::getFormFields('deal'),
                'company' => DropdownHelper::getFormFields('company')
            );
            $this->fields = $fields;
            $document->addScriptDeclaration('var fields='.json_encode($fields));

            //get joomla users to add
            $model = new UsersModel;
            $user_list = $model->getUsers();
            $document->addScriptDeclaration('var user_list='.json_encode($user_list).';');
        }

        /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //display
        return parent::render();
    }

}
