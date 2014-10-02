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

use Cobalt\Model\Goal as GoalModel;
use Cobalt\Helper\ViewHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class GetLeaderBoard extends DefaultController
{
    public function execute()
    {
        //get model
        $model = new GoalModel;

        //get data
        $leaderboard = $model->getLeaderBoards($this->input->get('id'));

        //pass data to view
        $view = ViewHelper::getView('goals','leaderboard', 'raw', array('leaderboard'=>$leaderboard ));

        //display view
        echo $view->render();
    }

}
