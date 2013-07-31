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

class CobaltControllerDeleteReport extends CobaltControllerDefault
{

    public function execute()
    {
        //gen return info
        $return = array();
        $return['error'] = true;

        //get model
        $model = new CobaltModelReport();
        if ( $model->deleteReport($app->input->get('id')) ) {
            $return['error'] = false;
        }

        //return json info
        echo json_encode($return);

    }

}
