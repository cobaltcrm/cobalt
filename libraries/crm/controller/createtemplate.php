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

class CobaltControllerCreateTemplate extends CobaltControllerDefault
{

   public function execute()
   {
        $return = array();
        $return['success'] = FALSE;

        $model = new CobaltModelTemplate();

        if ( $model->createTemplate() ) {
            $return['success'] = TRUE;
        }

        echo json_encode($return);

   }

}
