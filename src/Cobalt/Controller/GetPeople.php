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

use Cobalt\Model\People as PeopleModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class GetPeople extends DefaultController
{

    public function execute()
    {
        //open model
        $model = new PeopleModel;

        //retrieve all people
        $people = $model->getPeopleList();

        //return results as json object
        echo json_encode($people);
    }

}
