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
defined( '_CEXEC' ) or die( 'Restricted access' ); ?>
<table class="com_cobalt_table">
    <thead>
        <tr>
            <th></th>
            <th><?php echo TextHelper::_('COBALT_NAME'); ?></th>
            <th><?php echo TextHelper::_('COBALT_DESCRIPTION'); ?></th>
            <th><?php echo TextHelper::_('COBALT_ACTIONS'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            if ( count($this->lists) > 0 ) {
                foreach ($this->lists as $list) {
                    $jsaction = $list->isSubscribed > 0 ? 1 : 0;
                    $action = $jsaction ? TextHelper::_('COBALT_REMOVE') : TextHelper::_('COBALT_ADD');
                    echo '<tr>';
                        echo "<td><div class='status-dot' style='background-color:".$list->color.";'></div></td>";
                        echo "<td>".$list->name."</td>";
                        echo "<td>".$list->description."</td>";
                        echo "<td id='mailing_list_".$list->listid."'><a href='javascript:void(0);' onclick=\"toggleMailingList('".$list->listid."','".$jsaction."')\">".$action."</a></td>";
                    echo '</tr>';
                }
            }
        ?>
    </tbody>
</table>
