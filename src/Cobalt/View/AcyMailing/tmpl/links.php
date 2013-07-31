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
            <th><?php echo TextHelper::_('COBALT_NAME'); ?></th>
            <th><?php echo TextHelper::_('COBALT_URL'); ?></th>
            <th><?php echo TextHelper::_('COBALT_CLICKED'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            if ( count($this->links) > 0 ) {
                foreach ($this->links as $link) {
                    $text = $link->click ? TextHelper::_('COBALT_YES') : TextHelper::_('COBALT_NO');
                    echo '<tr>';
                        echo "<td>".$link->name."</td>";
                        echo "<td><a target='_blank' href='$link->url'>".$link->url."</a></td>";
                        echo "<td>".$text."</td>";
                    echo '</tr>';
                }
            } else {
                echo '<tr>';
                    echo '<td colspan="3">';
                    echo '<div class="notice">'.TextHelper::_('COBALT_NO_NEWSLETTER_LINKS').'</div>';
                    echo '</td>';
                echo '</tr>';
            }
        ?>
    </tbody>
</table>
