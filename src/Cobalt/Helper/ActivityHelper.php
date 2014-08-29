<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Helper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class ActivityHelper
{
    public static $limit = null;

    public static function saveActivity($old_info, $new_info, $model, $action_type)
    {
        $db = \Cobalt\Container::get('db');
        $query = $db->getQuery(true);
        $user_id = UsersHelper::getUserId();
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));

        if ($action_type == 'created')
        {
            $query->clear()
                ->insert('#__history')
                ->set('type='.$db->q($model))
                ->set('type_id='.$db->q($new_info->id))
                ->set('user_id='.$db->q($user_id))
                ->set('date='.$db->q($date))
                ->set('new_value='.$db->q($new_info->id))
                ->set('action_type='.$db->q($action_type))
                ->set('field="id"');
            $db->setQuery($query);
            $db->query();
        }
        else
        {
            // @TODO: comparing arrays does not work. Fix this in future. Now there are more important bugs
            // $differences = self::recursive_array_diff((array) $old_info,(array) $new_info);

            // if (count($differences) > 0)
            // {
            //     foreach ($differences as $key => $old_value)
            //     {
            //         $insertObject = new \stdClass;
            //         $insertObject->type = $model;
            //         $insertObject->type_id = $new_info->id;
            //         $insertObject->user_id = $user_id;
            //         $insertObject->date = $date;
            //         $insertObject->old_value = $old_value;
            //         $insertObject->new_value = $new_info->$key;
            //         $insertObject->action_type = $action_type;
            //         $insertObject->field = $key;

            //         // $db->insertObject('#__history', $insertObject);
            //     }
            // }
        }
    }

    public static function saveUserLoginHistory()
    {
        $db = \Cobalt\Container::get('db');
        $query = $db->getQuery(true);

        $user_id = UsersHelper::getUserId();
        $today = DateHelper::formatDBDate(date("Y-m-d"));

        $query->clear();
        $query->select("COUNT(id)");
        $query->from("#__login_history");
        $query->where("date='".$today."'");
        $query->where("user_id=".$user_id);
        $db->setQuery($query);
        $existing = $db->loadResult();

        if (!$existing) {
            $query->clear();
            $query->insert("#__login_history");
            $query->set("user_id=".$user_id.",date='".$today."'");
            $db->setQuery($query);
            $db->query();
        }

    }

    public static function getActivity()
    {
        $db = \Cobalt\Container::get('db');
        $query = $db->getQuery(true);

        $query->select('h.*, CONCAT(u.first_name," ", u.last_name) AS owner_name, c.name as company_name, CONCAT(p.first_name," ", p.last_name) AS person_name,
                        d.name as deal_name, e.name as event_name, note_cat.name as notes_category_name,
                        event_cat.name as events_category_name, old_event_cat.name as events_category_name_old, old_note_cat.name AS notes_category_name_old,
                        doc.name AS document_name,status.name AS deal_status_name_old, status2.name AS deal_status_name,deal_source.name AS deal_source_name_old,deal_source_2.name AS deal_source_name,
                        deal_stage.name AS deal_stage_name_old,deal_stage_2.name AS deal_stage_name,CONCAT(deal_owner.first_name," ",deal_owner.last_name) AS deal_owner_name_old,
                        CONCAT(deal_owner_2.first_name," ",deal_owner_2.last_name) AS deal_owner_name

                        ');

        $query->from('#__history AS h');
        $query->leftJoin('#__users AS u ON u.id = h.user_id');
        $query->leftJoin('#__companies AS c ON c.id = h.type_id AND h.type="company"');
        $query->leftJoin('#__notes AS n ON n.id = h.type_id AND h.type="note"');
        $query->leftJoin('#__deals AS d on d.id = h.type_id AND h.type="deal"');
        $query->leftJoin('#__people AS p on p.id = h.type_id AND h.type="person"');
        $query->leftJoin('#__goals AS g on g.id = h.type_id AND h.type="goal"');
        $query->leftJoin('#__events AS e on e.id = h.type_id AND h.type="event"');
        $query->leftJoin('#__reports AS r on r.id = h.type_id AND h.type="report"');
        $query->leftJoin('#__documents AS doc ON doc.id = h.type_id AND h.type="document"');
        $query->leftJoin('#__notes_categories as note_cat ON note_cat.id = h.new_value AND h.field="category_id" AND h.type="notes"');
        $query->leftJoin('#__events_categories as event_cat ON event_cat.id = h.new_value AND h.field="category_id" AND h.type="events"');
        $query->leftJoin('#__notes_categories as old_note_cat ON old_note_cat.id = h.old_value AND h.field="category_id" AND h.type="notes"');
        $query->leftJoin('#__events_categories as old_event_cat ON old_event_cat.id = h.old_value AND h.field="category_id" AND h.type="events"');
        $query->leftJoin("#__deal_status AS status ON status.id = h.old_value AND h.type='deal'");
        $query->leftJoin("#__deal_status AS status2 ON status2.id = h.new_value AND h.type='deal'");
        $query->leftJoin("#__sources AS deal_source ON deal_source.id = h.old_value AND h.type='deal'");
        $query->leftJoin("#__sources AS deal_source_2 ON deal_source_2.id = h.new_value AND h.type='deal'");
        $query->leftJoin("#__stages AS deal_stage ON deal_stage.id = h.old_value AND h.type='deal'");
        $query->leftJoin("#__stages AS deal_stage_2 ON deal_stage_2.id = h.new_value AND h.type='deal'");
        $query->leftJoin("#__users AS deal_owner ON deal_owner.id = h.old_value AND h.type='deal'");
        $query->leftJoin("#__users AS deal_owner_2 ON deal_owner_2.id = h.new_value AND h.type='deal'");

        $member_id = UsersHelper::getUserId();
        $member_role = UsersHelper::getRole();
        $team_id = UsersHelper::getTeamId();
        if ($member_role != 'exec') {
             //manager filter
            if ($member_role == 'manager') {
                $query->where('u.team_id = '.$team_id);
            } else {
            //basic user filter
                $query->where(array('h.user_id = '.$member_id));
            }
        }

        //TODO: Add assignees to the display (massive left join)
        $query->where('h.field!="assignee_id" AND h.field!="repeats"');

        $query->order('h.date DESC');

        if (self::$limit != null) {
            $query .= " LIMIT ".$this->limit;
        } else {
            $query .= " LIMIT 10";
        }

        $db->setQuery($query);

        $activity = $db->loadObjectList();

        return $activity;

    }

    public static function recursive_array_diff($a1, $a2)
    {
        $r = array();
        foreach ($a1 as $k => $v) {
            if ($k[0]!='_' && $k!='modified') {
                if (array_key_exists($k, $a2)) {
                    if (is_array($v)) {
                        $rad = self::recursive_array_diff($v, $a2[$k]);
                        if (count($rad)) { $r[$k] = $rad; }
                    } else {
                        if ($v != $a2[$k]) {
                            $r[$k] = $v;
                        }
                    }
                } else {
                    $r[$k] = $v;
                }
            }
        }

        return $r;
    }

}
