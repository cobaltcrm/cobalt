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

class CobaltControllerFilterPeople extends CobaltControllerDefault
{

    public function execute()
    {
        //set view
        $view = CobaltHelperView::getView('people','raw');
        $view->setLayout('list');

        //get deals
        $model = new CobaltModelPeople();
        $people = $model->getPeople();

        //assign references
        $view->people = $people;

        //display
        echo $view->render();

    }

}
