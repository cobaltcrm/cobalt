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

class Delete extends DefaultController
{
    public function execute()
    {
        $view = $this->input->get("view");
        $modelName = "Cobalt\\Model\\".ucwords($this->input->get("model"));
        $model = new $modelName();

        $model->delete($this->input->get("cid", null, 'array'));
        $this->app->redirect("index.php?view=".$view);
    }

}
