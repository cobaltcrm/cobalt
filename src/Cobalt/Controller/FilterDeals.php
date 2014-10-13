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

class FilterDeals extends DefaultController
{
    public function execute()
    {
        //get filters
        $type = $this->getInput()->get('type');
        $stage = $this->getInput()->get('stage');
        $user = $this->getInput()->get('user');
        $close = $this->getInput()->get('close');
        $team = $this->getInput()->get('team_id');

        //get deals
        $model = Factory::getModel('Deal');
        $deals = $model->getDeals(null,$type,$user,$stage,$close,$team);

        //set view
        $view = Factory::getView('deals','list','raw',array('deals' => $deals, 'pagination' => $model->getPagination()),$model);

        //display
        echo $view->render();
    }

}
