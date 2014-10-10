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
$app = \Cobalt\Factory::getApplication();
?>

<?php if (isset($this->type) && isset($this->id)): ?>
<script>
    association_type = '<?php echo $this->type; ?>';
    id = '<?php echo intval($this->id); ?>';
    <?php if (isset($this->var)) echo sprintf('%s=%s',$this->var,$this->id); ?>
</script>
<?php endif; ?>

<?php if ( $app->input->get('format') != "raw" ) { ?>
<h2 id="notes_header"><?php echo TextHelper::_('COBALT_EDIT_NOTES'); ?></h2><hr />
<?php } ?>

<div class="clearfix padding">
    <span class="pull-right"><a class="btn btn-default" id="edit_note_message" data-target="#addNote" onclick="Cobalt.resetModalForm(this);" data-toggle="modal"><i class="glyphicon glyphicon-plus icon-mini"></i><?php echo TextHelper::_('COBALT_ADD_NOTE_BUTTON'); ?></a></span>
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
                    <input type="hidden" name="deal_id" id="note_deal_id" value="" />
                    <input type="hidden" name="person_id" id="note_person_id" value="" />
                    <input type="hidden" name="company_id" id="note_company_id" value="" />
                    <input type="hidden" name="event_id" id="note_event_id" value="" />
                    <input type="hidden" name="note_id" id="note_note_id" value="">
                    <input type="hidden" name="model" value="note">
                    <textarea rows="6" class="form-control" id="deal_note" name="note"></textarea>
                    <br />
                    <div class="row">
                        <div class="col-xs-4">
                            <select name="category_id" id="note_category_id" class="form-control">
                                <option value=""><?php echo TextHelper::_('COBALT_NONE'); ?></option>
                                <?php foreach ($this->categories as $value => $text): ?>
                                    <option value="<?php echo $value; ?>"><?php echo $text; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div id="note_autocomplete_person_container" class="hidden">
                        <input type="text" name="note_autocomplete_person" placeholder="<?php echo TextHelper::_('COBALT_BEGIN_TYPING_TO_SEARCH_PERSON'); ?>" class="form-control"  />
                    </div>
                    <div id="note_autocomplete_deal_container" class="hidden">
                        <input type="text" name="note_autocomplete_deal" placeholder="<?php echo TextHelper::_('COBALT_BEGIN_TYPING_TO_SEARCH_DEAL'); ?>" class="form-control"  />
                    </div>
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
    Notes.loadMore(association_type,id,'#note_entries','#notes_start','#notes_limit', 4);
</script>
