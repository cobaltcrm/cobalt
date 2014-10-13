<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\DealCustom;

use Cobalt\Model\DealCustom as DealCustomModel;
use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Helper\DropdownHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //application
        $app = Factory::getApplication();

        /** Menu Links **/
        $this->menu = MenuHelper::getMenuModules();

        //model
        $model = new DealCustomModel;

        // Initialise state variables.
        $this->state = $model->getState();

        //gather information for view
        $layout = $this->getLayout();
        $model->set("_layout",$layout);

        if ($layout && $layout == 'edit')
        {
            //toolbar
            $this->toolbar = new Toolbar;
            $this->toolbar->save();
            $this->toolbar->cancel('dealcustom');

            //assign view info
            $this->custom_types = DropdownHelper::getCustomTypes('deal');
            $this->custom = $model->getItem();

            $app->getDocument()->addScriptDeclaration('jQuery(function() { CustomFieldConfig.bind(); });');
        }
        else
        {
            //buttons
            $this->toolbar = new Toolbar;
            $this->toolbar->addNew('dealcustom');
            $this->toolbar->addDeleteRow();

            $app->getDocument()->addScriptDeclaration("
                var loc = 'dealcustom';
                var order_dir = '" . $this->state->get('Dealcustom.filter_order_Dir') . "';
                var order_col = '" . $this->state->get('Dealcustom.filter_order') . "';
                var dataTableColumns = " . json_encode($model->getDataTableColumns()) . ";");

            //assign view info
            $custom = $model->getCustom();
            $this->custom_fields = $custom;
        }

        //display
        return parent::render();
    }
}
