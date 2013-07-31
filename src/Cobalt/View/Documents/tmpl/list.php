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

?>
    <thead>
        <th><div class="sort_order"><a href="javascript:void(0);"class="d.type" onclick="sortTable('d.type',this)"><?php echo TextHelper::_('COBALT_DOCUMENT_TYPE'); ?></a></div></th>
        <th><div class="sort_order"><a href="javascript:void(0);"class="d.filename" onclick="sortTable('d.filename',this)"><?php echo TextHelper::_('COBALT_DOCUMENT_NAME'); ?></a></div></th>
        <th><?php echo TextHelper::_('COBALT_DOCUMENT_ASSOCIATION'); ?></th>
        <th><div class="sort_order"><a href="javascript:void(0);"class="p.last_name" onclick="sortTable('p.last_name',this)"><?php echo TextHelper::_('COBALT_DOCUMENT_OWNER'); ?></a></div></th>
        <th><?php echo TextHelper::_('COBALT_DOCUMENT_SIZE'); ?></th>
        <th><div class="sort_order"><a href="javascript:void(0);"class="d.created" onclick="sortTable('d.created',this)"><?php echo TextHelper::_('COBALT_DOCUMENT_UPLOADED'); ?></a></div></th>
    </thead>
   <tbody class="results" id="documents">

<?php

    if ( count($this->documents) > 0 ) {
        foreach ($this->documents as $key => $document) {
            $k = $key%2;

            //assign association link
            switch ($document['association_type']) {
                case "deal":
                    $view = 'deals';
                    $association_type = "deal";
                    $document['association_name'] = $document['deal_name'];
                    break;
                case "person":
                    $view = "people";
                    $association_type = "person";
                    $document['association_name'] = $document['first_name']." ".$document['last_name'];
                    break;
                case "company";
                    $view = "companies";
                    $association_type = "company";
                    $document['association_name'] = $document['company_name'];
                    break;
            }
            if (array_key_exists('association_name',$document) AND $document['association_name'] != null) {
                $association_link = '<a href="'.JRoute::_('index.php?view='.$view.'&layout='.$association_type.'&id='.$document['association_id']).'" >'.$document['association_name'];
            } else {
                $association_link = "";
            }

            echo '<tr class="document_'.$key.'" id="document_row_'.$document['id'].'" class="cobalt_row_'.$k.'">';
                echo '<td><img width="30px" height="30px" src="'.JURI::base().'libraries/crm/media/images/'.$document['filetype'].'.png'.'" /><br /><b>'.strtoupper($document['filetype']).'<b></td>';
                echo '<td><div class="dropdown"><span class="caret"></span> <a href="javascript:void(0);" class="document_edit dropdown-toggle" role="button" data-toggle="dropdown" id="'.$document['id'].'">'.$document['name'].'</a>';
                echo '<ul class="dropdown-menu" role="menu">';
                echo '<input type="hidden" name="document_'.$document['id'].'_hash" id="document_'.$document['id'].'_hash" value="'.$document['filename'].'" />';
                    if ($document['is_image']) {
                        echo '<li><a href="javascript:void(0);" class="document_preview" id="preview_'.$document['id'].'"><i class="icon-eye-open"></i> '.TextHelper::_('COBALT_PREVIEW').'</a></li>';
                    }
                echo '<li><a href="javascript:void(0);" class="document_download" id="download_'.$document['id'].'"> <i class="icon-download"></i> '.TextHelper::_('COBALT_DOWNLOAD').'</a></li>';
                echo '<li><a href="javascript:void(0);" class="document_delete" id="delete_'.$document['id'].'"><i class="icon-trash"></i> '.TextHelper::_('COBALT_DELETE').'</a></li>';
                echo '</ul>';
                echo '</div></td>';
                echo '<td>'.$association_link.'</a></td>';
                echo '<td>'.$document['owner_name'].'</td>';
                echo '<td>'.$document['size'].'Kb</td>';
                echo '<td>'.DateHelper::formatDate($document['created']).'</td>';
            echo '</tr>';
        }
    }
?>
<script type="text/javascript">
    jQuery("#documents_matched").html('<?php echo count($this->documents); ?>');
    var documents = <?php echo json_encode($this->documents); ?>;
    var document_names = <?php
                                //assign document names for autocomplete dialogs
                                $names = array();

                                if ( count($this->documents)> 0 ) {
                                    foreach ($this->documents as $key => $document) {
                                        $names[] = $document['filename'];
                                    }
                                }

                                 echo json_encode($names);
                            ?>;
    order_url = "<?php echo 'index.php?view=documents&layout=list&format=raw&tmpl=component'; ?>";
    order_dir = "<?php echo $this->state->get('Document.filter_order_Dir'); ?>";
    order_col = "<?php echo $this->state->get('Document.filter_order'); ?>";
</script>
</tbody>
