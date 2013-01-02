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

class CobaltControllerCheckEmailName extends CobaltControllerDefault
{

    function execute()
      {
      		$app = JFactory::getApplication();
            $emailExists = CobaltHelperCobalt::checkEmailName($app->input->get('email'));
            if ( $emailExists ){
                $success = true;
                $msg = CRMText::_('COBALT_EMAIL_EXISTS');
            }else{
                $success = true;
                $msg = CRMText::_('COBALT_EMAIL_IS_AVAILABLE');
            }
            $return = array('success'=>$success,'message'=>$msg,'email_exists'=>$emailExists);
            echo json_encode($return);
       }

}