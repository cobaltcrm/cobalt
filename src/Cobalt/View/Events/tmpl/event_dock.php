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

?>
<form action="<?php echo RouteHelper::_('index.php?view=printFriendly'); ?>" class="print_form">
    <div class="panel panel-default">
        <div class="panel-heading">
        <?php if ($app->input->get('view')!="print") { ?>
            <div class="btn-group pull-right">
                <a rel="tooltip" class="btn btn-xs btn-default" title="<?php echo TextHelper::_('COBALT_ADD_TASK'); ?>" onclick="Task.add('task');" href="javascript:void(0);"><i class="glyphicon glyphicon-tasks"></i></a>
                <a rel="tooltip" class="btn btn-xs btn-default" title="<?php echo TextHelper::_('COBALT_ADD_EVENT'); ?>" onclick="Task.add('event');" href="javascript:void(0);"><i class="glyphicon glyphicon-calendar"></i></a>
                <a rel="tooltip" class="btn btn-xs btn-default" title="<?php echo TextHelper::_('COBALT_PRINT'); ?>" onclick="Cobalt.printItems(this)" href="javascript:void(0);"><i class="glyphicon glyphicon-print"></i></a>
                <a rel="tooltip" class="btn btn-xs btn-default dropdown-toggle" title="<?php echo TextHelper::_('COBALT_APPLY_A_WORKFLOW'); ?>" data-toggle="dropdown" id="templates_link" href="javascript:void(0);"><i class="glyphicon glyphicon-list"></i></a>
                <ul class="dropdown-menu padding">
                    <?php $templates = CobaltHelper::getTaskTemplates($app->input->get('layout'));
                        if ( count($templates) > 0 ) { foreach ($templates as $template) { ?>
                            <li><a href="javascript:void(0)" onclick="createTemplate(<?php echo $template['id']; ?>)"><?php echo $template['name']; ?></a>
                        <?php } } else { ?>
                            <li><?php echo TextHelper::_('COBALT_NO_TEMPLATES_HAVE_BEEN_CREATED'); ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
        <h4 class="panel-title"><?php echo ucwords(TextHelper::_('COBALT_TASKS_AND_EVENTS')); ?></h4>
        </div>
<div class="panel-body">
    <div id="event_list">
        <?php if ($app->input->get('view')!="printFriendly") { ?>
        <div class='alert alert-info'>
            <?php echo TextHelper::_('COBALT_SHOW_TASKS_FOR'); ?>:
            <span class="dropdown">
                <a class="dropdown-toggle" href="#" data-toggle="dropdown" role="button" href="javascript:void(0);" id="event_user_link" ><?php echo TextHelper::_('COBALT_ME'); ?></a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="event_user_link">
                    <?php
                        $user_role = UsersHelper::getRole();
                        $user_id = UsersHelper::getUserId();
                    ?>
                    <li><a class="filter_user_<?php echo $user_id; ?>" onclick="Task.updateEventList(<?php echo $user_id; ?>,0)" ><?php echo TextHelper::_('COBALT_ME'); ?></a></li>
                    <?php if ($user_role != 'basic') { ?>
                        <li><a href="javascript:void(0);" class="filter_user_all" onclick="Task.updateEventList('all',0)" >all users</a></li>
                    <?php } ?>
                    <?php
                        if ($user_role == 'exec') {
                            $teams = UsersHelper::getTeams();
                            if ( count($teams) > 0 ) {
                                foreach ($teams as $team) {
                                     echo "<li><a href='javascript:void(0);' class='filter_team_".$team['team_id']."' onclick='Task.updateEventList(0,".$team['team_id'].")'>".$team['team_name'].TextHelper::_('COBALT_TEAM_APPEND')."</a></li>";
                                 }
                            }
                        }
                        $users = UsersHelper::getUsers();
                        if ( count($users) > 0 ) {
                            foreach ($users as $user) {
                                echo "<li><a href='javascript:void(0);' class='filter_user_".$user['id']."' onclick='Task.updateEventList(".$user['id'].",0)'>".$user['first_name']."  ".$user['last_name']."</a></li>";
                            }
                        }

                    ?>
                </ul>
            </span>
        <?php } ?>
        </div>
        <div id="task_container">
            <div id="task_list">
                <?php
                     $task_list = \Cobalt\Factory::getView('events','event_listings','phtml',array('events'=>$this->events));
                     echo $task_list->render();
                ?>
            </div>
            <div id="edit_task" style="display:none;">
            </div>
            <div id="edit_event" style="display:none;">
            </div>
        </div>
    </div>
</div>
<?php if ($app->input->get('view')!="print") { ?>
<div class="text-center" id="controls_area_bottom">
    <p><a class="btn btn-default" href="<?php echo RouteHelper::_('index.php?view=events'); ?>"><?php echo ucwords(TextHelper::_('COBALT_SEE_ALL_TASKS')); ?></a></p>
</div>
<?php } ?>
</div>
    <input type="hidden" name="model" value="events">
    <input type="hidden" name="layout" value="events">

</form>
<script>
    Task.current_area = 'task_list';
</script>
