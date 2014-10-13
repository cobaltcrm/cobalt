<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Menu;

use Cobalt\Factory;
use Joomla\View\AbstractHtmlView;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Model\Menu as MenuModel;

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
        $document->addScript($app->get('uri.media.full')."js/cobalt-admin.js");

        /** Menu Links **/
        $side_menu = MenuHelper::getMenuModules();
        $this->side_menu = $side_menu;

        // Create toolbar
        $this->toolbar = new Toolbar;
        $this->toolbar->save();

        $model = new MenuModel;
        $menu = $model->getMenu();
        $menu_template = $model->getMenuTemplate();
        $this->menu = $menu;
        $this->menu_template = $menu_template;

        //display
        return parent::render();
    }
}
