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

class CobaltControllerRemoveEvent extends CobaltControllerDefault
{

	function execute(){
		$model = new CobaltModelEvent();
		if($model->removeEvent()) {
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('success'=>false));
		}
	}

}