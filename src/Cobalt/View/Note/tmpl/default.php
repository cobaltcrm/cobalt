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
$app = JFactory::getApplication();
?>

<?php if ( $app->input->get('format') != "raw" ) { ?>
<h2 id="notes_header"><?php echo TextHelper::_('COBALT_EDIT_NOTES'); ?></h2><hr />
<?php } ?>

<div class="clearfix padding">
    <span class="pull-right"><a class="btn" id="edit_note_message" data-target="#addNote" onclick="Notes.resetModalForm();" data-toggle="modal"><i class="glyphicon glyphicon-plus icon-mini"></i><?php echo TextHelper::_('COBALT_ADD_NOTE_BUTTON'); ?></a></span>
</div>

<?php if ( $app->input->get('view')!="print" ) { ?>
<div class="modal fade" id="addNote" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo ucwords(TextHelper::_('COBALT_ADD_NOTE')); ?></h3>
            </div>
            <div class="modal-body">
                <form action="<?php echo RouteHelper::_('index.php?task=save'); ?>" method="post" id="note" name="note">
                    <input type="hidden" name="deal_id" id="deal_id" value="" />
                    <input type="hidden" name="note_id" id="note_id" value="">
                    <input type="hidden" name="model" value="note">
                    <textarea rows="6" class="form-control" id="deal_note" name="note"></textarea>
                </form>
            </div>
            <div class="modal-footer">
                <div class="actions"><input class="btn btn-success" type="button" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" onclick="Cobalt.sumbitModalForm(this);"/> <?php echo TextHelper::_('COBALT_OR'); ?> <a href="javascript:void(0);" data-dismiss="modal" aria-hidden="true"><?php echo TextHelper::_('COBALT_CANCEL'); ?></a></div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<div id="note_entries">
</div>
<input type="hidden" name="notes_start" id="notes_start" value="0">
<input type="hidden" name="notes_limit" id="notes_limit" value="4">
<script>
    Notes.init();
    Notes.loadMore(association_type,<?php echo $this->object_id; ?>,'#note_entries','#notes_start','#notes_limit', 4);
</script>