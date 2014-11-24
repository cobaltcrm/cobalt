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

class GetTeamGoals extends DefaultController
{
    public function execute()
    {
        //get model
        $model = Factory::getModel('Goal');

        //get data
        $goals = $model->getTeamGoals($this->getInput()->get('id'));

        //pass data to view
        $view = Factory::getView('goals','filters', 'phtml', array('goals'=>$goals ), $model);

        //display view
        echo $view->render();

    }

}
