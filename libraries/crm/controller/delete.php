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

class CobaltControllerDelete extends CobaltControllerDefault
{

    public function execute()
    {
        $app = JFactory::getApplication();
        $view = $app->input->get("view");
        $modelName = "CobaltModel".ucwords($app->input->get("model"));
        $model = new $modelName();

        $model->delete($app->input->get("cid",null,'array'));
        $app->redirect("index.php?view=".$view);

    }

}
