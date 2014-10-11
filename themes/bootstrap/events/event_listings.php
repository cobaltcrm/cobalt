<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

defined('_CEXEC') or die;

use Cobalt\Helper\DateHelper;
use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\TextHelper;

// Available variables in this layout
/** @var \Symfony\Component\Templating\PhpEngine $view */
/** @var array $events */

$current_heading = '';

if (count($events) > 0) :
	foreach ($events as $event) :
		$display_date = ($event['type'] == 'event') ? $event['start_time'] . ' ' . $event['start_time_hour'] : $event['due_date'] . ' ' . $event['due_date_hour'];
		$time         = ($event['type'] == 'event') ? $event['start_time_hour'] : $event['due_date_hour'];

		$display_date = $display_date == '' ? TextHelper::_('COBALT_NA') : DateHelper::formatDate($display_date, false, false);

		$relative_date_title = DateHelper::getRelativeDate($display_date);

		if ($event['completed'] == 1) :
			$completed = 'line-through';
		else :
			$completed = '';
		endif;

		if ($current_heading != $relative_date_title) :
			if ($current_heading != '') :
				echo '</table>';
			endif; ?>
			<h4><?php echo $relative_date_title; ?></h4>
			<?php $current_heading = $relative_date_title; ?>
			<table class="table table-striped table-hover">
		<?php endif; ?>
				<tr class="com_crmery_task_event" id="com_crmery_listing_<?php echo $event['id']; ?>">
					<td><input type="checkbox" class="event_list_checkbox" name="item_id[<?php echo $event['id']; ?>]" /></td>
					<td>
						<div class="dropdown">
							<a class="dropdown-toggle <?php echo $completed; ?>" data-toggle="dropdown" role="button" href="javascript:void(0);" id="event_menu_<?php echo $event['id']; ?>_link"><?php echo $event['name']; ?></a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="event_menu_<?php echo $event['id']; ?>_link">
								<?php if ($event['completed'] == 1) : ?>
								<li><a href="javascript:void(0);" onclick="Calendar.markAsIncomplete(this)" ><?php echo TextHelper::_('COBALT_MARK_INCOMPLETE'); ?></a></li>
								<?php else : ?>
								<li><a href="javascript:void(0);" onclick="Calendar.markAsComplete(this)" ><?php echo TextHelper::_('COBALT_MARK_COMPLETE'); ?></a></li>
								<li><a href="javascript:void(0);" onclick="Calendar.postponeEvent(this,1)" ><?php echo TextHelper::_('COBALT_POSTPONE_1_DAY'); ?></a></li>
								<li><a href="javascript:void(0);" onclick="Calendar.postponeEvent(this,7)" ><?php echo TextHelper::_('COBALT_POSTPONE_7_DAYS'); ?></a></li>
								<?php endif; ?>
								<?php $id = (array_key_exists('parent_id', $event) && $event['parent_id']) != 0 ? $event['parent_id'] : $event['id']; ?>
								<li><a href="javascript:void(0);" onclick="Calendar.editEvent(<?php echo $id; ?>,'<?php echo $event['type']; ?>')" ><?php echo TextHelper::_('COBALT_EDIT'); ?></a></li>
								<li><a href="javascript:void(0);" onclick="Calendar.removeCalendarEvent(this)" ><?php echo TextHelper::_('COBALT_DELETE'); ?></a></li>
							</ul>
						</div>
						<div id="event_form_<?php echo $event['id']; ?>">
							<input type="hidden" name="event_id" value="<?php echo $event['id']; ?>" />
							<input type="hidden" name="parent_id" value="<?php echo $event['parent_id']; ?>" />
							<?php if ($event['type'] == "task") : ?>
							<input type="hidden" name="due_date" value="<?php echo $event['due_date']; ?>" />
							<?php else : ?>
							<input type="hidden" name="start_time" value="<?php echo $event['start_time']; ?>" />
							<input type="hidden" name="end_time" value="<?php echo $event['end_time']; ?>" />
							<?php endif; ?>
							<input type="hidden" name="event_type" value="<?php echo $event['type']; ?>" />
							<input type="hidden" name="repeats" value="<?php echo $event['repeats']; ?>" />
							<input type="hidden" name="type" value="single" />
						</div>
					</td>
					<?php $display_date = ($event['type'] == 'event') ? $event['start_time'] : $event['due_date']; ?>
					<?php $display_date = ($display_date == '') ? TextHelper::_('COBALT_NA') : $display_date; ?>
					<td class="date">
						<small><?php echo DateHelper::formatDateString($display_date) . ' ' . DateHelper::formatTime($time, '(' . UsersHelper::getTimeFormat() . ')'); ?></small>
					</td>
					<?php switch ($event['association_type']) :
						case 'company': ?>
						<td>
							<i class="glyphicon glyphicon-briefcase"></i> <a href="<?php echo RouteHelper::_('index.php?view=companies&layout=company&id=' . $event['company_id']); ?>"><?php echo $event['company_name']; ?></a>
						</td>
						<?php break;
						case 'deal': ?>
						<td>
							<i class="glyphicon glyphicon-tag"></i> <a href="<?php echo RouteHelper::_('index.php?view=deals&layout=deal&id=' . $event['deal_id']); ?>"><?php echo $event['deal_name']; ?></a>
						</td>
						<?php break;
						case 'person': ?>
						<td>
							<i class="glyphicon glyphicon-user"></i> <a href="<?php echo RouteHelper::_('index.php?view=people&layout=person&id=' . $event['person_id']); ?>"><?php echo $event['person_first_name'] . ' ' . $event['person_last_name']; ?></a>
						</td>
						<?php break;
						default: ?>
						<td>&nbsp;</td>
						<?php break;
					endswitch; ?>
				</tr>
			<?php endforeach; ?>
			</table>
<?php endif;
