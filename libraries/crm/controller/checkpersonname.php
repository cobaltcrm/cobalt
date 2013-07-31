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

class CobaltControllerCheckPersonName extends CobaltControllerDefault
{

    public function execute()
  {
          $app = JFactory::getApplication();
        $person_name = $app->input->get('person_name');
        $personModel = new CobaltModelPeople();
        $existingPerson = $personModel->checkPersonName($person_name);

        if ($existingPerson!="") {
            echo json_encode(array('success' => true, 'person_id' => $existingPerson,'message' => ""));
        } else {
            echo json_encode(array('success' => true, 'message' => ucwords(CRMText::_('COBALT_PERSON_WILL_BE_CREATED'))));
        }
   }

}
