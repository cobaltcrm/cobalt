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

 class PeopleHelper
 {

    //get an individual person
    public static function getPerson($id)
    {
        //get db object
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //generate query
        $query->select("p.first_name,p.last_name,p.id,p.company_id,c.name as company_name FROM #__people AS p");
        $query->leftJoin("#__companies AS c ON c.id = p.company_id");
        $query->where('p.id='.$id);
        $db->setQuery($query);

        //return results
        $row = $db->loadAssocList();

        return $row;

    }

    //return statuses
    public static function getStatusList($idsOnly = FALSE)
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //select statuses from db
        $query->select("*");
        $query->from("#__people_status");

        $query->order("ordering");

        $db->setQuery($query);

        //return statuses
        return $db->loadAssocList();
    }

    //return tags
    public static function getTagList()
    {
        //get db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //select statuses from db
        $query->select("*");
        $query->from("#__people_tags");
        $db->setQuery($query);

        //return statuses
        return $db->loadAssocList();
    }

    //return people filter types
    public static function getPeopleTypes($filters=TRUE)
    {
        if ($filters) {
            return array(   'all' => TextHelper::_('COBALT_ALL_PEOPLE'),
                            'leads' => TextHelper::_('COBALT_ALL_LEADS'),
                            'not_leads' => TextHelper::_('COBALT_ALL_PEOPLE_WHO_ARE_NOT_LEADS')   );
        } else {
            return array( 'contact' => TextHelper::_('COBALT_CONTACT') , 'lead' => TextHelper::_('COBALT_LEAD') );
        }
    }

    //return stages
    public static function getStages()
    {
        return array(   'all'=> TextHelper::_('COBALT_INCLUDE_EVERYTHING'),
                        'today'=> TextHelper::_('COBALT_TASKS_DUE_TODAY'),
                        'tomorrow'=> TextHelper::_('COBALT_TASKS_DUE_TOMORROW'),
                        'past_thirty'=> TextHelper::_('COBALT_UPDATED_PAST_THIRTY'),
                        'recently_added'=> TextHelper::_('COBALT_RECENTLY_ADDED'),
                        'last_import'=>  TextHelper::_('COBALT_PART_OF_LAST_IMPORT')
                    );
    }

    //TODO: Language File
    //get column filters
    public static function getColumnFilters()
    {
        return array(   'avatar'        =>  ucwords(TextHelper::_('COBALT_AVATAR')),
                        'company'       =>  ucwords(TextHelper::_('COBALT_COMPANY')),
                        'email'         =>  ucwords(TextHelper::_('COBALT_EMAIL')),
                        'phone'         =>  ucwords(TextHelper::_('COBALT_PHONE')),
                        'owner'         =>  ucwords(TextHelper::_('COBALT_OWNER')),
                        'status'        =>  ucwords(TextHelper::_('COBALT_STATUS')),
                        'source'        =>  ucwords(TextHelper::_('COBALT_SOURCE')),
                        //'tags'          =>  'Tags',
                        'type'          =>  ucwords(TextHelper::_('COBALT_TYPE')),
                        'next_task'     =>  ucwords(TextHelper::_('COBALT_NEXT_TASK')),
                        'notes'         =>  ucwords(TextHelper::_('COBALT_NOTES')),
                        'city'          =>  ucwords(TextHelper::_('COBALT_CITY')),
                        'state'         =>  ucwords(TextHelper::_('COBALT_STATE')),
                        'postal_code'   =>  ucwords(TextHelper::_('COBALT_POSTAL_CODE')),
                        'country'       =>  ucwords(TextHelper::_('COBALT_COUNTRY')),
                        'added'         =>  ucwords(TextHelper::_('COBALT_ADDED')),
                        'updated'       =>  ucwords(TextHelper::_('COBALT_UPDATED'))
                    );
    }

    //get selected column filters
    public static function getSelectedColumnFilters()
    {
        //get the user session data
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("people_columns");
        $query->from("#__users");
        $query->where("id=".CobaltHelperUsers::getUserId());
        $db->setQuery($query);
        $results = $db->loadResult();

        //unserialize columns
        $columns = unserialize($results);
        if ( is_array($columns) ) {
            return $columns;
        } else {
            //if it is empty then load a default set
            return PeopleHelper::getDefaultColumnFilters();
        }
    }

    //get default column filters
    public static function getDefaultColumnFilters()
    {
        return array( 'avatar','company','email','phone','type','status','source','notes');
    }

 }
