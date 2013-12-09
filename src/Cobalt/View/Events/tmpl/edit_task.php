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

//assign event
$event = $this->event;
$app = JFactory::getApplication();

?>

<div class="validate" id="edit_task_form" method="post">
<ul class="nav nav-tabs" id="myTab">
      <li class="active"><a href="#Task" data-toggle="tab" >Task</a></li>
      <li><a href="#Assignment" data-toggle="tab" >Assignment</a></li>
      <li><a href="#Date" data-toggle="tab">Date</a></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active fade in" id="Task">
          <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_EDIT_TASK_NAME')); ?><span class="required">*</span></div>
            <div class="cobaltValue"><input class="inputbox required" type="text" name="name" value="<?php if(array_key_exists('name',$event)) echo $event['name']; ?>" /></div>
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
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_TASK_TYPE'); ?></div>
            <div class="cobaltValue">
                <select class="inputbox" name="category_id">
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
                <textarea class="inputbox" name="description"><?php if(array_key_exists('description',$event)) echo $event['description']; ?></textarea>
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
                            <input class="inputbox" type="text" name="associate_name" value="<?php echo $event['association_name']; ?>" />
                        </div>
                    <?php } else { ?>
                        <?php if ( $app->input->getVar('association_id') ) { $association_name = $this->association_name;  } else { $association_name = ucwords(TextHelper::_('COBALT_COMPANY_DEAL_OR_PERSON'));} ?>
                        <span class="associate_to"><?php echo $association_name; ?></span>
                        <div style="display:none;" id="associate_to">
                            <input class="inputbox" type="text" name="associate_name" value="" />
                        </div>
                    <?php } ?>
                    <?php $association_id = $app->input->getVar('association_id'); ?>
                    <?php $association_type = $app->input->getVar('association_type'); ?>
                    <?php if ($association_id) { ?>
                        <input type="hidden" name="association_id" value="<?php echo $association_id; ?>" />
                        <input type="hidden" name="association_type" value="<?php echo $association_type; ?>" />
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_TASK_ASSIGN_TO'); ?></div>
            <div class="cobaltValue">
                <select class="inputbox" name="assignee_id">
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
        <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_TASK_DUE_DATE'); ?></div>
        <div class="cobaltValue">
            <div id="due_date_container">
                <?php if (array_key_exists('due_date',$event) ) { ?>
                    <span style="display:none;" class="due_date"><?php echo TextHelper::_('COBALT_EDIT_TASK_DUE_DATE_MESSAGE'); ?></span>
                <div id="due_date">
                    <input id="due_date_input"class="inputbox date_input" type="text" name="due_date_input" value="<?php if (array_key_exists('due_date',$event)) echo DateHelper::formatDate($event['due_date']); ?>" />
                    <input id="due_date_input_hidden" name="due_date" type="hidden" value="<?php if ( array_key_exists('due_date',$event) ) { echo $event['due_date']; } ?>" />
                    <select class="inputbox" name="due_date_hour">
                        <?php
                            $time = DateHelper::getTimeIntervals();
                            echo JHtml::_('select.options', $time, 'value', 'text', $event['due_date_hour'], true);
                        ?>
                    </select>
                </div>
                <?php } else { ?>
                <span class="due_date"><?php echo TextHelper::_('COBALT_EDIT_TASK_DUE_DATE_MESSAGE'); ?></span>
                <div style="display:none;" id="due_date">
                    <input id="due_date_input" class="inputbox date_input" type="text" name="due_date_input" value="<?php if (array_key_exists('due_date',$event)) echo DateHelper::formatDate($event['due_date']); ?>" />
                    <input id="due_date_input_hidden" name="due_date" type="hidden" value="<?php if ( array_key_exists('due_date',$event) ) { echo $event['due_date']; } ?>" />
                    <select class="inputbox" name="due_date_hour">
                        <?php
                            $time = DateHelper::getTimeIntervals();
                            echo JHtml::_('select.options', $time, 'value', 'text', $event['due_date_hour'], true);
                        ?>
                    </select>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="cobaltRow">
        <div class="cobaltField"><?php echo TextHelper::_('COBALT_END_DATE'); ?></div>
        <div class="cobaltValue">
                <?php if ( array_key_exists('end_date',$event) && $event['end_date'] != null ) { $hidden = "style='display:none;'"; $show = ""; } else { $hidden = ""; $show = "style='display:none;'"; } ?>
                <span <?php echo $hidden; ?> class="end_date"><?php echo TextHelper::_('COBALT_END_DATE_MESSAGE'); ?></span>
                <div <?php echo $show; ?> id="end_date">
                    <input id="end_date_input" class="inputbox date_input" type="text" name="end_date_input" value="<?php if (array_key_exists('end_date',$event)) echo DateHelper::formatDate($event['end_date']); ?>" />
                    <input id="end_date_input_hidden" name="end_date" type="hidden" value="<?php if ( array_key_exists('end_date',$event) ) { echo $event['end_date']; } ?>" />
                </div>
        </div>
    </div>
    <div class="cobaltRow">
         <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_TASK_REPEAT'); ?></div>
        <div class="cobaltValue">
            <select class="inputbox" name="repeats">
                <?php
                    $repeat_intervals = EventHelper::getRepeatIntervals();
                    echo JHtml::_('select.options', $repeat_intervals, 'value', 'text', $event['repeats'], true);
                ?>
            </select>
        </div>
    </div>
    <?php if ( array_key_exists('repeats',$event) && $event['repeats'] != "none" ) { ?>
    <div class="cobaltRow">
        <label class="checkbox">
        </label>
      <div class="cobaltField"><?php echo TextHelper::_('COBALT_UPDATE_FUTURE_EVENTS'); ?></div>
      <div class="cobaltValue"><input class="inputbox" type="checkbox" name="update_future_events" checked="checked" /></div>
    </div>
    <?php } ?>
      </div>
    </div>
    <?php
        if ( array_key_exists('id',$event) ) {
            echo '<input type="hidden" value="'.$event['id'].'" name="id" />';
        }
        if ( $app->input->get('parent_id') > 0 ) {
            echo '<input type="hidden" name="parent_id" value="'.$app->input->get('parent_id').'" />';
        }
    ?>
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="type" value="task" />
</div>
