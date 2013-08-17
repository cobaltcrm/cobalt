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
defined( '_CEXEC' ) or die( 'Restricted access' );

$conversation = $this->conversation;
?>
<form id="convo_edit" method="post" action="<?php echo 'index.php?task=save&model=conversations'; ?>" onsubmit="return save(this)" >
    <input type="hidden" name="id" value="<?php echo $conversation['id']; ?>" />
    <div id="editForm">
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_SENTENCE'); ?></div>
            <div class="cobaltValue">
                <textarea class="inputbox" name="conversation"><?php echo $conversation['conversation']; ?></textarea>
            </div>
        </div>
        <div class="actions">
            <a href="javascript:void(0);" onclick="addConvoEntry('convo_edit');" class="button"><?php echo TextHelper::_('COBALT_SAVE_BUTTON'); ?></a>
            <a href="javascript:void(0);" onclick="window.top.window.jQuery('.ui-dialog-content').dialog('close');"><?php echo TextHelper::_('COBALT_CANCEL_BUTTON'); ?></a>
        </div>
    </div>
</form>
