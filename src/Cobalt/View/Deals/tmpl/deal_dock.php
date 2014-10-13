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
defined( '_CEXEC' ) or die( 'Restricted access' );?>

<table class="table table-striped table-hover table-bordered" id="deal_list">
    <tr>
        <th><?php echo ucwords(TextHelper::_('COBALT_DEAL_NAME')); ?></th>
        <th><?php echo ucwords(TextHelper::_('COBALT_DEAL_OWNER')); ?></th>
        <th><?php echo ucwords(TextHelper::_('COBALT_DEAL_STATUS')); ?></th>
        <th class="right"><?php echo ucwords(TextHelper::_('COBALT_DEAL_AMOUNT')); ?></th>
    </tr>
    <tbody id="deal_dock_list">
        <?php
            $deal_dock_list = \Cobalt\Factory::getView('deals','deal_dock_list', 'phtml', array('deals'=>$this->deals));
            echo $deal_dock_list->render();
        ?>
    </tbody>
</table>
