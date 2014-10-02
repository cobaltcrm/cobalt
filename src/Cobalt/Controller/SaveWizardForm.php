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

use Cobalt\Model\People as PeopleModel;
use Cobalt\Model\Company as CompanyModel;
use Cobalt\Model\Deal as DealModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class SaveWizardForm extends DefaultController
{
    public function execute()
    {
        $type = $this->input->get('save_type');
        switch ($type) {
            case "lead":
            case "contact":
                $model = new PeopleModel;
            break;
            case "company":
                $model = new CompanyModel;
            break;
            case "deal":
                $model = new DealModel;
            break;
        }
        $model->store();

        header('Location: '.base64_decode($this->input->get('return')));
   }

}
