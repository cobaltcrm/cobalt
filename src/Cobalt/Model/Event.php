<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
# Version 1.170
-------------------------------------------------------------------------*/

namespace Cobalt\Model;

use Cobalt\Table\EventTable;
use JFactory;
use Cobalt\Helper\RouteHelper;
use Joomla\Registry\Registry;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\ActivityHelper;
use Cobalt\Helper\TextHelper;


// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Event extends DefaultModel
{
    public $_id = null;
    public $deal_id = null;
    public $current_events = false;
    public $published = 1;
    public $view = null;
    public $layout = null;
    public $start_date = null;
    public $end_date = null;
    public $loc = null;
    public $filter_order = null;
    public $filter_order_Dir = null;
    public $completed = null;

    public function __construct()
    {
        parent::__construct();
        $app = \Cobalt\Container::fetch('app');
        $this->view = $app->input->get('view');
        $this->layout = $app->input->get('layout','list');

    }

    /**
     * Method to store a record
     *
     * @return boolean True on success
     */
    public function store($data = null)
    {

        $app = \Cobalt\Container::fetch('app');
        $db = JFactory::getDBO();

        //Load Tables
        $row = new EventTable;
        $oldRow = new EventTable;

        $data = ( $data == null ) ? $_POST: $data;

        //date generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));

        if ( !array_key_exists('id',$data) ) {
            $data['created'] = $date;
            $status = "created";
        } else {
            $row->load($data['id']);
            $oldRow->load($data['id']);
            $status = "updated";
        }

        if ( array_key_exists('completed',$data) && $data['completed'] == 1 ) {
            $status = "completed";
            $data['actual_close'] = $date;
        }

        $data['modified'] = $date;
        $data['owner_id'] = array_key_exists('owner_id',$data) ? $data['owner_id'] : UsersHelper::getUserId();
        $data['assignee_id'] = array_key_exists('assignee_id',$data) ? $data['assignee_id'] : UsersHelper::getUserId();

        //clear any data we dont want to bind
        if ( array_key_exists('associate_name',$data) ) unset($data['associate_name']);

        //create event dates
        if ( array_key_exists('type',$data) && $data['type'] == "task" && array_key_exists('due_date_hour',$data) ) {
            $data['due_date'] = $data['due_date']." ".$data['due_date_hour'];
        }
        if ( array_key_exists('type',$data) && $data['type'] == "event" && array_key_exists('start_time_hour',$data) ) {
            $data['start_time'] = $data['start_time']." ".$data['start_time_hour'];
        }
        if ( array_key_exists('type',$data) && $data['type'] == "event" && array_key_exists('end_time_hour',$data) ) {
            $data['end_time'] = $data['end_time']." ".$data['end_time_hour'];
        }

        if ( array_key_exists('due_date',$data) && $data['due_date'] != "" && $data['due_date'] != "0000-00-00 00:00:00" && !is_null($data['end_date']) ) {
            $data['due_date'] = DateHelper::formatDBDate($data['due_date']);
        }
        if ( array_key_exists('start_date',$data) && $data['start_date'] != "" && $data['start_date'] != "0000-00-00 00:00:00" && !is_null($data['end_date']) ) {
            $data['start_date'] = DateHelper::formatDBDate($data['start_date']);
        }

        if ( array_key_exists('end_date',$data) && $data['end_date'] != "" && $data['end_date'] != "0000-00-00 00:00:00" && !is_null($data['end_date']) ) {
            $data['end_date'] = DateHelper::formatDBDate($data['end_date']);
        }

        //all day events
        $data['all_day'] = ( array_key_exists('all_day',$data) && $data['all_day'] == true ) ? 1 : 0;

        if ( array_key_exists('update_future_events',$data) && $data['update_future_events'] == 0 && array_key_exists('parent_id',$data) && $data['parent_id'] != 0 ) {

            if ( array_key_exists('due_date',$data) ) $date = $data['due_date'];
            if ( array_key_exists('start_time',$data) ) $date = $data['start_time'];

            $this->addExcludes($data['parent_id'],$date);
            unset($data['id']);
            unset($data['repeats']);
        }

        if ( array_key_exists('start_time',$data) && $data['start_time'] != "" && $data['start_time'] != "0000-00-00 00:00:00" && !is_null($data['start_time']) ) {
            $data['start_time'] = DateHelper::formatDBDate($data['start_time']);
        }

        if ( array_key_exists('end_time',$data) && $data['end_time'] != "" && $data['end_time'] != "0000-00-00 00:00:00" && !is_null($data['end_time']) ) {
            $data['end_time'] = DateHelper::formatDBDate($data['end_time']);
        }

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            $this->setError($db->getErrorMsg());

            return false;
        }

        // $dispatcher = JEventDispatcher::getInstance();
        // $dispatcher->trigger('onBeforeEventSave', array(&$row));

        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($db->getErrorMsg());

            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($db->getErrorMsg());

            return false;
        }

        if ( $oldRow->id && $row->type == "task" && ( $row->due_date > $oldRow->due_date )) {
            $status = "postponed";
        }

        if ( $oldRow->id && $row->type == "event" && ( $row->start_time > $oldRow->start_time ) ) {
            $status = "postponed";
        }

         //event id
        $event_id = ( array_key_exists('id',$data) ) ? $data['id'] : $db->insertId();

        $row->load($event_id);

        ActivityHelper::saveActivity($oldRow, $row,'event', $status);

        //if we receive information concerning cf tables
        if ( array_key_exists('association_id',$data) ) {
            $postcfdata = array(
                'association_id'    => $data['association_id'],
                'event_id'          => $event_id
            );
            $this->eventsCf($postcfdata,$data['association_type']);
        }

        // $dispatcher = JEventDispatcher::getInstance();
        // $dispatcher->trigger('onAfterEventSave', array(&$row));
        return $event_id;

    }

    /*
     * Method to access tasks
     *
     * @return array
     */
    public function getEvents($loc=null,$user=null,$association=null)
    {
        $app = \Cobalt\Container::fetch('app');
        $loc = $loc ? $loc : $this->loc;

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select("e.*,".
                       "a.*,".
                       "ci.name AS category_name,".
                       "c.name as company_name, c.id as company_id,".
                       "d.name as deal_name,d.id as deal_id,".
                       "p.first_name as person_first_name, p.last_name as person_last_name,p.id as person_id,".
                       "assignee.color AS assignee_color,".
                       'assignee.first_name AS assignee_first_name,assignee.last_name AS assignee_last_name,'.
                       "owner.first_name as owner_first_name, owner.last_name as owner_last_name ".
                       "FROM #__events AS e");
        $query->leftJoin("#__events_categories AS ci ON ci.id = e.category_id");
        $query->leftJoin("#__events_cf AS a ON e.id = a.event_id");
        $query->leftJoin("#__companies AS c ON a.association_type = 'company' AND a.association_id = c.id AND c.published>0");
        $query->leftJoin("#__deals AS d ON a.association_type = 'deal' AND a.association_id = d.id AND d.published>0");
        $query->leftJoin("#__people AS p ON a.association_type = 'person' AND a.association_id = p.id AND p.published>0");
        $query->leftJoin('#__users AS assignee ON assignee.id = e.assignee_id');
        $query->leftJoin('#__users AS owner ON owner.id = e.owner_id');

        //gather info
        $user_role = UsersHelper::getRole();
        $user_id = UsersHelper::getUserId();
        $team_id = UsersHelper::getTeamId();

        //filter based on user role
        if ($user_role != 'exec' && $this->view != "print") {
            //manager filter
            if ($user_role == 'manager') {
                $query->where('(assignee.team_id = '.$team_id.' OR owner.team_id = '.$team_id.")");
            } else {
            //basic user filter
                $query->where("(e.assignee_id = ".$user_id." OR e.owner_id =".$user_id.")");
            }
        }

        //search for certain user events
        if ($user && $this->view != "print") {
            if ($user == $user_id) {
                $query->where("(e.assignee_id=".$user_id.' OR e.owner_id='.$user_id.')');
            } elseif ($user != 'all') {
                $query->where(array("e.assignee_id=".$user));
            }
        }

        if (!$association) {
            $association = $app->input->get('association_id') ? $app->input->get('association_id') : $app->input->get('id');
        }
        $association_type = $app->input->get('association_type') ? $app->input->get('association_type') : $app->input->get('layout');
        $association_types = array("company","deal","person");

        if ($association) {
            $association_type = $association_type ? $association_type : $loc;
            if ($association_type == "company") {
                if ( is_array($association)) {
                    $query->where("(p.company_id=".$association." OR d.company_id=".$association." OR ( a.association_type=".$db->quote("company")." AND a.association_id IN(".implode(",",$association).") ))");
                } else {
                    $query->where("(p.company_id=".$association." OR d.company_id=".$association." OR ( a.association_type=".$db->quote("company")." AND a.association_id=".$association." ))");
                }
            } else {
                if ( is_array($association) ) {
                    $query->where("a.association_id IN(".implode(",",$association).")");
                } else {
                    $query->where("a.association_id=".$association);
                }
                $query->where("a.association_type=".$db->quote($association_type));
            }
        } elseif ( $association_type && in_array($association_type,$association_types)) {
            $query->where("a.association_type=".$db->Quote($association_type));
        } else {
            /** hide events associated with archived deals **/
            $query->where("(d.archived=0 OR d.archived IS NULL)");
        }

        if ($this->_id != null) {
            if ( is_array($this->_id) ) {
                $query->where("e.id IN (".implode(',',$this->_id).")");
            } else {
                $query->where("e.id=$this->_id");
            }
        }

        if ($this->current_events) {
           $now = DateHelper::formatDBDate(date('Y-m-d'));
           $query->where('e.due_date != "0000-00-00 00:00:00" AND e.due_date >="'.$now.'"');
        }

        /** Filter by status **/
        $status_filter = $this->getState('Event.'.$this->view.'_'.$this->layout.'_status');
        if ($status_filter != null && $this->view != "print") {
            $query->where("e.completed=$status_filter");
        } else {
            if ($this->completed != null) {
                if ($this->completed == 'true') {
                    $query->where("e.completed=1");
                } elseif ($this->completed != 'false') {
                    $query->where("e.completed=".$this->completed);
                }
            } else {
                $query->where("e.completed=0");
            }
        }

        /** Filter by type **/
        $type_filter = $this->getState('Event.'.$this->view.'_'.$this->layout.'_type');
        if ($type_filter != null && $type_filter != "all" && $this->view != "print") {
            $query->where("e.type='$type_filter'");
        }

        /** Filter by category **/
        $category_filter = $this->getState('Event.'.$this->view.'_'.$this->layout.'_category');
        if ($category_filter != null && $category_filter != "any" && $this->view != "print") {
            $query->where("e.category_id=$category_filter");
        }

        /** Filter by due date **/
        $due_date_filter = $this->getState('Event.'.$this->view.'_'.$this->layout.'_due_date');
        if ($due_date_filter != null && $due_date_filter != "any" && $this->view != "print") {
            $date = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));
            switch ($due_date_filter) {
                case "today":
                    $tomorrow = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() + (1*24*60*60)));
                    $query->where("((e.due_date >= '$date' AND e.due_date < '$tomorrow') OR (e.start_time >= '$date' AND e.start_time < '$tomorrow'))");
                break;
                case "tomorrow":
                    $tomorrow = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() + (1*24*60*60)));
                    $day_after_tomorrow = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() + (2*24*60*60)));
                    $query->where("((e.due_date >= '$tomorrow' AND e.due_date < '$day_after_tomorrow') OR (e.start_time >= '$tomorrow' AND e.start_time < '$day_after_tomorrow'))");
                break;
                case "this_week":
                    $date_info = getDate(strtotime($date));
                    $today = $date_info['wday'];
                    $days_to_remove = -1 + $today;
                    $days_to_add = 5 - $today;
                    $beginning_of_week = DateHelper::formatDBDate(date('Y-m-d 00:00:00',strtotime($date." - $days_to_remove days")));
                    $end_of_week = DateHelper::formatDBDate(date('Y-m-d 00:00:00',strtotime($date." + $days_to_add days")));
                    $query->where("((e.due_date >= '$beginning_of_week' AND e.due_date < '$end_of_week') OR (e.start_time >= '$beginning_of_week' AND e.start_time < '$end_of_week'))");
                break;
                case "past_due":
                    $query->where("((e.due_date < '$date' AND e.due_date != '0000-00-00 00:00:00') OR (e.start_time < '$date' AND e.start_time != '0000-00-00 00:00:00'))");
                break;
                case "not_past_due":
                    $query->where("((e.due_date >= '$date' AND e.due_date != '0000-00-00 00:00:00') OR (e.start_time >= '$date' AND e.start_time != '0000-00-00 00:00:00'))");
                break;
            }
        }

        /** Filter by assignee id **/
        $assignee_id_filter = $this->getState('Event.'.$this->view.'_'.$this->layout.'_assignee_id');
        $assignee_filter_type = $this->getState('Event.'.$this->view.'_'.$this->layout.'_assignee_filter_type');
        if ($loc != "calendar" && $assignee_id_filter != null && $assignee_id_filter != 'all' && $this->view != "print") {
            if ($assignee_filter_type == "team") {
                $team_members = UsersHelper::getTeamUsers($assignee_id_filter,TRUE);
                $query->where("e.assignee_id IN(".implode(',',$team_members).")");
            } else {
                $query->where("e.assignee_id=$assignee_id_filter");
            }
        }

        /** Filter by association type **/
        $association_type_filter = $this->getState('Event.'.$this->view.'_'.$this->layout.'_association_type');
        if ($association_type_filter != null && $association_type_filter != "any" && !$association && $assignee_id_filter != 'all' && $this->view != "print" /*&& !$assignee_id_filter */) {
            $query->where("a.association_type='".$association_type_filter."'");
        }

        $query->where("e.published=".$this->published);

        if ($this->start_date) {
            $query->where("(e.due_date >= '".$this->start_date."' OR e.start_time >= '".$this->start_date."' OR e.repeats != 'none' )");
        }

        if ($this->end_date) {
            $query->where("(e.due_date < '".$this->end_date."' OR e.end_time < '".$this->end_date."' OR e.repeats != 'none' )");
        }

        if ($this->deal_id > 0) {
            $query->where("(a.association_id=".$this->deal_id." AND a.association_type='deal')");
        }

        $this->filter_order = $this->getState('Event.'.$this->view.'_'.$this->layout.'_filter_order');
        $this->filter_order_Dir = $this->getState('Event.'.$this->view.'_'.$this->layout.'_filter_order_Dir');
        $query->order($this->filter_order . ' ' . $this->filter_order_Dir);

        /** ------------------------------------------
         * Set query limits and load results
         */
        if (  $this->getState("Event.".$this->view.'_'.$this->layout.'_limit') != 0 ) {
            $query .= " LIMIT ".($this->getState("Event.".$this->view.'_'.$this->layout.'_limit'))." OFFSET ".($this->getState("Event.".$this->view.'_'.$this->layout.'_limitstart'));
        }

        $db->setQuery($query);
        $rows = $db->loadAssocList();

        /**
         * VIRTUAL EVENT GENERATION -----------------------------------------
         *
         * Only generate virtual events for the current month // year
         * Recurring events past the specified dates will have to be loaded via ajax on the clients end
         * While generating dates if an event is in the excludes field we will not include that in the generation
         * This accounts for deleted and/or unique modified events that expand from the recurring series, OR
         * assign the event and event_parent field and dont display if we find an event_parent filled
        */

        $rowsToAdd = array();
        if ( count($rows) > 0 ) {
            foreach ($rows as $key=>$event) {

                $rows[$key]['association_link'] = $association_link = "";
                $rows[$key]['association_link_lang'] = $association_link_lang = "";

                if ($event['association_type'] != null) {
                    switch ($event['association_type']) {
                        case "company":
                            $view = "companies";
                        break;
                        case "deal":
                            $view = "deals";
                        break;
                        case "person":
                            $view = "people";
                        break;
                    }
                    $rows[$key]['association_link'] = $association_link = RouteHelper::_('index.php?view='.$view."&layout=".$event['association_type']."&id=".$event['association_id']);
                    $rows[$key]['association_link_lang'] = $association_link_lang = ucwords(TextHelper::_("COBALT_ASSOCIATION_LINK_".strtoupper($event['association_type'])));
                }

                if ($event['type'] == "event") {
                    $stime = explode(" ",$event['start_time']);
                    $rows[$key]['start_time_hour'] = $stime[1];
                    $etime = explode(" ",$event['end_time']);
                    $rows[$key]['end_time_hour'] =   $etime[1];
                    $rows[$key]['start_time'] = $stime[0];
                    $rows[$key]['end_time'] = $etime[0];
                } else {
                    $dtime = explode(" ",$event['due_date']);
                    $rows[$key]['due_date_hour'] = $dtime[1];
                    $rows[$key]['due_date'] = $dtime[0];
                }

                //Append server flag
                $rows[$key]['server'] = true;

                //Determine if event is recurring
                if ( array_key_exists('repeats',$event) && $event['repeats'] != 'none' ) {

                    //Get current date so we know when to stop looping for event virtualization
                    $date = DateHelper::formatDBDate(date("Y-m-d H:i:s"));
                    $start_month = $this->start_date != null ? $this->start_date : DateHelper::formatDBDate(date("Y-m-1 00:00:00"));
                    $end_month = $this->end_date != null ? $this->end_date : date("Y-m-1 00:00:00", strtotime($date . " +1 month"));

                    $dates = array();

                    //Get excluded dates
                    $excludes = ( array_key_exists('excludes',$event) ) ? unserialize($event['excludes']) : null;
                    $excludes = ( count($excludes)>0 && $excludes != null ) ? $excludes : array();

                    //Determine which field we should increment and base repetitions off of
                    $event['start_time'] = ( $event['type'] == 'task' ) ? $event['due_date'] : $event['start_time'];
                    $event['end_time'] = ( $event['type'] == 'task' ) ? $event['due_date'] : $event['end_time'];

                    $initialTime = $event['start_time'];
                    $initialKey = $key;

                    //Determine virtual events to generate
                    switch ($event['repeats']) {
                    //Daily
                        case "daily":
                            if ( isset($event['end_date']) && strtotime($event['end_date']) > 0 ) {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) ) && ( isset($event['end_date']) && strtotime($event['start_time']) < strtotime($event['end_date']) )) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . " +1 days"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 days"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( !in_array($event['start_time'],$excludes) && !$past_due ) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            } else {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) ) ) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . " +1 days"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 days"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( !in_array($event['start_time'],$excludes) && !$past_due ) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            }
                            break;
                    //Weekdays
                        case "weekdays":
                            $days = array( 1,2,3,4,5 );
                            if ( isset($event['end_date']) && strtotime($event['end_date']) > 0 ) {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) ) && ( isset($event['end_date']) && strtotime($event['start_time']) < strtotime($event['end_date']) ) ) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . "+1 days"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 days"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( in_array(date('w',strtotime($event['start_time'])),$days) && !in_array($event['start_time'],$excludes)  ) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            } else {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) ) ) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . "+1 days"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 days"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( in_array(date('w',strtotime($event['start_time'])),$days) && !in_array($event['start_time'],$excludes)  ) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            }
                            break;
                    //Weekly
                        case "weekly":
                            if ( isset($event['end_date']) && strtotime($event['end_date']) > 0 ) {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) ) && ( isset($event['end_date']) && strtotime($event['start_time']) < strtotime($event['end_date']) ) ) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . "+1 week"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 week"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( !in_array($event['start_time'],$excludes) && !$past_due ) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            } else {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) )) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . "+1 week"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 week"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( !in_array($event['start_time'],$excludes) && !$past_due ) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            }
                            break;
                    //Weekly Monday Wednesday and Friday
                        case "weekly-mwf":
                            $days = array ( 1,3,5 );
                            if ( isset($event['end_date']) && strtotime($event['end_date']) > 0 ) {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) ) && ( isset($event['end_date']) && strtotime($event['start_time']) < strtotime($event['end_date']) ) ) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . "+1 day"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 day"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( in_array(date('w',strtotime($event['start_time'])),$days) && !in_array($event['start_time'],$excludes)  && !$past_due) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            } else {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) )) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . "+1 day"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 day"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( in_array(date('w',strtotime($event['start_time'])),$days) && !in_array($event['start_time'],$excludes)  && !$past_due) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            }
                            break;
                    //Weekly Tuesday Thursday
                        case "weekly-tr":
                            $days = array ( 2,4 );
                            if ( isset($event['end_date']) && strtotime($event['end_date']) > 0 ) {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) ) && ( isset($event['end_date']) && strtotime($event['start_time']) < strtotime($event['end_date']) ) ) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . "+1 day"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 day"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( in_array(date('w',strtotime($event['start_time'])),$days) && !in_array($event['start_time'],$excludes) && !$past_due ) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            } else {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) )) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . "+1 day"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 day"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( in_array(date('w',strtotime($event['start_time'])),$days) && !in_array($event['start_time'],$excludes) && !$past_due ) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            }
                            break;
                    //Monthly
                        case "monthly":
                        if ( isset($event['end_date']) && strtotime($event['end_date']) > 0 ) {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) ) && ( isset($event['end_date']) && strtotime($event['start_time']) < strtotime($event['end_date']) ) ) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . "+1 month"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 month"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( !in_array($event['start_time'],$excludes) && !$past_due ) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            } else {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) )) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . "+1 month"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 month"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( !in_array($event['start_time'],$excludes) && !$past_due ) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            }
                            break;
                    //Yearly
                        case "yearly":
                            if ( isset($event['end_date']) && strtotime($event['end_date']) > 0 ) {
                                while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) ) && ( isset($event['end_date']) && strtotime($event['start_time']) < strtotime($event['end_date']) ) ) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . "+1 year"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 year"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( !in_array($event['start_time'],$excludes) && !$past_due ) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            } else {
                                 while ( ( strtotime($event['start_time']) < strtotime($end_month) || strtotime($event['repeat_end']) > strtotime($event['end_time']) )) {
                                    $event['start_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['start_time'])) . "+1 year"));
                                    $event['end_time'] = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($event['end_time'])) . "+1 year"));
                                    $past_due = $due_date_filter == "past_due" ? strtotime($event['start_time']) >= strtotime($date)  : false;
                                    if ( !in_array($event['start_time'],$excludes) && !$past_due ) {
                                        $dates[] = array( 'start' => $event['start_time'], 'end' => $event['end_time'] );
                                    }
                                }
                            }
                            break;
                    }

                    //Once we have generated the event dates for the virtual events, then add them to the object as new "unique" events
                    if ( count($dates) > 0 ) {
                        $i=0;
                        foreach ($dates as $key=>$date) {
                            $new_event = array(
                                'assignee_color'            => $event['assignee_color'],
                                // 'id'                     => null,
                                // 'id'                     => $event['id'],
                                'id'                        => $event['id']."-".$i,
                                'owner_id'                  => $event['owner_id'],
                                'name'                      => $event['name'],
                                'description'               => $event['description'],
                                'created'                   => $event['created'],
                                'type'                      => $event['type'],
                                'assignee_id'               => $event['assignee_id'],
                                'due_date'                  => $date['start'],
                                'repeats'                   => $event['repeats'],
                                'repeat_end'                => $event['repeat_end'],
                                'start_time'                => $date['start'],
                                'end_time'                  => $date['end'],
                                'all_day'                   => $event['all_day'],
                                'category_id'               => $event['category_id'],
                                'category_name'             => $event['category_name'],
                                'modified'                  => $event['modified'],
                                'completed'                 => 0,
                                'actual_close'              => $event['actual_close'],
                                'company_name'              => $event['company_name'],
                                'company_id'                => $event['company_id'],
                                'deal_name'                 => $event['deal_name'],
                                'deal_id'                   => $event['deal_id'],
                                'person_first_name'         => $event['person_first_name'],
                                'person_last_name'          => $event['person_last_name'],
                                'person_id'                 => $event['person_id'],
                                'parent_id'                 => $event['id'],
                                'server'                    => true,
                                'clone'                     => true,
                                'association_type'          => $event['association_type'],
                                'association_link'          => $association_link,
                                'association_link_lang'     => $association_link_lang,
                                'owner_first_name'          => $event['owner_first_name'],
                                'owner_last_name'           => $event['owner_last_name'],
                                'assignee_first_name'       => $event['assignee_first_name'],
                                'assignee_last_name'        => $event['assignee_last_name'],
                                'end_date'                  => $event['end_date'],
                            );
                            if ($event['type'] == "event") {
                                $stime = explode(" ",$new_event['start_time']);
                                $new_event['start_time_hour'] = $stime[1];
                                $etime = explode(" ",$new_event['end_time']);
                                $new_event['end_time_hour'] =   $etime[1];
                                $new_event['start_time'] = $stime[0];
                                $new_event['end_time'] = $etime[0];
                            } else {
                                $dtime = explode(" ",$new_event['due_date']);
                                $new_event['due_date_hour'] = $dtime[1];
                                $new_event['due_date'] = $dtime[0];
                            }
                            $rowsToAdd[] = $new_event;
                            $i++;
                        }
                    }

                    if ( in_array($initialTime,$excludes) ) {
                        if ($this->completed != null) {
                            if ($event['completed'] != $this->completed) {
                                unset($rows[$initialKey]);
                            }
                        } elseif ( isset($status_filter) && $status_filter != null ) {
                            if ($event['completed'] != $status_filter) {
                                unset($rows[$initialKey]);
                            }
                        } else {
                            unset($rows[$initialKey]);
                        }
                    }

                }

            }
        }

        if ( is_array($rows) && is_array($rowsToAdd) ) {
            $rows = array_merge($rows,$rowsToAdd);
        }

        //filter data for calendar rendering
        if ($loc=='calendar' || $this->loc == "calendar") {
            if ( count($rows) > 0 ) {
                foreach ($rows as $key => $row) {
                    $rows[$key]['title']    = $row['name'];
                    $rows[$key]['allDay']   = $row['all_day'];

                    //determine event type
                    if ($row['type'] == 'event') {
                        $rows[$key]['start']    = $row['start_time'];
                        $rows[$key]['end']      = $row['end_time'];
                    }

                    if ($row['type'] == 'task') {
                        $rows[$key]['start'] = $row['due_date'];
                    }

                }
            }
        }

        /** Sort events after the virtual events have been created **/
        if ( is_array($rows) && count($rows) > 0 ) {
            switch ($this->filter_order) {
                case "e.due_date":
                    usort($rows,'self::dateSort');
                break;
                case "e.category_id":
                    usort($rows,'self::typeSort');
                break;
                default:
                    usort($rows,'self::dateSort');
                break;
            }
        }

        // filter events after event generation
        if ( is_array($rows) && count($rows) > 0 ) {
            if ($this->completed != null) {
                foreach ($rows as $key => $row) {
                    if ($row['completed'] != $this->completed) {
                        unset($rows[$key]);
                    }
                }
            } elseif ( isset($status_filter) && $status_filter != null ) {
                foreach ($rows as $key=>$row) {
                    if ($row['completed'] != $status_filter) {
                        unset($rows[$key]);
                    }
                }
            } else {
                foreach ($rows as $key=>$row) {
                    if ($row['completed'] == 1) {
                        unset($rows[$key]);
                    }
                }
            }
        }

        $app->triggerEvent('onEventLoad', array(&$rows));

        //Return results
        return $rows;

    }

    public function dateSort($a,$b)
    {
        $date1 = $a['type'] == "event" ? $a['start_time'] : $a['due_date'];
        $date2 = $b['type'] == "event" ? $b['start_time'] : $b['due_date'];
        if (strtotime($date1) == strtotime($date2)) {
            return 0;
        }
        if ($this->filter_order == "e.due_date") {
            if ($this->filter_order_Dir == "asc") {
                return (strtotime($date1) < strtotime($date2)) ? -1 : 1;
            } else {
                return (strtotime($date1) > strtotime($date2)) ? -1 : 1;
            }
        } else {
            return (strtotime($date1) < strtotime($date2)) ? -1 : 1;
        }
    }

    public function typeSort($a,$b)
    {
        if ($a['category_id'] == $b['category_id']) {
            return 0;
        }
        if ($this->filter_order_Dir == "asc") {
            return $a['category_id'] < $b['category_id'] ? -1 : 1;
        } else {
            return $a['category_id'] > $b['category_id'] ? -1 : 1;
        }
    }

    /**
     * Method to access an event
     *
     * @param  int   $id specific id requested
     * @return mixed
     */
    public function getEvent($id=null,$formatTime=true)
    {
        $app = \Cobalt\Container::fetch('app');

        //db
        $db = JFactory::getDBO();

        if (!$id) {
            $event = new EventTable();

            return $event;
        }

        $data = $app->input->getRequest('post');

        //determine if we are trying to retrieve a virtual event, if so retrieve its parent instead
        $query = $db->getQuery(true);
        $query->select('id')->from("#__events")->where("id=".$id);
        $db->setQuery($query);
        $result = $db->loadAssocList();

        //if we dont find any results use the parent id instead
        if ( count($result) == 0 || count(explode("-",$id)) > 1 ) {
            $id = $app->input->get('parent_id');
        }

        //gen query
        $query->clear();
        $query->select("e.*,ecf.association_id,ecf.association_type,ecf.event_id,owner.first_name as owner_first_name, owner.last_name as owner_last_name,
            d.name as deal_name, d.amount as deal_amount, CONCAT(p.first_name,' ',p.last_name) AS person_name, c.name as company_name, c.id as company_id,
            c.address_1 as company_address_1, c.address_city as company_address_city, c.address_state as company_address_state, c.address_zip as company_address_zip,
            c.phone as company_phone, c.website as company_website,assignee.first_name AS assignee_first_name,assignee.last_name AS assignee_last_name
            ");
        $query->from("#__events as e");

        //left join any assocations
        $query->leftJoin('#__events_cf AS ecf ON ecf.event_id = e.id');
        $query->leftJoin('#__users AS owner ON owner.id = e.owner_id');
        $query->leftJoin('#__deals as d ON ecf.association_id = d.id AND ecf.association_type = "deal" AND d.published>0');
        $query->leftJoin('#__people as p ON ecf.association_id = p.id AND ecf.association_type = "person" AND p.published>0');
        $query->leftJoin('#__companies as c ON ecf.association_id = c.id AND ecf.association_type = "company" AND c.published>0');
        $query->leftJoin('#__users AS assignee ON assignee.id = e.assignee_id');

        $query->where("e.id=".$id);

        //set query
        $db->setQuery($query);

        //load results
        $results = $db->loadAssocList();

        //clean results
        //assign the parent id if needed
        if ( count($result) == 0 && is_array($results) && array_key_exists(0,$results)) {
            $results[0]['parent_id'] = $results[0]['id'];
            unset($results[0]['id']);
        }

        if ( is_array($results) && array_key_exists(0,$results) ) {

            if ( $results[0]['repeats'] != "none" && $app->input->get('date') && count($results) > 0 ) {
                if ( array_key_exists('type',$results[0]) && $results[0]['type'] == "event" ) {
                    $stime = explode(" ",$results[0]['start_time']);
                    $results[0]['start_time_hour'] = $stime[1];
                    $etime = explode(" ",$results[0]['end_time']);
                    $results[0]['end_time_hour'] =   $etime[1];
                    $results[0]['start_time'] = $app->input->get('date');
                    $results[0]['end_time'] = $app->input->get('date');
                } elseif ( array_key_exists('due_date',$results[0]) ) {
                    $dtime = explode(" ",$results[0]['due_date']);
                    $results[0]['due_date_hour'] = $dtime[1];
                    $results[0]['due_date'] = $app->input->get('date');
                }
            }

            if ($formatTime) {
                $originalDate = $results[0]['created'];
                if ( array_key_exists('created',$results[0])) {
                    $results[0]['created_formatted'] = array_key_exists('created',$results[0]) ? DateHelper::formatDate($originalDate) : "";
                    $results[0]['created'] = DateHelper::formatDate($originalDate,true,false);
                }
                if ( array_key_exists('repeat_end',$results[0])) {
                    $results[0]['repeat_end_formatted'] = DateHelper::formatDate($results[0]['repeat_end']);
                    $results[0]['repeat_end'] = DateHelper::formatDate($results[0]['repeat_end'],true,false);
                }
                if ( array_key_exists('modified',$results[0])) {
                    $results[0]['modified_formatted'] = DateHelper::formatDate($results[0]['modified']);
                    $results[0]['modified'] = DateHelper::formatDate($results[0]['modified'],true,false);
                }
                if ( array_key_exists('actual_close',$results[0])) {
                    $results[0]['actual_close_formatted'] = DateHelper::formatDate($results[0]['actual_close']);
                    $results[0]['actual_close'] = DateHelper::formatDate($results[0]['actual_close'],true,false);
                }
                if ( array_key_exists('due_date',$results[0])) {
                    $results[0]['due_date_formatted'] = $app->input->get('date') ? DateHelper::formatDateString($results[0]['due_date']) : DateHelper::formatDate($results[0]['due_date']);
                    $results[0]['due_date'] = $app->input->get('date') ? DateHelper::formatDateString($results[0]['due_date']) : DateHelper::formatDate($results[0]['due_date'],true,false);
                }
                if ( array_key_exists('end_date',$results[0])) {
                    $results[0]['end_date_formatted'] = $app->input->get('date') ? DateHelper::formatDateString($results[0]['end_date']) : DateHelper::formatDate($results[0]['end_date']);
                    $results[0]['end_date'] = $app->input->get('date') ? DateHelper::formatDateString($results[0]['end_date']) : DateHelper::formatDate($results[0]['end_date'],true,false);
                }
                if ( array_key_exists('start_time',$results[0])) {
                    $results[0]['start_time_formatted'] = $app->input->get('date') ? DateHelper::formatDateString($results[0]['start_time']) : DateHelper::formatDate($results[0]['start_time']);
                    $results[0]['start_time'] = $app->input->get('date') ? DateHelper::formatDateString($results[0]['start_time']) : DateHelper::formatDate($results[0]['start_time'],true,false);
                }
                if ( array_key_exists('end_time',$results[0])) {
                    $results[0]['end_time_formatted'] = $app->input->get('date') ? DateHelper::formatDateString($results[0]['end_time']) : DateHelper::formatDate($results[0]['end_time']);
                    $results[0]['end_time'] = $app->input->get('date') ? DateHelper::formatDateString($results[0]['end_time']) : DateHelper::formatDate($results[0]['end_time'],true,false);
                }
                if ( array_key_exists(0,$results) && !$app->input->get('date') ) {
                    if ( array_key_exists('type',$results[0]) && $results[0]['type'] == "event" ) {
                        $stime = explode(" ",$results[0]['start_time']);
                        $results[0]['start_time_hour'] = $stime[1];
                        $etime = explode(" ",$results[0]['end_time']);
                        $results[0]['end_time_hour'] =   $etime[1];
                        $results[0]['start_time'] = $stime[0];
                        $results[0]['end_time'] = $etime[0];
                    } elseif ( array_key_exists('due_date',$results[0]) ) {
                        $dtime = explode(" ",$results[0]['due_date']);
                        $results[0]['due_date_hour'] = $dtime[1];
                        $results[0]['due_date'] = $dtime[0];
                    }
                }
            }

            //filter results for calendar display
            if ( $app->input->get('calendar_filter') ) {
                $results[0]['title']    = $results[0]['name'];
                $results[0]['allDay']   = $results[0]['all_day'];

                //determine event type
                if ($results[0]['type'] == 'event') {
                    $results[0]['start']    = $results[0]['start_time'];
                    $results[0]['end']      = $results[0]['end_time'];
                }

                if ($results[0]['type'] == 'task') {
                    $results[0]['start'] = $results[0]['due_date'];
                }
            }

            //join any association data
            if ( array_key_exists('association_id',$results[0] ) AND !is_null($results[0]['association_id']) AND $results[0]['association_id'] != 0 ) {
                $query->clear();
                switch ($results[0]['association_type']) {
                    case 'company':
                        $query->select("name as association_name, id as association_id");
                        $query->from("#__companies");
                        break;
                    case 'deal':
                        $query->select("name as association_name, id as association_id");
                        $query->from("#__deals");
                        break;
                    case 'person':
                        $query->select("first_name as association_first_name, last_name as association_last_name, id as association_id");
                        $query->from("#__people");
                        break;
                }
                $query->where("published>0");
                $query->where("id=".$results[0]['association_id']);
                $db->setQuery($query);
                $data = $db->loadAssocList();
                if ( !empty($data) ) {
                    $results[0] = array_merge( $results[0], $data[0] );
                    if ($results[0]['association_type'] == 'person') {
                        $results[0]['association_name'] = $data[0]['association_first_name']. " ".$data[0]['association_last_name'];
                    }
                }
            }

            // $dispatcher = JDispatcher::getInstance();
            // $dispatcher->trigger('onEventLoad', array(&$results[0]));

        }

        //return results
        if ( is_array($results) && array_key_exists(0,$results) ) {
            return $results[0];
        } else {
            $table = new EventTable();

            return $table;
        }

    }

    /*
     * Method to link events and cf tables
     */

    public function eventsCf($cfdata,$type)
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //search for existing associations
        $query->select('*');
        $query->from("#__events_cf");
        $query->where("event_id=".$cfdata['event_id']);
        $db->setQuery($query);

        //perform search
        $search = $db->loadAssocList();

        //insert or update data depending on if we find any results
        if ( count($search) > 0 ) {
            //flush query object
            $query->clear();
            $query->update('#__events_cf');
            $query->set(array("association_id=".$cfdata['association_id'],"association_type='".$type."'","event_id=".$cfdata['event_id']));
            $query->where("event_id=".$cfdata['event_id']);
        } else {
            //flush query object
            $query->clear();
            $query->insert('#__events_cf');
            $query->set(array("association_id=".$cfdata['association_id'],"association_type='".$type."'","event_id=".$cfdata['event_id']));
        }

        //return
        $db->setQuery($query);
        $db->execute();

        return true;

    }

    /**
     * Add our excluded dates to the parent
     * @param int                $parent_id parent id to update
     * @param datetime timestamp $date      date to exclude
     */
    public function addExcludes($parent_id,$date)
    {
        //Dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //gen query
        $query->select("excludes")->from("#__events")->where("id=".$parent_id);

        //get results
        $db->setQuery($query);
        $result = $db->loadResult();

        //unserialize excludes
        $result = unserialize($result);
        $result = ( is_array($result) && count($result)>0 ) ? $result : array();

        //add new dates to excludes
        $result[] = $date;

        //serialize excludes
        $result = serialize($result);

        //write new information to database
        $query->clear();
        $query->update('#__events');
        $query->set(array("excludes='".$result."'"));
        $query->where("id=".$parent_id);
        $db->setQuery($query);
        $db->query();

    }

    /**
     * Manually update parent events to reflect changes on virtual generated events
     * @param int   $parent_id the id of the parent event to update
     * @param mixed $data      the array of data to update the parent with
     */
    public function updateEvent($parent_id,$data)
    {
        //dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //unset values
        unset($data['due_date']);
        unset($data['start_time']);
        unset($data['end_time']);
        unset($data['id']);
        unset($data['update_future_events']);
        unset($data['parent_id']);
        unset($data['task']);
        unset($data['created']);
        unset($data['calendar_filter']);

        //construct values
        $values = array();
        foreach ($data as $key=>$info) {
                $values[] = $key." = '".$info."'";
        }

        //gen query
        $query->update("#__events")->set($values)->where("id=".$parent_id);

        //set query and update db with new data
        $db->setQuery($query);
        $db->query();

    }

    /**
     * Remove an event or series of events
     * This will either :
     * Remove the parent OR
     * Add excluded dates to the parent id OR
     * If removing the parent event ONLY we will change the event date to the next respective date in the series
     * @param int    $id   of the REAL event we wish to modify
     * @param string $type 'single','series'
     */
    public function removeEvent($id=null,$type=null)
    {
        $app = \Cobalt\Container::fetch('app');

        $type = ( $type == null ) ? $app->input->get('type') : $type;
        $date = $app->input->get('date');
        $repeats = $app->input->get('repeats');
        $event_type = ( $app->input->get('event_type') ) ? $app->input->get('event_type') : $app->input->get('type');
        $data = $app->input->getArray();
        if ( $id != null ) $data['event_id'] = $id;

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //remove an entire series
        if ($type == 'series') {
            $id = ( $app->input->get('parent_id') ) ? $app->input->get('parent_id') : $app->input->get('event_id');
            $query->update('#__events')->set("published=-1")->where('id='.$id." OR parent_id=".$id);
            //run database query
            $db->setQuery($query);
            $db->query();
        }

        //Load Tables
        $rowId = $id ? $id : $data['event_id'];
        $oldRow = new EventTable();
        $oldRow->load($rowId);

        //remove individual events
        if ($type == 'single') {
                //if we are receiving the parent id
                if ($data['parent_id'] == 0) {
                    if ( !array_key_exists('repeats',$data) || $data['repeats'] == "" || $data['repeats'] == "none" ) {
                        //delete event entry
                        $query->update('#__events')->set("published=-1")->where('id='.$data['event_id']);
                        $db->setQuery($query);
                        $db->query();
                        //delete from cf table
                        // $query->clear();
                        // $query->delete("#__events_cf")->where('event_id='.$data['event_id']);
                        // $db->setQuery($query);
                        // $db->query();

                    } else {

                        //Determine virtual events to generate
                        switch ($data['repeats']) {
                        //Daily
                            case "daily":
                                if ($event_type == 'event') {
                                    $start_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['start_time'])) . "+1 days"));
                                    $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['end_time'])) . "+1 days"));
                                } else {
                                    $due_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['due_date'])) . "+1 days"));
                                }
                                break;
                        //Weekdays
                            case "weekdays":
                                $days = array ( 1,2,3,4,5 );
                                if ($event_type == 'event') {
                                    $start_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['start_time'])) . "+1 days"));
                                    $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['end_time'])) . "+1 days"));
                                    while ( !in_array(date('w',strtotime($start_time)),$days) ) {
                                        $start_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($start_time)) . "+1 days"));
                                        $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($end_time)) . "+1 days"));
                                    }
                                } else {
                                    $due_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['due_date'])) . "+1 days"));
                                    while ( !in_array(date('w',strtotime($due_date)),$days) ) {
                                        $due_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($due_date)) . "+1 days"));
                                    }
                                }
                                break;
                        //Weekly
                            case "weekly":
                                if ($event_type == 'event') {
                                    $start_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['start_time'])) . "+1 week"));
                                    $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['end_time'])) . "+1 week"));
                                } else {
                                    $due_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['due_date'])) . "+1 week"));
                                }
                                break;
                        //Weekly Monday Wednesday and Friday
                            case "weekly-mwf":
                                $days = array ( 1,3,5 );
                                if ($event_type == 'event') {
                                    $start_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['start_time'])) . "+1 days"));
                                    $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['end_time'])) . "+1 days"));
                                    while ( !in_array(date('w',strtotime($start_time)),$days) ) {
                                        $start_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($start_time)) . "+1 days"));
                                        $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($end_time)) . "+1 days"));
                                    }
                                } else {
                                    $due_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['due_date'])) . "+1 days"));
                                    while ( !in_array(date('w',strtotime($due_date)),$days) ) {
                                        $due_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($due_date)) . "+1 days"));
                                    }
                                }
                                break;
                        //Weekly Tuesday Thursday
                            case "weekly-tr":
                                $days = array ( 2,4 );
                                if ($event_type == 'event') {
                                    $start_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['start_time'])) . "+1 days"));
                                    $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['end_time'])) . "+1 days"));
                                    while ( !in_array(date('w',strtotime($start_time)),$days) ) {
                                        $start_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($start_time)) . "+1 days"));
                                        $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($end_time)) . "+1 days"));
                                    }
                                } else {
                                    $due_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['due_date'])) . "+1 days"));
                                    while ( !in_array(date('w',strtotime($due_date)),$days) ) {
                                        $due_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($due_date)) . "+1 days"));
                                    }
                                }
                                break;
                        //Monthly
                            case "monthly":
                                if ($event_type == 'event') {
                                    $start_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['start_time'])) . "+1 month"));
                                    $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['end_time'])) . "+1 month"));
                                } else {
                                    $due_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['due_date'])) . "+1 month"));
                                }
                                break;
                        //Yearly
                            case "yearly":
                                if ($event_type == 'event') {
                                    $start_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['start_time'])) . "+1 year"));
                                    $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['end_time'])) . "+1 year"));
                                } else {
                                    $due_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($data['due_date'])) . "+1 year"));
                                }
                                break;
                        }
                        $query->update("#__events");
                        if ($event_type == 'event') {
                            $query->set(array("start_time='".$start_time."'","end_time='".$end_time."'"));
                        } else {
                            $query->set(array("due_date='".$due_date."'"));
                        }
                        $query->set(array("completed=0"));
                        $query->where("id=".$data['event_id']);
                        $db->setQuery($query);
                        $db->query();
                    }
                } elseif ( array_key_exists('event_id',$data) && $data['event_id'] != 0 ) {
                    $query->update('#__events')->set("published=-1")->where('id='.$data['event_id']);
                    $db->setQuery($query);
                    $db->query();
                } else {
                    //if we are receiving a virtual event
                    $this->addExcludes($data['parent_id'],$date);
                }
            }

        $row = new EventTable();
        $row->load($rowId);
        $status = "deleted";

        ActivityHelper::saveActivity($oldRow, $row,'event', $status);

        return true;
    }

    /**
     * Mark events as completed
     * @return [type] [description]
     */
    public function markComplete()
    {
        $app = \Cobalt\Container::fetch('app');

        //Determine if we are editing a series of events of a single event
        $event_id = $app->input->get('event_id');
        $parent_id = $app->input->get('parent_id');
        $date = $app->input->get('due_date') ? $app->input->get('due_date') : $app->input->get('date');
        $event_type = $app->input->get('event_type');
        $repeats = $app->input->get('repeats');
        $completed = $app->input->get('completed') != "" ? $app->input->get('completed') : 1;

        //Load Tables
        $oldRow = new EventTable();
        $oldRow->load($event_id);

        //We are only editing a single event entry OR a parent entry
        if ($repeats == 'none' /*|| $parent_id == 0*/) {

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $date = DateHelper::formatDBDate(date("Y-m-d H:i:s"));
            $query->update("#__events")->set(array('completed='.$completed,'actual_close="'.$date.'"'))->where("id=".$event_id);
            $db->setQuery($query);
            $db->query();

        //We are dealing with a virtually generated event
        } else {

            $id = $parent_id > 0 ? $parent_id : $event_id;

            //Clone the event and give it the correct // new unique completion date
            $event = $this->getEvent($id,false);

            //Add the event to the parent exclusion
            $exp1 = explode(" ",$date);
            $append = $event['type'] == "task" ? $event['due_date'] : $event['start_time'];
            $exp2 = explode(" ",$append);
            $excludeDate = $exp1[0]." ".$exp2[1];

            $this->addExcludes($id,$excludeDate);

            //Merge arrays
            $data = $app->input->getRequest('post');
            $data['completed'] = $completed;
            $new_data = array_merge($event,$data);

            if ($event['type'] == "task") {
                $new_data['due_date'] = DateHelper::formatDate($excludeDate,false,false);
                $new_data['type'] = "task";
            } else {
                $new_data['start_time'] = DateHelper::formatDate($excludeDate,false,false);
                $new_data['end_time'] = DateHelper::formatDate($excludeDate,false,false);
                $new_data['type'] = "event";
            }

            unset($new_data['id']);
            unset($new_data['repeats']);
            unset($new_data['excludes']);
            // unset($new_data['parent_id']);
            foreach ($new_data as $key => $data) {
                if ( is_null($data) ) {
                    $new_data[$key] = 0;
                }
            }

            //Store new data
            $id = $this->store($new_data);
            $event = $this->getEvent($id);
            //echo json_encode($event);

        }

        $row = new EventTable();
        $row->load($event_id);
        $status = "completed";

        ActivityHelper::saveActivity($oldRow, $row,'event', $status);

    }

    /**
     * Mark events incomplete
     */
    public function markIncomplete()
    {
        $app = \Cobalt\Container::fetch('app');

        //Determine if we are editing a series of events of a single event
        $event_id = $app->input->get('event_id');
        $parent_id = $app->input->get('parent_id');
        $date = $app->input->get('date');
        $event_type = $app->input->get('event_type');
        $repeats = $app->input->get("repeats");

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update("#__events")->set(array('completed=0','actual_close="0000-00-00 00:00:00"'))->where("id=".$event_id);
        $db->setQuery($query);
        $db->query();

    }

    /**
     * Postpone events
     * @param [int] $days
     */
    public function postponeEvent($days=null,$event_id=null)
    {
            $app = \Cobalt\Container::fetch('app');

            $event_id = ( $event_id == null ) ? $app->input->get('event_id') : $event_id;
            $days = ( $days == null ) ? $app->input->get("days") : $days;

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select("e.type,e.due_date,e.start_time,e.end_time")->from("#__events AS e")->where("e.id=".$event_id);
            $db->setQuery($query);
            $dates = $db->loadObjectList();

            //Load Tables
            $oldRow = new EventTable();
            $oldRow->load($event_id);

            if ( count($dates) > 0 ) {
                foreach ($dates as $date) {
                    $query->clear();
                    $query->update('#__events');
                    if ($date->type == "task") {
                        $due_date = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($date->due_date)) . "+".$days." days"));
                        $query->set(array("due_date='".$due_date."'"));
                    } else {
                        $start_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($date->start_time)) . "+".$days." days"));
                        $end_time = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s", strtotime($date->end_time)) . "+".$days." days"));
                        $query->set(array("start_time='".$start_time."'","end_time='".$end_time."'"));
                    }
                    $query->where("id=".$event_id);
                    $db->setQuery($query);
                    $db->query();
                }
            }

            $row = new EventTable();
            $row->load($event_id);
            $status = "postponed";

            ActivityHelper::saveActivity($oldRow, $row,'event', $status);

    }

    /**
     * Populate user state requests
     */
    public function populateState()
    {
        //get states
        $app = \Cobalt\Container::fetch('app');

        //determine view so we set correct states
        $view = $this->view;
        $layout = $this->layout;

        // if ( $view == "events" && ( $layout == "default" || $layout == "list" || $layout == null ) ) {

            // Get pagination request variables
            $limit = $app->getUserStateFromRequest("Event.".$view.'_'.$layout.'_limit','limit',10);
            $limitstart = $app->getUserStateFromRequest("Event.".$view.'_'.$layout.'_limitstart','limitstart',0);

            // In case limit has been changed, adjust it
            $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

            $state = new Registry();
            $state->set("Event.".$view.'_limit', $limit);
            $state->set("Event.".$view.'_limitstart', $limitstart);

            //set default filter states for reports
            $filterOrder = "CASE e.type WHEN 'event' THEN e.start_time WHEN 'task' THEN e.due_date ELSE e.due_date END";
            $filterOrderDir = "ASC";
            $filter_order = $app->getUserStateFromRequest('Event.'.$view.'_'.$layout.'_filter_order','filter_order',$filterOrder);
            $filter_order_Dir = $app->getUserStateFromRequest('Event.'.$view.'_'.$layout.'_filter_order_Dir','filter_order_Dir',$filterOrderDir);
            $status_filter = $app->getUserStateFromRequest('Event.'.$view.'_'.$layout.'_status','status',0);
            $type_filter = $app->getUserStateFromRequest('Event.'.$view.'_'.$layout.'_type','type','all');
            $category_filter = $app->getUserStateFromRequest('Event.'.$view.'_'.$layout.'_category','category','any');
            $due_date_filter = $app->getUserStateFromRequest('Event.'.$view.'_'.$layout.'_due_date','due_date','any');
            $association_type_filter = $app->getUserStateFromRequest('Event.'.$view.'_'.$layout.'_association_type','association_type','any');
            $assignee_id_filter = $app->getUserStateFromRequest('Event.'.$view.'_'.$layout.'_assignee_id','assignee_id',UsersHelper::getUserId());
            $assignee_filter_type = $app->getUserStateFromRequest('Event.'.$view.'_'.$layout.'_assignee_filter_type','assignee_filter_type','individual');

            //set states for reports
            $state->set('Event.'.$view.'_'.$layout.'_filter_order',$filter_order);
            $state->set('Event.'.$view.'_'.$layout.'_filter_order_Dir',$filter_order_Dir);
            $state->set('Event.'.$view.'_'.$layout.'_status',$status_filter);
            $state->set('Event.'.$view.'_'.$layout.'_type',$type_filter);
            $state->set('Event.'.$view.'_'.$layout.'_category',$category_filter);
            $state->set('Event.'.$view.'_'.$layout.'_due_date',$due_date_filter);
            $state->set('Event.'.$view.'_'.$layout.'_association_type',$association_type_filter);
            $state->set('Event.'.$view.'_'.$layout.'_assignee_id',$assignee_id_filter);
            $state->set('Event.'.$view.'_'.$layout.'_assignee_filter_type',$assignee_filter_type);

            $this->setState($state);
        //}
    }

}
