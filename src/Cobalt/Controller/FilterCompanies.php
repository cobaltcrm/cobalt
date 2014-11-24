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

use Cobalt\Factory;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class FilterCompanies extends DefaultController
{
    public function execute()
    {
        //get filters
        $type = $this->getInput()->get('type');
        $user = $this->getInput()->get('user');
        $team = $this->getInput()->get('team_id');

        //get deals
        $model = Factory::getModel('Company');
        $companies = $model->getCompanies(null,$type,$user,$team);

         //set view
        $view = Factory::getView('companies','list','html', array('companies' => $companies), $model);

        //display
        echo $view->render();
    }
}
