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

use Cobalt\Model\People as PeopleModel;
use Cobalt\Helper\ViewHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class FilterPeople extends DefaultController
{
    public function execute()
    {
        //set view
        $view = ViewHelper::getView('people','raw');
        $view->setLayout('list');

        //get deals
        $model = new PeopleModel;
        $people = $model->getPeople();

        //assign references
        $view->people = $people;

        //display
        echo $view->render();

    }

}
