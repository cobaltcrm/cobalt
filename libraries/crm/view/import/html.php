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

class CobaltViewImportHtml extends JViewHtml
{
    public function render($tpl = null)
    {

        $app = JFactory::getApplication();

        //Load java libs
        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'libraries/crm/media/js/import_manager.js');

        if ( count($_FILES) > 0 ) {
            $model = new CobaltModelImport();
            foreach ($_FILES as $file) {
                $import_data = $model->readCSVFile($file['tmp_name']);
            }
            $this->headers = $import_data['headers'];
            unset($import_data['headers']);
            $this->import_data = $import_data;

            if ( count($import_data) > 500 ) {
                    switch ( $app->input->get('import_type') ) {
                        case "company":
                            $view = "companies";
                            $import_model = "company";
                        break;
                        case "people":
                            $view = "people";
                            $import_model = "people";
                        break;
                        case "deals":
                            $view = "deals";
                            $import_model = "deal";
                        break;
                    }
                    if ( $model->importCSVData($import_data,$import_model) ) {
                        $success = "SUCCESSFULLY";
                    } else {
                        $success = "UNSUCCESSFULLY";
                        $view = "import&import_type=".$app->input->get('import_type');
                    }
                $app = JFactory::getApplication();
                $msg = CRMText::_('COBALT_'.$success.'_IMPORTED_ITEMS');
                $app->redirect(JRoute::_('index.php?view='.$view),$msg);
            }

            $doc = JFactory::getDocument();
             $doc->addScriptDeclaration('import_length='.count($import_data).';');

        }

        $import_type = $app->input->get('import_type');
        $import_header = ucwords(CRMText::_('COBALT_IMPORT_'.$import_type));
        $this->import_type = $import_type;
        $this->import_header = $import_header;

        //display
        return parent::render();
    }

}
