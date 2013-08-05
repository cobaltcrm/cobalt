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

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Logout extends DefaultController
{
    public function execute()
    {
        if ($this->app->logout()) {
            $this->app->redirect(base64_decode($this->input->get('return')));
        } else {
            $this->app->redirect(base64_decode($this->input->get('return')));
        }

    }

}
