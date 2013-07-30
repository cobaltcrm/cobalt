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
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<script type="text/javascript">
    var eventsObj = <?php echo $this->events; ?>;
    var loc = 'calendar';
</script>

<form class="print_form" method="post" target="_blank" action="<?php echo JRoute::_('index.php?view=print'); ?>">
    <div class="pull-right text-right">
    <span class="calendar_actions">
        <input type="hidden" name="layout" value="calendar" />
        <input type="hidden" name="model" value="event" />
        <div class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="company_type_link" href="javascript:void(0);"><span class="dropdown-label"><?php echo CRMText::_('COBALT_SHOW'); ?></span><span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="company_type_link">
                <li><a class="dropdown_item" href="javascript:void(0);" onclick="showCalendarTasks()"><?php echo CRMText::_('COBALT_SHOW_TASKS'); ?></a></li>
                <li><a class="dropdown_item" href="javascript:void(0);" onclick="showCalendarEvents()"><?php echo CRMText::_('COBALT_SHOW_EVENTS'); ?></a></li>
                <li><a class="dropdown_item" href="javascript:void(0);" onclick="showAllCalendarEvents()"><?php echo CRMText::_('COBALT_SHOW_TASKS_EVENTS'); ?></a></li>
            </ul>
        </div>
        <a href="<?php echo JRoute::_('index.php?view=events'); ?>" ><?php echo CRMText::_('COBALT_CALENDAR_SHOW_ALL'); ?></a>
        <?php /** FUTURE <a href="javascript:void(0);" onclick="printItems(this);"><?php echo CRMText::_('COBALT_PRINT'); ?></a> **/ ?>
    </span>
    </div>
</form>

<h1><?php echo CRMText::_('COBALT_CALENDAR_HEADER'); ?></h1>

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
    <li><a href="javascript:void(0);" onclick="addTaskEvent('task');"><?php echo CRMText::_('COBALT_ADD_TASK'); ?></a></li>
    <li><a href="javascript:void(0);" onclick="addTaskEvent('event');"><?php echo CRMText::_('COBALT_ADD_EVENT'); ?></a></li>
</ul>
<!-- </div> -->

<div id="edit_menu" class="edit_menu" style="display:none;">
    <ul>
        <li><a href="javascript:void(0);" class="edit_event_button"><?php echo CRMText::_('COBALT_EDIT'); ?></a></li>
        <li><a href="javascript:void(0);" class="remove_event_button"><?php echo CRMText::_('COBALT_REMOVE_EVENT'); ?></a></li>
        <li><a href="javascript:void(0);" class="remove_event_series_button"><?php echo CRMText::_('COBALT_REMOVE_SERIES'); ?></a></li>
        <li><a href="javascript:void(0);" class="complete_event_button"><?php echo CRMText::_('COBALT_MARK_COMPLETE'); ?></a></li>
        <li><a href="javascript:void(0);" style="display:none;" class="show_event_association"></a></li>
    </ul>
</div>
