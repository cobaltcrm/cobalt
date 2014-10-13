<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Sources;

use Cobalt\Factory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\DropdownHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Model\Sources as SourcesModel;
use Joomla\View\AbstractHtmlView;

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
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //gather information for view
        $model = new SourcesModel;
        $layout = $this->getLayout();
        $model->set("_layout", $layout);

        // Initialise state variables.
        $state = $model->getState();
        $this->state = $state;

        if ($layout && $layout == 'edit')
        {
            //toolbar
            $this->toolbar = new Toolbar;
            $this->toolbar->save();
            $this->toolbar->cancel('sources');

            //information for view
            $this->source_types = DropdownHelper::getSources();
            $this->source = $model->getSource();
        }
        else
        {
            //buttons
            $this->toolbar = new Toolbar;
            $this->toolbar->addNew('sources');
            $this->toolbar->addDeleteRow();

            $app->getDocument()->addScriptDeclaration("
                var loc = 'sources';
                var order_dir = '" . $this->state->get('Sources.filter_order_Dir') . "';
                var order_col = '" . $this->state->get('Sources.filter_order') . "';
                var dataTableColumns = " . json_encode($model->getDataTableColumns()) . ";");

            //get sources
            $sources = $model->getSources();
            $this->sources = $sources;

            $this->listOrder  = $this->state->get('Sources.filter_order');
            $this->listDirn   = $this->state->get('Sources.filter_order_Dir');
            $this->saveOrder  = $this->listOrder == 's.ordering';
        }

        //display
        return parent::render();
    }
}
