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

class Login extends DefaultController
{
    public function execute()
    {
        $this->input->set('view', 'login');

        $credentials = array(
            'username' => $this->input->get('username'),
            'password' => $this->input->get('password', null, 'HTML')
        );

        if (isset($credentials['username']))
        {
            $this->app->login($credentials);
        }

        parent::execute();
    }

}
