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

 class CobaltHelperEvent extends JObject
 {

    //get category listings for events
    public static function getCategories($appendLanguage=FALSE) {

        //grab db
        $db = JFactory::getDbo();

        //generate query
        $query = $db->getQuery(true);
        $query->select("name,id");
        $query->from("#__events_categories");

        //return results
        $db->setQuery($query);
        $results = $db->loadAssocList();
        $categories = array();
        foreach ( $results as $key=>$category ){
            $categories[$category['id']] = $appendLanguage ? CRMText::_('COBALT_OF_TYPE').' '.$category['name'] : $category['name'];
        }
        return $categories;

    }

    //get repeat intervals
    public static function getRepeatIntervals() {

        return array(
            'none'      => CRMText::_("COBALT_DOESNT_REPEAT"),
            'daily'     => CRMText::_("COBALT_DAILY"),
            'weekdays'  => CRMText::_("COBALT_EVERY_WEEKDAY"),
            'weekly'    => CRMText::_("COBALT_WEEKLY"),
            'weekly-mwf'=> CRMText::_("COBALT_WEEKLY_MWF"),
            'weekly-tr' => CRMText::_("COBALT_WEEKLY_TTH"),
            'monthly'   => CRMText::_("COBALT_MONTHLY"),
            'yearly'    => CRMText::_("COBALT_YEARLY")
        );

    }

    public static function getEventStatuses(){

        return array(
                '0' =>  CRMText::_('COBALT_INCOMPLETE'),
                '1' =>  CRMText::_('COBALT_COMPLETED')
            );

    }

    public static function getEventTypes(){

        return array(
                'all'   =>  CRMText::_('COBALT_TASKS_SLASH_EVENTS'),
                'task'  =>  CRMText::_('COBALT_TASKS'),
                'event' =>  CRMText::_('COBALT_EVENTS')
            );

    }


    public static function getEventDueDates(){

        return array(
                'any'           => CRMText::_('COBALT_DUE_ANY_TIME'),
                'today'         => CRMText::_('COBALT_DUE_TODAY'),
                'tomorrow'      => CRMText::_('COBALT_DUE_TOMORROW'),
                'this_week'     => CRMText::_('COBALT_DUE_THIS_WEEK'),
                'past_due'      => CRMText::_('COBALT_PAST_DUE'),
                'not_past_due'  => CRMText::_('COBALT_NOT_PAST_DUE')
            );

    }

    public static function getEventAssociations(){

        return array(
                'any'       => CRMText::_('COBALT_ANYTHING'),
                'person'    => CRMText::_('COBALT_PEOPLE'),
                'deal'     => CRMText::_('COBALT_DEALS'),
                'company'   => CRMText::_('COBALT_COMPANIES')
            );

    }


 }