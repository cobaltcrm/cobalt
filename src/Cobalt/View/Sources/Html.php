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

use JUri;
use JFactory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\ToolbarHelper;
use Cobalt\Helper\DropdownHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\TextHelper;
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

        //document
        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'libraries/crm/media/js/cobalt-admin.js');

         /** Menu Links **/
        $menu = MenuHelper::getMenuModules();
        $this->menu = $menu;

        //gather information for view
        $model = new SourcesModel;
        $layout = $this->getLayout();
        $model->set("_layout",$layout);
        $this->pagination   = $model->getPagination();

        if ($layout && $layout == 'edit') {

            //toolbar
            ToolbarHelper::cancel('cancel');
            ToolbarHelper::save('save');

            //information for view
            $this->source_types = DropdownHelper::getSources();
            $this->source = $model->getSource();

        } else {

            //buttons
            ToolbarHelper::addNew('edit');
            ToolbarHelper::editList('edit');
            ToolbarHelper::deleteList(TextHelper::_('COBALT_CONFIRMATION'),'delete');

            //get sources
            $sources = $model->getSources();
            $this->sources = $sources;

            // Initialise state variables.
            $state = $model->getState();
            $this->state = $state;

            $this->listOrder  = $this->state->get('Sources.filter_order');
            $this->listDirn   = $this->state->get('Sources.filter_order_Dir');
            $this->saveOrder  = $this->listOrder == 's.ordering';

        }

        //display
        return parent::render();
    }
}
