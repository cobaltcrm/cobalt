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

class CobaltViewConfigHtml extends JViewHtml
{

    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //display toolbar
        ToolbarHelper::cancel('cancel');
        ToolbarHelper::save('save');

        //document
        $document = JFactory::getDocument();
        $document->addScript(JURI::base()."/libraries/crm/media/js/cobalt-admin.js");

        /* Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //get model
        $model = new CobaltModelConfig();
        $layout = $this->getLayout();
        $model->set("_layout",$layout);

        //get config
        $config = $model->getConfig();

        //generate timezones
        $list = timezone_identifiers_list();
        $timezones =  array();
        foreach ($list as $zone) {
           $timezones[$zone] = $zone;
        }

        //view references
        $this->imap_found = function_exists('imap_open') ? TRUE : FALSE ;
        $this->config = $config;
        $this->timezones = $timezones;
        $this->time_formats = DateHelper::getTimeFormats();
        $this->languages = ConfigHelper::getLanguages();
        $this->language = ConfigHelper::getLanguage();

        //display
        return parent::render();
    }
}
