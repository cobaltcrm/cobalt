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

    for ( $i=0; $i<count($this->sources); $i++ ) {
        $source = $this->sources[$i];
        // if ($source['number_of_deals'] != 0 AND $source['number_of_deals'] != null AND $source['cost'] !=0 AND $source['cost'] != null) {
        $k = $i%2; ?>
        <tr class="cobalt_row_<?php echo $k; ?>">
            <td><input type="checkbox" name="ids[<?php echo $source['id']; ?>]" value="<?php echo $source['id']; ?>" /></td>
            <td><?php echo $source['name']; ?></td>
            <td><?php echo $source['number_of_deals']; ?></td>
            <td><?php echo $source['revenue']; ?></td>
            <td>
                <?php
                    if ($source['type'] != 'per') {
                        echo CobaltHelperConfig::getCurrency().$source['cost'];
                    } else {
                        $cost = $source['cost'] * $source['number_of_deals'];
                        echo CobaltHelperConfig::getCurrency().$cost;
                    }
                ?>
            </td>
            <td>
                <?php
                    echo (int) $source['roi']."%";
                ?>
            </td>
        </tr>
    <?php }//}
