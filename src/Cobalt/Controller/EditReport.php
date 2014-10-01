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

use Cobalt\Model\Report as ReportModel;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class EditReport extends DefaultController
{
    public function execute()
    {
        $model = new ReportModel;

        //store data
        $link = 'index.php?view=reports&layout=custom_reports';
        if ( $model->store() ) {
            $msg = TextHelper::_('COBALT_CUSTOM_REPORT_SUCCESSFULLY_ADDED');
            $this->app->redirect($link, $msg);
        } else {
            $msg = TextHelper::_('COBALT_PROBLEM_CREATING_CUSTOM_REPORT');
            $this->app->redirect($link, $msg);
        }
    }

}
