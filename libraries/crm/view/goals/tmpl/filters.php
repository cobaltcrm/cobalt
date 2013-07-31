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

if ( count($this->goals > 0 ) ) {
        foreach ($this->goals as $goal) { ?>
           <div id="goal_<?php echo $goal['id']; ?>" class="goal_info well clearfix">
                <div class="clearfix">
                    <span class="goal_info_name"><?php echo $goal['name']; ?></span>
                    <span class="goal_info_due_date pull-right">by <?php echo CobaltHelperDate::formatDate($goal['end_date']); ?></span>
                </div>
                <div class="goal_info_progress progress progress-success clearfix">
                    <?php $bgcolor=CobaltHelperCobalt::percent2Color($goal['goal_info']/$goal['amount']*100); ?>
                    <div class="goal_info_progress_total bar" style="background-color:#<?php echo $bgcolor; ?>;width:<?php echo number_format($goal['goal_info']/$goal['amount']*100); ?>%;"></div>
                </div>
                <div class="clearfix">
                    <span class="goal_info_out_of">
                        <?php
                            if ($goal['goal_type'] == 'win_cash') {
                                echo "$".(int) $goal['goal_info'] ?> out of $<?php echo $goal['amount']." won.";
                            }
                            if ($goal['goal_type'] == 'win_deals') {
                                echo (int) $goal['goal_info'] ?> out of <?php echo $goal['amount'] . " deals won.";
                            }
                            if ($goal['goal_type'] == 'move_deals') {
                                echo (int) $goal['goal_info'] ?> out of <?php echo $goal['amount'] . " deals moved.";
                            }
                            if ($goal['goal_type'] == 'complete_tasks') {
                                echo (int) $goal['goal_info'] ?> out of <?php echo $goal['amount'] . " tasks completed.";
                            }
                            if ($goal['goal_type'] == 'write_notes') {
                                echo (int) $goal['goal_info'] ?> out of <?php echo $goal['amount'] . " notes written.";
                            }
                            if ($goal['goal_type'] == 'create_deals') {
                                echo (int) $goal['goal_info'] ?> out of <?php echo $goal['amount'] . " deals created.";
                            }
                        ?>
                    </span>
                    <span class="goal_info_progress_percentage pull-right"><?php echo number_format(($goal['goal_info']/$goal['amount']*100)); ?>% completed</span>
                </div>
            </div>
<?php } }
