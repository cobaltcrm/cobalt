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

  for ( $i=0; $i<count($this->note_entries); $i++ ) {
      $note = $this->note_entries[$i];
      $k = $i%2; ?>
     <tr class="cobalt_row_<?php echo $k; ?>">
        <?php if ( $app->input->get('view') != "print" ) { ?>
         <td><input type="checkbox" name="ids[]" value="<?php echo $note['id']; ?>" /></td>
         <?php } ?>
         <td>
             <?php
                if ($note['company_id'] != null AND $note['company_id'] != 0) {
                    echo '<a href="'.RouteHelper::_('index.php?view=companies&layout=company&id='.$note['company_id']).'">'.$note['company_name'].'</a>';
                }
             ?>
         </td>
         <td>
             <?php
                if ($note['deal_id'] != null AND $note['deal_id'] != 0) {
                    echo '<a href="'.RouteHelper::_('index.php?view=deals&layout=deal&id='.$note['deal_id']).'">'.$note['deal_name'].'</a>';
                }
             ?>
         </td>
         <td>
             <?php
                if ($note['person_id'] != null AND $note['person_id'] != 0) {
                    echo '<a href="'.RouteHelper::_('index.php?view=people&layout=person&id='.$note['person_id']).'">'.$note['person_first_name'].' '.$note['person_last_name'].'</a>';
                }
             ?>
         </td>
         <td><?php echo $note['owner_first_name']." ".$note['owner_last_name']; ?></td>
         <td><?php echo DateHelper::formatDate(($note['created'])); ?></td>
         <td><?php echo $note['category_name']; ?></td>
         <td><?php echo $note['note']; ?></td>
     </tr>
<?php }  ?>
