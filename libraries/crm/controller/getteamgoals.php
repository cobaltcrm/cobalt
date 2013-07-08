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
defined( '_JEXEC' ) or die( 'Restricted access' );

class CobaltControllerGetTeamGoals extends CobaltControllerDefault
{

    function execute(){

    	$app = JFactory::getApplication();

        //get model
        $model = newCobaltModelGoal();

        //get data
        $goals = $model->getTeamGoals($app->input->get('id'));

        //pass data to view
        $view = CobaltHelperView::getView('goals','filters', 'phtml', array('goals'=>$goals ));

        //display view
        echo $view->render();

    }

}