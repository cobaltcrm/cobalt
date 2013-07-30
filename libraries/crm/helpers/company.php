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

 class CobaltHelperCompany extends JObject
 {

	public static function getCompany($id){

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
    public static function getTypes(){
        return array(   'all'=>CRMText::_('COBALT_ALL_COMPANIES'),
                        'today'=>CRMText::_('COBALT_COMPANIES_TASKS_TODAY'),
                        'tomorrow'=>CRMText::_('COBALT_COMPANIES_TASKS_TOMORROW'),
                        'updated_thirty'=>CRMText::_('COBALT_COMPANIES_UPDATED_LAST_MONTH'),
                        'recent'=>CRMText::_('COBALT_RECENTLY_ADDED'),
                        'past'=>CRMText::_('COBALT_CONTACTED_LONG_AGO'));
    }

    //get column filters
    public static function getColumnFilters(){
        return array(   'avatar'        =>  ucwords(CRMText::_('COBALT_AVATAR')),
                        'description'   =>  ucwords(CRMText::_('COBALT_EDIT_TASK_DESCRIPTION')),
                        'phone'         =>  ucwords(CRMText::_('COBALT_PEOPLE_PHONE')),
                        'fax'           =>  ucwords(CRMText::_('COBALT_COMPANY_FAX')),
                        'email'         =>  ucwords(CRMText::_('COBALT_COMPANY_EMAIL')),
                        'address'       =>  ucwords(CRMText::_('COBALT_PERSON_ADDRESS')),
                        'country'       =>  ucwords(CRMText::_('COBALT_PEOPLE_COUNTRY')),
                        'next_task'     =>  ucwords(CRMText::_('COBALT_PEOPLE_TASK')),
                        'notes'         =>  ucwords(CRMText::_('COBALT_PEOPLE_NOTES')),
                        'added'         =>  ucwords(CRMText::_('COBALT_COMPANIES_ADDED')),
                        'updated'       =>  ucwords(CRMText::_('COBALT_COMPANIES_UPDATED'))
                    );
    }

    //get selected column filters
    public static function getSelectedColumnFilters(){

        //get the user session data
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("companies_columns");
        $query->from("#__users");
        $query->where("id=".CobaltHelperUsers::getUserId());
        $db->setQuery($query);
        $results = $db->loadResult();

        //unserialize columns
        $columns = unserialize($results);
        if ( is_array($columns) ){
            return $columns;
        }else{
            //if it is empty then load a default set
            return CobaltHelperCompany::getDefaultColumnFilters();
        }
    }

    //get default column filters
    public static function getDefaultColumnFilters(){
        return array( 'avatar','phone','notes','added','updated' );
    }


 }
