<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Branding;

use Cobalt\Factory;
use Joomla\View\AbstractHtmlView;
use Cobalt\Model\Branding as BrandingModel;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\StylesHelper;
use Cobalt\Helper\ToolbarHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //menu Links
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

         //add javascript
	    $app = Factory::getApplication();
        $document = $app->getDocument();
        $document->addScript($app->get('uri.media.full').'js/branding_manager.js');
        $document->addScript($app->get('uri.media.full').'js/cobalt-admin.js');

        //view refs
        $model = new BrandingModel;
        $themes = $model->getThemes();

        //toolbar buttons
        ToolbarHelper::save('save','Save');

        //toolbar items
        $list = array(
            'dashboard',
            'deals',
            'people',
            'companies',
            'calendar',
            'documents',
            'goals',
            'reports'
        );
        $this->toolbar_list = $list;
        $this->themes = $themes;
        $this->site_logo = StylesHelper::getSiteLogo();
        $this->site_name = StylesHelper::getSiteName();

        //assign default theme
        foreach ($this->themes as $key=>$row) {
                if ( $row['assigned'] )
                 $document->addScriptDeclaration("var assigned_theme=".$row['id'].";");
         }

        //display
        return parent::render();
    }
}
