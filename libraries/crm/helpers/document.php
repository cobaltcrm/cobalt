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

 class CobaltHelperDocument extends JObject
 {
    
    //get users total associated documents
    function getTotalDocuments(){
        
        //db
        $db =& JFactory::getDBO();
        $query = $db->getQuery(true);
        
        //select
        $query->select("count(*)");
        $query->from("#__documents AS d");
        
        //filter depending on user access
        $role = CobaltHelperUsers::getRole();
        $user_id = CobaltHelperUsers::getUserId();
        $team_id = CobaltHelperUsers::getTeamId();
        if ( $role != 'exec' ){
            if ( $role == 'manager' ){
                $query->join('inner','#__users AS u ON d.owner_id = u.id');
                $query->where('u.team_id='.$team_id);
            }else{
                $query->where(array("d.owner_id=".$user_id,'d.shared=1'),'OR');
            }
        }
        
        
        //return count
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }
    
    //function to get possible document association types
    function getAssocTypes(){
        return array(   'all'=>CRMText::_('COBALT_ALL_DOCUMENTS'),
                        'deals'=>CRMText::_('COBALT_DOCUMENTS_DEALS'),
                        'people'=>CRMText::_('COM_CMRERY_DOCUMENTS_PEOPLE'),
                        'companies'=>CRMText::_('COBALT_DOCUMENTS_COMPANIES'),
                        'emails'=>CRMText::_('COBALT_DOCUMENTS_EMAILS'),
                        'shared'=>CRMText::_('COBALT_DOCUMENTS_SHARED'));
    }
    
    //get different document doctypes
    function getDocTypes(){
        return array(   'all'=>CRMText::_('COBALT_ALL_TYPES'),
                        'spreadsheets'=>CRMText::_('COBALT_SPREADSHEETS'),
                        'images'=>CRMText::_('COBALT_IMAGES'),
                        'documents'=>CRMText::_('COBALT_DOCUMENTS'),
                        'pdfs'=>CRMText::_('COBALT_PDFS'),
                        'presentations'=>CRMText::_('COBALT_PRESENTATIONS'),
                        'others'=>CRMText::_('COBALT_OTHERS'));
    }
    
 }