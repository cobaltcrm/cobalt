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
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<table class="table table-striped table-hover table-bordered">
	<th></th>
	<th><?php echo CRMText::_('COBALT_PEOPLE_NAME'); ?></th>
	<th><?php echo CRMText::_('COBALT_PEOPLE_PHONE'); ?></th>
	<th><?php echo CRMText::_('COBALT_PEOPLE_OWNER'); ?></th>
	<th><?php echo CRMText::_('COBALT_PEOPLE_TYPE'); ?></th>
	<th><?php echo CRMText::_('COBALT_PEOPLE_CONTACTED'); ?></th>
	<tbody id="people_list">
	<?php
		$deal_dock_list = CobaltHelperView::getView('people','people_dock_list','phtml',array('people'=>$this->people));
		echo $deal_dock_list->render();
	?>
	</tbody>
</table>