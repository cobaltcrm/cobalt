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

class CobaltControllerEditReport extends CobaltControllerDefault
{

     function execute()
     {
        //get model
        $model = new CobaltModelReport();
        $app = JFactory::getApplication();

        //store data
        $link = 'index.php?view=reports&layout=custom_reports';
        if ( $model->store() ) {
            $msg = CRMText::_('COBALT_CUSTOM_REPORT_SUCCESSFULLY_ADDED');
            $app->redirect($link, $msg);
        } else {
            $msg = CRMText::_('COBALT_PROBLEM_CREATING_CUSTOM_REPORT');
            $app->redirect($link, $msg);
        }

    }

}
