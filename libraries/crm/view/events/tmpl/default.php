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
defined( '_JEXEC' ) or die( 'Restricted access' );
$app = JFactory::getApplication();
$view = $app->input->get('view');
$layout = $app->input->get('layout','list');
?>
<script type="text/javascript">
	var loc = "events";
	var order_url = "<?php echo 'index.php?view=events&layout=list&format=raw&tmpl=component'; ?>";
    var order_dir = "<?php echo $this->state->get('Event.'.$view.'_'.$layout.'_'.'filter_order_Dir'); ?>";
    var order_col = "<?php echo $this->state->get('Event.'.$view.'_'.$layout.'_'.'filter_order'); ?>";
</script>
<h1><?php echo ucwords(CRMText::_('COBALT_TASKS_HEADER')); ?></h1>
<div>
	<ul class="filter_lists">
    	<li class="filter_sentence">
    		<?php echo CRMText::_('COBALT_SHOW'); ?>
    		<span class="filters"><a class="dropdown" id="event_status_link" ><?php echo $this->event_statuses[$this->state->get('Event.'.$view.'_'.$layout.'_status')]; ?></a></span>
			<div class="filters" id="event_status">
				<ul>
				    <?php foreach ( $this->event_statuses as $title => $text ){
					     echo "<li><a class='filter_".$title." dropdown_item' onclick=\"eventFilter('status','".$title."')\">".$text."</a></li>";
		            }?>
				</ul>
			</div>
			<span class="filters"><a class="dropdown" id="event_type_link" ><?php echo $this->event_types[$this->state->get('Event.'.$view.'_'.$layout.'_type')]; ?></a></span>
			<div class="filters" id="event_type">
				<ul>
				    <?php foreach ( $this->event_types as $title => $text ){
					     echo "<li><a class='filter_".$title." dropdown_item' onclick=\"eventFilter('type','".$title."')\">".$text."</a></li>";
		            }?>
				</ul>
			</div>
			<?php echo CRMText::_('COBALT_OF'); ?>
			<?php
				$arr = array('any'=>CRMText::_('COBALT_ANY_TYPE'));
				$this->event_categories = $arr+$this->event_categories;
			?>
			<span class="filters"><a class="dropdown" id="event_category_link" ><?php echo $this->event_categories[$this->state->get('Event.'.$view.'_'.$layout.'_category')]; ?></a></span>
			<div class="filters" id="event_category">
				<ul>
				    <?php foreach ( $this->event_categories as $title => $text ){
					     echo "<li><a class='filter_".$title." dropdown_item' onclick=\"eventFilter('category','".$title."')\">".$text."</a></li>";
		            }?>
				</ul>
			</div>
			<?php echo CRMText::_('COBALT_THAT_ARE'); ?>
			<span class="filters"><a class="dropdown" id="event_due_date_link" ><?php echo $this->event_due_dates[$this->state->get('Event.'.$view.'_'.$layout.'_due_date')]; ?></a></span>
			<div class="filters" id="event_due_date">
				<ul>
				    <?php foreach ( $this->event_due_dates as $title => $text ){
					     echo "<li><a class='filter_".$title." dropdown_item' onclick=\"eventFilter('due_date','".$title."')\">".$text."</a></li>";
		            }?>
				</ul>
			</div>
			<?php echo CRMText::_('COBALT_FOR'); ?>
			<span class="filters"><a class="dropdown" id="event_association_link" ><?php echo $this->event_associations[$this->state->get('Event.'.$view.'_'.$layout.'_association_type')]; ?></a></span>
			<div class="filters" id="event_association">
				<ul>
				    <?php foreach ( $this->event_associations as $title => $text ){
					     echo "<li><a class='filter_".$title." dropdown_item' onclick=\"eventFilter('association_type','".$title."')\">".$text."</a></li>";
		            }?>
				</ul>
			</div>
			<?php echo CRMText::_('COBALT_ASSIGNED_TO'); ?>
			<?php
				$arr = array(array('value'=>CobaltHelperUsers::getUserId(),'label'=>CRMText::_('COBALT_ME')));
				$users = CobaltHelperUsers::getUsers();
				$teams = CobaltHelperUsers::getTeams();
				if ( CobaltHelperUsers::getRole() != 'basic' ){
					$arr[]=array('value'=>'all','label'=>CRMText::_('COBALT_ALL_USERS'));
				}
				$this->event_users = array_merge($arr,$this->event_users);
				$assignee_filter = $this->state->get('Event.'.$view.'_'.$layout.'_assignee_id');
				$filter_type = $this->state->get('Event.'.$view.'_'.$layout.'_assignee_filter_type');
				if ( $filter_type == "individual" ){
					foreach ( $this->event_users as $key => $user ){
						if ( $user['value'] == $assignee_filter ){
							$user_filter = $user['label'];
						}
					}
				}else if( $filter_type == "team" ) {
					foreach ( $this->event_teams as $key => $team ){
						if ( $team['team_id'] == $assignee_filter ){
							$user_filter = $team['team_name'].CRMText::_('COBALT_TEAM_APPEND');
						}
					}
				}else{
					$user_filter = CRMText::_('COBALT_ME');
				}
			?>
			<span class="filters"><a class="dropdown" id="event_assignee_link" ><?php echo $user_filter; ?></a></span>
			<div class="filters" id="event_assignee">
				<ul>
				    <?php
		                $user_role = CobaltHelperUsers::getRole();
		                $user_id = CobaltHelperUsers::getUserId();
		            ?>
		            <li><a class="dropdown_item filter_user_<?php echo $user_id; ?>" onclick="eventUserFilter('individual',<?php echo CobaltHelperUsers::getLoggedInUser()->id; ?>)" ><?php echo CRMText::_('COBALT_ME'); ?></a></li>
		            <?php if ( $user_role != 'basic' ) { ?>
		                <?php if ( $user_role == "exec" ){ ?>
		                    <li><a class="dropdown_item filter_user_all" onclick="eventUserFilter('individual','all')" ><?php echo ucwords(CRMText::_('COBALT_ALL_USERS')); ?></a></li>
		                <?php } ?>
		                <?php if ( $user_role == "manager" ){ ?>
		                    <li><a class="dropdown_item filter_user_all" onclick="eventUserFilter('individual','all')" ><?php echo ucwords(CRMText::_('COBALT_ALL_USERS_ON_MY_TEAM')); ?></a></li>
		                <?php } ?>
		            <?php } ?>
		            <?php
		                if ( $user_role == 'exec' ){
		                    if ( count($teams) > 0 ){
		                        foreach($teams as $team){
		                             echo "<li><a class='dropdown_item filter_team_".$team['team_id']."' onclick='eventUserFilter(\"team\",".$team['team_id'].")'>".$team['team_name'].CRMText::_('COBALT_TEAM_APPEND')."</a></li>";
		                         }
		                    }
		                }
		                if ( count($users) > 0 ){
		                    foreach($users as $user){
		                        echo "<li><a class='dropdown_item filter_user_".$user['id']."' onclick='eventUserFilter(\"individual\",".$user['id'].")'>".$user['first_name']."  ".$user['last_name']."</a></li>";
		                    }
		                }

		            ?>
				</ul>
			</div>
    	</li>
    	<li class="filter_sentence">
		    <div class="ajax_loader"></div>
		</li>
	</ul>
	<div class="actions_container">
		<span class="actions">
			<a onclick="addTaskEvent('task')"><?php echo ucwords(CRMText::_('COBALT_ADD_TASK')); ?></a> -
			<a onclick="addTaskEvent('event')"><?php echo ucwords(CRMText::_('COBALT_ADD_EVENT')); ?></a> -
			<a href="<?php echo JRoute::_('index.php?view=calendar'); ?>" ><?php echo CRMText::_('COBALT_SHOW_CALENDAR'); ?></a> -
			<a href="javascript:void(0)" onclick="printItems('event_form');"><?php echo CRMText::_('COBALT_PRINT'); ?></a>
		</span>
	</div>
	<?php echo CobaltHelperTemplate::getEventListEditActions(); ?>
<form id="event_form" class="print_form" method="post" target="_blank" action="<?php echo JRoute::_('index.php?view=print'); ?>">
<input type="hidden" name="layout" value="events" />
<input type="hidden" name="model" value="event" />
<table id='events_list' class="com_cobalt_table">
	   <thead>
	   		<tr>
	   			<th class="checkbox_column"><input type="checkbox" onclick="selectAll(this);" /></th>
				<th><div class="sort_order"><a class="e.name" onclick="sortTable('e.name',this)"><?php echo ucwords(CRMText::_('COBALT_EVENTS_NEXT_TASK')); ?></div></th>
				<th><div class="sort_order"><a class="e.due_date" onclick="sortTable('e.due_date',this)"><?php echo ucwords(CRMText::_('COBALT_EVENTS_DUE_DATE')); ?></div></th>
				<th><?php echo CRMText::_('COBALT_EVENTS_FOR'); ?></th>
				<th><?php echo CRMText::_('COBALT_CREATED_BY'); ?></th>
				<th><?php echo CRMText::_('COBALT_ASSIGNED_TO'); ?></th>
				<th><div class="sort_order"><a class="e.category_id" onclick="sortTable('e.category_id',this)"><?php echo ucwords(CRMText::_('COBALT_EVENTS_TYPE')); ?></div></th>
				<th><?php echo CRMText::_('COBALT_EVENTS_CONTACTS'); ?></th>
				<th><?php echo CRMText::_('COBALT_EVENTS_NOTES'); ?></th>
			</tr>
		</thead>
		<tbody id="events">
			<?php
				$data = array('events'=>$this->events);
				$event_list = CobaltHelperView::getView('events','list','phtml',$data);
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