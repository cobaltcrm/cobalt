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

class CobaltControllerEdit extends CobaltControllerDefault
{

    public function execute()
    {

        /*
        $app = JFactory::getApplication();
        $viewName = $app->input->get('view');
        $app->input->set('layout','edit');
        $app->input->set('view', $viewName);

        $view = CobaltHelperView::getView($viewName,'edit','html');

        //display view
        echo $view->render();
        */
       parent::execute();
    }

}
