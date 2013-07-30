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
defined( '_JEXEC' ) or die( 'Restricted access' );

    foreach ($this->leaderboard[0]['members'] as $member) {
        echo '<div class="leader clearfix well">';
        echo '<div class="leader_name">'.$member['first_name']." ".$member['last_name'].'</div>';
        echo '<div class="progress progress-success">';
        //determine bar progress
        //win_cash
        if ($this->leaderboard[0]['goal_type'] == 'win_cash') {
            $width = $member['cash_won'] / $this->leaderboard[0]['amount'] * 100;
        }
        //win_deals
        if ($this->leaderboard[0]['goal_type'] == 'win_deals') {
            $width = $member['deals_won'] / $this->leaderboard[0]['amount'] * 100;
        }
        //move_deals
        if ($this->leaderboard[0]['goal_type'] == 'move_deals') {
            $width = $member['deals_moved'] / $this->leaderboard[0]['amount'] * 100;
        }
        //complete_tasks
        if ($this->leaderboard[0]['goal_type'] == 'complete_tasks') {
            $width = $member['tasks_completed'] / $this->leaderboard[0]['amount'] * 100;
        }
        //write_notes
        if ($this->leaderboard[0]['goal_type'] == 'write_notes') {
            $width = $member['notes_written'] / $this->leaderboard[0]['amount'] * 100;
        }
        //create_deals
        if ($this->leaderboard[0]['goal_type'] == 'create_deals') {
            $width = $member['deals_created'] / $this->leaderboard[0]['amount'] * 100;
        }
        echo '<div class="bar" style="background:#'.CobaltHelperCobalt::percent2color($width).';width:'.$width.'%;"></div>';
        echo '</div>';

        //output info
        //win_cash
        echo '<span class="pull-right">';
        if ($this->leaderboard[0]['goal_type'] == 'win_cash') {
            echo '$'.(int) $member['cash_won'].' cash won.';
        }
        //win_deals
        if ($this->leaderboard[0]['goal_type'] == 'win_deals') {
            echo (int) $member['deals_won'].' deals won.';
        }
        //move_deals
        if ($this->leaderboard[0]['goal_type'] == 'move_deals') {
            echo (int) $member['deals_moved'].' deals moved.';
        }
        //complete_tasks
        if ($this->leaderboard[0]['goal_type'] == 'complete_tasks') {
            echo (int) $member['tasks_completed'].' tasks completed.';
        }
        //write_notes
        if ($this->leaderboard[0]['goal_type'] == 'write_notes') {
            echo (int) $member['notes_written'].' notes written.';
        }
        //create_deals
        if ($this->leaderboard[0]['goal_type'] == 'create_deals') {
            echo (int) $member['deals_created'].' deals created.';
        }
        echo '</span>';
        echo '</div>';
    }
