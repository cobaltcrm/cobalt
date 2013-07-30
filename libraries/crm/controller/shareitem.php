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
defined( '_JEXEC' ) or die( 'Restricted access' );

class CobaltControllerShareItem extends CobaltControllerDefault
{

    public function execute()
    {
        $return = array();

        if ( CobaltHelperCobalt::shareItem() ) {
            $return['success'] = true;
        } else {
            $return['success'] = false;
        }

        echo json_encode($return);

   }

}
