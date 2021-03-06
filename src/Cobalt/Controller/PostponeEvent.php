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

use Cobalt\Model\Event as EventModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class PostponeEvent extends DefaultController
{

    public function execute()
    {
        $model = new EventModel;
        $model->postponeEvent();
    }

}
