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

 
 class CobaltHelperCpanel extends JObject
 {
     
        //load the navigation menu
        function getUsers(){
            return array( 
                            array(
                                'link' => JRoute::_('index.php?view=users'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/users.png',
                                'text' => JText::_('Manage Users'),
                                'access' => array( )
                            )
                        );
        }
        
        function getCustom(){
            return array( 
                            array(
                                'link' => JRoute::_('index.php?view=branding'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/branding.png',
                                'text' => JText::_('Colors and Branding'),
                                'access' => array( )
                            ),
                            array(
                                'link' => JRoute::_('index.php?view=stages'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/stages.png',
                                'text' => JText::_('Deal Stages'),
                                'access' => array( )
                            ),
                            array(
                                'link' => JRoute::_('index.php?view=categories'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/categories.png',
                                'text' => JText::_('Note Categories'),
                                'access' => array( )
                            ),
                            array(
                                'link' => JRoute::_('index.php?view=sources'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/sources.png',
                                'text' => JText::_('Sources'),
                                'access' => array( )
                            ),
                            array(
                                'link' => JRoute::_('index.php?view=custom'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/custom.png',
                                'text' => JText::_('Deal Custom Fields'),
                                'access' => array( )
                            ),
                            array(
                                'link' => JRoute::_('index.php?view=statuses'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/statuses.png',
                                'text' => JText::_('People Statuses'),
                                'access' => array( )
                            ),
                            array(
                                'link' => JRoute::_('index.php?view=templates'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/templates.png',
                                'text' => JText::_('Templates'),
                                'access' => array( )
                            ),
                            array(
                                'link' => JRoute::_('index.php?view=documents'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/documents.png',
                                'text' => JText::_('Shared Documents'),
                                'access' => array( )
                            ),
                            array(
                                'link' => JRoute::_('index.php?view=menu'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/menu.png',
                                'text' => JText::_('Menu'),
                                'access' => array( )
                            ),
                            array(
                                'link' => JRoute::_('index.php?view=config'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/config.png',
                                'text' => JText::_('Config'),
                                'access' => array( )
                            ),
                        );
        }

    function getMenu(){
        return array( 
                            array(
                                'link' => JRoute::_('index.php?view=cobalt'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/home.png',
                                'text' => JText::_(''),
                                'access' => array( )
                            ),
                            array(
                                'link' => JRoute::_('index.php?view=users'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/users.png',
                                'text' => JText::_(''),
                                'access' => array( )
                            )
                            
                        );
    }

    function button($button){
        $html  = '<div class="icon-wrapper">';
        $html .= '<div class="icon">';
        $html .= '<a href="'.$button['link'].'">';
            $html .= JHtml::_('image', $button['image'], NULL, NULL, true);
            $html .= '<span>'.$button['text'].'</span></a>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
 }
    