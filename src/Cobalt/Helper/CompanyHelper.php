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

 class CompanyHelper
 {

    public static function getCompany($id)
    {
        //get db object
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //generate query
        $query->select("name,id FROM #__companies");
        $query->where('id='.$id);
        $db->setQuery($query);

        //return results
        $row = $db->loadAssocList();

        return $row;

    }

    //get company filter types
    public static function getTypes()
    {
        return array(   'all'=>TextHelper::_('COBALT_ALL_COMPANIES'),
                        'today'=>TextHelper::_('COBALT_COMPANIES_TASKS_TODAY'),
                        'tomorrow'=>TextHelper::_('COBALT_COMPANIES_TASKS_TOMORROW'),
                        'updated_thirty'=>TextHelper::_('COBALT_COMPANIES_UPDATED_LAST_MONTH'),
                        'recent'=>TextHelper::_('COBALT_RECENTLY_ADDED'),
                        'past'=>TextHelper::_('COBALT_CONTACTED_LONG_AGO'));
    }

    //get column filters
    public static function getColumnFilters()
    {
        return array(   'avatar'        =>  ucwords(TextHelper::_('COBALT_AVATAR')),
                        'description'   =>  ucwords(TextHelper::_('COBALT_EDIT_TASK_DESCRIPTION')),
                        'phone'         =>  ucwords(TextHelper::_('COBALT_PEOPLE_PHONE')),
                        'fax'           =>  ucwords(TextHelper::_('COBALT_COMPANY_FAX')),
                        'email'         =>  ucwords(TextHelper::_('COBALT_COMPANY_EMAIL')),
                        'address'       =>  ucwords(TextHelper::_('COBALT_PERSON_ADDRESS')),
                        'country'       =>  ucwords(TextHelper::_('COBALT_PEOPLE_COUNTRY')),
                        'next_task'     =>  ucwords(TextHelper::_('COBALT_PEOPLE_TASK')),
                        'notes'         =>  ucwords(TextHelper::_('COBALT_PEOPLE_NOTES')),
                        'added'         =>  ucwords(TextHelper::_('COBALT_COMPANIES_ADDED')),
                        'updated'       =>  ucwords(TextHelper::_('COBALT_COMPANIES_UPDATED'))
                    );
    }

    //get selected column filters
    public static function getSelectedColumnFilters()
    {
        //get the user session data
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("companies_columns");
        $query->from("#__users");
        $query->where("id=".UsersHelper::getUserId());
        $db->setQuery($query);
        $results = $db->loadResult();

        //unserialize columns
        $columns = unserialize($results);
        if ( is_array($columns) ) {
            return $columns;
        } else {
            //if it is empty then load a default set
            return CompanyHelper::getDefaultColumnFilters();
        }
    }

    //get default column filters
    public static function getDefaultColumnFilters()
    {
        return array( 'avatar','phone','notes','added','updated' );
    }

 }
