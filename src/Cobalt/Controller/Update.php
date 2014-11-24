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

class Update extends DefaultController
{
    public function execute()
    {
        $modelName = 'Cobalt\\Model\\'.ucwords($this->getInput()->get('model'));

        $model = new $modelName();

        //get tasks
        $items = $model->getItems();

        //return json list of tasks
        echo json_encode($items);
    }
}
