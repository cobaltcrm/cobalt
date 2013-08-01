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
use Cobalt\Helper\ToolbarHelper;
use Cobalt\Helper\DropdownHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Model\CompanyCustom as CompanyCustomModel;

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

        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'libraries/crm/media/js/cobalt-admin.js');
        $document->addScript(JURI::base().'libraries/crm/media/js/custom_manager.js');

        //gather information for view
        $model = new CompanyCustomModel;

        $layout = $this->getLayout();
        $model->set("_layout",$layout);
        $this->pagination   = $model->getPagination();

        if ($layout && $layout == 'edit') {

            ToolbarHelper::cancel('cancel');
            ToolbarHelper::save('save');

            //assign view info
            $this->custom_types = DropdownHelper::getCustomTypes('company');
            $this->custom = $model->getItem();

        } else {

            //buttons
            ToolbarHelper::addNew('edit');
            ToolbarHelper::editList('edit');
            ToolbarHelper::deleteList(TextHelper::_('COBALT_CONFIRMATION'),'delete');

            $custom = $model->getCustom();
            $this->custom_fields = $custom;

            // Initialise state variables.
            $state = $model->getState();
            $this->state = $state;

            $this->listOrder  = $state->get('Companycustom.filter_order');
            $this->listDirn   = $state->get('Companycustom.filter_order_Dir');
            $this->saveOrder  = $this->listOrder == 'c.ordering';

        }

        //display
        return parent::render();
    }
}
