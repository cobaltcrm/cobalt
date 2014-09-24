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

//generate user dropdown
$user_html = '<select class="form-control" id="assigned_id">';
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
$date_html = "<select class='form-control' id='date_picker'>";
$dates = DateHelper::getGoalDates();
$date_html .= JHtml::_('select.options', $dates, 'value', 'text', '', true);
$date_html .= "</select>";
?>

<div class="modal-header">
    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
    <h1><img src="<?php echo JURI::base() . $this->header_img; ?>"> <?php echo ucwords($this->header); ?></h1>
</div>
<div class="modal-body">

<form id="edit-form" action="<?php echo RouteHelper::_('index.php?task=save&model=goal&view=goals&refresh_page=1'); ?>" method="post" class="form-horizontal">
<h3 class="page-header"><?php echo ucwords(TextHelper::_('COBALT_GOAL_STEP_ONE')); ?></h3>

<div>
    <div class="input-group">
        <div class="input-group-addon"><?php echo TextHelper::_('COBALT_I_WANT'); ?></div>
        <?php echo $user_html; ?>
    </div>
</div>
<br />
<div>
    <div class="input-group">
        <div class="input-group-addon">
            <?php if ($this->type == 'win_cash'): ?>
                <?php echo TextHelper::_('COBALT_TO_SELL'); ?>
            <?php endif; ?>
            <?php if ($this->type == 'win_deals'): ?>
                <?php echo TextHelper::_('COBALT_TO_WIN'); ?>
            <?php endif; ?>
            <?php if ($this->type == 'move_deals'): ?>
                <?php echo TextHelper::_('COBALT_TO_MOVE'); ?>
            <?php endif; ?>
            <?php if ($this->type == 'complete_tasks'): ?>
                <?php echo TextHelper::_('COBALT_TO_COMPLETE'); ?>
            <?php endif; ?>
            <?php if ($this->type == 'write_notes'): ?>
                <?php echo TextHelper::_('COBALT_TO_WRITE'); ?>
            <?php endif; ?>
            <?php if ($this->type == 'create_deals'): ?>
                <?php echo TextHelper::_('COBALT_TO_CREATE'); ?>
            <?php endif; ?>
        </div>
        <input class="required form-control" type="text" value="" placeholder="0" name="amount" />

        <?php if ($this->type == 'win_cash'): ?>
            <div class="input-group-addon"><?php echo TextHelper::_('COBALT_OF_NEW_BUSINESS'); ?></div>
        <?php endif; ?>
        <?php if ($this->type == 'win_deals'): ?>
            <div class="input-group-addon"><?php echo TextHelper::_('COBALT_NEW_DEALS'); ?></div>
        <?php endif; ?>
        <?php if ($this->type == 'move_deals'): ?>
        <div class="input-group-addon"><?php echo TextHelper::_('COBALT_DEALS_FORWARD_TO_THE'); ?></div>
        <?php endif; ?>
        <?php if ($this->type == 'write_notes'): ?>
            <div class="input-group-addon"><?php echo TextHelper::_('COBALT_NEW_NOTES_OF_TYPE'); ?></div>
        <?php endif; ?>
        <?php if ($this->type == 'create_deals'): ?>
            <div class="input-group-addon"><?php echo TextHelper::_('COBALT_DEALS'); ?></div>
        <?php endif; ?>

    </div>
</div>
<?php if ($this->type == 'move_deals'): ?>
<br />
<div>
        <div class="input-group">
            <div class="input-group-addon">
            <?php echo TextHelper::_('COBALT_TO'); ?>
                </div>
            <select class="form-control" name="stage_id">
                <?php
                $stages = DealHelper::getGoalStages();
                echo JHtml::_('select.options', $stages, 'value', 'text', '', true);
                ?>
            </select>
            <div class="input-group-addon">
                <?php echo TextHelper::_('COBALT_STAGE'); ?>
            </div>
    </div>
</div>
<?php endif; ?>
<?php if ($this->type == 'complete_tasks'): ?>
<br />
<div>
    <div class="input-group">
        <div class="input-group-addon"><?php echo TextHelper::_('COBALT_TASKS_OF_TYPE'); ?></div>
        <select class="form-control" name="category_id">
            <option value="">Any</option>
            <?php
            $categories = EventHelper::getCategories();
            echo JHtml::_('select.options', $categories, 'value', 'text', '', true);
            ?>
        </select>
    </div>
</div>
<?php endif; ?>
<?php if ($this->type == 'write_notes'): ?>
<br />
<div>
    <div class="input-group">
        <div class="input-group-addon">
            <?php echo TextHelper::_('COBALT_OF_TYPE'); ?>
        </div>
        <select class="form-control" name="category_id">
            <option value="">Any</option>
            <?php
            $categories = EventHelper::getCategories();
            echo JHtml::_('select.options', $categories, 'value', 'text', '', true);
            ?>
        </select>
    </div>
</div>
<?php endif; ?>
<br />
<div>
    <div class="input-group">
        <div class="input-group-addon"><?php echo TextHelper::_('COBALT_AT'); ?></div>
        <?php echo $date_html; ?>
    </div>
</div>
<div id="date_selection_area_template">
    <h3 class="page-header"><?php echo ucwords(TextHelper::_('COBALT_SET_YOUR_DATE')); ?></h3>
    <ul class="list-unstyled">
        <li>
            <label><?php echo TextHelper::_('COBALT_START_DATE'); ?></label>
                            <span class="input-append">
                                <input class="date_input inputbox required" type="text" name="start_date_hidden" id="start_date">
                                <input type="hidden" id="start_date_hidden" value="" name="start_date"/>
                                <a class="btn add-on" href="javascript:void(0);" onclick='jQuery("#start_date").datepicker().focus();'><i class="glyphicon glyphicon-calendar"></i></a>
                            </span>
        </li>
        <li>
            <label><?php echo TextHelper::_('COBALT_END_DATE'); ?></label>
                            <span class="input-append">
                                <input class="date_input inputbox required" type="text" id="end_date" name="end_date_hidden">
                                <input type="hidden" id="end_date_hidden" value="" name="end_date"/>
                                <a class="btn add-on" href="javascript:void(0);" onclick='jQuery("#end_date").datepicker().focus();'><i class="glyphicon glyphicon-calendar"></i></a>
                            </span>
        </li>
    </ul>
</div>
<h3 class="page-header"><?php echo ucwords(TextHelper::_('COBALT_GOAL_STEP_TWO')); ?></h3>
<input type="text" name="name" class="form-control required" value="" />
<h3 class="page-header"><?php echo ucwords(TextHelper::_('COBALT_GOAL_STEP_THREE')); ?></h3>
<div class="checkbox">
    <label>
        <input type="checkbox" name="leaderboard"><?php echo TextHelper::_('COBALT_GOAL_CREATE_LEADERBOARD'); ?>
    </label>
    <p class="help-block"><?php echo TextHelper::_('COBALT_GOAL_STEP_THREE_DESC'); ?></p>
</div>
<?php if ($this->member_role == 'exec') { ?>
    <input type="hidden" name="assigned_id" value="0">
    <input type="hidden" name="assigned_type" value="company">
<?php } else { ?>
    <input type="hidden" name="assigned_id" value="<?php echo $this->user_id; ?>">
    <input type="hidden" name="assigned_type" value="member">
<?php } ?>
<input type="hidden" name="goal_type" value="<?php echo $this->type; ?>">
</form>
</div>
<div class="modal-footer">
    <div class="actions"><input type="button" onclick="Cobalt.sumbitModalForm(this);" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" class="btn btn-success"> or <a aria-hidden="true" data-dismiss="modal" href="javascript:void(0);"><?php echo TextHelper::_('COBALT_CANCEL_BUTTON'); ?></a></div>
</div>
<script>
    $('#date_selection_area_template').css('display','none');
    jQuery('#date_picker').on('change', function(){
        var currentDate = new Date;
        switch (jQuery('#date_picker').val()) {
            case 'this_week':
                var firstday = new Date(currentDate.setDate(currentDate.getDate() - currentDate.getDay()));
                var lastday = new Date(currentDate.setDate(currentDate.getDate() - currentDate.getDay()+6));
                break;
            case 'next_week':
                var nextweek = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate()+7);
                var firstday = new Date(nextweek.setDate(nextweek.getDate() - nextweek.getDay()));
                var lastday = new Date(nextweek.setDate(nextweek.getDate() - nextweek.getDay()+6));
                break;
            case 'this_month':
                var firstday = new Date(currentDate.setDate(currentDate.getDate() - currentDate.getDay()));
                var lastday = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
                break;
            case 'next_month':
                var nextMonth = new Date(currentDate.getFullYear(), currentDate.getMonth()+1, 1);
                var firstday = new Date(nextMonth.getFullYear(), nextMonth.getMonth(), 1);
                var lastday = new Date(nextMonth.getFullYear(), nextMonth.getMonth() + 1, 0);
                break;
            case 'this_quarter':

                break;
            case 'next_quarter':

                break;
            case 'this_year':
                var firstday = new Date(currentDate.setDate(currentDate.getDate() - currentDate.getDay()));
                var lsatday = new Date(new Date().getFullYear(), 11, 31);
                break;
        }

        if (typeof firstday != 'undefined') {
            var start_date = firstday.getDate()+'/'+firstday.getMonth()+'/'+firstday.getFullYear();
            $('#start_date_hidden').val(start_date);
        }
        if (typeof lastday != 'null') {
            var end_date = lastday.getDate()+'/'+lastday.getMonth()+'/'+lastday.getFullYear();
            $('#end_date_hidden').val(end_date);
        }

        if (jQuery('#date_picker').val() == 'custom') {
            $('#date_selection_area_template').slideDown(1000);
        } else {
            $('#date_selection_area_template').slideUp();
        }
    });
    currentDate = new Date;
    firstday = new Date(currentDate.setDate(currentDate.getDate() - currentDate.getDay()));
    $('#start_date_hidden').val(firstday.getDate()+'/'+firstday.getMonth()+'/'+firstday.getFullYear());
    lastday = new Date(currentDate.setDate(currentDate.getDate() - currentDate.getDay()+6));
    $('#end_date_hidden').val(lastday.getDate()+'/'+lastday.getMonth()+'/'+lastday.getFullYear());
</script>

