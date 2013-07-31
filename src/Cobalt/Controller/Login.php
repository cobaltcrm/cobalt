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

use JFactory;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Login extends DefaultController
{
    public function execute()
    {
        $app = JFactory::getApplication();
        $credentials = array('username'=>$app->input->get('username'),'password'=>$app->input->get('password',null,'HTML'));
        if ($app->login($credentials)) {
            $app->redirect(base64_decode($app->input->get('return')));
        } else {
            $app->redirect('/');
        }
    }

}
