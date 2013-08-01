<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Companies;

use JFactory;
use Joomla\View\AbstractHtmlView;;
use Cobalt\Helper\ViewHelper;
use Cobalt\Model\Company as CompanyModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Raw extends AbstractHtmlView
{
    public function render()
    {
        //japplication
        $app = JFactory::getApplication();
        $id = $app->input->get('id') ? $app->input->get('id') : null;
        $layout = $this->getLayout();

        //get model
        $model = new CompanyModel;

        //layout
        switch ($layout) {
            case "add":
            case "edit":
                $this->company = $model->getCompany();
                $edit_custom_fields_view = ViewHelper::getView('custom','edit','html');
                $edit_custom_fields_view->type = "company";
                $edit_custom_fields_view->item = $this->company;
                $this->edit_custom_fields_view = $edit_custom_fields_view;
            break;
            case "entry":
                $this->company = $model->getCompany();
                $this->k = 0;
            break;
            case "list":
                $this->companies = $model->getCompanies($id);
                $this->total = $model->getTotal();
                $this->pagination = $model->getPagination();
                $this->state = $model->getState();
            break;
        }

        //display view
        echo parent::render();
    }

}
