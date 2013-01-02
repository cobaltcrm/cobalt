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

class CobaltControllerImport extends CobaltControllerDefault

		function execute(){

			$app = JFactory::getApplication();

			$success = false;

			$data = $app->input->getRequest('post');

			if ( is_array($data) && count($data) > 0 ){

				$import_type = $data['import_type'];
				unset($data['import_type']);

				switch($import_type){
	                case "companies":
	                    	$import_model = "company";
	                    break;
	                case "deals":
	                    	$import_model = "deal";
	                    break;
	                case "people":
	                    	$import_model = "people";  
	                    break;
            	}

            	if ( isset($import_model) ) {		
            		$model = new CobaltModelImport();
					if ( $model->importCSVData($data['import_id'],$import_model) ){
						$success = true;
					}
				}

			}

			if ( $success ){

				$msg = CRMText::_('COBALT_IMPORT_WAS_SUCCESSFUL');
				$app->redirect(JRoute::_('index.php?view='.$import_type),$msg);

			}else{

				$msg = CRMText::_('COBALT_ERROR_IMPORTING');
				$app->redirect(JRoute::_('index.php?view='.$import_type),$msg);
				
			}
		}


}