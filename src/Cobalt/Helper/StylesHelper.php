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

 class StylesHelper
 {
        public static function getSiteName()
        {
            $db = Factory::getDb();
            $query = $db->getQuery(true);

            $query->select("site_name")->from("#__branding")->where("assigned=1");

            $db->setQuery($query);

            $name = $db->loadResult();

            return $name;

        }

        public static function getSiteLogo()
        {
            $db = Factory::getDb();
            $query = $db->getQuery(true);

            $query->select("site_logo")->from("#__branding")->where("assigned=1");

            $db->setQuery($query);

            $logo = $db->loadResult();

            return Factory::getApplication()->get('uri.media.full') . "logos/" . $logo;
        }

        //dynamically generate styles
        public static function getDynamicStyle()
        {
            //database
            $db = Factory::getDb();
            $query = $db->getQuery(true);

            //query
            $query->select("b.*");
            $query->from("#__branding AS b");
            $query->where("assigned=1");

            //return results
            $db->setQuery($query);
            $theme = $db->loadAssocList();
            $theme = $theme[0];

            //assign style declarations
            $style = ".google_map_center{background:".$theme['table_header_text'].";border:3px solid ".$theme['table_header_row'].";}";
            $style .= ".navbar-inner{background:".$theme['header']." ;}";
            $style .= ".navbar .nav > li > a:hover{background:".$theme['tabs_hover']." ;}";
            $style .= ".navbar .nav > li > a:hover{color:".$theme['tabs_hover_text']." ;}";
            $style .= ".table th{background:".$theme['table_header_row']." ;}";
            $style .= ".table th{color:".$theme['table_header_text']." ;}";
            $style .= ".table th a{color:".$theme['table_header_text']." ;}";
            $style .= ".table tr td a,a{color:".$theme['link']." ;}";
            $style .= ".table tr td a:hover,a:hover{color:".$theme['link_hover']." ;}";
            $style .= ".block-btn{border-left:1px solid ".$theme['block_btn_border']." ;}";
            $style .= ".feature-btn{border-left:1px solid ".$theme['feature_btn_border']." ; border-right:1px solid ".$theme['feature_btn_border']." ;background:".$theme['feature_btn_bg'].";}";

            //return
            return $style;
        }
 }
