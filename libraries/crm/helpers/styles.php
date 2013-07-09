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

 class CobaltHelperStyles extends JObject
 {
        function getSiteName(){

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            $query->select("site_name")->from("#__branding")->where("assigned=1");

            $db->setQuery($query);

            $name = $db->loadResult();

            return $name;

        }

        function getSiteLogo(){

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            $query->select("site_logo")->from("#__branding")->where("assigned=1");

            $db->setQuery($query);

            $logo = $db->loadResult();

            return JURI::base()."/libraries/crm/media/logos/".$logo;
        }

        //get base stylesheets
        function getBaseStyle(){
            return JURI::base()."libraries/crm/media/css/bootstrap.css";
        }

        //dynamically generate styles
        function getDynamicStyle(){
            //database
            $db = JFactory::getDBO();
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

        //load all styles
        function loadStyleSheets(){

            $app = JFactory::getApplication();
            $document = JFactory::getDocument();

            $view = $app->input->get('view');
            if ( $view == "print" ){
                $document->addStyleSheet( JURI::base().'libraries/crm/media/css/print.css' );
            }

			if(CobaltHelperTemplate::isMobile()) {
                $document->addStyleSheet( JURI::base().'libraries/crm/media/css/mobile.css' );
				$document->addStyleSheet( JURI::base().'libraries/crm/media/css/jquery.mobile.min.css' );
                $document->addStyleSheet( JURI::base().'libraries/crm/media/css/jquery.mobile.datepicker.css' );
			} else {
	            //base stylesheet
	            $base_style = CobaltHelperStyles::getBaseStyle();

	            //dynamic stylesheet
	            $dyn_style = CobaltHelperStyles::getDynamicStyle();

	            //add sheets to document
	            $document->addStyleSheet($base_style);
                $document->addStyleSheet(JURI::base().'libraries/crm/media/css/datepicker.css');
                $document->addStyleSheet(JURI::base().'libraries/crm/media/css/bootstrap-responsive.css');
                $document->addStyleSheet(JURI::base().'libraries/crm/media/css/bootstrap-colorpicker.css');
                $document->addStyleSheet(JURI::base().'libraries/crm/media/css/style.css');
                $document->addStyleSheet('http://fonts.googleapis.com/css?family=Open+Sans:300,400');
	            $document->addStyleDeclaration($dyn_style);

            }

            jimport('joomla.environment.browser');
            $browser = JBrowser::getInstance();
            $browserType = $browser->getBrowser();
            $browserVersion = $browser->getMajor();
            if(($browserType == 'msie') && ($browserVersion < 8))
            {
              $document->addStyleSheet( JURI::base().'libraries/crm/media/css/ie.css' );
            }

        }



 }
