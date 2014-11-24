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

use Cobalt\Model\Report as ReportModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class DeleteReport extends DefaultController
{
    public function execute()
    {
        //gen return info
        $return = array();
        $return['error'] = true;

        //get model
        $model = new ReportModel;
        if ( $model->deleteReport($this->getInput()->get('id')) ) {
            $return['error'] = false;
        }

        //return json info
        echo json_encode($return);

    }

}
