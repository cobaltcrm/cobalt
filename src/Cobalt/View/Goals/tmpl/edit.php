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

$company_id = (isset($this->goal) && intval($this->goal->assigned_id) && $this->goal->assigned_type == 'company') ? $this->goal->assigned_id : 0 ;
$selected_me = (isset($this->goal) && $this->goal->assigned_type == 'member' && $this->goal->assigned_id == $this->user_id) ? 'selected="selected"' : '';
$selected_company = (isset($this->goal) && $this->goal->assigned_type == 'company') ? 'selected="selected"' : '';
$selected_leaderboard = (isset($this->goal) && intval($this->goal->leaderboard)) ? 'checked="checked"' : '';
$amount = isset($this->goal) ? $this->goal->amount : 0 ;
$goal_name = isset($this->goal) ? $this->goal->name : '' ;
$start_date = isset($this->goal) ? $this->goal->start_date : '' ;
$end_date = isset($this->goal) ? $this->goal->end_date : '' ;

//generate user dropdown
$user_html = '<select class="form-control" id="assigned_id">';
//if an executive is creating a goal
if ($this->member_role == 'exec') {
    $user_html .= "<option ". $selected_company  ." value='company_".$company_id."'>".ucwords(TextHelper::_('COBALT_THE_COMPANY'))."</option>";
}
//individual assignment
$user_html .= "<option ". $selected_me  ." value='member_".$this->user_id."'>".TextHelper::_('COBALT_ME')."</option>";
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
$date_html .= JHtml::_('select.options', $dates, 'value', 'text', isset($this->goal) ? 'custom' : '', true);
$date_html .= "</select>";
?>
<style>
    #edit-form select {
        -webkit-appearance: none;
        -webkit-border-radius: 0px 4px 4px 0px;
    }
</style>
<div class="modal-header">
    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
    <h4 class="modal-title"><img src="<?php echo \Cobalt\Factory::getApplication()->get('uri.media.full') . $this->header_img; ?>"> <?php echo ucwords($this->header); ?></h4>
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
        <input class="required form-control" type="text" value="<?php echo $amount; ?>" placeholder="0" name="amount" />

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
                echo JHtml::_('select.options', $stages, 'value', 'text', isset($this->deal) ? $this->deal->state_id : '', true);
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
            echo JHtml::_('select.options', $categories, 'value', 'text', isset($this->goal) ? $this->goal->category_id : '' , true);
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
            echo JHtml::_('select.options', $categories, 'value', 'text', isset($this->goal) ? $this->goal->category_id : '' , true);
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
                                <input class="date_input inputbox required" type="text" value="<?php echo $start_date; ?>" name="start_date_hidden" id="start_date">
                                <input type="hidden" id="start_date_hidden" value="<?php echo $start_date; ?>" name="start_date"/>
                                <a class="btn add-on" href="javascript:void(0);" onclick='jQuery("#start_date").datepicker().focus();'><i class="glyphicon glyphicon-calendar"></i></a>
                            </span>
        </li>
        <li>
            <label><?php echo TextHelper::_('COBALT_END_DATE'); ?></label>
                            <span class="input-append">
                                <input class="date_input inputbox required" type="text" value="<?php echo $end_date; ?>" id="end_date" name="end_date_hidden">
                                <input type="hidden" id="end_date_hidden" value="<?php echo $end_date; ?>" name="end_date"/>
                                <a class="btn add-on" href="javascript:void(0);" onclick='jQuery("#end_date").datepicker().focus();'><i class="glyphicon glyphicon-calendar"></i></a>
                            </span>
        </li>
    </ul>
</div>
<h3 class="page-header"><?php echo ucwords(TextHelper::_('COBALT_GOAL_STEP_TWO')); ?></h3>
<input type="text" name="name" class="form-control required" value="<?php echo $goal_name; ?>" />
<h3 class="page-header"><?php echo ucwords(TextHelper::_('COBALT_GOAL_STEP_THREE')); ?></h3>
<div class="checkbox">
    <label>
        <input type="checkbox" <?php echo $selected_leaderboard; ?> name="leaderboard"><?php echo TextHelper::_('COBALT_GOAL_CREATE_LEADERBOARD'); ?>
    </label>
    <p class="help-block"><?php echo TextHelper::_('COBALT_GOAL_STEP_THREE_DESC'); ?></p>
</div>
<?php if ($this->member_role == 'exec' && !empty($selected_company)) { ?>
    <input type="hidden" name="assigned_id" value="<?php echo $company_id ; ?>">
    <input type="hidden" name="assigned_type" value="company">
<?php } else { ?>
    <input type="hidden" name="assigned_id" value="<?php echo $this->user_id; ?>">
    <input type="hidden" name="assigned_type" value="member">
<?php } ?>
<input type="hidden" name="goal_type" value="<?php echo $this->type; ?>">
<?php if (isset($this->goal)): ?>
<input type="hidden" name="id" value="<?php echo $this->goal->id; ?>">
    <input type="hidden" name="id" value="<?php echo $this->goal->id; ?>">
<?php endif; ?>
</form>
</div>
<div class="modal-footer">
    <div class="actions"><input type="button" onclick="Cobalt.sumbitModalForm(this);" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" class="btn btn-success"> or <a aria-hidden="true" data-dismiss="modal" href="javascript:void(0);"><?php echo TextHelper::_('COBALT_CANCEL_BUTTON'); ?></a></div>
</div>
<script>
    <?php if (isset($this->goal)): ?>
        $('#date_selection_area_template').css('display','block');
    <?php else: ?>
        $('#date_selection_area_template').css('display','none');
    <?php endif; ?>
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
                var d = new Date();
                var quarter = Math.floor((d.getMonth() / 3));
                var firstday = new Date(d.getFullYear(), quarter * 3, 1);
                var lastday = new Date(firstday.getFullYear(), firstday.getMonth() + 3, 0);
                break;
            case 'next_quarter':
                var d = new Date();
                var quarter = Math.floor((d.getMonth() / 3) + 1);
                var firstday = new Date(d.getFullYear(), quarter * 3, 1);
                var lastday = new Date(firstday.getFullYear(), firstday.getMonth() + 3, 0);
                break;
            case 'this_year':
                var firstday = new Date(currentDate.setDate(currentDate.getDate() - currentDate.getDay()));
                var lastday = new Date(new Date().getFullYear(), 11, 31);
                break;
        }

        if (typeof firstday != 'undefined') {
            var start_date = firstday.getDate()+'/'+firstday.getMonth()+'/'+firstday.getFullYear();
            console.log('Start: '+start_date);
            $('#start_date_hidden').val(start_date);
        }
        if (typeof lastday != 'undefined') {
            var end_date = lastday.getDate()+'/'+lastday.getMonth()+'/'+lastday.getFullYear();
            console.log('End: '+end_date);
            $('#end_date_hidden').val(end_date);
        }

        if (jQuery('#date_picker').val() == 'custom') {
            $('#date_selection_area_template').slideDown(1000);
        } else {
            $('#date_selection_area_template').slideUp();
        }
    });


    //bind the assignment areas
    jQuery("#assigned_id").bind('change',function(){
        updateAssignedType(jQuery(this));
    });
    //change the type of assignment we are taking to hidden fields
    function updateAssignedType(ele){

        //get element value
        var value = jQuery(ele).val();
        var assigned_id = null;
        var assigned_type = null;

        //assign member data
        if ( value.indexOf('member_') != -1 ){
            assigned_id = value.replace('member_','');
            assigned_type = 'member';
        }
        //assign company data
        if ( value.indexOf('company_') != -1 ){
            assigned_id = value.replace('company_','');
            assigned_type = 'company';
        }
        //assign team data
        if ( value.indexOf('team_') != -1 ){
            assigned_id = value.replace('team_','');
            assigned_type = 'team';
        }

        //update hidden fields
        jQuery("input[name=assigned_id]").val(assigned_id);
        jQuery("input[name=assigned_type]").val(assigned_type);
    }
</script>

