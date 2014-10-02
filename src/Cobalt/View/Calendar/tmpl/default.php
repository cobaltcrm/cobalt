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
<script type="text/javascript">
    var new_event_date = null;
    var menu = true;
    var curr_cal_event = null;
    var cloning = false;
    var eventsObj = <?php echo $this->events; ?>;
    var loc = 'calendar';
</script>

<div class="pull-right btn-group">
    <button onclick="location.href = '<?php echo RouteHelper::_('index.php?view=events'); ?>'" class="btn btn-default" type="button"><?php echo TextHelper::_('COBALT_CALENDAR_SHOW_ALL'); ?></button>
    <button id="company_type_link" class="btn btn-default"data-toggle="dropdown" type="button">
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        <li><a href="javascript:void(0);" onclick="Calendar.showCalendarTasks()"><?php echo TextHelper::_('COBALT_SHOW_TASKS'); ?></a></li>
        <li><a href="javascript:void(0);" onclick="Calendar.showCalendarEvents()"><?php echo TextHelper::_('COBALT_SHOW_EVENTS'); ?></a></li>
        <li><a href="javascript:void(0);" onclick="Calendar.showAllCalendarEvents()"><?php echo TextHelper::_('COBALT_SHOW_TASKS_EVENTS'); ?></a></li>
    </ul>
</div>

<h1><?php echo TextHelper::_('COBALT_CALENDAR_HEADER'); ?></h1>

<div id="calendar"></div>

<div id="team_members" class="padding">
    <form class="inline-form">
        <?php if (count($this->team_members)>0) {
            echo '<ul id="team_member_calendar_filter">';
            foreach ($this->team_members as $team_member) {
                echo '<li class="badge user-filter-badge" style="background-color:#'.$team_member['color'].';"><label class="checkbox"><input type="checkbox" onclick="toggleTeamMemberEvents('.$team_member['id'].');" /><span>'.$team_member['first_name'].' '.$team_member['last_name'].'</span></label></li>';
            }
            echo '</ul>';
        }?>
    </form>
</div>

<div id="edit_task" style="display:none;">
</div>

<div id="edit_event" style="display:none;">
</div>

<!-- <div id="addTaskEvent" style="display: none;"> -->
<ul id="addTaskEvent" class="dropdown-menu">
    <li><a href="javascript:void(0);" onclick="Task.add('task');"><?php echo TextHelper::_('COBALT_ADD_TASK'); ?></a></li>
    <li><a href="javascript:void(0);" onclick="Task.add('event');"><?php echo TextHelper::_('COBALT_ADD_EVENT'); ?></a></li>
</ul>
<!-- </div> -->

<div id="edit_menu" class="edit_menu" style="display:none;">
    <ul>
        <li><a href="javascript:void(0);" class="edit_event_button"><?php echo TextHelper::_('COBALT_EDIT'); ?></a></li>
        <li><a href="javascript:void(0);" class="remove_event_button"><?php echo TextHelper::_('COBALT_REMOVE_EVENT'); ?></a></li>
        <li><a href="javascript:void(0);" class="remove_event_series_button"><?php echo TextHelper::_('COBALT_REMOVE_SERIES'); ?></a></li>
        <li><a href="javascript:void(0);" class="complete_event_button"><?php echo TextHelper::_('COBALT_MARK_COMPLETE'); ?></a></li>
        <li><a href="javascript:void(0);" style="display:none;" class="show_event_association"></a></li>
    </ul>
</div>
<script>
jQuery(document).ready(function() {
    Calendar.initialise();
});
</script>