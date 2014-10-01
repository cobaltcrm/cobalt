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

use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Remove extends DefaultController
{
    public function execute()
    {
        $modelName = 'Cobalt\\Model\\'.ucwords($this->input->get('model'));
        $controllerName = $this->input->get('controller');

        $objectName = $this->input->get('model');

        $model = new $modelName();

        $ids = $this->input->get('id');

        if ( is_array($ids) ) {
            foreach ($ids as $id) {
                $model->remove($id);
            }
        } else {
            $model->remove($this->id);
        }

        $msg = TextHelper::_('COBALT_'.strtoupper($objectName).'_REMOVED');
        $this->app->redirect('index.php?view='.$controllerName,$msg);
    }
}
