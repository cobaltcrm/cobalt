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

use RouteHelper;
use Cobalt\Model\Import as ImportModel;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Import extends DefaultController
{
    public function execute()
    {
        $success = false;

        $data = $this->input->getRequest('post');

        if ( is_array($data) && count($data) > 0 ) {

            $import_type = $data['import_type'];
            unset($data['import_type']);

            switch ($import_type) {
                case "companies":
                        $import_model = "company";
                    break;
                case "deals":
                        $import_model = "deal";
                    break;
                case "people":
                        $import_model = "people";
                    break;
            }

            if ( isset($import_model) ) {
                $model = new ImportModel;
                if ( $model->importCSVData($data['import_id'],$import_model) ) {
                    $success = true;
                }
            }
        }

        if ($success) {

            $msg = TextHelper::_('COBALT_IMPORT_WAS_SUCCESSFUL');
            $this->app->redirect(RouteHelper::_('index.php?view='.$import_type),$msg);

        } else {

            $msg = TextHelper::_('COBALT_ERROR_IMPORTING');
            $this->app->redirect(RouteHelper::_('index.php?view='.$import_type),$msg);

        }
    }

}
