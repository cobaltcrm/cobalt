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

use Cobalt\Helper\CobaltHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class UnshareItem extends DefaultController
{
    public function execute()
    {
        $return = array();

        if ( CobaltHelper::unshareItem() ) {
            $return['success'] = true;
        } else {
            $return['success'] = false;
        }

        echo json_encode($return);

    }
}
