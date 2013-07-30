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

$convo = $this->conversation;
echo '<div class="media" id="convo_entry_'.$convo['id'].'">';
    if ( CobaltHelperUsers::getRole() == "exec" || $convo['author'] == CobaltHelperUsers::getUserId() ) {
        echo '<div class="btn-group pull-right"><a class="btn" href="javascript:void(0);" onclick="editConvoEntry('.$convo['id'].')"><i class="icon-pencil"></i></a><a class="btn" href="javascript:void(0);" onclick="trashConvoEntry('.$convo['id'].')"><i class="icon-trash"></i></a></div>';
    }
    echo '<div class="pull-left"><img class="media-object widget" src="'.$convo['owner_avatar'].'" /></div>';
    echo '<div class="media-body">';
        echo '<h4 class="media-heading">'.$convo['owner_first_name'].' '.$convo['owner_last_name'].'</h4>';
        echo nl2br($convo['conversation']);
    echo '</div>';
echo '<hr /></div>';
