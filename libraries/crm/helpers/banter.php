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
defined( '_CEXEC' ) or die( 'Restricted access' );

 class CobaltHelperBanter extends JObject
 {

    public static function hasBanter()
    {
        jimport('joomla.filesystem.folder');
        if ( JFolder::exists(JPATH_ROOT.'/administrator/components/com_banter') ) {
            return 1;
        } else {
            return 0;
        }
    }

     public static function getAssociationName($associationId,$associationType)
     {
         $db = JFactory::getDBO();
         $query = $db->getQuery(true);

         switch ($associationType) {
             case "deal":
                 $table = "deals";
                 $select = "name";
             break;
             case "person":
                 $table = "people";
                 $select = "CONCAT(first_name,' ',last_name)";
             break;
             case "company":
                 $table = "companies";
                 $select = "name";
            break;
         }

         $query->select($select)
             ->from("#__".$table)
             ->where("id=".$associationId);

        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
     }

     public static function getAssociationLink($associationId,$associationType)
     {
         switch ($associationType) {
             case "deal":
                 $view = "deals";
             break;
             case "person":
                 $view = "people";
             break;
             case "company":
                 $view = "companies";
             break;
         }

         return JRoute::_('index.php?view='.$view."&id=".$associationId);
     }

 }
