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

use Cobalt\Helper\RouteHelper;
use JUri;
use JHtml;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

 class CPanelHelper
 {
        //load the navigation menu
        public static function getUsers()
        {
            return array(
                            array(
                                'link' => RouteHelper::_('index.php?view=users'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/users.png',
                                'text' => TextHelper::_('Manage Users'),
                                'access' => array( )
                            )
                        );
        }

        public static function getCustom()
        {
            return array(
                            array(
                                'link' => RouteHelper::_('index.php?view=branding'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/branding.png',
                                'text' => TextHelper::_('Colors and Branding'),
                                'access' => array( )
                            ),
                            array(
                                'link' => RouteHelper::_('index.php?view=stages'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/stages.png',
                                'text' => TextHelper::_('Deal Stages'),
                                'access' => array( )
                            ),
                            array(
                                'link' => RouteHelper::_('index.php?view=categories'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/categories.png',
                                'text' => TextHelper::_('Note Categories'),
                                'access' => array( )
                            ),
                            array(
                                'link' => RouteHelper::_('index.php?view=sources'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/sources.png',
                                'text' => TextHelper::_('Sources'),
                                'access' => array( )
                            ),
                            array(
                                'link' => RouteHelper::_('index.php?view=custom'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/custom.png',
                                'text' => TextHelper::_('Deal Custom Fields'),
                                'access' => array( )
                            ),
                            array(
                                'link' => RouteHelper::_('index.php?view=statuses'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/statuses.png',
                                'text' => TextHelper::_('People Statuses'),
                                'access' => array( )
                            ),
                            array(
                                'link' => RouteHelper::_('index.php?view=templates'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/templates.png',
                                'text' => TextHelper::_('Templates'),
                                'access' => array( )
                            ),
                            array(
                                'link' => RouteHelper::_('index.php?view=documents'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/documents.png',
                                'text' => TextHelper::_('Shared Documents'),
                                'access' => array( )
                            ),
                            array(
                                'link' => RouteHelper::_('index.php?view=menu'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/menu.png',
                                'text' => TextHelper::_('Menu'),
                                'access' => array( )
                            ),
                            array(
                                'link' => RouteHelper::_('index.php?view=config'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/config.png',
                                'text' => TextHelper::_('Config'),
                                'access' => array( )
                            ),
                        );
        }

    public static function getMenu()
    {
        return array(
                            array(
                                'link' => RouteHelper::_('index.php?view=cobalt'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/home.png',
                                'text' => TextHelper::_(''),
                                'access' => array( )
                            ),
                            array(
                                'link' => RouteHelper::_('index.php?view=users'),
                                'image' => JURI::base().'libraries/crm/media/images/cpanel/users.png',
                                'text' => TextHelper::_(''),
                                'access' => array( )
                            )

                        );
    }

    public static function button($button)
    {
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
