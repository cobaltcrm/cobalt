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
?>
<thead>
    <tr>
        <th class="checkbox_column"><input rel="tooltip" title="<?php echo TextHelper::_('COBALT_CHECK_ALL_ITEMS'); ?>" data-placement="bottom" type="checkbox" onclick="Cobalt.selectAll(this);" /></th>
        <th class="name" ><div class="sort_order"><a href="javascript:void(0);" class="d.name"><?php echo ucwords(TextHelper::_('COBALT_DEALS_NAME')); ?></a></div></th>
        <th class="company"><div class="sort_order"><a href="javascript:void(0);" class="c.name"><?php echo ucwords(TextHelper::_('COBALT_DEALS_COMPANY')); ?></a></div></th>
        <th class="amount" ><div class="sort_order"><a href="javascript:void(0);" class="d.amount"><?php echo ucwords(TextHelper::_('COBALT_DEALS_AMOUNT')); ?></a></div></th>
        <th class="status" ><div class="sort_order"><a href="javascript:void(0);" class="d.status_id"><?php echo ucwords(TextHelper::_('COBALT_DEALS_STATUS')); ?></a></div></th>
        <th class="stage" ><div class="sort_order"><a href="javascript:void(0);" class="d.stage_id"><?php echo ucwords(TextHelper::_('COBALT_DEALS_STAGE')); ?></a></div></th>
        <th class="source" ><div class="sort_order"><a href="javascript:void(0);" class="d.source_id"><?php echo ucwords(TextHelper::_('COBALT_DEAL_SOURCE')); ?></a></div></th>
        <th class="expected_close" ><div class="sort_order"><a href="javascript:void(0);" class="d.expected_close"><?php echo ucwords(TextHelper::_('COBALT_DEALS_EXPECTED_CLOSE')); ?></a></div></th>
        <th class="actual_close" ><div class="sort_order"><a href="javascript:void(0);" class="d.actual_close"><?php echo ucwords(TextHelper::_('COBALT_DEALS_ACTUAL_CLOSE')); ?></a></div></th>
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
