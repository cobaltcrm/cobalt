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

class CobaltControllerFilterDocuments extends CobaltControllerDefault
{

    public function execute()
    {
        //set view
        $view = CobaltHelperView::getView('documents','raw');
        $view->setLayout('list');

        //get deals
        $model = new CobaltModelDocument();
        $documents = $model->getDocuments();

        //assign references
        $view->documents = $documents;

        //display
        echo $view->render();

    }

}
