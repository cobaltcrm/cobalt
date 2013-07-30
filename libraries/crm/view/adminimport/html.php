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

class CobaltViewAdminimportHtml extends JViewHtml
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        CobaltHelperUsers::authenticateAdmin();

        //app
        $app = JFactory::getApplication();
        if ($app->input->get('layout')=='sample') {
            $this->_displaySample($tpl);

            return;
        }

        /** Menu Links **/
        $menu = CobaltHelperMenu::getMenuModules();
        $this->menu = $menu;

        //javascripts
        $doc = JFactory::getDocument();
        $doc->addScript(JURI::base()."/libraries/crm/media/js/cobalt-admin.js");
        $doc->addScript(JURI::base()."/libraries/crm/media/js/document_manager.js");

        //import data
        $import_post = FALSE;
        if ( count($_FILES) > 0 ) {

            $import_post = TRUE;
            $import_data = array();
            $import_type = $app->input->get('import_type');

            $model = new CobaltModelImport();

            foreach ($_FILES as $file) {
                $data = $model->readCSVFile($file['tmp_name']);
                $import_data = array_merge($import_data,$data);
            }

            if ( count($import_data) > 500 ) {

                switch ( $app->input->get('import_type') ) {
                    case "company":
                        $import_model = "company";
                    break;
                    case "people":
                        $import_model = "people";
                    break;
                    case "deals":
                        $import_model = "deal";
                    break;
                }

                if ( $model->importCSVData($import_data,$import_model) ) {
                    $success = "SUCCESSFULLY";
                } else {
                    $success = "UNSUCCESSFULLY";
                }

                $view = "import";
                $app = JFactory::getApplication();
                $msg = CRMText::_('COBALT_'.$success.'_IMPORTED_ITEMS');
                $app->redirect('index.php?view='.$view,$msg);

            }

            $this->headers = $import_data['headers'];
            unset($import_data['headers']);
            $this->import_data = $import_data;
            $this->import_type = $import_type;
             $doc->addScriptDeclaration('import_length='.count($import_data).';');
             $doc->addScriptDeclaration("show_tab='import_review';");

        }

        $this->import_post = $import_post;

        //display
        return parent::render();
    }

    public function _displaySample($tpl=null)
    {
        /** Menu Links **/
        $menu = CobaltHelperMenu::getMenuModules();
        $this->menu = $menu;

        $doc = JFactory::getDocument();
        $doc->addScript(JURI::base()."/libraries/crm/media/js/cobalt-admin.js");

        return parent::render();
    }

}
