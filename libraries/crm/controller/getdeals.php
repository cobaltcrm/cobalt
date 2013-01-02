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

class CobaltControllerGetDeals extends CobaltControllerDefault
{

	function execute(){
		
		//open model
		$model = new CobaltModelDeal();

		//retrieve all people
		$deals = $model->getDealList();
		
		//return results as json object
		echo json_encode($deals);
		
	}

}