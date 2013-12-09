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

use Cobalt\Model\Deal as DealModel;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class CheckDealName extends DefaultController
{
    public function execute()
    {
        $deal_name = $this->input->get('deal_name');
        $dealModel = new DealModel;
        $existingDeal = $dealModel->checkDealName($deal_name);

        if ($existingDeal!="") {
            echo json_encode(array('success' => true, 'deal_id' => $existingDeal,'message' => ""));
        } else {
            echo json_encode(array('success' => true, 'message' => ucwords(TextHelper::_('COBALT_DEAL_WILL_BE_CREATED'))));
        }
    }
}
