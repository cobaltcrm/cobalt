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
use Cobalt\Helper\MailinglistsHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class ToggleMailingList extends DefaultController
{
    public function execute()
    {
        $app = JFactory::getApplication();
        $data = $app->input->getRequest('post');
        $subscribe = $data['subscribe'];
        $success = FALSE;
        if (!$subscribe) {
            if ( MailinglistsHelper::addMailingList($data) ) {
                $success = TRUE;
            }
        } else {
            if ( MailinglistsHelper::removeMailingList($data) ) {
                $success = TRUE;
            }
        }
        $return = array('success'=>$success);
        echo json_encode($return);
    }
}
