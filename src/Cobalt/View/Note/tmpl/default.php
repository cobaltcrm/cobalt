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
    <span class="pull-right"><a class="btn" id="edit_note_message" data-target="#addNote" data-toggle="modal"><i class="glyphicon glyphicon-plus icon-mini"></i><?php echo TextHelper::_('COBALT_ADD_NOTE_BUTTON'); ?></a></span>
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
                <form action="index.php?task=save&model=note&view=<?php echo $app->input->getCmd('view'); ?>" method="post" id="note" name="note">
                    <input type="hidden" name="deal_id" value="<?php echo $app->input->get('id'); ?>" />
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
<?php
    $c = count($this->notes);
        $limit = ( $c > 3 && $app->input->get('format')=='raw' ) ? 3 : $c;
        for ($i=0; $i<$limit; $i++) {
            $note = $this->notes[$i];
            $view = ViewHelper::getView('note','entry','phtml',array('note'=>$note));
            echo $view->render();
        }
?>
</div>
<script>
    $('input[name=deal_id]').val(deal_id);
</script>