<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\AdminDocuments;

use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Model\Documents as DocumentsModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    /**
     * display method
     **/
    public function render()
    {
        //authenticate the current user to make sure they are an admin
        UsersHelper::authenticateAdmin();

        //application
        $app = Factory::getApplication();

        // Create toolbar
        $this->toolbar = new Toolbar;

        //get the layout
        $layout = $this->getLayout();

        //gather information for view
        $model = new DocumentsModel;

        // Initialise state variables.
        $this->state = $model->getState();

        $model->set("_layout", $layout);

        if ($layout != "upload")
        {
            /** Menu Links **/
            $menu = MenuHelper::getMenuModules();
            $this->menu = $menu;
        }

        //determine layout type
        if ($layout && $layout == 'edit')
        {
            $this->toolbar->cancel('documents');
            $this->toolbar->save();
        }
        else
        {
            //buttons
            $this->toolbar->add(array('view' => 'documents', 'layout' => 'edit', 'format' => 'raw', 'tmpl' => 'component', 'return' => base64_encode(\JUri::current())), 'COBALT_TOOLBAR_NEW', 'btn btn-primary', 'plus', array('data-toggle'=> 'modal', 'data-target'=> '#uploadModal'));
            // ToolbarHelper::editList('edit');
            $this->toolbar->addDeleteRow();

            $app->getDocument()->addScriptDeclaration("
                var loc = 'documents';
                var order_dir = '" . $this->state->get('Documents.filter_order_Dir') . "';
                var order_col = '" . $this->state->get('Documents.filter_order') . "';
                var dataTableColumns = " . json_encode($model->getDataTableColumns()) . ";");

            $this->documents = $model->getDocuments();
        }

        //display
        return parent::render();
    }
}
