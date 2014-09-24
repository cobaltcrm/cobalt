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

<div class="page-header">
    <h1><?php echo ucwords(TextHelper::_('COBALT_GOALS')); ?></h1>
</div>

<div class="row-fluid">
    <div class="col-md-6">
    <ul class="goal_float_list list-unstyled" id="goal_floats_left">
        <li class="widget clearfix">
            <h3><?php echo ucwords(TextHelper::_('COBALT_LEADERBOARD')); ?></h3>
            <div class="goal_filter_container alert">
            <?php if ( count($this->leaderboard_list) > 0 ) { ?>
                <?php echo TextHelper::_("COBALT_SHOWING_LEADERBOARD"); ?> -
                    <span class="dropdown">
                        <a class="dropdown-toggle update-toggle-text" data-toggle="dropdown" href="#" role="button" id="leaderboard_filter_link" ><span class="dropdown-label"><?php $keys = array_keys($this->leaderboard_list); echo $this->leaderboard_list[$keys[0]]; ?></span><span class="caret"></span></a>
                        <ul id="leaderboard_filter" class="list-unstyled dropdown-menu" role="menu">
                            <?php foreach ($this->leaderboard_list as $key=>$name) {
                                 echo "<li><a href='javascript:void(0);' class='filter_".$key." dropdown_item' onclick=\"changeLeaderBoard('".$key."')\">".$name."</a></li>";
                            }?>
                        </ul>
                    </span>
                </div>
                <?php } ?>
                <div id="leaderboards" class="clearfix goal_list">
                <?php
                    if ( count($this->goals->leaderboards) > 0 ) {
                    //show first leaderboard
                        $first_leaderboard = $this->goals->leaderboards[0];
                        foreach ($first_leaderboard['members'] as $member) {
                            echo '<div class="leader clearfix well">';
                            echo '<div class="leader_name">'.$member['first_name'].' '.$member['last_name'].'</div>';
                            echo '<div class="progress progress-success">';
                            //determine bar progress
                            //win_cash
                            if ($first_leaderboard['goal_type'] == 'win_cash') {
                                $width = $member['cash_won'] / $first_leaderboard['amount'] * 100;
                            }
                            //win_deals
                            if ($first_leaderboard['goal_type'] == 'win_deals') {
                                $width = $member['deals_won'] / $first_leaderboard['amount'] * 100;
                            }
                            //move_deals
                            if ($first_leaderboard['goal_type'] == 'move_deals') {
                                $width = $member['deals_moved'] / $first_leaderboard['amount'] * 100;
                            }
                            //complete_tasks
                            if ($first_leaderboard['goal_type'] == 'complete_tasks') {
                                $width = $member['tasks_completed'] / $first_leaderboard['amount'] * 100;
                            }
                            //write_notes
                            if ($first_leaderboard['goal_type'] == 'write_notes') {
                                $width = $member['notes_written'] / $first_leaderboard['amount'] * 100;
                            }
                            //create_deals
                            if ($first_leaderboard['goal_type'] == 'create_deals') {
                                $width = $member['deals_created'] / $first_leaderboard['amount'] * 100;
                            }
                            echo '<div class="bar" style="background:#'.CobaltHelper::percent2color($width).';width:'.$width.'%;"></div>';
                            echo '</div>';
                            //output info
                            //win_cash
                            echo '<span class="pull-right">';
                            if ($first_leaderboard['goal_type'] == 'win_cash') {
                                echo ConfigHelper::getConfigValue('currency').(int) $member['cash_won'].' '.TextHelper::_('COBALT_CASH_WON');
                            }
                            //win_deals
                            if ($first_leaderboard['goal_type'] == 'win_deals') {
                                echo (int) $member['deals_won'].' '.TextHelper::_('COBALT_DEALS_WON');
                            }
                            //move_deals
                            if ($first_leaderboard['goal_type'] == 'move_deals') {
                                echo (int) $member['deals_moved'].' '.TextHelper::_('COBALT_DEALS_MOVED');
                            }
                            //complete_tasks
                            if ($first_leaderboard['goal_type'] == 'complete_tasks') {
                                echo (int) $member['tasks_completed'].' '.TextHelper::_('COBALT_TASKS_COMPLETED');
                            }
                            //write_notes
                            if ($first_leaderboard['goal_type'] == 'write_notes') {
                                echo (int) $member['notes_written'].' '.TextHelper::_('COBALT_NOTES_WRITTEN_MESSAGE');
                            }
                            //create_deals
                            if ($first_leaderboard['goal_type'] == 'create_deals') {
                                echo (int) $member['deals_created'].' '.TextHelper::_('COBALT_DEALS_CREATED_MESSAGE');
                            }
                            echo '</span>';
                            echo '</div>';

                        }
                    }
                ?>
                </div>
        </li>
    </ul>
</div>

<div class="col-md-6">
    <ul class="goal_float_list list-unstyled" id="goal_floats_right">
        <li class="widget clearfix">
                <h3><?php echo TextHelper::_('COBALT_INDIVIDUAL_GOALS'); ?></h3>
                <div class="goal_filter_container alert">
                    <?php if ( ( count($this->users) > 0 ) && $this->member_role != 'basic' ) { ?>

                        <?php echo TextHelper::_('COBALT_GOALS_FOR'); ?> -
                        <span class="dropdown">
                            <a class="dropdown-toggle update-toggle-text" data-toggle="dropdown" href="#" role="button" id="user_goal_select_link" ><span class="dropdown-label"><?php echo TextHelper::_('COBALT_ME'); ?></span><span class="caret"></span></a>
                            <ul class="list-unstyled dropdown-menu" role="menu">
                                <li><a href="javascript:void(0);" class='filter_<?php echo $this->user_id; ?> dropdown_item'  onclick="changeIndividual(<?php echo $this->user_id; ?>);"><?php echo TextHelper::_('COBALT_ME'); ?></a></li>
                                <?php foreach ($this->users as $user) { ?>
                                     <li><a href="javascript:void(0);" class='filter_<?php echo $user['id']; ?> dropdown_item' onclick="changeIndividual(<?php echo $user['id']; ?>);"><?php echo $user['first_name']." ".$user['last_name']; ?></a></li>
                                <?php }?>
                            </ul>
                        </span>

                    <?php } else { ?>

                    <?php echo TextHelper::_("COBALT_SHOWING_GOALS_FOR"); ?> <b><?php echo TextHelper::_('COBALT_ME'); ?></b>

                    <?php } ?>

                </div>
                <div id="individual_goals" class="goal_list">
                    <?php
                        if ( count($this->goals->individual_goals) > 0 ) {
                            foreach ($this->goals->individual_goals as $goal) { ?>
                               <div id="goal_<?php echo $goal['id']; ?>" class="goal_info clearfix well">
                                    <div class="clearfix">
                                        <span class="goal_info_name"><?php echo $goal['name']; ?></span>
                                        <span class="goal_info_due_date pull-right"><?php echo TextHelper::_('COBALT_BY'); ?> <?php echo DateHelper::formatDate($goal['end_date']); ?></span>
                                    </div>
                                    <div class="goal_info_progress clearfix progress progress-success">
                                        <?php $bgcolor=CobaltHelper::percent2Color($goal['goal_info']/$goal['amount']*100); ?>
                                        <div class="goal_info_progress_total bar" style="background-color:#<?php echo $bgcolor; ?>;width:<?php echo number_format($goal['goal_info']/$goal['amount']*100); ?>%;"></div>
                                    </div>
                                    <div class="clearfix">
                                        <span class="goal_info_out_of">
                                            <?php
                                                if ($goal['goal_type'] == 'win_cash') {
                                                    echo ConfigHelper::getCurrency().(int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo ConfigHelper::getConfigValue('currency'); ?><?php echo $goal['amount']." ".TextHelper::_('COBALT_WON');
                                                }
                                                if ($goal['goal_type'] == 'win_deals') {
                                                    echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_DEALS_WON');
                                                }
                                                if ($goal['goal_type'] == 'move_deals') {
                                                    echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_DEALS_MOVED');
                                                }
                                                if ($goal['goal_type'] == 'complete_tasks') {
                                                    echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_TASKS_COMPLETED');
                                                }
                                                if ($goal['goal_type'] == 'write_notes') {
                                                    echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_NOTES_WRITTEN_MESSAGE');
                                                }
                                                if ($goal['goal_type'] == 'create_deals') {
                                                    echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_DEALS_CREATED_MESSAGE');
                                                }
                                            ?>
                                        </span>
                                        <span class="goal_info_progress_percentage pull-right"><?php echo number_format(($goal['goal_info']/$goal['amount']*100)); ?>% <?php echo TextHelper::_("COBALT_COMPLETED"); ?></span>
                                    </div>
                                </div>
                    <?php } }
                    ?>
            </div>
            <div class="pull-right">
                    <a class="btn btn-success" href="<?php echo RouteHelper::_('index.php?view=goals&layout=add'); ?>"><i class="glyphicon glyphicon-plus icon-white"></i><?php echo TextHelper::_("COBALT_ADD_GOAL"); ?></a>
                    <a href="javascript:void(0);" class="btn delete_goals" id="goal_type_member"><i class="glyphicon glyphicon-trash"></i><?php echo TextHelper::_("COBALT_DELETE_GOALS"); ?></a>
            </div>
        </li>
        <li class="widget clearfix">
            <h3><?php echo TextHelper::_("COBALT_TEAM_GOALS"); ?></h3>
            <div class="goal_filter_container alert">

                <?php if ( ( count($this->teams) > 0 ) && $this->member_role == 'exec' ) { ?>

                   <?php echo TextHelper::_('COBALT_GOALS_FOR'); ?> -
                   <span class="dropdown">
                       <a href="javascript:void(0);" class="dropdown-toggle update-toggle-text" data-toggle="dropdown" role="button" id="team_goal_select_link" ><span class="dropdown-label"><?php echo TextHelper::_("COBALT_MY_TEAM"); ?></span><span class="caret"></span></a>
                        <ul class="list-unstyled dropdown-menu" role="menu" id="team_goal_select">
                            <li><a href="javascript:void(0);" class='filter_<?php echo $this->team_id; ?> dropdown_item'  onclick="changeTeam(<?php echo $this->team_id; ?>)"><?php echo TextHelper::_("COBALT_MY_TEAM"); ?></a></li>
                            <?php foreach ($this->teams as $team) { ?>
                                <li><a href="javascript:void(0);" class='filter_<?php echo $team['team_id']; ?> dropdown_item'  onclick="changeTeam(<?php echo $team['team_id']; ?>)"><?php echo $team['team_name'].TextHelper::_('COBALT_TEAM_APPEND'); ?></a></li>
                            <?php } ?>
                        </ul>
                    </span>

                <?php } else { ?>

                <?php echo TextHelper::_("COBALT_SHOWING_GOALS_FOR"); ?> <b><?php echo TextHelper::_("COBALT_MY_TEAM"); ?></b>

                <?php } ?>

            </div>
            <div id="team_goals" class="goal_list">
                <?php
                    if ( count($this->goals->team_goals) > 0 ) {
                        foreach ($this->goals->team_goals as $goal) { ?>
                            <div id="goal_<?php echo $goal['id']; ?>" class="goal_info well">
                                <div class="clearfix">
                                    <span class="goal_info_name"><?php echo $goal['name']; ?></span>
                                    <span class="goal_info_due_date pull-right"><?php echo TextHelper::_('COBALT_BY'); ?> <?php echo DateHelper::formatDate($goal['end_date']); ?></span>
                                </div>
                                <div class="goal_info_progress clearfix progress progress-success">
                                    <?php $bgcolor=CobaltHelper::percent2Color($goal['goal_info']/$goal['amount']*100); ?>
                                    <div class="goal_info_progress_total bar" style="background-color:#<?php echo $bgcolor; ?>;width:<?php echo number_format($goal['goal_info']/$goal['amount']*100); ?>%;"></div>
                                </div>
                                <div class="clearfix">
                                    <span class="goal_info_out_of">
                                        <?php
                                            if ($goal['goal_type'] == 'win_cash') {
                                                echo ConfigHelper::getCurrency().(int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo ConfigHelper::getConfigValue('currency'); ?><?php echo $goal['amount']." ".TextHelper::_('COBALT_WON');
                                            }
                                            if ($goal['goal_type'] == 'win_deals') {
                                                echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_DEALS_WON');
                                            }
                                            if ($goal['goal_type'] == 'move_deals') {
                                                echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_DEALS_MOVED');
                                            }
                                            if ($goal['goal_type'] == 'complete_tasks') {
                                                echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_TASKS_COMPLETED');
                                            }
                                            if ($goal['goal_type'] == 'write_notes') {
                                                echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_NOTES_WRITTEN_MESSAGE');
                                            }
                                            if ($goal['goal_type'] == 'create_deals') {
                                                echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_DEALS_CREATED_MESSAGE');
                                            }
                                        ?>
                                    </span>
                                    <span class="goal_info_progress_percentage pull-right"><?php echo number_format(($goal['goal_info']/$goal['amount']*100)); ?>% <?php echo TextHelper::_("COBALT_COMPLETED"); ?></span>
                                </div>
                            </div>
                <?php } }
                ?>
            </div>
            <?php if ($this->member_role != 'basic') { ?>
            <div class="pull-right">
                    <a class="btn btn-success" href="<?php echo RouteHelper::_('index.php?view=goals&layout=add'); ?>"><i class="glyphicon glyphicon-plus icon-white"></i><?php echo TextHelper::_("COBALT_ADD_GOAL"); ?></a>
                    <a href="javascript:void(0);" class="btn delete_goals" id="goal_type_team"><i class="glyphicon glyphicon-trash"></i><?php echo TextHelper::_("COBALT_DELETE_GOALS"); ?></a>
            </div>
            <?php } ?>
        </li>
        <li class="widget clearfix">
            <h3><?php echo ucwords(TextHelper::_("COBALT_COMPANY_GOALS")); ?></h3>
                <div id="company_goals" class="goal_list">
                    <?php
                        if ( count($this->goals->company_goals) > 0 ) {
                            foreach ($this->goals->company_goals as $goal) { ?>
                              <div id="goal_<?php echo $goal['id']; ?>" class="goal_info well">
                                    <div class="clearfix">
                                        <span class="goal_info_name"><?php echo $goal['name']; ?></span>
                                        <span class="goal_info_due_date pull-right"><?php echo TextHelper::_('COBALT_BY'); ?> <?php echo DateHelper::formatDate($goal['end_date']); ?></span>
                                    </div>
                                    <div class="goal_info_progress progress progress-success clearfix">
                                        <?php $bgcolor=CobaltHelper::percent2Color($goal['goal_info']/$goal['amount']*100); ?>
                                        <div class="goal_info_progress_total bar" style="background-color:#<?php echo $bgcolor; ?>;width:<?php echo number_format($goal['goal_info']/$goal['amount']*100); ?>%;"></div>
                                    </div>
                                    <div class="clearfix">
                                        <span class="goal_info_out_of">
                                            <?php
                                                if ($goal['goal_type'] == 'win_cash') {
                                                    echo ConfigHelper::getCurrency().(int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo ConfigHelper::getConfigValue('currency'); ?><?php echo $goal['amount']." ".TextHelper::_('COBALT_WON');
                                                }
                                                if ($goal['goal_type'] == 'win_deals') {
                                                    echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_DEALS_WON');
                                                }
                                                if ($goal['goal_type'] == 'move_deals') {
                                                    echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_DEALS_MOVED');
                                                }
                                                if ($goal['goal_type'] == 'complete_tasks') {
                                                    echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_TASKS_COMPLETED');
                                                }
                                                if ($goal['goal_type'] == 'write_notes') {
                                                    echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_NOTES_WRITTEN_MESSAGE');
                                                }
                                                if ($goal['goal_type'] == 'create_deals') {
                                                    echo (int) $goal['goal_info'] ?> <?php echo TextHelper::_("COBALT_OUT_OF"); ?> <?php echo $goal['amount'] . " ".TextHelper::_('COBALT_DEALS_CREATED_MESSAGE');
                                                }
                                            ?>
                                        </span>
                                        <span class="goal_info_progress_percentage pull-right"><?php echo number_format($goal['goal_info']/$goal['amount']*100); ?>% <?php echo TextHelper::_("COBALT_COMPLETED"); ?></span>
                                    </div>
                                </div>
                    <?php } }
                    ?>
                </div>
                <?php if ($this->member_role == 'exec') { ?>
                <div class="pull-right">
                    <a class="btn btn-success" href="<?php echo RouteHelper::_('index.php?view=goals&layout=add'); ?>"><i class="glyphicon glyphicon-plus icon-white"></i><?php echo TextHelper::_("COBALT_ADD_GOAL"); ?></a>
                    <a href="javascript:void(0);" class="btn delete_goals" id="goal_type_company"><i class="glyphicon glyphicon-trash"></i><?php echo TextHelper::_("COBALT_DELETE_GOALS"); ?></a>
                </div>
                <?php } ?>
        </li>
    </ul>
</div>
</div>
<div id="delete_goals"></div>
