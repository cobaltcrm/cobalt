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

use JFactory;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

 class EventHelper
 {

    //get category listings for events
    public static function getCategories($appendLanguage=FALSE)
    {
        //grab db
        $db = \Cobalt\Container::get('db');

        //generate query
        $query = $db->getQuery(true);
        $query->select("name,id");
        $query->from("#__events_categories");

        //return results
        $db->setQuery($query);
        $results = $db->loadAssocList();
        $categories = array();
        foreach ($results as $key=>$category) {
            $categories[$category['id']] = $appendLanguage ? TextHelper::_('COBALT_OF_TYPE').' '.$category['name'] : $category['name'];
        }

        return $categories;

    }

    //get repeat intervals
    public static function getRepeatIntervals()
    {
        return array(
            'none'      => TextHelper::_("COBALT_DOESNT_REPEAT"),
            'daily'     => TextHelper::_("COBALT_DAILY"),
            'weekdays'  => TextHelper::_("COBALT_EVERY_WEEKDAY"),
            'weekly'    => TextHelper::_("COBALT_WEEKLY"),
            'weekly-mwf'=> TextHelper::_("COBALT_WEEKLY_MWF"),
            'weekly-tr' => TextHelper::_("COBALT_WEEKLY_TTH"),
            'monthly'   => TextHelper::_("COBALT_MONTHLY"),
            'yearly'    => TextHelper::_("COBALT_YEARLY")
        );

    }

    public static function getEventStatuses()
    {
        return array(
                '0' =>  TextHelper::_('COBALT_INCOMPLETE'),
                '1' =>  TextHelper::_('COBALT_COMPLETED')
            );

    }

    public static function getEventTypes()
    {
        return array(
                'all'   =>  TextHelper::_('COBALT_TASKS_SLASH_EVENTS'),
                'task'  =>  TextHelper::_('COBALT_TASKS'),
                'event' =>  TextHelper::_('COBALT_EVENTS')
            );

    }

    public static function getEventDueDates()
    {
        return array(
                'any'           => TextHelper::_('COBALT_DUE_ANY_TIME'),
                'today'         => TextHelper::_('COBALT_DUE_TODAY'),
                'tomorrow'      => TextHelper::_('COBALT_DUE_TOMORROW'),
                'this_week'     => TextHelper::_('COBALT_DUE_THIS_WEEK'),
                'past_due'      => TextHelper::_('COBALT_PAST_DUE'),
                'not_past_due'  => TextHelper::_('COBALT_NOT_PAST_DUE')
            );

    }

    public static function getEventAssociations()
    {
        return array(
                'any'       => TextHelper::_('COBALT_ANYTHING'),
                'person'    => TextHelper::_('COBALT_PEOPLE'),
                'deal'     => TextHelper::_('COBALT_DEALS'),
                'company'   => TextHelper::_('COBALT_COMPANIES')
            );

    }

 }
