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

class FilterPeople extends DefaultController
{
    public function execute()
    {
        //set view
        $view = Factory::getView('people','raw');
        $view->setLayout('list');

        //get deals
        $model = Factory::getModel('People');
        $people = $model->getPeople();

        //assign references
        $view->people = $people;

        //display
        echo $view->render();

    }

}
