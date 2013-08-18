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
$view = $app->input->get('view');
$layout = $app->input->get('layout','list');

$cat_arr = array('any'=>TextHelper::_('COBALT_ANY_TYPE'));
$this->event_categories = $cat_arr + $this->event_categories;

$user_arr = array(array('value'=>UsersHelper::getUserId(),'label'=>TextHelper::_('COBALT_ME')));
$users = UsersHelper::getUsers();
$teams = UsersHelper::getTeams();

$user_role = UsersHelper::getRole();
$user_id = UsersHelper::getUserId();

if ( $user_role != 'basic' ) {
    $user_arr[]=array('value'=>'all','label'=>TextHelper::_('COBALT_ALL_USERS'));
}
$this->event_users = array_merge($user_arr,$this->event_users);
$assignee_filter = $this->state->get('Event.'.$view.'_'.$layout.'_assignee_id');
$filter_type = $this->state->get('Event.'.$view.'_'.$layout.'_assignee_filter_type');
if ($filter_type == "individual") {
    foreach ($this->event_users as $key => $user) {
        if ($user['value'] == $assignee_filter) {
            $user_filter = $user['label'];
        }
    }
} elseif ($filter_type == "team") {
    foreach ($this->event_teams as $key => $team) {
        if ($team['team_id'] == $assignee_filter) {
            $user_filter = $team['team_name'].TextHelper::_('COBALT_TEAM_APPEND');
        }
    }
} else {
    $user_filter = TextHelper::_('COBALT_ME');
}
?>
<script type="text/javascript">
    var loc = "events";
    var order_url = "<?php echo 'index.php?view=events&layout=list&format=raw&tmpl=component'; ?>";
    var order_dir = "<?php echo $this->state->get('Event.'.$view.'_'.$layout.'_'.'filter_order_Dir'); ?>";
    var order_col = "<?php echo $this->state->get('Event.'.$view.'_'.$layout.'_'.'filter_order'); ?>";
</script>

<div class="page-header">
        <div class="btn-group pull-right">
            <a class="btn" onclick="addTaskEvent('task')"><?php echo ucwords(TextHelper::_('COBALT_ADD_TASK')); ?></a> -
            <a class="btn" onclick="addTaskEvent('event')"><?php echo ucwords(TextHelper::_('COBALT_ADD_EVENT')); ?></a> -
            <a class="btn" href="<?php echo JRoute::_('index.php?view=calendar'); ?>" ><?php echo TextHelper::_('COBALT_SHOW_CALENDAR'); ?></a> -
            <a class="btn" href="javascript:void(0)" onclick="printItems('event_form');"><?php echo TextHelper::_('COBALT_PRINT'); ?></a>
        </div>
    <h1><?php echo ucwords(TextHelper::_('COBALT_TASKS_HEADER')); ?></h1>
</div>
<div>
    <ul class="inline filter-sentence">
        <li><span><?php echo TextHelper::_('COBALT_SHOW'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="event_status_link" ><span class="dropdown-label"><?php echo $this->event_statuses[$this->state->get('Event.'.$view.'_'.$layout.'_status')]; ?></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_status_link">
                <?php foreach ($this->event_statuses as $title => $text) { ?>
                     <li><a href='javascript:void(0);' class='filter_<?php echo $title; ?>' onclick="dealType('<?php echo $title; ?>')"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="event_type_link" ><span class="dropdown-label"><?php echo $this->event_types[$this->state->get('Event.'.$view.'_'.$layout.'_type')]; ?></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_type_link">
                <?php foreach ($this->event_types as $title => $text) { ?>
                     <li><a href='javascript:void(0);' class='filter_<?php echo $title; ?>' onclick="dealType('<?php echo $title; ?>')"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li><span><?php echo TextHelper::_('COBALT_OF'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="event_category_link" ><span class="dropdown-label"><?php echo $this->event_categories[$this->state->get('Event.'.$view.'_'.$layout.'_category')]; ?></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_category_link">
                <?php foreach ($this->event_categories as $title => $text) { ?>
                     <li><a href='javascript:void(0);' class='filter_<?php echo $title; ?>' onclick="dealType('<?php echo $title; ?>')"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li><span><?php echo TextHelper::_('COBALT_THAT_ARE'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="event_due_date_link" ><span class="dropdown-label"><?php echo $this->event_due_dates[$this->state->get('Event.'.$view.'_'.$layout.'_due_date')]; ?></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_due_date_link">
                <?php foreach ($this->event_due_dates as $title => $text) { ?>
                     <li><a href='javascript:void(0);' class='filter_<?php echo $title; ?>' onclick="dealType('<?php echo $title; ?>')"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li><span><?php echo TextHelper::_('COBALT_FOR'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="event_association_link" ><span class="dropdown-label"><?php echo $this->event_associations[$this->state->get('Event.'.$view.'_'.$layout.'_association_type')]; ?></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_association_link">
                <?php foreach ($this->event_associations as $title => $text) { ?>
                     <li><a href='javascript:void(0);' class='filter_<?php echo $title; ?>' onclick="dealType('<?php echo $title; ?>')"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li><span><?php echo TextHelper::_('COBALT_ASSIGNED_TO'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="event_assignee_link" ><span class="dropdown-label"><?php echo $user_filter; ?></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_assignee_link">
                <li><a class="dropdown_item filter_user_<?php echo $user_id; ?>" onclick="eventUserFilter('individual',<?php echo UsersHelper::getLoggedInUser()->id; ?>)" ><?php echo TextHelper::_('COBALT_ME'); ?></a></li>
                <?php if ($user_role != 'basic') { ?>
                        <?php if ($user_role == "exec") { ?>
                            <li><a href="javascript:void(0);" onclick="eventUserFilter('individual','all')" ><?php echo ucwords(TextHelper::_('COBALT_ALL_USERS')); ?></a></li>
                        <?php } ?>
                        <?php if ($user_role == "manager") { ?>
                            <li><a href="javascript:void(0);" onclick="eventUserFilter('individual','all')" ><?php echo ucwords(TextHelper::_('COBALT_ALL_USERS_ON_MY_TEAM')); ?></a></li>
                        <?php } ?>
                    <?php } ?>
                    <?php
                        if ($user_role == 'exec') {
                            if ( count($teams) > 0 ) {
                                foreach ($teams as $team) { ?>
                                     <li><a href='javascript:void(0);' class='filter_team_<?php echo $team['team_id']; ?>' onclick='eventUserFilter("team","<?php echo $team['team_id']; ?>");'><?php echo $team['team_name'].TextHelper::_('COBALT_TEAM_APPEND'); ?></a></li>
                                 <?php }
                            }
                        }
                        if ( count($users) > 0 ) {
                            foreach ($users as $user) { ?>
                                <li><a class='dropdown_item filter_user_<?php echo $user['id']; ?>' onclick='eventUserFilter("individual","<?php echo $user['id']; ?>")'><?php echo $user['first_name']."  ".$user['last_name']; ?></a></li>
                            <?php }
                        }
                    ?>
            </ul>
        </li>
        <li class="filter_sentence">
            <div class="ajax_loader"></div>
        </li>
    </ul>
    <?php echo TemplateHelper::getEventListEditActions(); ?>
<form id="event_form" class="print_form" method="post" target="_blank" action="<?php echo JRoute::_('index.php?view=print'); ?>">
<input type="hidden" name="layout" value="events" />
<input type="hidden" name="model" value="event" />
<table id='events_list' class="table table-hover table-striped">
       <thead>
            <tr>
                <th class="checkbox_column"><input type="checkbox" onclick="selectAll(this);" /></th>
                <th><div class="sort_order"><a class="e.name" onclick="sortTable('e.name',this)"><?php echo ucwords(TextHelper::_('COBALT_EVENTS_NEXT_TASK')); ?></div></th>
                <th><div class="sort_order"><a class="e.due_date" onclick="sortTable('e.due_date',this)"><?php echo ucwords(TextHelper::_('COBALT_EVENTS_DUE_DATE')); ?></div></th>
                <th><?php echo ucwords(TextHelper::_('COBALT_EVENTS_FOR')); ?></th>
                <th><?php echo ucwords(TextHelper::_('COBALT_CREATED_BY')); ?></th>
                <th><?php echo ucwords(TextHelper::_('COBALT_ASSIGNED_TO')); ?></th>
                <th><div class="sort_order"><a class="e.category_id" onclick="sortTable('e.category_id',this)"><?php echo ucwords(TextHelper::_('COBALT_EVENTS_TYPE')); ?></div></th>
                <th><?php echo ucwords(TextHelper::_('COBALT_EVENTS_CONTACTS')); ?></th>
                <th><?php echo ucwords(TextHelper::_('COBALT_EVENTS_NOTES')); ?></th>
            </tr>
        </thead>
        <tbody id="events">
            <?php
                $data = array('events'=>$this->events);
                $event_list = ViewHelper::getView('events','list','phtml',$data);
                echo $event_list->render();
            ?>
        </tbody>
</table>
<input type="hidden" name="list_type" value="events" />
</form>
</div>
<div id="deal_contacts_modal_dialog" style="display:none;"></div>
<div id="edit_task" style="display:none;"></div>
<div id="edit_event" style="display:none;"></div>
<div id="note_modal" style="display:none;"></div>
