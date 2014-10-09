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
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\DropdownHelper;
use Cobalt\Helper\TextHelper;
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
        $app = \Cobalt\Container::fetch('app');

        /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //gather information for view
        $model = new TemplatesModel;

        //get layout
        $layout = $this->getLayout();
        $model->set("_layout",$layout);

        //filter for layout type
        if ($layout == "edit")
        {
            //toolbar buttons
            $this->toolbar->cancel('templates');
            $this->toolbar->save();

            //javascripts
            // $document->addScript(JURI::base().'src/Cobalt/media/js/template_manager.js');

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

            $templates = $model->getTemplates();
            $this->templates = $templates;

            // Initialise state variables.
            $state = $model->getState();
            $this->state = $state;

            $this->listOrder = $this->state->get('Templates.filter_order');
            $this->listDirn   = $this->state->get('Templates.filter_order_Dir');

        }

        //display
        return parent::render();
    }
}
