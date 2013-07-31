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

class CobaltControllerGetPeople extends CobaltControllerDefault
{

        function execute()
        {
            //open model
            $model = new CobaltModelPeople();

            //retrieve all people
            $people = $model->getPeopleList();

            //return results as json object
            echo json_encode($people);

        }

}
