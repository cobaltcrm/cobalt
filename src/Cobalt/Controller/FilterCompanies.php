<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Controller;

use Cobalt\Model\Company as CompanyModel;
use Cobalt\Helper\ViewHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class FilterCompanies extends DefaultController
{
    public function execute()
    {
         //set view
        $view = ViewHelper::getView('companies','raw','html');
        $view->setLayout('list');

        //get filters
        $type = $this->input->get('type');
        $user = $this->input->get('user');
        $team = $this->input->get('team_id');

        //get deals
        $model = new CompanyModel;
        $companies = $model->getCompanies(null,$type,$user,$team);

        //assign references
        $view->companies = $companies;

        //display
        echo $view->render();
    }

}
