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

use Cobalt\Helper\MailinglistsHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class ToggleMailingList extends DefaultController
{
    public function execute()
    {
        $data = $this->input->getRequest('post');
        $subscribe = $data['subscribe'];
        $success = false;

        if (!$subscribe) {
            if ( MailinglistsHelper::addMailingList($data) ) {
                $success = true;
            }
        } else {
            if ( MailinglistsHelper::removeMailingList($data) ) {
                $success = true;
            }
        }

        echo json_encode(array('success' => $success));
    }
}
