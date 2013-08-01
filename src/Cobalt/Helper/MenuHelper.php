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
use JRoute;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class MenuHelper
{
    //load the navigation menu
    public static function loadNavi()
    {
        $links = self::getMenuLinks();

        foreach ($links as $link) {
            JHtmlSidebar::addEntry(
                $link['text'],
                $link['link']
            );
        }

    }

    public static function getMenuLinks()
    {
        return array(
            array(
                'link'   => JRoute::_('index.php?view=cobalt'),
                'class' => 'icon-home',
                'text' => TextHelper::_('Cobalt Dashboard'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_DASHBOARD_MENU_TOOLTIP'),
                'id'        => "dashboard_menu_link",
                'view'      => "cobalt"
            ),
            array(
                'link' => JRoute::_('index.php?view=users'),
                'class' => 'icon-user',
                'text' => TextHelper::_('Users'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_USERS_MENU_TOOLTIP'),
                'id'        => "user_menu_link",
                'view'      => "users"
            ),
            array(
                'link' => JRoute::_('index.php?view=branding'),
                'class' => 'icon-tint',
                'text' => TextHelper::_('Colors and Branding'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_COLORS_MENU_TOOLTIP'),
                'id'        => "colors_menu_link",
                'view'      => "branding"
            ),
            array(
                'link' => JRoute::_('index.php?view=stages'),
                'class' => 'icon-tasks',
                'text' => TextHelper::_('Deal Stages'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_STAGES_MENU_TOOLTIP'),
                'id'        => "stages_menu_link",
                'view'      => "stages"
            ),
            array(
                'link' => JRoute::_('index.php?view=categories'),
                'class' => 'icon-th-list',
                'text' => TextHelper::_('Note Categories'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_NOTES_MENU_TOOLTIP'),
                'id'        => "notes_menu_link",
                'view'      => "categories"
            ),
            array(
                'link' => JRoute::_('index.php?view=sources'),
                'class' => 'icon-random',
                'text' => TextHelper::_('Sources'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_SOURCES_MENU_TOOLTIP'),
                'id'        => "sources_menu_link",
                'view'      => "sources"
            ),
            array(
                'link' => JRoute::_('index.php?view=companycustom'),
                'class' => 'icon-edit',
                'text' => TextHelper::_('Company Custom Fields'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_CUSTOM_MENU_TOOLTIP'),
                'id'        => "companycustom_menu_link",
                'view'      => "companycustom"
            ),
            array(
                'link' => JRoute::_('index.php?view=peoplecustom'),
                'class' => 'icon-edit',
                'text' => TextHelper::_('People Custom Fields'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_CUSTOM_MENU_TOOLTIP'),
                'id'        => "peoplecustom_menu_link",
                'view'      => "peoplecustom"
            ),
            array(
                'link' => JRoute::_('index.php?view=dealcustom'),
                'class' => 'icon-edit',
                'text' => TextHelper::_('Deal Custom Fields'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_CUSTOM_MENU_TOOLTIP'),
                'id'        => "dealcustom_menu_link",
                'view'      => "dealcustom"
            ),
            array(
                'link' => JRoute::_('index.php?view=statuses'),
                'class' => 'icon-thumbs-up',
                'text' => TextHelper::_('People Statuses'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_STATUSES_MENU_TOOLTIP'),
                'id'        => "statuses_menu_link",
                'view'      => "statuses"
            ),
            array(
                'link' => JRoute::_('index.php?view=templates'),
                'class' => 'icon-filter',
                'text' => TextHelper::_('Workflow'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_WORKFLOW_MENU_TOOLTIP'),
                'id'        => "workflow_menu_link",
                'view'      => "templates"
            ),
            array(
                'link' => JRoute::_('index.php?view=admindocuments'),
                'class' => 'icon-folder-open',
                'text' => TextHelper::_('Shared Documents'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_DOCUMENTS_MENU_TOOLTIP'),
                'id'        => "admindocuments_menu_link",
                'view'      => "admindocuments"
            ),
            array(
                'link' => JRoute::_('index.php?view=menu'),
                'class' => 'icon-align-justify',
                'text' => TextHelper::_('Menu'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_MENU_MENU_TOOLTIP'),
                'id'        => "menu_menu_link",
                'view'      => "menu"
            ),
             array(
                'link' => JRoute::_('index.php?view=adminimport'),
                'class' => 'icon-upload',
                'text' => TextHelper::_('Import'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_CONFIG_IMPORT_TOOLTIP'),
                'id'        => "adminimport_menu_link",
                'view'      => "adminimport"
            ),
             array(
                'link' => JRoute::_('index.php?view=formwizard'),
                'class' => 'icon-star-empty',
                'text' => TextHelper::_('Form Wizard'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_FORMWIZARD_TOOLTIP'),
                'id'        => "formwizard_menu_link",
                'view'      => "formwizard"
            ),
            array(
                'link' => JRoute::_('index.php?view=config'),
                'class' => 'icon-cog',
                'text' => TextHelper::_('Settings'),
                'access' => array( ),
                'tooltip'   => TextHelper::_('COBALT_CONFIG_MENU_TOOLTIP'),
                'id'        => "config_menu_link",
                'view'      => "config"
            )
        );
    }

    public static function getQuickMenuLinks()
    {
        return array(
            array(
                'link' => JRoute::_('index.php?controller=users&task=add'),
                'class' => 'icon-user',
                'text' => TextHelper::_('COBALT_ADD_NEW_USER'),
                'access' => array( )
            ),
            array(
                'link' => JRoute::_('index.php?controller=stages&task=add'),
                'class' => 'icon-tasks',
                'text' => TextHelper::_('COBALT_ADD_NEW_DEAL_STAGE'),
                'access' => array( )
            ),
            array(
                'link' => JRoute::_('index.php?controller=categories&task=add'),
                'class' => 'icon-th-list',
                'text' => TextHelper::_('COBALT_ADD_NEW_NOTE_CATEGORY'),
                'access' => array( )
            ),
            array(
                'link' => JRoute::_('index.php?controller=sources&task=add'),
                'class' => 'icon-random',
                'text' => TextHelper::_('COBALT_ADD_NEW_SOURCE'),
                'access' => array( )
            ),
            array(
                'link' => JRoute::_('index.php?controller=companycustom&task=add'),
                'class' => 'icon-edit',
                'text' => TextHelper::_('COBALT_ADD_NEW_COMPANY_CUSTOM_FIELD'),
                'access' => array( )
            ),
            array(
                'link' => JRoute::_('index.php?controller=peoplecustom&task=add'),
                'class' => 'icon-edit',
                'text' => TextHelper::_('COBALT_ADD_NEW_PEOPLE_CUSTOM_FIELD'),
                'access' => array( )
            ),
            array(
                'link' => JRoute::_('index.php?controller=dealcustom&task=add'),
                'class' => 'icon-edit',
                'text' => TextHelper::_('COBALT_ADD_NEW_DEAL_CUSTOM_FIELD'),
                'access' => array( )
            ),
            array(
                'link' => JRoute::_('index.php?controller=statuses&task=add'),
                'class' => 'icon-thumbs-up',
                'text' => TextHelper::_('COBALT_ADD_NEW_PERSON_STATUS'),
                'access' => array( )
            ),
            array(
                'link' => JRoute::_('index.php?controller=templates&task=add'),
                'class' => 'icon-filter',
                'text' => TextHelper::_('COBALT_CREATE_NEW_WORKFLOW'),
                'access' => array( )
            )
        );
}

public static function getHelpMenuLinks()
{
    $types = array(
             array(
                'link' => 'index.php?view=users&layout=edit&show_fields=id',
                'class' => 'icon-user',
                'text' => TextHelper::_('COBALT_CREATE_NEW_USERS_HELP'),
                'access' => array( ),
                'config' => 'users_add',
                'completed_status' => ConfigHelper::getConfigValue('users_add'),
            ),
             array(
                'link' => 'index.php?view=config&layout=default&show_fields=timezone',
                'class' => 'icon-cog',
                'text' => TextHelper::_('COBALT_CREATE_LOCALE_HELP'),
                'access' => array( ),
                'config' => 'config_default',
                'completed_status' => ConfigHelper::getConfigValue('config_default')
            ),
             array(
                'link' => 'index.php?view=templates&layout=edit&show_fields=name',
                'class' => 'icon-filter',
                'text' => TextHelper::_('COBALT_CREATE_WORKFLOWS_HELP'),
                'access' => array( ),
                'config' => 'templates_edit',
                'completed_status' => ConfigHelper::getConfigValue('templates_edit')
            ),
             array(
                'link' => 'index.php?view=menu&layout=default&show_fields=header',
                'class' => 'icon-align-justify',
                'text' => TextHelper::_('COBALT_CREATE_MENU_ITEMS_HELP'),
                'access' => array( ),
                'config' => 'menu_default',
                'completed_status' => ConfigHelper::getConfigValue('menu_default')
            ),
             array(
                'link' => 'index.php?view=adminimport&layout=default&tab=sample',
                'class' => 'icon-list-alt',
                'text' => TextHelper::_('COBALT_CREATE_INSTALL_SAMPLE'),
                'access' => array( ),
                'config' => 'import_sample',
                'completed_status' => is_array(ConfigHelper::getConfigValue('import_sample',TRUE)) ? 1 : 0
            ),
             array(
                'link' => 'index.php?view=adminimport&layout=default',
                'class' => 'icon-share',
                'text' => TextHelper::_('COBALT_CREATE_IMPORT_HELP'),
                'access' => array( ),
                'config' => 'import_default',
                'completed_status' => ConfigHelper::getConfigValue('import_default')
            ),
             array(
                'link' => 'index.php?view=launch&layout=default',
                'class' => 'icon-arrow-right',
                'text' => TextHelper::_('COBALT_CREATE_LAUNCH_HELP'),
                'access' => array( ),
                'config' => 'launch_default',
                'completed_status' => ConfigHelper::getConfigValue('launch_default')
            )
        );

        return $types;

    }

    public static function getMenuModules()
    {
        $modules = array();

        $app = JFactory::getApplication();

        /** Side menu links **/
        $menu_links = MenuHelper::getMenuLinks();
        $menu = ViewHelper::getView('cobalt','menu','phtml');
        $menu->menu_links = $menu_links;
        $modules['menu'] = $menu;

        /** Quick Menu Links **/
        $quick_menu_links = MenuHelper::getQuickMenuLinks();
        $quick_menu = ViewHelper::getView('cobalt','quick_menu','phtml');
        $quick_menu->quick_menu_links = $quick_menu_links;
        $modules['quick_menu'] = $quick_menu;

        /** Determine help type on page **/
        $help_type_1 = $app->input->get('view') != '' || !is_null($app->input->get('view')) ? $app->input->get('view') : $app->input->get('controller');
        $help_type_2 = $app->input->get('layout') != '' || !is_null($app->input->get('layout')) ? $app->input->get('layout') : $app->input->get('task');
        $help_type_1 = ( $help_type_1 == "" || is_null($help_type_1) ) ? "" : $help_type_1;
        $help_type_2 = ( $help_type_2 == "" || is_null($help_type_2) ) ? "" : '_'.$help_type_2;
        $help_type = str_replace(".","_",$help_type_1.$help_type_2);
        $help_types = self::getHelpTypes();
        $show_help = ConfigHelper::getConfigValue('show_help');
        $launch_default = ConfigHelper::getConfigValue('launch_default');
        $step_completed = ConfigHelper::getConfigValue($help_type);
        $show_update_buttons = in_array($help_type,$help_types);

        /** Help Menu Links **/
        $help_menu_links = MenuHelper::getHelpMenuLinks();
        $help_menu = ViewHelper::getView('cobalt','help_menu','phtml');
        $help_menu->help_menu_links = $help_menu_links;
        $help_menu->help_type = $help_type;
        $help_menu->show_help = $show_help;
        $help_menu->launch_default = $launch_default;
        $help_menu->step_completed = $step_completed;
        $help_menu->show_update_buttons = $show_update_buttons;
        $modules['help_menu'] = $help_menu;

        $count = count($help_menu_links)-1;
        $completed = 0;
        foreach ($help_menu_links as $link) {
            if ($link['completed_status'] == 1) {
                $completed++;
            }
        }
        $modules['percentage'] = number_format(($completed/$count)*100,0) ;

        /** Auto show input tooltips **/
        $input_fields = explode(',',$app->input->get('show_fields'));
        if ( count ( $input_fields ) > 0 ) {
            $document = JFactory::getDocument();
            foreach ($input_fields as $input_field) {
                $document->addScriptDeclaration(
                        "$(document).ready(function(){showTooltip('".$input_field."');});"
                    );
            }
        }

        return $modules;

    }

    public static function getHelpTypes()
    {
        $links = self::getHelpMenuLinks();
        $types = array();
        foreach ($links as $link) {
            $types[] = $link['config'];
        }

        return $types;
    }

}
