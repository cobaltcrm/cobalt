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
if ( count($this->transcripts) > 0 ) {
    $i = 0;
    foreach ($this->transcripts as $transcript) { ?>
      <?php $i %= 2; ?>
        <tr class="cobalt_row_<?php echo $i; ?>">
          <td><?php echo $transcript->room_name; ?></td>
          <td><?php echo CobaltHelperDate::formatDate($transcript->created); ?></td>
          <td>
              <a target="_blank" href="<?php echo JRoute::_('index.php?option=com_banter&view=transcripts&layout=transcript&id='.$transcript->id);?>">
                  <?php echo CRMText::_('COBALT_VIEW'); ?>
              </a>
          </td>
        </tr>
     <?php $i++; ?>
<?php } }
