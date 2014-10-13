<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Categories;

use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\MenuHelper;
use Cobalt\Helper\Toolbar;
use Cobalt\Model\Categories as CategoriesModel;

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

        //site document
        $document = $app->getDocument();
        $document->addScript($app->get('uri.media.full')."js/cobalt-admin.js");

         //gather information for view
        $model = new CategoriesModel;

        $this->state = $model->getState();

        $layout = $this->getLayout();
        $model->set("_layout", $layout);

        if ($layout && $layout == 'edit')
        {
            $this->toolbar = new Toolbar;
            $this->toolbar->save();
            $this->toolbar->cancel('categories');

            $this->category = $model->getCategory();
        }
        else
        {
            $this->toolbar = new Toolbar;
            $this->toolbar->addNew('categories');
            $this->toolbar->addDeleteRow();

            $app->getDocument()->addScriptDeclaration("
                var loc = 'categories';
                var order_dir = '" . $this->state->get('Categories.filter_order_Dir') . "';
                var order_col = '" . $this->state->get('Categories.filter_order') . "';
                var dataTableColumns = " . json_encode($model->getDataTableColumns()) . ";");

            //view references
            $categories = $model->getCategories();
            $this->categories = $categories;

            $this->listOrder  = $this->state->get('Categories.filter_order');
            $this->listDirn   = $this->state->get('Categories.filter_order_Dir');
        }

        //display
        return parent::render();
    }
}
