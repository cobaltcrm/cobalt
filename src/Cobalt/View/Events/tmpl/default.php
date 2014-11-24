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
$app = \Cobalt\Factory::getApplication();
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
    var current_area = '';
</script>

<div class="page-header">
        <div class="btn-group pull-right">
            <div class="btn-group">
                <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="btnGroupVerticalDrop1">
                    <?php echo ucwords(TextHelper::_('COBALT_ADD')); ?>
                    <span class="caret"></span>
                </button>
                <ul aria-labelledby="btnGroupVerticalDrop1" role="menu" class="dropdown-menu">
                    <li><a href="javascript:void(0);" onclick="Task.add('task')"><?php echo ucwords(TextHelper::_('COBALT_ADD_TASK')); ?></a></li>
                    <li><a href="javascript:void(0);" onclick="Task.add('event')"><?php echo ucwords(TextHelper::_('COBALT_ADD_EVENT')); ?></a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-default" onclick="location.href = '<?php echo RouteHelper::_('index.php?view=calendar'); ?>'" ><?php echo TextHelper::_('COBALT_SHOW_CALENDAR'); ?></button>
            <button type="button" class="btn btn-default" href="javascript:void(0)" onclick="Cobalt.printItems('#events_form');"><?php echo TextHelper::_('COBALT_PRINT'); ?></button>
        </div>
    <h1><?php echo ucwords(TextHelper::_('COBALT_TASKS_HEADER')); ?></h1>
</div>
<div>
    <ul class="list-inline filter-sentence">
        <li><span><?php echo TextHelper::_('COBALT_SHOW'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="event_status_link" ><span class="dropdown-label"><?php echo $this->event_statuses[$this->state->get('Event.'.$view.'_'.$layout.'_status')]; ?></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_status_link" data-filter="completed" >
                <?php foreach ($this->event_statuses as $title => $text) { ?>
                     <li><a href='javascript:void(0);' data-filter-value="<?php echo $title; ?>"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="event_type_link"><span class="dropdown-label"><?php echo $this->event_types[$this->state->get('Event.'.$view.'_'.$layout.'_type')]; ?></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_type_link" data-filter="type">
                <?php foreach ($this->event_types as $title => $text) { ?>
                     <li><a href='javascript:void(0);' data-filter-value="<?php echo $title; ?>"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li><span><?php echo TextHelper::_('COBALT_OF'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="event_category_link" ><span class="dropdown-label"><?php echo $this->event_categories[$this->state->get('Event.'.$view.'_'.$layout.'_category')]; ?></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_category_link" data-filter="catid">
                <?php foreach ($this->event_categories as $title => $text) { ?>
                     <li><a href='javascript:void(0);' data-filter-value="<?php echo $title; ?>"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li><span><?php echo TextHelper::_('COBALT_THAT_ARE'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="event_due_date_link" ><span class="dropdown-label"><?php echo $this->event_due_dates[$this->state->get('Event.'.$view.'_'.$layout.'_due_date')]; ?></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_due_date_link" data-filter="due_date_string">
                <?php foreach ($this->event_due_dates as $title => $text) { ?>
                     <li><a href='javascript:void(0);' data-filter-value="<?php echo $title; ?>"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li><span><?php echo TextHelper::_('COBALT_FOR'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="event_association_link" ><span class="dropdown-label"><?php echo $this->event_associations[$this->state->get('Event.'.$view.'_'.$layout.'_association_type')]; ?></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_association_link" data-filter="association_type">
                <?php foreach ($this->event_associations as $title => $text) { ?>
                     <li><a href='javascript:void(0);' data-filter-value="<?php echo $title; ?>"><?php echo $text; ?></a></li>
                <?php }?>
            </ul>
        </li>
        <li><span><?php echo TextHelper::_('COBALT_ASSIGNED_TO'); ?></span></li>
        <li class="dropdown">
            <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="event_assignee_link" ><span class="dropdown-label"><?php echo $user_filter; ?></span></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_assignee_link" data-filter="assigned">
                <li><a class="dropdown_item filter_assign" data-filter-value="{'individual',<?php echo UsersHelper::getLoggedInUser()->id; ?>}" ><?php echo TextHelper::_('COBALT_ME'); ?></a></li>
                <?php if ($user_role != 'basic') { ?>
                        <?php if ($user_role == "exec") { ?>
                            <li><a href="javascript:void(0);" data-filter-value="{'individual','all'}"><?php echo ucwords(TextHelper::_('COBALT_ALL_USERS')); ?></a></li>
                        <?php } ?>
                        <?php if ($user_role == "manager") { ?>
                            <li><a href="javascript:void(0);" data-filter-value="{'individual','all'}"><?php echo ucwords(TextHelper::_('COBALT_ALL_USERS_ON_MY_TEAM')); ?></a></li>
                        <?php } ?>
                    <?php } ?>
                    <?php
                        if ($user_role == 'exec') {
                            if ( count($teams) > 0 ) {
                                foreach ($teams as $team) { ?>
                                     <li><a href='javascript:void(0);' data-filter-value='{"team","<?php echo $team['team_id']; ?>"}'><?php echo $team['team_name'].TextHelper::_('COBALT_TEAM_APPEND'); ?></a></li>
                                 <?php }
                            }
                        }
                        if ( count($users) > 0 ) {
                            foreach ($users as $user) { ?>
                                <li><a class='dropdown_item ' data-filter-value='{"individual","<?php echo $user['id']; ?>"}'><?php echo $user['first_name']."  ".$user['last_name']; ?></a></li>
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
<form id="events_form" class="print_form" method="post" target="_blank" action="<?php echo RouteHelper::_('index.php?view=events'); ?>">
<input type="hidden" name="layout" value="events" />
<input type="hidden" name="model" value="event" />
<table id="event" class="table table-hover table-striped data-table table-bordered">
   <thead>
        <tr>
            <th class="checkbox_column"><input type="checkbox" onclick="Cobalt.selectAll(this);" /></th>
            <th class="name"><div class="sort_order"><a class="e.name" onclick="sortTable('e.name',this)"><?php echo ucwords(TextHelper::_('COBALT_EVENTS_NEXT_TASK')); ?></div></th>
            <th class="due_date"><div class="sort_order"><a class="e.due_date" onclick="sortTable('e.due_date',this)"><?php echo ucwords(TextHelper::_('COBALT_EVENTS_DUE_DATE')); ?></div></th>
            <th class="for"><?php echo ucwords(TextHelper::_('COBALT_EVENTS_FOR')); ?></th>
            <th class="owner"><?php echo ucwords(TextHelper::_('COBALT_CREATED_BY')); ?></th>
            <th class="assigned_to"><?php echo ucwords(TextHelper::_('COBALT_ASSIGNED_TO')); ?></th>
            <th class="type"><div class="sort_order"><a class="e.category_id" onclick="sortTable('e.category_id',this)"><?php echo ucwords(TextHelper::_('COBALT_EVENTS_TYPE')); ?></div></th>
            <th class="contacts"><?php echo ucwords(TextHelper::_('COBALT_EVENTS_CONTACTS')); ?></th>
            <th class="notes"><?php echo ucwords(TextHelper::_('COBALT_EVENTS_NOTES')); ?></th>
        </tr>
    </thead>
    <tbody id="list">
        <?php
            $data = array('events'=>$this->events);
            $event_list = \Cobalt\Factory::getView('events','list','phtml',$data);
            echo $event_list->render();
        ?>
    </tbody>
</table>
</form>
</div>
<div id="deal_contacts_modal_dialog" style="display:none;"></div>
<div id="edit_task" style="display:none;"></div>
<div id="edit_event" style="display:none;"></div>
<div id="note_modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 id="noteModalHeaderTitle">&nbsp;</h3>
            </div>
            <div class="modal-body" id="noteModalBody"></div>
            <div class="modal-footer">
                <button aria-hidden="true" data-dismiss="note_modal" class="btn btn-default" id="CobaltAjaxModalCloseButton"><?php echo TextHelper::_('COBALT_CANCEL'); ?></button>
            </div>
        </div>
    </div>
</div>
<div id="contacts_modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>&nbsp;</h3>
            </div>
            <div class="modal-body" id="contactsModalBody"></div>
            <div class="modal-footer">
                <button aria-hidden="true" data-dismiss="modal" class="btn btn-default"><?php echo TextHelper::_('COBALT_CANCEL'); ?></button>
            </div>
        </div>
    </div>
</div>
