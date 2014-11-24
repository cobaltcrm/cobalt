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

use Cobalt\Factory;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class ActivityHelper
{
    public static $limit = null;

    public static function saveActivity($old_info, $new_info, $model, $action_type)
    {
        $db = Factory::getDb();
        $query = $db->getQuery(true);
        $user_id = UsersHelper::getUserId();
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));

        if ($action_type == 'created')
        {
	        $columns = array('type', 'type_id', 'user_id', 'date', 'new_value', 'action_type', 'field');
	        $values = array($db->quote($model), $db->quote($new_info->id), $db->quote($user_id), $db->quote($date), $db->quote($new_info->id), $db->quote($action_type), $db->quote('id'));

	        $query->insert('#__history')
	            ->columns($columns)
	            ->values(implode(', ', $values));

            $db->setQuery($query)->execute();
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
	    $db = Factory::getDb();
        $query = $db->getQuery(true);

        $user_id = UsersHelper::getUserId();
        $today = DateHelper::formatDBDate(date("Y-m-d"));

        $query->select("COUNT(id)");
        $query->from("#__login_history");
        $query->where("date=" . $db->quote($today));
        $query->where("user_id=".$user_id);
        $db->setQuery($query);
        $existing = $db->loadResult();

		if (!$existing) {
			$values = array($user_id,$db->quote($today));

			$query->clear();
			$query->insert("#__login_history");
			$query->columns(array($db->quoteName('user_id'), $db->quoteName('date')));
			$query->values(implode(',', $values));
			$db->setQuery($query);
			$db->execute();
		}
    }

    public static function getActivity()
    {
	    $db = Factory::getDb();
        $query = $db->getQuery(true);

        $query->select(
            'h.*, ' . $query->concatenate(array('u.first_name', $db->quote(' '), 'u.last_name')) . ' AS owner_name, c.name as company_name,'
            . $query->concatenate(array('p.first_name', $db->quote(' '), 'p.last_name')) . ' AS person_name, d.name as deal_name, e.name as event_name,
            note_cat.name as notes_category_name, event_cat.name as events_category_name, old_event_cat.name as events_category_name_old,
            old_note_cat.name AS notes_category_name_old, doc.name AS document_name,status.name AS deal_status_name_old, status2.name AS deal_status_name,
            deal_source.name AS deal_source_name_old,deal_source_2.name AS deal_source_name, deal_stage.name AS deal_stage_name_old,deal_stage_2.name AS deal_stage_name,'
	        . $query->concatenate(array('deal_owner.first_name', $db->quote(' '), 'deal_owner.last_name')) . ' AS deal_owner_name_old,'
            . $query->concatenate(array('deal_owner_2.first_name', $db->quote(' '), 'deal_owner_2.last_name')) . ' AS deal_owner_name');

        $query->from('#__history AS h');
        $query->leftJoin('#__users AS u ON u.id = h.user_id');
        $query->leftJoin('#__companies AS c ON c.id = h.type_id AND h.type=' . $db->quote('company'));
        $query->leftJoin('#__notes AS n ON n.id = h.type_id AND h.type=' . $db->quote('note'));
        $query->leftJoin('#__deals AS d on d.id = h.type_id AND h.type=' . $db->quote('deal'));
        $query->leftJoin('#__people AS p on p.id = h.type_id AND h.type=' . $db->quote('person'));
        $query->leftJoin('#__goals AS g on g.id = h.type_id AND h.type=' . $db->quote('goal'));
        $query->leftJoin('#__events AS e on e.id = h.type_id AND h.type=' . $db->quote('event'));
        $query->leftJoin('#__reports AS r on r.id = h.type_id AND h.type=' . $db->quote('report'));
        $query->leftJoin('#__documents AS doc ON doc.id = h.type_id AND h.type=' . $db->quote('document'));
        $query->leftJoin('#__notes_categories as note_cat ON note_cat.id = h.new_value AND h.field=' . $db->quote('category_id') . ' AND h.type=' . $db->quote('notes'));
        $query->leftJoin('#__events_categories as event_cat ON event_cat.id = h.new_value AND h.field=' . $db->quote('category_id') .'  AND h.type=' . $db->quote('events'));
        $query->leftJoin('#__notes_categories as old_note_cat ON old_note_cat.id = h.old_value AND h.field=' . $db->quote('category_id') . ' AND h.type=' . $db->quote('notes'));
        $query->leftJoin('#__events_categories as old_event_cat ON old_event_cat.id = h.old_value AND h.field=' . $db->quote('category_id') . ' AND h.type=' . $db->quote('events'));
        $query->leftJoin('#__deal_status AS status ON status.id = h.old_value AND h.type=' . $db->quote('deal'));
        $query->leftJoin('#__deal_status AS status2 ON status2.id = h.new_value AND h.type=' . $db->quote('deal'));
        $query->leftJoin('#__sources AS deal_source ON deal_source.id = h.old_value AND h.type=' . $db->quote('deal'));
        $query->leftJoin('#__sources AS deal_source_2 ON deal_source_2.id = h.new_value AND h.type=' . $db->quote('deal'));
        $query->leftJoin('#__stages AS deal_stage ON deal_stage.id = h.old_value AND h.type=' . $db->quote('deal'));
        $query->leftJoin('#__stages AS deal_stage_2 ON deal_stage_2.id = h.new_value AND h.type=' . $db->quote('deal'));
        $query->leftJoin('#__users AS deal_owner ON deal_owner.id = h.old_value AND h.type=' . $db->quote('deal'));
        $query->leftJoin('#__users AS deal_owner_2 ON deal_owner_2.id = h.new_value AND h.type=' . $db->quote('deal'));

        $member_id = UsersHelper::getUserId();
        $member_role = UsersHelper::getRole();
        $team_id = UsersHelper::getTeamId();
        if ($member_role != 'exec') {
             //manager filter
            if ($member_role == 'manager') {
                $query->where('u.team_id = '.$team_id);
            } else {
            //basic user filter
                $query->where('h.user_id = '.$member_id);
            }
        }

        //TODO: Add assignees to the display (massive left join)
        $query->where('h.field!=' . $db->quote('assignee_id') . ' AND h.field!=' . $db->quote('repeats'));

        $query->order('h.date DESC');

	    $limit = is_null(self::$limit) ? 10 : self::$limit;

        return $db->setQuery($query, 0, $limit)->loadObjectList();
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
