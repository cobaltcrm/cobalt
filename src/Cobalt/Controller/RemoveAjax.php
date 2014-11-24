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

class RemoveAjax extends DefaultController
{
    public function execute()
    {
        $modelName = 'Cobalt\\Model\\'.ucwords($this->getInput()->get('model'));
        $controllerName = $this->getInput()->get('controller');

        $objectName = $this->getInput()->get('model');

        $model = new $modelName();

        $ids = $this->getInput()->get('id');

        if ( is_array($ids) ) {
            foreach ($ids as $id) {
                $deleted = $model->remove($id);
            }
        } else {
            $deleted = $model->remove($ids);
        }

        $response = new \stdClass();
        $response->alert = new \stdClass;
        $response->alert->message = TextHelper::_('COBALT_'.strtoupper($objectName).'_REMOVED');
        $response->alert->type = $deleted ? 'success' : 'error' ;
        echo json_encode($response);
    }
}
