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
$app = \Cobalt\Container::fetch('app');
?>
<thead>
    <tr>
        <th class="checkbox_column"><input rel="tooltip" title="<?php echo TextHelper::_('COBALT_CHECK_ALL_ITEMS'); ?>" data-placement="bottom" type="checkbox" onclick="Cobalt.selectAll(this);" /></th>
        <th class="name" ><?php echo ucwords(TextHelper::_('COBALT_DEALS_NAME')); ?></th>
        <th class="company"><?php echo ucwords(TextHelper::_('COBALT_DEALS_COMPANY')); ?></th>
        <th class="amount" ><?php echo ucwords(TextHelper::_('COBALT_DEALS_AMOUNT')); ?></th>
        <th class="status" ><?php echo ucwords(TextHelper::_('COBALT_DEALS_STATUS')); ?></th>
        <th class="stage" ><?php echo ucwords(TextHelper::_('COBALT_DEALS_STAGE')); ?></th>
        <th class="source" ><?php echo ucwords(TextHelper::_('COBALT_DEAL_SOURCE')); ?></th>
        <th class="expected_close" ><?php echo ucwords(TextHelper::_('COBALT_DEALS_EXPECTED_CLOSE')); ?></th>
        <th class="actual_close" ><?php echo ucwords(TextHelper::_('COBALT_DEALS_ACTUAL_CLOSE')); ?></th>
        <th class="contacts" >&nbsp;</th>
    </tr>
</thead>
<tbody id="list">
<?php
    $stages = DealHelper::getStages(null,TRUE,FALSE);
    $statuses = DealHelper::getStatuses(null,true);
    $sources = DealHelper::getSources(null);
    $users = UsersHelper::getUsers(null,TRUE);
    $n = count($this->dealList);
    $k = 0;
        for ($i=0;$i<$n;$i++) {
            $deal = $this->dealList[$i];
            $k = $i%2;
            $entryView = ViewHelper::getView('deals','entry','phtml');
            $entryView->deal = $deal;
            $entryView->stages = $stages;
            $entryView->statuses = $statuses;
            $entryView->sources = $sources;
            $entryView->users = $users;
            $entryView->k = $k;
            echo $entryView->render();
        }
?>
</tbody>
