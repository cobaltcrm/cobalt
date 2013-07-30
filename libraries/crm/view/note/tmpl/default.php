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
$app = JFactory::getApplication();
?>

<?php if ( $app->input->get('format') != "raw" ) { ?>
<h2 id="notes_header"><?php echo CRMText::_('COBALT_EDIT_NOTES'); ?></h2><hr />
<?php } ?>

<div class="clearfix padding">
    <span class="pull-right"><a class="btn" id="edit_note_message"><i class="icon-plus icon-mini"></i><?php echo CRMText::_('COBALT_ADD_NOTE_BUTTON'); ?></a></span>
</div>

<?php if ( $app->input->get('view')!="print" ) { ?>
<form id="note" name="note">
    <input type="hidden" name="<?php echo $app->input->get('type'); ?>_id" value="<?php echo $app->input->get('id');; ?>" />
    <div style="display:none;" id="note_entry_area" class="width-auto well note_entry_area">
        <div class="large_info">
            <div class="lead">
                <textarea class="width-100 hidden" id="deal_note" name="note"></textarea>
            </div>
        </div>
        <div style="display: none;" id="note_details_area">
        </div>
        <div class="btn-group" id="note_actions_area" style="display:none;">
            <a class="btn btn-success" id="add_note_entry_button" href="javascript:void(0);"><?php echo CRMText::_('COBALT_SAVE'); ?></a>
            <a class="btn" onclick="hideNoteArea();" href="javascript:void(0);"><?php echo CRMText::_('COBALT_CANCEL'); ?></a>
        </div>
    </div>
</form>
<?php } ?>
<div id="note_entries">
<?php
    $c = count($this->notes);
        $limit = ( $c > 3 && $app->input->get('format')=='raw' ) ? 3 : $c;
        for ($i=0; $i<$limit; $i++) {
            $note = $this->notes[$i];
            $view = CobaltHelperView::getView('note','entry','phtml',array('note'=>$note));
            echo $view->render();
        }
?>
</div>
