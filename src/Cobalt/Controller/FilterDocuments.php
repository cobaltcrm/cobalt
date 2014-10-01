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

use Cobalt\Model\Document as DocumentModel;
use Cobalt\Helper\ViewHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class FilterDocuments extends DefaultController
{

    public function execute()
    {
        //set view
        $view = ViewHelper::getView('documents','raw');
        $view->setLayout('list');

        //get deals
        $model = new DocumentModel;
        $documents = $model->getDocuments();

        //assign references
        $view->documents = $documents;

        //display
        echo $view->render();

    }

}
