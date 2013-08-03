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
use Cobalt\Helper\ToolbarHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\DropdownHelper;
use Cobalt\Model\PeopleCustom as PeopleCustomModel;
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

        //javascripts
        $document = JFactory::getDocument();
        $document->addScript(JURI::base().'src/Cobalt/media/js/cobalt-admin.js');
        $document->addScript(JURI::base().'src/Cobalt/media/js/custom_manager.js');

         //gather information for view
        $model = new PeopleCustomModel;
        $layout = $this->getLayout();
        $model->set("_layout",$layout);
        $this->pagination   = $model->getPagination();

        if ($layout && $layout == 'edit') {

            //toolbar button
            ToolbarHelper::cancel('cancel');
            ToolbarHelper::save('save');

            //assign view info
            $this->custom_types = DropdownHelper::getCustomTypes('people');
            $this->custom = $model->getItem();

            if ($this->custom['type'] != null) {
                $document->addScriptDeclaration('var type = "'.$this->custom['type'].'";');
            }

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

            $this->listOrder  = $this->state->get('Peoplecustom.filter_order');
            $this->listDirn   = $this->state->get('Peoplecustom.filter_order_Dir');
            $this->saveOrder  = $listOrder == 'c.ordering';

        }

        //display
        return parent::render();
    }
}
