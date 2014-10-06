<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Config;

use Joomla\View\AbstractHtmlView;
use JUri;
use JFactory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\ToolbarHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\ConfigHelper;
use Cobalt\Model\Config as ConfigModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{

    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //display toolbar
        $this->toolbar = new Toolbar;
        $this->toolbar->save();

        //document
        $document = JFactory::getDocument();
        $document->addScript(JURI::base()."/src/Cobalt/media/js/cobalt-admin.js");

        /* Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //get model
        $model = new ConfigModel;
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
