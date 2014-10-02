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

use JUri;
use JFactory;
use Joomla\View\AbstractHtmlView;
use Cobalt\Helper\ToolbarHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Model\Stages as StagesModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //gather information for view
        $model = new StagesModel;

        $layout = $this->getLayout();
        $model->set("_layout",$layout);
        $this->pagination   = $model->getPagination();
        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'src/Cobalt/media/js/cobalt-admin.js');

        if ($layout && $layout == 'edit') {

            ToolbarHelper::cancel('cancel');
            ToolbarHelper::save('save');

            $document->addScript(JURI::base().'src/Cobalt/media/js/stage_manager.js');
            $document->addScript(JURI::base().'src/Cobalt/media/js/bootstrap-colorpicker.js');
            //stylesheets
            $document->addStylesheet(JURI::base().'src/Cobalt/media/css/bootstrap-colorpicker.css');

            $this->stage = $model->getStage();

        } else {

            //buttons
            ToolbarHelper::addNew('edit');
            ToolbarHelper::editList('edit');
            ToolbarHelper::deleteList(TextHelper::_('COBALT_CONFIRMATION'),'delete');

            $stages = $model->getStages();
            $this->stages = $stages;

            // Initialise state variables.
            $state = $model->getState();
            $this->state = $state;

            $this->listOrder  = $this->state->get('Stages.filter_order');
            $this->listDirn   = $this->state->get('Stages.filter_order_Dir');
            $this->saveOrder  = $this->listOrder == 's.ordering';
        }

        //display
        return parent::render();
    }
}
