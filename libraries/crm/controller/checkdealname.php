<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class CobaltControllerCheckDealName extends CobaltControllerDefault
{

    public function execute()
      {
              $app = JFactory::getApplication();
            $deal_name = $app->input->get('deal_name');
            $dealModel = new CobaltModelDeal();
            $existingDeal = $dealModel->checkDealName($deal_name);

            if ($existingDeal!="") {
                echo json_encode(array('success' => true, 'deal_id' => $existingDeal,'message' => ""));
            } else {
                echo json_encode(array('success' => true, 'message' => ucwords(CRMText::_('COBALT_DEAL_WILL_BE_CREATED'))));
            }
       }

}
