<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\People;

use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;

defined( '_CEXEC' ) or die( 'Restricted access' );

class Raw extends AbstractHtmlView
{
    public function render()
    {
        $app = Factory::getApplication();
        $document = $app->getDocument();
        $id = $app->input->get('id') ? $app->input->get('id') : null;

        $company_id = $app->input->get('company_id');

        //retrieve people from model
        $model = Factory::getModel('People');
        $model->set('company_id',$company_id);

        $layout = $this->getLayout();

        $this->total = $model->getTotal();
        $this->pagination = $model->getPagination();

        //assign refs
        switch ($layout) {
            case "entry":
                $this->k = 0;
                $this->person = $model->getPerson();
            break;
            case "add":
            case "edit":
                $this->person = $model->getPerson();

                $this->edit_custom_fields_view = Factory::getView('custom','edit','phtml',array('type'=>'people','item'=>$this->person));

                $companyModel = Factory::getModel('Company');
                $json = TRUE;

                $companyNames = $companyModel->getCompanyNames($json);
                $document->addScriptDeclaration("var company_names=".$companyNames.";");
            break;
            case "people_dock_list":
                $people = $model->getPeople($id);
                $this->people = $people;
            break;
            default:
                $people = $model->getPeople($id);
                $this->people = $people;
                $this->state = $model->getState();
            break;
        }

        //display view
        echo parent::render();
    }

}
