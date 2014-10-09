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

$app = \Cobalt\Container::fetch('app');

if ( $app->input->get('loc') ) {
    $model = new CobaltModelEvent();
    $events = $model->getEvents($app->input->get('loc'),null,$app->input->get($app->input->get('loc').'_id'));
    $this->events = $events;
}

if ( count($this->events) > 0 ) {
    foreach ($this->events as $event) {
        echo '<li>';
        echo "<a href='".RouteHelper::_('index.php?view=events&id='.$event['id'])."'>";
            echo '<span class="ui-li-count">'.DateHelper::formatDate($event['due_date']).'</span>';
            if ($event['completed'] == 1) {
                $completed = "line-through";
            } else {
                $completed = "";
            }
            echo "<h3 class='ui-li-heading'>".$event['name']."</h3>";
           switch ($event['association_type']) {
               case "company":
                   echo "<div class='ui-li-desc'>(".$event['company_name'].")</div>";
                   break;
               case "deal":
                   echo "<div class='ui-li-desc'>(".$event['deal_name'].")</div>";
                   break;
               case "person":
                   echo "<div class='ui-li-desc'>(".$event['person_first_name']." ".$event['person_last_name'].")</div>";
                   break;
           }
        echo '</a></li>';
    }
}
