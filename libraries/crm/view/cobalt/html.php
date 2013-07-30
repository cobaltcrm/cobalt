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

class CobaltViewCobaltHtml extends JViewHtml
{

    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        CobaltHelperUsers::authenticateAdmin();

        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'libraries/crm/media/js/cobalt-admin.js');

        /** Menu Links **/
        $menu = CobaltHelperMenu::getMenuModules();
        $this->menu = $menu;

        $configModel = new CobaltModelConfig();

        /** Component version **/
        $installedVersion   = CobaltHelperConfig::getVersion();
        $latestVersion      = CobaltHelperVersion::getLatestVersion();
        $updateUrl = "http://www.cobaltcrm.org/support/login";
        $updatesFeed = $configModel->getUpdatesRSS();

        /** Launch completion **/
        $configModel = new CobaltModelConfig();
        $config = $configModel->getConfig();
        $this->launch_default = $config->launch_default;
        $percentage = $menu['percentage'];
        $this->setup_percent = $percentage;

        /** php version check **/
        $this->php_version = (float) phpversion();
        $this->php_version_check = $this->php_version >= 5.3 ? TRUE : FALSE;

        /** View Ref **/
        $this->installedVersion = $installedVersion;
        $this->latestVersion = $latestVersion;
        $this->updateUrl = $updateUrl;
        $this->updatesFeed = $updatesFeed;

        //display
        return parent::render();
    }
}
