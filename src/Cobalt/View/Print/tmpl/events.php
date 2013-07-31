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

 $events = $this->info;
    if ( count($events) > 0 ) {
        foreach ($events as $event) {
         echo '<div class="com_cobalt_task_event">';
                    $display_date = ( $event['type'] == "event" ) ? $event['start_time'] : $event['due_date'];
                    if ( $display_date == "" ) $display_date = TextHelper::_('COBALT_NA');
                    echo '<div class="date">'.DateHelper::formatDate($display_date).'</div>';
                    if ($event['completed'] == 1) {
                        $completed = "line-through";
                    } else {
                        $completed = "";
                    }
                    echo '<span class="'.$completed.'" >';
                    echo '<b>'.$event['name'].'</b>';
                    echo '</a>';
                   switch ($event['association_type']) {
                       case "company":
                           echo "<div class='task_association'>(".$event['company_name'].")</div>";
                           break;
                       case "deal":
                           echo "<div class='task_association'>(".$event['deal_name'].")</div>";
                           break;
                       case "person":
                           echo "<div class='task_association'>(".$event['person_first_name']." ".$event['person_last_name'].")</div>";
                           break;
                   }
                   if ( array_key_exists('category_name',$event)) {
                           echo '<div class="task_category">';
                               echo $event['category_name'];
                           echo '</div>';
                       }
                   echo '<div class="task_description">';
                           echo $event['description'];
                   echo '</div>';
          echo '</div>';
} }
