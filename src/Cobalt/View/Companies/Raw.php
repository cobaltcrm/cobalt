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

use Joomla\Utilities\ArrayHelper;
use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Raw extends AbstractHtmlView
{
    public function render()
    {
        //japplication
        $app = Factory::getApplication();
        $id = $app->input->get('id') ? $app->input->get('id') : null;
        $layout = $this->getLayout();

        //get model
	    /** @var \Cobalt\Model\Company $model */
        $model = Factory::getModel('Company');

        //layout
        switch ($layout) {
            case "add":
            case "edit":
                $this->company = $model->getCompany();
                if (is_array($this->company)) {
                    $this->company = ArrayHelper::toObject($this->company);
                }
                $edit_custom_fields_view = Factory::getView('custom','edit','html', array('type' => 'company', 'item' => $this->company));
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
