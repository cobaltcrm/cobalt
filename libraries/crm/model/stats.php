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

class CobaltModelStats extends JModelBase
{

    var $person_id;
    var $access;
    var $users;
    var $today;
    var $previousDay;

    public function __construct(){
        $this->previousDay = CobaltHelperDate::formatDBDate(date('Y-m-d')." - 1 day");
        $this->today = CobaltHelperDate::formatDBDate(date('Y-m-d'));
    	$this->access = CobaltHelperUsers::getRole($this->person_id);
    	$this->users = $this->getUsers($this->person_id,$this->access);
    }

    public function getDistinctEntries($type,$field){

        $db =& JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("DISTINCT h.type_id");
        $query->from("#__history AS h");
        $query->where("h.field='".$field."' AND h.type='".$type."'");
        // $query->where("(h.date >= '".$this->previousDay."' AND h.date < '".$this->today."')");
        $query->where("h.user_id IN(".implode(',',$this->users).")");
        $db->setQuery($query);
        $results = $db->loadColumn();

        return $results;

    }

    public function joinField($ids,$type,$field){
        $results = array();

        $db =& JFactory::getDBO();
        $query = $db->getQuery(true);

        if ( count ( $ids ) > 0 ){
            foreach ( $ids as $id ){
                $query->clear();
                $query->select("h.type_id,".$type.".*,h.new_value");
                $query->from("#__history AS h");
                $query->leftJoin("#__".$type." AS ".$type." ON ".$type.".id = h.type_id");
                $query->where("h.type_id=".$id);
                // $query->where("(h.date >= '".$this->previousDay."' AND h.date < '".$this->today."')");
                $query->where("h.field='".$field."'");
                $query->order("h.date DESC LIMIT 1");
                $db->setQuery($query);
                $results[] = $db->loadObject();

            }
        }

        return $results;
    }

    public function getUsers($user_id,$user_role){
        
        if( $user_role != 'basic' ){
            
            $db =& JFactory::getDBO();
            $query = $db->getQuery(true);
            
            $query->select("id");
            $query->from("#__users");

            //if manager
            if ( $user_role == "manager" ){
            	$team_id = CobaltHelperUsers::getTeamId($user_id);
                $query->where('team_id='.$team_id);
            }
            //if exec there is no where clause, load all users
            
            //load results
            $db->setQuery($query);
            $results = $db->loadColumn();

        }else{
        	$results = array($user_id);
        }

        return $results;
    }


    public function getActiveDealsAmount(){

    	$db =& JFactory::getDBO();
    	$query = $db->getQuery(true);

    	/** get unique history **/
        $deal_ids = $this->getDistinctEntries('deal','stage_id');

        $query->clear();
        $query->select("SUM(d.amount)");
        $query->from("#__deals AS d");
        $query->where("d.id IN(".implode(',',$deal_ids).')');
        // $query->where("(h.date >= '".$this->previousDay."' AND h.date < '".$this->today."')");

    	$db->setQuery($query);
    	$result = $db->loadResult();

    	return $result;

    }

    public function getStages(){

		$db =& JFactory::getDBO();
    	$query = $db->getQuery(true);

        /** Select distinct history entries **/
        $results = $this->getDistinctEntries('deal','stage_id');

        /** Get most recent entry from the above **/
        $deals = $this->joinField($results,'deals','stage_id');

        /** Merge with all possible stages **/
        $query->clear();
        $query->select("s.name,s.color,s.id,0 AS amount");
        $query->from("#__stages AS s");
        $db->setQuery($query);
        $stages = $db->loadAssocList('id');

        /** Sum amounts from above **/
        if ( count ($deals) > 0 ){
            foreach ( $deals as $deal ){
                if ( array_key_exists($deal->new_value,$stages) ){
                    $stages[$deal->new_value]['amount'] += $deal->amount;
                }
            }   
        }

        usort($stages,'self::sortAmount');

    	return $stages;

    }

    function sortAmount($a,$b) {
          return $a['amount']<$b['amount'];
    }

    public function getLeads(){

    	$db =& JFactory::getDBO();
    	$query = $db->getQuery(true);

    	/** person ids **/
        $person_ids = $this->getDistinctEntries('person','type');
        $people = $this->joinField($person_ids,'people','type');
        $leads = array('lead'=>0,'contact'=>0);
        if ( count($people) > 0 ){
            foreach ( $people as $person ){
                $leads[$person->type]++;
            }
        }

    	return $leads;

    }

    public function getNotes(){

        $db =& JFactory::getDBO();
        $query = $db->getQuery(true);

        $note_ids = $this->getDistinctEntries('note','id');

        $query->clear();
        $query->select("c.*");
        $query->from("#__notes_categories AS c");
        $db->setQuery($query);

        $categories = $db->loadAssocList();

        $totals = array();

        if ( count($categories) > 0 ){
            foreach ( $categories as $category ){
                $query->clear();
                $query->select("COUNT(n.id)");
                $query->from("#__notes AS n");
                $query->where("n.category_id = ".$category['id']);
                $query->where("n.id IN(".implode(',',$note_ids).")");
                $db->setQuery($query);
                $totals[$category['name']] = $db->loadResult();
            }
        }

        return $totals;

    }

    public function getTodos(){

        $db =& JFactory::getDBO();
        $query = $db->getQuery(true);

        $events = $this->getDistinctEntries('event','id');

        $query->clear();
        $query->select("c.*");
        $query->from("#__events_categories AS c");
        $db->setQuery($query);

        $categories = $db->loadAssocList();

        $totals = array();

        if ( count($categories) > 0 ){
            foreach ( $categories as $category ){
                $query->clear();
                $query->select("COUNT(e.id) AS total,SUM(e.completed) AS completed");
                $query->from("#__events AS e");
                $query->where("e.category_id = ".$category['id']);
                $query->where("e.id IN(".implode(',',$events).")");
                $db->setQuery($query);
                $totals[$category['name']] = $db->loadObject();
            }
        }

        return $totals;

    }

    public function getDealActivity(){

    }



}