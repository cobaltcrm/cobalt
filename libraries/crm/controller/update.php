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

class CobaltControllerUpdate extends CobaltControllerDefault
{

    public function execute()
    {

        $app = JFactory::getApplication();
        $modelName = 'CobaltModel'.ucwords($app->input->get('model'));

        $model = new $modelName();

        //get tasks
        $items = $model->getItems();

        //return json list of tasks
        echo json_encode($items);

    }
}
