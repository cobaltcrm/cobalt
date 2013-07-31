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
use Cobalt\Helper\DateHelper;
use Cobalt\Model\Event as EventModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class GetCalendarEvents extends DefaultController
{
    public function execute()
    {
        //application
        $app = JFactory::getApplication();

        //post data
        $data = $app->input->getRequest('get');

        //set date parameters
        $start_date = DateHelper::formatDBDate(date("Y-m-d 00:00:00",$data['start']));
        $end_date = DateHelper::formatDBDate(date("Y-m-d 00:00:00",$data['end']));

        //load model
        $model = new EventModel;

        //set model parameters
        $model->set('start_date',"$start_date");
        $model->set('end_date',"$end_date");
        $model->set('loc',"calendar");

        //get events
        $events = $model->getEvents();
        echo json_encode($events);
   }

}
