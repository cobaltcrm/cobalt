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

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Edit extends DefaultController
{

    public function execute()
    {
        /*
        $app = JFactory::getApplication();
        $viewName = $app->input->get('view');
        $app->input->set('layout','edit');
        $app->input->set('view', $viewName);

        $view = ViewHelper::getView($viewName,'edit','html');

        //display view
        echo $view->render();
        */
       parent::execute();
    }

}
