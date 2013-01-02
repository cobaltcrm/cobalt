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

class CobaltControllerLogout extends CobaltControllerDefault
{
	    function execute() 
	    {	
	    	$app = JFactory::getApplication();
			if($app->logout()) {
				$app->redirect(base64_decode($app->input->get('return')));
			}else{
				$app->redirect(base64_decode($app->input->get('return')));
			}

	    }
	
}