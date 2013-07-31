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

for ( $i=0; $i<count($this->reports); $i++ ) {
    $report = $this->reports[$i];
    $k = $i%2; ?>
    <tr id="custom_report_<?php echo $report['id']; ?>" class="cobalt_row_<?php echo $k; ?>">
        <td><a href="<?php echo JRoute::_('index.php?view=reports&layout=custom_report&id='.$report['id']); ?>"><?php echo $report['name']; ?></a></td>
        <td><?php echo CobaltHelperDate::formatDate($report['modified']); ?></td>
        <td><?php echo CobaltHelperDate::formatDate($report['created']); ?></td>
        <td>
            <a href="<?php echo JRoute::_('index.php?view=reports&layout=edit_custom_report&id='.$report['id']); ?>"><?php echo TextHelper::_('COBALT_EDIT_BUTTON'); ?></a>
            |
            <a href="javascript:void(0);" class="delete delete_custom_report"><i class="icon-trash"></i></a>
        </td>
    </tr>
<?php }
