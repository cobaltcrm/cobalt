<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

defined('_CEXEC') or die;

use Cobalt\Factory;
use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\UsersHelper;

// Available variables in this layout
/** @var \Symfony\Component\Templating\PhpEngine $view */
/** @var array $events */

$app = Factory::getApplication();
?>

<script>Task.current_area = "task_list"</script>
<form class="print_form" method="post" target="_blank" action="<?php echo RouteHelper::_('index.php?view=printFriendly&layout=events'); ?>">
	<input type="hidden" name="layout" value="events" />
	<input type="hidden" name="model" value="event" />

	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="btn-group pull-right">
				<a href="javascript:void(0);" rel="tooltip" title="<?php echo TextHelper::_('COBALT_ADD_TASK'); ?>" onclick="Task.add('task');" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-tasks"></i></a>
				<a href="javascript:void(0);" rel="tooltip" title="<?php echo TextHelper::_('COBALT_ADD_EVENT'); ?>" onclick="Task.add('event');" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-calendar"></i></a>
				<a href="javascript:void(0);" rel="tooltip" title="<?php echo TextHelper::_('COBALT_PRINT'); ?>" onclick="Cobalt.printItems(this)" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-print"></i></a>
			</div>
			<h4 class="panel-title"><?php echo ucwords(TextHelper::_('COBALT_TASKS_AND_EVENTS')); ?></h4>
		</div>
		<div class="panel-body">
			<div class="alert alert-info">
				<?php echo TextHelper::_('COBALT_SHOW_TASKS_FOR'); ?>:
                <span class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown" role="button" href="javascript:void(0);" id="event_user_link"><?php echo TextHelper::_('COBALT_ME'); ?></a>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="event_user_link">
	                    <?php
	                    $user_role = UsersHelper::getRole();
	                    $user_id   = UsersHelper::getUserId();
	                    ?>
	                    <li>
		                    <a class="filter_user_<?php echo $user_id; ?>" onclick="Task.updateEventList(<?php echo $user_id; ?>,0)"><?php echo TextHelper::_('COBALT_ME'); ?></a>
	                    </li>
	                    <?php if ($user_role != 'basic') : ?>
		                    <li><a href="javascript:void(0);" class="filter_user_all" onclick="Task.updateEventList('all',0)">all users</a></li>
	                    <?php endif; ?>
	                    <?php if ($user_role == 'exec') :
		                    $teams = UsersHelper::getTeams();
		                    if (count($teams) > 0) :
			                    foreach ($teams as $team) : ?>
	                                <li>
		                                <a href="javascript:void(0);" class="filter_team_<?php echo $team['team_id']; ?>" onclick="Task.updateEventList(0, <?php echo $team['team_id']; ?>)">
			                                <?php echo $team['team_name'] . TextHelper::_('COBALT_TEAM_APPEND'); ?>
		                                </a>
	                                </li>
	                            <?php endforeach;
	                        endif;
	                    endif;
	                    $users = UsersHelper::getUsers();
	                    if (count($users) > 0) :
		                    foreach ($users as $user) : ?>
	                            <li>
		                            <a href="javascript:void(0);" class="filter_user_<?php echo $user['id']; ?>" onclick="Task.updateEventList(<?php echo $user['id']; ?>, 0)">
			                            <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
		                            </a>
	                            </li>
		                    <?php endforeach;
	                    endif; ?>
                    </ul>
                </span>
			</div>
			<div id="task_container">
				<div id="task_list">
					<?php echo $view->render('events/event_listings', array('events' => $events));?>
				</div>
				<div id="edit_task" style="display:none;"></div>
				<div id="edit_event" style="display:none;"></div>
				<div class="controls_area">
					<a class="btn btn-default" href="<?php echo RouteHelper::_('index.php?view=events'); ?>"><?php echo ucwords(TextHelper::_('COBALT_SEE_ALL_TASKS')); ?></a>
				</div>
			</div>
		</div>
	</div>
</form>
