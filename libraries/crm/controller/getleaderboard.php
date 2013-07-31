<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class CobaltControllerGetLeaderBoard extends CobaltControllerDefault
{

    //get a leaderboard
    public function execute()
    {
        $app = JFactory::getApplication();

        //get model
        $model = new CobaltModelGoal();

        //get data
        $leaderboard = $model->getLeaderBoards($app->input->get('id'));

        //pass data to view
        $view = CobaltHelperView::getView('goals','leaderboard', 'raw', array('leaderboard'=>$leaderboard ));

        //display view
        echo $view->render();

    }

}
