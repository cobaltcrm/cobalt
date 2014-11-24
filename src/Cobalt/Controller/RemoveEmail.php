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

use Cobalt\Model\Mail as MailModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class RemoveEmail extends DefaultController
{

    public function execute()
    {
        $model = new MailModel;
        $model->removeEmail();
    }

}
