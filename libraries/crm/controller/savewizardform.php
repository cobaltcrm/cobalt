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

class CobaltControllerSaveWizardForm extends CobaltControllerDefault
{

    function execute(){

    	$app = JFactory::getApplication();
        $type = $app->input->get('save_type');
        switch($type){
            case "lead":
            case "contact":
                $model = new CobaltModelPeople();
            break;
            case "company":
                $model = new CobaltModelCompany();
            break;
            case "deal":
                $model = new CobaltModelDeal();;
            break;
        }
        $model->store();
        header('Location: '.base64_decode($app->input->get('return')));
   }

}