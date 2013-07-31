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

 class DocumentHelper
 {

    //get users total associated documents
    public static function getTotalDocuments()
    {
        //db
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //select
        $query->select("count(*)");
        $query->from("#__documents AS d");

        //filter depending on user access
        $role = UsersHelper::getRole();
        $user_id = UsersHelper::getUserId();
        $team_id = UsersHelper::getTeamId();
        if ($role != 'exec') {
            if ($role == 'manager') {
                $query->join('inner','#__users AS u ON d.owner_id = u.id');
                $query->where('u.team_id='.$team_id);
            } else {
                $query->where(array("d.owner_id=".$user_id,'d.shared=1'),'OR');
            }
        }

        //return count
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }

    //function to get possible document association types
    public static function getAssocTypes()
    {
        return array(   'all'=>TextHelper::_('COBALT_ALL_DOCUMENTS'),
                        'deals'=>TextHelper::_('COBALT_DOCUMENTS_DEALS'),
                        'people'=>TextHelper::_('COM_CMRERY_DOCUMENTS_PEOPLE'),
                        'companies'=>TextHelper::_('COBALT_DOCUMENTS_COMPANIES'),
                        'emails'=>TextHelper::_('COBALT_DOCUMENTS_EMAILS'),
                        'shared'=>TextHelper::_('COBALT_DOCUMENTS_SHARED'));
    }

    //get different document doctypes
    public static function getDocTypes()
    {
        return array(   'all'=>TextHelper::_('COBALT_ALL_TYPES'),
                        'spreadsheets'=>TextHelper::_('COBALT_SPREADSHEETS'),
                        'images'=>TextHelper::_('COBALT_IMAGES'),
                        'documents'=>TextHelper::_('COBALT_DOCUMENTS'),
                        'pdfs'=>TextHelper::_('COBALT_PDFS'),
                        'presentations'=>TextHelper::_('COBALT_PRESENTATIONS'),
                        'others'=>TextHelper::_('COBALT_OTHERS'));
    }

 }
