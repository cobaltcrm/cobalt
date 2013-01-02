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


	$c = count($this->notes);
	for ( $i=0; $i<$c; $i++ ) {
		$note = $this->notes[$i];
		echo '<li>';
			echo '<div class="ui-li-aside"><b>'.CobaltHelperDate::formatDate($note['created']).'</b></div>';
			echo '<h3 class="ui-li-heading"><b>'.$note['owner_first_name'].' '.$note['owner_last_name'].'</b></h3>';
			echo '<p class="ui-li-desc">'.$note['note'].'</p>';					
		echo '</li>';
	}
