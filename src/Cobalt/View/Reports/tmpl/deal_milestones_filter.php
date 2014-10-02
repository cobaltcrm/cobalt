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

for ($i=0;$i<count($this->deals);$i++) {
    $deal = $this->deals[$i]; ?>
    <div class="widget">
        <h2><a href="<?php echo RouteHelper::_('index.php?view=deals&layout=deal&id='.$deal->id); ?>"><?php echo $deal->name; ?></a></h2>
        <div class="details">
            <div class="row-fluid">
                <span class="well well-small col-md-4"><?php echo TextHelper::_('COBALT_AMOUNT').": ".ConfigHelper::getCurrency().$deal->amount; ?></span>
                <span class="well well-small col-md-4"><?php echo TextHelper::_('COBALT_DEAL_STAGE').": ".$deal->stage_name; ?></span>
                <span class="well well-small col-md-4"><?php echo TextHelper::_('COBALT_OWNER').": ".$deal->owner_first_name." ".$deal->owner_last_name; ?></span>
            </div>
        </div>
        <div class="events">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <th class="logo" ><?php echo TextHelper::_('COBALT_MILESTONE_NAME'); ?></th>
                <th class="logo" ><?php echo TextHelper::_('COBALT_EXPECTED'); ?></th>
                <th class="logo" ><?php echo TextHelper::_('COBALT_ACTUAL'); ?></th>
                <th class="logo" ><?php echo TextHelper::_('COBALT_STATUS'); ?></th>
            </thead>
            <tbody id="reports">
                <tr>
                    <td><?php echo $deal->name; ?></td>
                    <td><?php echo DateHelper::formatDate($deal->expected_close); ?></td>
                    <td><?php echo DateHelper::formatDate($deal->actual_close); ?></td>
                    <td><div class="deal-status-<?php echo strtolower($deal->status_name); ?>"></div></td>
                </tr>
                <?php
                    for ($i2=0;$i2<count($deal->events);$i2++) {
                        $event = $deal->events[$i2];?>
                        <tr>
                            <td><?php echo $event['name']; ?></td>
                            <td><?php echo DateHelper::formatDate($event['due_date']); ?></td>
                            <td><?php echo DateHelper::formatDate($event['actual_close']); ?></td>
                            <td></td>
                        </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
    </div>
<?php }
