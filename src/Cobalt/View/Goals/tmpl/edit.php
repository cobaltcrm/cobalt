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
    <h1><?php echo ucwords($this->header); ?></h1>
</div>
<form id="edit-form" action="<?php echo JRoute::_('index.php?task=editGoal'); ?>" method="post" onsubmit="return save(this);">
    <div id="goal_edit">
        <ul class="unstyled">
             <li>
                    <legend><b><?php echo ucwords(TextHelper::_('COBALT_GOAL_STEP_ONE')); ?></b></legend>
                    <?php
                        //generate user dropdown
                        $user_html = '<select class="inputbox" id="assigned_id">';
                            //if an executive is creating a goal
                            if ($this->member_role == 'exec') {
                                $user_html .= "<option value='company_0' selected='selected'>".ucwords(TextHelper::_('COBALT_THE_COMPANY'))."</option>";
                            }
                            //individual assignment
                            $user_html .= "<option value='member_".$this->user_id."'>".TextHelper::_('COBALT_ME')."</option>";
                            //if a manager is creating a goal
                            if ($this->member_role == 'manager') {
                                if ($this->team_id != null) {
                                    $user_html .= "<option value='team_".$this->team_id."'>".TextHelper::_('COBALT_MY_TEAM')."</option>";
                                }
                                if ( count($this->users) > 0 ) {
                                    foreach ($this->users as $user) {
                                        $user_html .= "<option value='member_".$user['id']."'>".$user['first_name']." ".$user['last_name']."</option>";
                                    }
                                }
                            }
                            //an executive can also see all of the teams and individual users
                            if ($this->member_role == 'exec') {
                                if ( count($this->teams) > 0 ) {
                                    foreach ($this->teams as $team) {
                                        $user_html .= "<option value='team_".$team['team_id']."'>".$team['team_name'].TextHelper::_('COBALT_TEAM_APPEND')."</option>";
                                    }
                                }
                                if ( count($this->users) > 0 ) {
                                    foreach ($this->users as $user) {
                                        $user_html .= "<option value='member_".$user['id']."'>".$user['first_name']." ".$user['last_name']."</option>";
                                    }
                                }
                            }
                            //if a basic member is creating a goal we show no more options
                        $user_html .= "</select>";

                        //generate date html
                        $date_html = "<select class='inputbox' id='date_picker'>";
                        $dates = DateHelper::getGoalDates();
                        $date_html .= JHtml::_('select.options', $dates, 'value', 'text', '', true);
                        $date_html .= "</select>";
                    ?>
                    <?php switch ($this->type) {
                        case "win_cash": ?>
                                <?php echo TextHelper::_('COBALT_I_WANT'); ?>
                                    <?php echo $user_html; ?>
                                    <?php echo TextHelper::_('COBALT_TO_SELL'); ?>
                                    <input class="required inputbox" type="text" value="" placeholder="0" name="amount" />
                                    <?php echo TextHelper::_('COBALT_OF_NEW_BUSINESS'); ?>
                                   <?php echo $date_html; ?>
                                <?php
                            break;
                        case "win_deals": ?>
                                <?php echo TextHelper::_('COBALT_I_WANT'); ?>
                                    <?php echo $user_html; ?>
                                    <?php echo TextHelper::_('COBALT_TO_WIN'); ?>
                                    <input class="inputbox required" type="text" value="" placeholder="0" name="amount" />
                                    <?php echo TextHelper::_('COBALT_NEW_DEALS'); ?>
                                    <?php echo $date_html; ?>
                                <?php
                            break;
                        case "move_deals": ?>
                                <?php echo TextHelper::_('COBALT_I_WANT'); ?>
                                    <?php echo $user_html; ?>
                                    <?php echo TextHelper::_('COBALT_TO_MOVE'); ?>
                                    <input class="inputbox required" type="text" placeholder="0" value="" name="amount" />
                                    <?php echo TextHelper::_('COBALT_DEALS_FORWARD_TO_THE'); ?>
                                    <select class="inputbox" name="stage_id">
                                        <?php
                                            $stages = DealHelper::getGoalStages();
                                            echo JHtml::_('select.options', $stages, 'value', 'text', '', true);
                                        ?>
                                    </select>
                                    <?php echo TextHelper::_('COBALT_STAGE'); ?>
                                    <?php echo $date_html; ?>
                                <?php
                            break;
                        case "complete_tasks": ?>
                                 <?php echo TextHelper::_('COBALT_I_WANT'); ?>
                                    <?php echo $user_html; ?>
                                    <?php echo TextHelper::_('COBALT_TO_COMPLETE'); ?>
                                    <input class="inputbox required" type="text" value="" placeholder="0" name="amount" />
                                    <?php echo TextHelper::_('COBALT_TASKS_OF_TYPE'); ?>
                                    <select class="inputbox" name="category_id">
                                        <option value="">Any</option>
                                        <?php
                                            $categories = EventHelper::getCategories();
                                            echo JHtml::_('select.options', $categories, 'value', 'text', '', true);
                                        ?>
                                    </select>
                                    <?php echo $date_html; ?>
                            <?php
                            break;
                        case "write_notes": ?>
                             <?php echo TextHelper::_('COBALT_I_WANT'); ?>
                                    <?php echo $user_html; ?>
                                    <?php echo TextHelper::_('COBALT_TO_WRITE'); ?>
                                    <input class="inputbox required" type="text" value="" placeholder="0" name="amount" />
                                    <?php echo TextHelper::_('COBALT_NEW_NOTES_OF_TYPE'); ?>
                                    <select class="inputbox" name="category_id">
                                        <option value="">Any</option>
                                        <?php
                                            $categories = NoteHelper::getCategories();
                                            echo JHtml::_('select.options', $categories, 'value', 'text', '', true);
                                        ?>
                                    </select>
                                    <?php echo $date_html; ?>
                            <?php
                            break;
                        case "create_deals": ?>
                             <?php echo TextHelper::_('COBALT_I_WANT'); ?>
                                    <?php echo $user_html; ?>
                                    <?php echo TextHelper::_('COBALT_TO_CREATE'); ?>
                                    <input class="inputbox required" type="text" value="" placeholder="0" name="amount" />
                                    <?php echo TextHelper::_('COBALT_DEALS'); ?>
                                    <?php echo $date_html; ?>
                            <?php
                            break;
                    }?>
            </li>
            <li id="date_selection_area" style="display:none;">
                <div id="date_selection_area_template">
                    <legend><b><?php echo TextHelper::_('COBALT_SET_YOUR_DATE'); ?></b></legend>
                    <ul class="unstyled">
                        <li>
                            <label><?php echo TextHelper::_('COBALT_START_DATE'); ?></label>
                            <span class="input-append">
                                <input class="date_input inputbox required" type="text" name="start_date_hidden" id="start_date">
                                <input type="hidden" id="start_date_hidden" value="" name="start_date"/>
                                <a class="btn add-on" href="javascript:void(0);" onclick='jQuery("#start_date").datepicker().focus();'><i class="icon-calendar"></i></a>
                            </span>
                        </li>
                        <li>
                            <label><?php echo TextHelper::_('COBALT_END_DATE'); ?></label>
                            <span class="input-append">
                                <input class="date_input inputbox required" type="text" id="end_date" name="end_date_hidden">
                                <input type="hidden" id="end_date_hidden" value="" name="end_date"/>
                                <a class="btn add-on" href="javascript:void(0);" onclick='jQuery("#end_date").datepicker().focus();'><i class="icon-calendar"></i></a>
                            </span>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <legend><b><?php echo ucwords(TextHelper::_('COBALT_GOAL_STEP_TWO')); ?></b></legend>
                <input type="text" name="name" class="inputbox required" value="" />
            </li>
            <li>
                <legend><b><?php echo ucwords(TextHelper::_('COBALT_GOAL_STEP_THREE')); ?></b></legend>
                    <ul class="unstyled">
                        <li><label><?php echo TextHelper::_('COBALT_GOAL_STEP_THREE_DESC'); ?></label></li>
                        <li><label class="small checkbox"><input type="checkbox" name="leaderboard"><?php echo TextHelper::_('COBALT_GOAL_CREATE_LEADERBOARD'); ?></label></li>
                    </ul>
            </li>
            <li>
                <div class="well text-center">
                    <a href="javascript:void(0);" onclick="jQuery('#edit-form').submit();" class="btn btn-success"><i class="icon-plus icon-white"></i> <?php echo TextHelper::_('COBALT_ADD'); ?></a>
                    <a href="javascript:void(0);" onclick="window.history.back()"><?php echo TextHelper::_('COBALT_CANCEL_BUTTON'); ?></a>
                </div>
            </li>
        </ul>
    </div>
    <div id="hidden">
        <?php if ($this->member_role == 'exec') { ?>
            <input type="hidden" name="assigned_id" value="0">
            <input type="hidden" name="assigned_type" value="company">
        <?php } else { ?>
            <input type="hidden" name="assigned_id" value="<?php echo $this->user_id; ?>">
            <input type="hidden" name="assigned_type" value="member">
        <?php } ?>
        <input type="hidden" name="goal_type" value="<?php echo $this->type; ?>">
    </div>
</form>
