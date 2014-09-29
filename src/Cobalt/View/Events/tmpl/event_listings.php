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

        $current_heading = "";

        if ( count($this->events) > 0 ) {
            foreach ($this->events as $event) {

              $display_date = ( $event['type'] == "event" ) ? $event['start_time']." ".$event['start_time_hour'] : $event['due_date']." ".$event['due_date_hour'];
              $time = ( $event['type'] == "event" ) ? $event['start_time_hour'] : $event['due_date_hour'];

              $display_date = $display_date == "" ? TextHelper::_('COBALT_NA') : DateHelper::formatDate($display_date,false,false);

              $relative_date_title = DateHelper::getRelativeDate($display_date);

              if ($event['completed'] == 1) {
                  $completed = "line-through";
              } else {
                  $completed = "";
              }

                if ($current_heading!=$relative_date_title) {
                  if ($current_heading!="") {
                    echo '</table>';
                  }

                  echo '<h4>'.$relative_date_title.'</h4>';

                  $current_heading = $relative_date_title;
                  echo '<table class="table table-striped table-hover">';
                }

                echo '<tr class="com_crmery_task_event" id="com_crmery_listing_'.$event['id'].'">';

                    echo '<td><input type="checkbox" class="event_list_checkbox" name="item_id['.$event['id'].']" /></td>';

                    echo '<td><div class="dropdown">';
                    echo '<a class="dropdown-toggle '.$completed.'" data-toggle="dropdown" role="button" href="javascript:void(0);" id="event_menu_'.$event['id'].'_link">'.$event['name'].'</a>';
                          echo '<ul class="dropdown-menu" role="menu" aria-labelledby="event_menu_'.$event['id'].'_link">';
                           if ($event['completed'] == 1) {
                            echo '<li><a href="javascript:void(0);" onclick="Calendar.markEventIncomplete(this)" >'.TextHelper::_('COBALT_MARK_INCOMPLETE').'</a></li>';
                           } else {
                            echo '<li><a href="javascript:void(0);" onclick="Calendar.markEventComplete(this)" >'.TextHelper::_('COBALT_MARK_COMPLETE').'</a></li>';
                            echo '<li><a href="javascript:void(0);" onclick="Calendar.postponeEvent(this,1)" >'.TextHelper::_('COBALT_POSTPONE_1_DAY').'</a></li>';
                            echo '<li><a href="javascript:void(0);" onclick="Calendar.postponeEvent(this,7)" >'.TextHelper::_('COBALT_POSTPONE_7_DAYS').'</a></li>';
                          }
                            $id = ( array_key_exists('parent_id',$event) && $event['parent_id'] ) != 0 ? $event['parent_id'] : $event['id'];
                            echo '<li><a href="javascript:void(0);" onclick="Calendar.editEvent('.$id.',\''.$event['type'].'\')" >'.TextHelper::_('COBALT_EDIT').'</a></li>';
                            echo '<li><a href="javascript:void(0);" onclick="Calendar.removeCalendarEvent(this)" >'.TextHelper::_('COBALT_DELETE').'</a></li>';
                          echo '</ul>';
                        echo '</div>';
                        echo '<div id="event_form_'.$event['id'].'">';
                            echo '<input type="hidden" name="event_id" value="'.$event['id'].'" />';
                            echo '<input type="hidden" name="parent_id" value="'.$event['parent_id'].'" />';
                              if ($event['type'] == "task") {
                                echo '<input type="hidden" name="due_date" value="'.$event['due_date'].'" />';
                              } else {
                                echo '<input type="hidden" name="start_time" value="'.$event['start_time'].'" />';
                                echo '<input type="hidden" name="end_time" value="'.$event['end_time'].'" />';
                              }
                            echo '<input type="hidden" name="event_type" value="'.$event['type'].'" />';
                            echo '<input type="hidden" name="repeats" value="'.$event['repeats'].'" />';
                            echo '<input type="hidden" name="type" value="single" />';
                        echo '</div>';
                    echo '</td>';

                    $display_date = ( $event['type'] == "event" ) ? $event['start_time'] : $event['due_date'];
                    if ( $display_date == "" ) $display_date = TextHelper::_('COBALT_NA');
                    echo '<td class="date"><small>'.DateHelper::formatDateString($display_date).' '.DateHelper::formatTime($time,"(".UsersHelper::getTimeFormat().")").'</small></td>';

                   switch ($event['association_type']) {
                      case "company":
                           echo "<td><i class='glyphicon glyphicon-briefcase'></i> <a href='".RouteHelper::_('index.php?view=companies&layout=company&id='.$event['company_id'])."'>".$event['company_name']."</a></td>";
                           break;
                      case "deal":
                           echo "<td><i class='glyphicon glyphicon-tag'></i> <a href='".RouteHelper::_('index.php?view=deals&layout=deal&id='.$event['deal_id'])."'>".$event['deal_name']."</a></td>";
                           break;
                      case "person":
                           echo "<td><i class='glyphicon glyphicon-user'></i> <a href='".RouteHelper::_('index.php?view=people&layout=person&id='.$event['person_id'])."'>".$event['person_first_name']." ".$event['person_last_name']."</a></td>";
                           break;
                      default:
                          echo "<td>&nbsp;</td>";
                          break;
                   }
                echo '</tr>';
            }

            echo '</table>';

        }
