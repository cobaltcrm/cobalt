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

class CobaltControllerFilterDeals extends CobaltControllerDefault
{

    function execute()
    {
        $app = JFactory::getApplication();

    	//set view
        $view = CobaltHelperView::getView('deals','raw');
        $view->setLayout('list');
        
        //get filters
        $type = $app->input->get('type');
        $stage = $app->input->get('stage');
        $user = $app->input->get('user');
        $close = $app->input->get('close');
        $team = $app->input->get('team_id');
        
        //get deals
        $model = new CobaltModelDeal();
        $deals = $model->getDeals(null,$type,$user,$stage,$close,$team);
        
        //assign references
        $view->deals = $deals;
        $view->pagination = $model->getPagination();

        //display
        echo $view->render();
    }

}