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

class AddPersonToCompany extends DefaultController
{
    public function execute()
    {
        //parameters
        $person_id = $this->getInput()->get("person_id");
        $company_id = $this->getInput()->get('company_id');

        //construct data
        $data = array('id' => $person_id, 'company_id' => $company_id);

        //load model and save
        $model = new PeopleModel;
        if ( $model->store($data) ) {
            $success = true;
        } else {
            $success = false;
        }

        //return json
        echo json_encode(array('success' => $success));
    }

}
