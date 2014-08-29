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

use JRoute;
use Cobalt\Model\Goal as GoalModel;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class EditGoal extends DefaultController
{

    public function execute()
    {
        //get model
        $model = new GoalModel;

        //store data
        $link = RouteHelper::_('index.php?view=goals');
        if ( $model->store() ) {
            $msg = TextHelper::_('COBALT_SUCCESS');
            $this->app->redirect($link, $msg);
        } else {
            $msg = TextHelper::_('COBALT_FAILURE');
            $this->app->redirect($link, $msg);
        }

    }

}
