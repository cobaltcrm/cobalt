<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Stages;

use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Model\Stages as StagesModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        //application
        $app = Factory::getApplication();

        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //gather information for view
        $model = new StagesModel;

        $layout = $this->getLayout();
        $model->set("_layout", $layout);
        $this->state = $model->getState();
        $this->pagination   = $model->getPagination();

        if ($layout && $layout == 'edit')
        {
            $this->toolbar = new Toolbar;
            $this->toolbar->save();
            $this->toolbar->cancel('stages');

            $this->stage = $model->getStage();
        }
        else
        {
            //buttons
            $this->toolbar = new Toolbar;
            $this->toolbar->addNew('stages');
            $this->toolbar->addDeleteRow();

            $app->getDocument()->addScriptDeclaration("
                var loc = 'stages';
                var order_dir = '" . $this->state->get('Stages.filter_order_Dir') . "';
                var order_col = '" . $this->state->get('Stages.filter_order') . "';
                var dataTableColumns = " . json_encode($model->getDataTableColumns()) . ";");

            $stages = $model->getStages();
            $this->stages = $stages;
        }

        //display
        return parent::render();
    }
}
