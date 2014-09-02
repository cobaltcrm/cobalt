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
</script>
<form class="print_form" method="post" target="_blank" action="<?php echo RouteHelper::_('index.php?view=print'); ?>">
<input type="hidden" name="layout" value="events" />
<input type="hidden" name="model" value="event" />

        <div class="btn-group pull-right">
            <a href="javascript:void(0);" rel="tooltip" title="<?php echo TextHelper::_('COBALT_ADD_TASK'); ?>" onclick="Cobalt.addTaskEvent('task');" class="btn"><i class="icon-tasks"></i></a>
            <a href="javascript:void(0);" rel="tooltip" title="<?php echo TextHelper::_('COBALT_ADD_EVENT'); ?>" onclick="Cobalt.addTaskEvent('event');" class="btn"><i class="icon-calendar"></i></a>
            <a href="javascript:void(0);" rel="tooltip" title="<?php echo TextHelper::_('COBALT_PRINT'); ?>" onclick="printItems(this)" class="btn"><i class="icon-print"></i></a>
        </div>

<h3><?php echo ucwords(TextHelper::_('COBALT_TASKS_AND_EVENTS')); ?></h3>

<div class="alert">
    <?php echo TextHelper::_('COBALT_SHOW_TASKS_FOR'); ?>:
        <span class="dropdown">
            <a class="dropdown-toggle" href="#" data-toggle="dropdown" role="button" href="javascript:void(0);" id="event_user_link" ><?php echo TextHelper::_('COBALT_ME'); ?></a>
            <ul class="dropdown-menu" role="menu" aria-labelledby="event_user_link">
                <?php
                    $user_role = UsersHelper::getRole();
                    $user_id = UsersHelper::getUserId();
                ?>
                <li><a class="filter_user_<?php echo $user_id; ?>" onclick="updateEventList(<?php echo $user_id; ?>,0)" ><?php echo TextHelper::_('COBALT_ME'); ?></a></li>
                <?php if ($user_role != 'basic') { ?>
                    <li><a href="javascript:void(0);" class="filter_user_all" onclick="updateEventList('all',0)" >all users</a></li>
                <?php } ?>
                <?php
                    if ($user_role == 'exec') {
                        $teams = UsersHelper::getTeams();
                        if ( count($teams) > 0 ) {
                            foreach ($teams as $team) {
                                 echo "<li><a href='javascript:void(0);' class='filter_team_".$team['team_id']."' onclick='updateEventList(0,".$team['team_id'].")'>".$team['team_name'].TextHelper::_('COBALT_TEAM_APPEND')."</a></li>";
                             }
                        }
                    }
                    $users = UsersHelper::getUsers();
                    if ( count($users) > 0 ) {
                        foreach ($users as $user) {
                            echo "<li><a href='javascript:void(0);' class='filter_user_".$user['id']."' onclick='updateEventList(".$user['id'].",0)'>".$user['first_name']."  ".$user['last_name']."</a></li>";
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
    <div class="controls_area"><a class="btn" href="<?php echo RouteHelper::_('index.php?view=events'); ?>"><?php echo ucwords(TextHelper::_('COBALT_SEE_ALL_TASKS')); ?></a></div>
</div>
</form>
