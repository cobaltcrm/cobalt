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

$note = $this->notes[0];
?>
<form id="note_edit" method="post" action="<?php echo 'index.php?controller=save&model=company&return=companies'; ?>" onsubmit="return save(this)" >
    <input type="hidden" name="id" value="<?php echo $note['id']; ?>" />
    <div id="editForm">
        <?php if ( array_key_exists('person_id', $note) && $note['person_id'] != 0 ) { ?>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_PERSON')); ?></div>
            <div class="cobaltValue">
                <select class="form-control" name="person_id">
                <?php
                    $people = DropdownHelper::getPeopleList();
                    echo JHtml::_('select.options', $people, 'value', 'text', $note['person_id'], true);
                ?>
                </select>
            </div>
        </div>
        <?php } ?>
        <?php if ( array_key_exists('deal_id', $note) && $note['deal_id'] != 0 ) { ?>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_DEAL')); ?></div>
            <div class="cobaltValue">
                <?php echo DropdownHelper::generateDropdown('deal',$note['deal_id']); ?>
            </div>
        </div>
        <?php } ?>
        <?php if ( array_key_exists('company_id', $note) && $note['company_id'] != 0 ) { ?>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_COMPANY')); ?></div>
            <div class="cobaltValue">
                <?php echo DropdownHelper::generateDropdown('company',$note['deal_id']); ?>
            </div>
        </div>
        <?php } ?>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_CATEGORY')); ?></div>
            <div class="cobaltValue">
                <select class="form-control" name="category_id">
                    <?php
                        $categories = NoteHelper::getCategories();
                        echo JHtml::_('select.options', $categories, 'value', 'text', $note['category_id'], true);
                    ?>
                </select>
            </div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_CREATED_ON'); ?></div>
            <div class="cobaltValue">
                <span class="date"><?php echo DateHelper::formatDate($note['created']). ' '.DateHelper::formatTime($note['created']); ?></span>
            </div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_CONTENT'); ?></div>
            <div class="cobaltValue">
                <textarea class="form-control" name="note"><?php echo $note['note']; ?></textarea>
            </div>
        </div>
        <?php /**
        <div class="actions"><a href="javascript:void(0);" onclick="addNoteEntry('note_edit');" class="button"><?php echo TextHelper::_('COBALT_SAVE_BUTTON'); ?></a><a href="javascript:void(0);" onclick="window.top.window.jQuery('.ui-dialog-content').dialog('close');"><?php echo TextHelper::_('COBALT_CANCEL_BUTTON'); ?></a></div>
        **/ ?>
    </div>
</form>
