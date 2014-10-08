<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\CompanyCustom;

use JUri;
use JFactory;
use Joomla\View\AbstractHtmlView;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\DropdownHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Model\CompanyCustom as CompanyCustomModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //application
        $app = \Cobalt\Container::fetch('app');

        /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //gather information for view
        $model = new CompanyCustomModel;

        // Initialise state variables.
        $this->state = $model->getState();

        $layout = $this->getLayout();
        $model->set("_layout", $layout);

        if ($layout && $layout == 'edit')
        {
            //toolbar
            $this->toolbar = new Toolbar;
            $this->toolbar->save();
            $this->toolbar->cancel('companycustom');

            //assign view info
            $this->custom_types = DropdownHelper::getCustomTypes('company');
            $this->custom = $model->getItem();

            $app->getDocument()->addScriptDeclaration('jQuery(function() { CustomFieldConfig.bind(); });');
        }
        else
        {
            //buttons
            $this->toolbar = new Toolbar;
            $this->toolbar->addNew('companycustom');
            $this->toolbar->addDeleteRow();

            $app->getDocument()->addScriptDeclaration("
                var loc = 'companycustom';
                var order_dir = '" . $this->state->get('Companycustom.filter_order_Dir') . "';
                var order_col = '" . $this->state->get('Companycustom.filter_order') . "';
                var dataTableColumns = " . json_encode($model->getDataTableColumns()) . ";");

            $custom = $model->getCustom();
            $this->custom_fields = $custom;
        }

        //display
        return parent::render();
    }
}
