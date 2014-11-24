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

$mediaURI = \Cobalt\Factory::getApplication()->get('uri.media.full');

    $n = count($this->events);
    for ($i=0; $i<$n; $i++) {

        $k = $i%2;

        $event = $this->events[$i];

        if ($event['type']=='task') {
            $due_date = $event['due_date']!= '0000-00-00 00:00:00' ? DateHelper::formatDate($event['due_date']) : TextHelper::_('COBALT_NA');
            $time = $event['due_date_hour'];
        } else {
            $due_date = DateHelper::formatDate($event['start_time']);
            $time = $event['start_time_hour'];
        }

        $id = ( array_key_exists('parent_id',$event) && $event['parent_id'] ) != 0 ? $event['parent_id'] : $event['id'];
        echo '<tr id="list_row_'.$id.'" class="cobalt_row_'.$k.'">';
             if ($event['completed'] == 1) {
                    $completed = "line-through";
                } else {
                    $completed = "";
                }
            echo '<td>';
                if ( UsersHelper::getLoggedInUser()->id == $event['owner_id'] || UsersHelper::getLoggedInUser()->id == $event['assignee_id'] || UsersHelper::isAdmin() || UsersHelper::getRole() == "exec" ) {
                    echo '<input type="checkbox" name="ids[]" value="'.$id.'" />';
                }
            echo '</td>';
            echo '<td><div class="dropdown"><a data-toggle="dropdown" role="button" class="dropdown-toggle '.$completed.'" id="event_menu_'.$event['id'].'_link" >';
            echo $event['name'];
            echo '</a>';

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

            echo '</div></td>';
            echo '<td class="due_date_column">'.$due_date.' '.DateHelper::formatTime($time,"(".UsersHelper::getTimeFormat().")").'</td>';
            echo '<td>';
                if ($event['deal_name']) { echo '<a href='.RouteHelper::_('index.php?view=deals&layout=deal&id='.$event['deal_id']).'>'.$event['deal_name'].'</a><br />';}
                if ($event['company_name']) { echo '<a href='.RouteHelper::_('index.php?view=companies&layout=company&id='.$event['company_id']).'>'.$event['company_name'].'</a>';}
                if ($event['person_id']) { echo '<a href='.RouteHelper::_('index.php?view=people&layout=person&id='.$event['person_id']).'>'.$event['person_first_name'].' '.$event['person_last_name'].'</a>';}
            echo '</td>';
            echo '<td>'.$event['owner_first_name'].' '.$event['owner_last_name'].'</td>';
            echo '<td>'.$event['assignee_first_name'].' '.$event['assignee_last_name'].'</td>';
            echo '<td>'.ucwords($event['category_name']).'</td>';
            echo '<td class="contacts" ><a href="javascript:void(0);" onclick="Calendar.showEventContactsDialogModal('.$event['id'].');"><img src="'.$mediaURI.'images/card.png'.'"/></a></td>';
            echo '<td class="notes"><a href="javascript:void(0);" onclick="Calendar.openNoteModal(\''.$event['id'].'\',\'event\');"><img src="'.$mediaURI.'images/notes.png'.'"/></a>';
            echo '<div id="event_form_'.$event['id'].'">';
                    echo '<input type="hidden" name="event_id" value="'.$event['id'].'" />';
                    echo '<input type="hidden" name="parent_id" value="'.$event['parent_id'].'" />';
                      if ($event['type'] == "task") {
                        echo '<input type="hidden" name="date" value="'.$event['due_date'].'" />';
                      } else {
                        echo '<input type="hidden" name="date" value="'.$event['start_time'].'" />';
                      }
                    echo '<input type="hidden" name="event_type" value="'.$event['type'].'" />';
                    echo '<input type="hidden" name="repeats" value="'.$event['repeats'].'" />';
                    echo '<input type="hidden" name="type" value="single" />';
           echo '</div>';
            echo '<div class="filters" id="event_menu_'.$event['id'].'">';
                  echo '<ul>';
                   if ( array_key_exists('completed',$event) && $event['completed'] == 1 ) {
                    echo '<li><a href="javascript:void(0);" onclick="markEventIncomplete(this)" >'.TextHelper::_('COBALT_MARK_INCOMPLETE').'</a></li>';
                   } else {
                    echo '<li><a href="javascript:void(0);" onclick="markEventComplete(this)" >'.TextHelper::_('COBALT_MARK_COMPLETE').'</a></li>';
                    echo '<li><a href="javascript:void(0);" onclick="postponeEvent(this,1)" >'.TextHelper::_('COBALT_POSTPONE_1_DAY').'</a></li>';
                    echo '<li><a href="javascript:void(0);" onclick="postponeEvent(this,7)" >'.TextHelper::_('COBALT_POSTPONE_7_DAYS').'</a></li>';
                  }
                    $id = ( array_key_exists('parent_id',$event) && $event['parent_id'] ) != 0 ? $event['parent_id'] : $event['id'];
                    echo '<li><a href="javascript:void(0);" onclick="editEvent('.$id.',\''.$event['type'].'\')" >'.TextHelper::_('COBALT_EDIT').'</a></li>';
                    echo '<li><a href="javascript:void(0);" onclick="deleteEvent(this)" >'.TextHelper::_('COBALT_DELETE').'</a></li>';
                  echo '</ul>';
                echo '</div>';
            echo '</td>';
        echo '</tr>';

    }
