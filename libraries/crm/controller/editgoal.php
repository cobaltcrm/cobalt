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

class CobaltControllerEditGoal extends CobaltControllerDefault
{

    public function execute()
    {
        //application
        //
        $app = JFactory::getApplication();
        //get model
        $model = new CobaltModelGoal();

        //store data
        $link = JRoute::_('index.php?view=goals');
        if ( $model->store() ) {
            $msg = CRMText::_('COBALT_SUCCESS');
            $app->redirect($link, $msg);
        } else {
            $msg = CRMText::_('COBALT_FAILURE');
            $app->redirect($link, $msg);
        }

    }

}
