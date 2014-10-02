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
        $view = $this->getInput()->get("view");
        $modelName = "Cobalt\\Model\\".ucwords($this->getInput()->get("model"));
        $model = new $modelName();

        $model->delete($this->getInput()->get("cid", null, 'array'));
        $this->getApplication()->redirect("index.php?view=".$view);
    }

}
