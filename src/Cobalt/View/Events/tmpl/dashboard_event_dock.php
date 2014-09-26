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
?>

<script type="text/javascript">
    Task.current_area = 'task_list';
</script>
<form class="print_form" method="post" target="_blank" action="<?php echo RouteHelper::_('index.php?view=print'); ?>">
<input type="hidden" name="layout" value="events" />
<input type="hidden" name="model" value="event" />
<div class="panel panel-default">
    <div class="panel-heading">
            <div class="btn-group pull-right">
                <a href="javascript:void(0);" rel="tooltip" title="<?php echo TextHelper::_('COBALT_ADD_TASK'); ?>" onclick="Cobalt.addTaskEvent('task');" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-tasks"></i></a>
                <a href="javascript:void(0);" rel="tooltip" title="<?php echo TextHelper::_('COBALT_ADD_EVENT'); ?>" onclick="Cobalt.addTaskEvent('event');" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-calendar"></i></a>
                <a href="javascript:void(0);" rel="tooltip" title="<?php echo TextHelper::_('COBALT_PRINT'); ?>" onclick="printItems(this)" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-print"></i></a>
            </div>
            <h4 class="panel-title"><?php echo ucwords(TextHelper::_('COBALT_TASKS_AND_EVENTS')); ?></h4>
        </div>
        <div class="panel-body">
        <div class="alert alert-info">
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
        </div>
        <div id="task_container">
            <div id="task_list">
                <?php
                     $task_list = ViewHelper::getView('events','event_listings','phtml', array('events'=>$this->events));
                     echo $task_list->render();
                ?>
            </div>
            <div id="edit_task" style="display:none;"></div>
            <div id="edit_event" style="display:none;"></div>
            <div class="controls_area"><a class="btn btn-default" href="<?php echo RouteHelper::_('index.php?view=events'); ?>"><?php echo ucwords(TextHelper::_('COBALT_SEE_ALL_TASKS')); ?></a></div>
        </div>
    </div>
</div>
</form>