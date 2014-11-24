<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Events;

use Joomla\View\AbstractHtmlView;
use Cobalt\Model\Event as EventModel;
use Cobalt\Helper\CobaltHelper;
use Cobalt\Factory;

defined( '_CEXEC' ) or die( 'Restricted access' );

class Raw extends AbstractHtmlView
{
    public function render()
    {
        $app = Factory::getApplication();

        //grab model
        $model = new EventModel;

        if ( $this->getLayout() == "event_listings" || $this->getLayout() == "list" ) {

            $events = $model->getEvents();
            $this->events = $events;

        } else {
            //null event
            $event = array();

            $id = null;
            if ( $app->input->get('parent_id') && !$app->input->get('id') ) {
                $id = $app->input->get('parent_id');
            } else {
                $id = $app->input->get('id');
            }

            //grab event
            if ($id != null) {
                $event = $model->getEvent($id);
            }

            //pass reference
            $this->event = $event;
        }

        if ( $app->input->get('association_id') ) {
            $this->association_name = CobaltHelper::getAssociationName();
        }

        //display
        echo parent::render();
    }

}
