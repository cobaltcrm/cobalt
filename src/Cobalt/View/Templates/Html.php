<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Templates;

use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\DropdownHelper;
use Cobalt\Model\Templates as TemplatesModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{

    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        // Create toolbar
        $this->toolbar = new Toolbar;

        //application
        $app = Factory::getApplication();

        /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //gather information for view
        $model = new TemplatesModel;

        // Initialise state variables.
        $this->state = $model->getState();

        //get layout
        $layout = $this->getLayout();
        $model->set("_layout", $layout);

        //filter for layout type
        if ($layout == "edit")
        {
            //toolbar buttons
            $this->toolbar->cancel('templates');
            $this->toolbar->save();

            //javascripts
            $app->getDocument()->addScriptDeclaration('jQuery(function() { TemplateConfig.bind(); });');

            //assign view data
            $this->template_types = DropdownHelper::getTemplateTypes();
            $this->template =  $model->getTemplate();
        }
        else
        {
            //buttons
            $this->toolbar->addNew('templates');
            // ToolbarHelper::editList('edit');
            $this->toolbar->addDeleteRow();

            $app->getDocument()->addScriptDeclaration("
                var loc = 'templates';
                var order_dir = '" . $this->state->get('Templates.filter_order_Dir') . "';
                var order_col = '" . $this->state->get('Templates.filter_order') . "';
                var dataTableColumns = " . json_encode($model->getDataTableColumns()) . ";");

            $templates = $model->getTemplates();
            $this->templates = $templates;
        }

        //display
        return parent::render();
    }
}
