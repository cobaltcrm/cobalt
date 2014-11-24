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

class FilterDocuments extends DefaultController
{

    public function execute()
    {
        //get deals
        $model = Factory::getModel('Document');
        $documents = $model->getDocuments();

        //set view
        $view = Factory::getView('documents','list','raw',array('documents' => $documents),$model);

        //display
        echo $view->render();

    }

}
