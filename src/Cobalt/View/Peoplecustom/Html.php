<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\PeopleCustom;

use Joomla\View\AbstractHtmlView;
use JUri;
use JFactory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\DropdownHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Model\PeopleCustom as PeopleCustomModel;
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
        $model = new PeopleCustomModel;
        $layout = $this->getLayout();
        $model->set("_layout",$layout);
        
        // Initialise state variables.
        $this->state = $model->getState();

        if ($layout && $layout == 'edit')
        {
            //toolbar
            $this->toolbar = new Toolbar;
            $this->toolbar->save();
            $this->toolbar->cancel('peoplecustom');

            //assign view info
            $this->custom_types = DropdownHelper::getCustomTypes('people');
            $this->custom = $model->getItem();

            if ($this->custom->type != null)
            {
                $app->getDocument()->addScriptDeclaration('jQuery(function() { CustomFieldConfig.bind(); });');
            }
        }
        else
        {
            //buttons
            $this->toolbar = new Toolbar;
            $this->toolbar->addNew('peoplecustom');
            $this->toolbar->addDeleteRow();

            $app->getDocument()->addScriptDeclaration("
                var loc = 'peoplecustom';
                var order_dir = '" . $this->state->get('Peoplecustom.filter_order_Dir') . "';
                var order_col = '" . $this->state->get('Peoplecustom.filter_order') . "';
                var dataTableColumns = " . json_encode($model->getDataTableColumns()) . ";");

            $this->custom_fields = $model->getCustom();
        }

        //display
        return parent::render();
    }
}
