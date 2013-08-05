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
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class CheckPersonName extends DefaultController
{
    public function execute()
    {
        $person_name = $this->input->get('person_name');
        $personModel = new PeopleModel;
        $existingPerson = $personModel->checkPersonName($person_name);

        if ($existingPerson!="") {
            echo json_encode(array('success' => true, 'person_id' => $existingPerson,'message' => ""));
        } else {
            echo json_encode(array('success' => true, 'message' => ucwords(TextHelper::_('COBALT_PERSON_WILL_BE_CREATED'))));
        }
    }
}
