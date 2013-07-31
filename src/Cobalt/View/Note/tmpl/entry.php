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

$note = $this->note; ?>
<div class="media" id="note_entry_<?php echo $note['id']; ?>">
    <?php if ( UsersHelper::getRole() == "exec" || $note['author'] == UsersHelper::getUserId() ) { ?>
        <div class="btn-group pull-right"><a class="btn" href="javascript:void(0);" onclick="editNoteEntry(<?php echo $note['id']; ?>)"><i class="icon-pencil"></i></a><a class="btn" href="javascript:void(0);" onclick="trashNoteEntry(<?php echo $note['id']; ?>)"><i class="icon-trash"></i></a></div>
    <?php } ?>
    <div class="pull-left"><img class="media-object widget" src="<?php echo $note['owner_avatar']; ?>" /></div>
    <div class="media-body">
        <h4 class="media-heading"><?php echo $note['owner_first_name'].' '.$note['owner_last_name']; ?></h4>
        <div>
            <small>
            <?php echo TextHelper::_('COBALT_WROTE_NOTE');
            if ( array_key_exists('category_name',$note) && $note['category_name'] !=  "" ) {
                echo TextHelper::_('COBALT_IN').'<b> '.$note['category_name'].'</b> ';
            }
            if ( array_key_exists('person_first_name',$note) && $note['person_first_name'] !=  "" ) {
                echo TextHelper::_('COBALT_FOR').' <b>'.$note['person_first_name'].' '.$note['person_last_name'].'</b> ';
            }
            if ( array_key_exists('deal_name',$note) && $note['deal_name'] !=  ""  ) {
                echo TextHelper::_('COBALT_ON_THE_DEAL').' <b>'.$note['deal_name'].'</b>';
            }
            if ( array_key_exists('event_name',$note) && $note['event_name'] !=  "" ) {
                echo TextHelper::_('COBALT_ON_THE_EVENT').' <b>'.$note['event_name'].'</b>';
            }
            echo ' '.DateHelper::formatDate($note['created']); ?>
            </small>
        </div>
        <?php echo nl2br($note['note']); ?>
    </div>
    <hr />
</div>
