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

use JFactory;
use Cobalt\Model\Company as CompanyModel;
use Cobalt\Helper\ViewHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class CobaltControllerFilterCompanies extends DefaultController
{

    public function execute()
    {
        $app = JFactory::getApplication();

         //set view
        $view = ViewHelper::getView('companies','raw','html');
        $view->setLayout('list');

        //get filters
        $type = $app->input->get('type');
        $user = $app->input->get('user');
        $team = $app->input->get('team_id');

        //get deals
        $model = new CompanyModel;
        $companies = $model->getCompanies(null,$type,$user,$team);

        //assign references
        $view->companies = $companies;

        //display
        echo $view->render();
    }

}
