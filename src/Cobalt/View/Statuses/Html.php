<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Statuses;

use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Model\Statuses as StatusesModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render()
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //application
        $app = Factory::getApplication();

        // Create toolbar
        $this->toolbar = new Toolbar;

         /** Menu Links **/
        $this->menu = MenuHelper::getMenuModules();

        $layout = $this->getLayout();

        //gather information for view
        $model = new StatusesModel;

        // Initialise state variables.
        $this->state = $model->getState();

        $model->set("_layout", $layout);

        if ($layout && $layout == 'edit')
        {
            //toolbar buttons
            $this->toolbar->cancel('statuses');
            $this->toolbar->save();

            //get status
            $this->status = $model->getStatus();
        }
        else
        {
            //buttons
            $this->toolbar->addNew('statuses');
            // ToolbarHelper::editList('edit');
            $this->toolbar->addDeleteRow();

            $app->getDocument()->addScriptDeclaration("
                var loc = 'statuses';
                var order_dir = '" . $this->state->get('Statuses.filter_order_Dir') . "';
                var order_col = '" . $this->state->get('Statuses.filter_order') . "';
                var dataTableColumns = " . json_encode($model->getDataTableColumns()) . ";");

            //statuses
            $statuses = $model->getStatuses();
            $this->statuses  = $statuses;
        }

        //display
        return parent::render();
    }
}
