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

use JUri;
use JFactory;
use Joomla\View\AbstractHtmlView;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\ToolbarHelper;
use Cobalt\Model\Menu as MenuModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        $document = JFactory::getDocument();
        $document->addScript(JURI::base()."/libraries/crm/media/js/cobalt-admin.js");

        /** Menu Links **/
        $side_menu = MenuHelper::getMenuModules();
        $this->side_menu = $side_menu;

        ToolbarHelper::cancel('cancel');
        ToolbarHelper::save('save');

        $model = new MenuModel;
        $menu = $model->getMenu();
        $menu_template = $model->getMenuTemplate();
        $this->menu = $menu;
        $this->menu_template = $menu_template;

        //display
        return parent::render();
    }
}
