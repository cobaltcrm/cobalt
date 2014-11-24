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

use Cobalt\Model\Deal as DealModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class GetDeals extends DefaultController
{
    public function execute()
    {
        //open model
        $model = new DealModel;

        //retrieve all people
        $deals = $model->getDealList();

        //return results as json object
        echo json_encode($deals);
    }

}
