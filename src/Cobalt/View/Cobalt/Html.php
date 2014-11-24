<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Cobalt;

use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\ConfigHelper;
use Cobalt\Helper\VersionHelper;
use Cobalt\Model\Config as ConfigModel;
// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();
        $app = Factory::getApplication();
        $document = $app->getDocument();
        $document->addScript($app->get('uri.media.full').'js/cobalt-admin.js');

        /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        $configModel = new ConfigModel;

        /** Component version **/
        $installedVersion   = ConfigHelper::getVersion();
        $latestVersion      = VersionHelper::getLatestVersion();
        $updateUrl = "http://www.cobaltcrm.org/support/login";
        $updatesFeed = $configModel->getUpdatesRSS();

        /** Launch completion **/
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
