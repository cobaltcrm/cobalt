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

$conversation = $this->conversation;
?>
<form id="convo_edit" method="POST" action="<?php echo 'index.php?controller=save&model=conversations'; ?>" onsubmit="return save(this)" >
	<input type="hidden" name="id" value="<?php echo $conversation['id']; ?>" />
	<div id="editForm">
		<div class="cobaltRow">
			<div class="cobaltField"><?php echo CRMText::_('COBALT_SENTENCE'); ?></div>
			<div class="cobaltValue">
				<textarea class="inputbox" name="conversation"><?php echo $conversation['conversation']; ?></textarea>
			</div>
		</div>
		<div class="actions">
			<a href="javascript:void(0);" onclick="addConvoEntry('convo_edit');" class="button"><?php echo CRMText::_('COBALT_SAVE_BUTTON'); ?></a>
			<a href="javascript:void(0);" onclick="window.top.window.jQuery('.ui-dialog-content').dialog('close');"><?php echo CRMText::_('COBALT_CANCEL_BUTTON'); ?></a>
		</div>
	</div>
</form>