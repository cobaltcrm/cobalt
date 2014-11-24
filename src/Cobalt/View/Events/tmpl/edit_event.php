<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

defined('_CEXEC') or die('Restricted access');

//assign event
$event = $this->event;
$app = \Cobalt\Factory::getApplication();

?>
<form class="validate" id="edit_event_form">
    <ul class="nav nav-tabs" id="myTab">
      <li class="active"><a href="#Event" data-toggle="tab" >Event</a></li>
      <li><a href="#Assignment" data-toggle="tab" >Assignment</a></li>
      <li><a href="#Date" data-toggle="tab">Date</a></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active fade in" id="Event">
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_TASK_NAME'); ?><span class="required">*</span></div>
                <div class="cobaltValue"><input class="form-control inputbox" type="text" name="name" value="<?php if ( array_key_exists('name',$event) ) echo $event['name']; ?>"/></div>
            </div>
            <?php if ( array_key_exists('id',$event) && $event['id'] > 0 ) { ?>
                <div class="cobaltRow">
                    <div class="cobaltField">
                        <?php echo TextHelper::_('COBALT_CREATED_BY'); ?>
                    </div>
                    <div class="cobaltValue">
                        <?php echo $event['owner_first_name'].' '.$event['owner_last_name']; ?>
                    </div>
                </div>
            <?php } ?>
            <div class="cobaltRow">
                <div class="cobaltField">
                    <?php echo TextHelper::_('COBALT_EDIT_TASK_TYPE'); ?>
                </div>
                <div class="cobaltValue">
                            <select class="form-control" name="category_id">
                                <?php
                                    $categories = EventHelper::getCategories();
                                    echo JHtml::_('select.options', $categories, 'value', 'text', $event['category_id'], true);
                                ?>
                            </select>
                </div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_TASK_DESCRIPTION'); ?></div>
                <div class="cobaltValue">
                    <textarea class="form-control" name="description"><?php if(array_key_exists('description',$event)) echo $event['description']; ?></textarea>
                </div>
            </div>
      </div>
      <div class="tab-pane fade" id="Assignment">
              <div class="cobaltRow">
                <div class="cobaltField">
                    <?php echo TextHelper::_('COBALT_ASSOCIATE'); ?>
                </div>
                <div class="cobaltValue">
                    <div id="associate_to_container">
                        <?php if ( array_key_exists('association_name',$event) ) { ?>
                            <div id="associate_to">
                                <input class="form-control" type="text" name="associate_name" value="<?php echo $event['association_name']; ?>" />
                            </div>
                        <?php } else { ?>
                            <?php if ( $app->input->get('association_id') ) { $association_name = $this->association_name;  } else { $association_name = ucwords(TextHelper::_('COBALT_COMPANY_DEAL_OR_PERSON'));} ?>
                            <span class="associate_to"><?php echo $association_name; ?></span>
                            <div style="display:none;" id="associate_to">
                                <input class="form-control" type="text" name="associate_name" value="" />
                            </div>
                        <?php } ?>
                        <?php $association_id = $app->input->get('association_id'); ?>
                        <?php $association_type = $app->input->get('association_type'); ?>
                        <?php if ($association_id) { ?>
                            <input type="hidden" name="association_id" value="<?php echo $association_id; ?>" />
                            <input type="hidden" name="association_type" value="<?php echo $association_type; ?>" />
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField">
                    <?php echo TextHelper::_('COBALT_EDIT_TASK_ASSIGN_TO'); ?>
                </div>
                <div class="cobaltValue">
                            <select class="form-control" name="assignee_id">
                                <?php
                                    $users = array();
                                    $users[UsersHelper::getUserId()] = TextHelper::_('COBALT_ME');
                                    $users += DropdownHelper::getUserNames();
                                    echo JHtml::_('select.options', $users, 'value', 'text', $event['assignee_id'], true);
                                ?>
                            </select>
                </div>
            </div>
      </div>
      <div class="tab-pane fade" id="Date">
          <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_EVENT_START_TIME'); ?><span class="required">*</span></div>
            <div class="cobaltValue">
                        <input id="start_time" class="form-control inputbox date_input" type="text" value="<?php if ( array_key_exists('start_time',$event) ) echo DateHelper::formatDate($event['start_time']); ?>" name="start_time_input" />
                        <input id="start_time_hidden" name="start_time" type="hidden" value="<?php if ( array_key_exists('start_time',$event) ) { echo $event['start_time']; } ?>" />
                        <select class="form-control" name="start_time_hour">
                            <?php
                                $time = DateHelper::getTimeIntervals();
                                echo JHtml::_('select.options', $time, 'value', 'text', $event['start_time_hour'], true);
                            ?>
                        </select>
            </div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_EVENT_END_TIME'); ?><span class="required">*</span></div>
            <div class="cobaltValue">
                        <input id="end_time" class="required form-control date_input" type="text" value="<?php if ( array_key_exists('end_time',$event) ) echo DateHelper::formatDate($event['end_time']); ?>" name="end_time_input" />
                        <input id="end_time_hidden" type="hidden" name="end_time" value="<?php if ( array_key_exists('end_time',$event) ) { echo $event['end_time']; } ?>" />
                        <select class="form-control" name="end_time_hour">
                            <?php
                                $time = DateHelper::getTimeIntervals();
                                echo JHtml::_('select.options', $time, 'value', 'text', $event['end_time_hour'], true);
                            ?>
                        </select>
            </div>
        </div>
          <div class="checkbox">
              <label>
                  <input type="checkbox" name="all_day" /><?php echo TextHelper::_('COBALT_EDIT_EVENT_ALL_DAY_MESSAGE'); ?>
              </label>
          </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_TASK_REPEAT'); ?></div>
            <div class="cobaltValue">
                        <select class="form-control" name="repeats">
                            <?php
                                $repeat_intervals = EventHelper::getRepeatIntervals();
                                echo JHtml::_('select.options', $repeat_intervals, 'value', 'text', $event['repeats'], true);
                            ?>
                        </select>
            </div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_END_DATE'); ?></div>
            <div class="cobaltValue">
                <?php if ( array_key_exists('end_date',$event) && $event['end_date'] != null ) { $hidden = "style='display:none;'"; $show = ""; } else { $hidden = ""; $show = "style='display:none;'"; } ?>
                    <span <?php echo $hidden; ?> class="end_date"><?php echo TextHelper::_('COBALT_END_DATE_MESSAGE'); ?></span>
                    <div <?php echo $show; ?> id="end_date">
                        <input id="end_date" class="form-control date_input" type="text" name="end_date_input" value="<?php if (array_key_exists('end_date',$event)) echo DateHelper::formatDate($event['end_date']); ?>" />
                        <input id="end_date_input_hidden" name="end_date" type="hidden" value="<?php if ( array_key_exists('end_date',$event) ) { echo $event['end_date']; } ?>" />
                    </div>
            </div>
        </div>
        <?php if ( array_key_exists('repeats',$event) && $event['repeats'] != "none" ) { ?>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_UPDATE_FUTURE_EVENTS'); ?></div>
                <div class="cobaltValue"><input type="checkbox" name="update_future_events" checked="checked" /></div>
            </div>
        <?php } ?>
      </div>
    </div>
    <?php
        if ( array_key_exists('id',$event) ) {
            echo '<input type="hidden" name="id" value="'.$event['id'].'" />';
        }
        if ( $app->input->get('parent_id') > 0 ) {
            echo '<input type="hidden" name="parent_id" value="'.$data['parent_id'].'" />';
        }
    ?>
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="model" value="event">
    <input type="hidden" name="type" value="event" />
    <input type="hidden" name="layout" value="edit_event" />
</form>
