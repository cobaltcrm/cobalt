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

 class NoteHelper
 {

    //get category listings for notes
    public static function getCategories()
    {
        //grab db
        $db = Factory::getDb();

        //generate query
        $query = $db->getQuery(true);
        $query->select("name,id");
        $query->from("#__notes_categories");

        //return results
        $db->setQuery($query);
        $results = $db->loadAssocList();
        $categories = array();
        foreach ($results as $key=>$category) {
            $categories[$category['id']] = $category['name'];
        }

        return $categories;

    }

 }
