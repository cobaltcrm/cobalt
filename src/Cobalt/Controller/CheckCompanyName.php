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
use Cobalt\Model\Company as CompanyModel;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class CheckCompanyName extends DefaultController
{

    public function execute()
      {
          $app = JFactory::getApplication();
        $company_name = $app->input->get('company_name');
        $companyModel = new CompanyModel;
        $existingCompany = $companyModel->checkCompanyName($company_name);

        if ($existingCompany!="") {
            echo json_encode(array('success' => true, 'company_id' => $existingCompany,'message' => ""));
        } else {
            echo json_encode(array('success' => true, 'message' => ucwords(TextHelper::_('COBALT_COMPANY_WILL_BE_CREATED'))));
        }
   }

}
