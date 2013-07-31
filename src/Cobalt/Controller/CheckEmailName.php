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
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\CobaltHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class CheckEmailName extends DefaultController
{
    public function execute()
      {
              $app = JFactory::getApplication();
            $emailExists = CobaltHelper::checkEmailName($app->input->get('email'));
            if ($emailExists) {
                $success = true;
                $msg = TextHelper::_('COBALT_EMAIL_EXISTS');
            } else {
                $success = true;
                $msg = TextHelper::_('COBALT_EMAIL_IS_AVAILABLE');
            }
            $return = array('success'=>$success,'message'=>$msg,'email_exists'=>$emailExists);
            echo json_encode($return);
       }

}
