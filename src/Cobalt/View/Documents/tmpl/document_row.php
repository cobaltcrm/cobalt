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
    if ( count($this->documents) > 0 ) {
        foreach ($this->documents as $key => $document) {
            $k = $key%2;
            echo '<tr class="document_'.$key.'" id="document_row_'.$document['id'].'" class="cobalt_row_'.$k.'">';
                echo '<td><img width="30px" height="30px" src="'.JURI::base().'libraries/crm/media/images/'.$document['filetype'].'.png'.'" /><br /><b>'.strtoupper($document['filetype']).'<b></td>';
                echo '<td>';
                echo'<div class="dropdown">';
                echo '<a href="javascript:void(0);" class="dropdown-toggle" role="button" data-toggle="dropdown" id="'.$document['id'].'">'.$document['name'].'</a>';
                echo '<ul class="dropdown-menu" role="menu">';
                    if ($document['is_image']) {
                        echo '<li><a href="javascript:void(0);" class="document_preview" id="preview_'.$document['id'].'">'.TextHelper::_('COBALT_PREVIEW').'</a></li>';
                    }
                    echo '<li><a href="javascript:void(0);" class="document_download" id="download_'.$document['id'].'">'.TextHelper::_('COBALT_DOWNLOAD').'</a></li>';
                    echo '<li><a href="javascript:void(0);" class="document_delete" id="delete_'.$document['id'].'">'.TextHelper::_('COBALT_DELETE').'</a></li>';
                echo '</ul>';
                echo '<div class="document_edit_menu" id="document_'.$document['id'].'">';
                        echo '<input type="hidden" name="document_'.$document['id'].'_hash" id="document_'.$document['id'].'_hash" value="'.$document['filename'].'" />';
                    echo '</div>';
                echo '</div></td>';
                echo '<td>'.$document['owner_name'].'</td>';
                echo '<td>'.$document['size'].'Kb</td>';
                echo '<td>'.DateHelper::formatDate($document['created']).'</td>';
            echo '</tr>';
        }
    }
