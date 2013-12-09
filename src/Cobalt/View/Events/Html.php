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

use JFactory;
use Cobalt\Model\Event as EventModel;
use Cobalt\Model\People as PeopleModel;
use Cobalt\Helper\EventHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\TemplateHelper;
use Joomla\View\AbstractHtmlView;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        $app = JFactory::getApplication();

        $document = JFactory::getDocument();

        //event model
        $model = new EventModel;

        $view = $app->input->get('view');
        $layout = $this->getLayout();
        switch ($layout) {
            case 'event_dock':

            break;
            case 'edit_event':
            case 'edit_task':

            break;
            case 'default':
            default:
                $event_id = $app->input->get('id');

                if ( $app->input->get('loc') ) {
                    $events = $model->getEvents($app->input->get('loc'),null,$app->input->get($app->input->get('loc').'_id'));
                } else {
                    $events = $model->getEvents();
                }

                $state = $model->getState();

                $this->event_statuses = EventHelper::getEventStatuses();
                $this->event_types = EventHelper::getEventTypes();
                $this->event_categories = EventHelper::getCategories(TRUE);
                $this->event_due_dates = EventHelper::getEventDueDates();
                $this->event_associations = EventHelper::getEventAssociations();
                $this->event_users = UsersHelper::getUsers(NULL,TRUE);
                $this->event_teams = UsersHelper::getTeams();

                $this->state = $state;
            break;
        }

            if ($layout != 'edit_task' || $layout != "edit_event") {

                if (TemplateHelper::isMobile()) {
                    $model->set('current_events',true);
                    $document->addScriptDeclaration('loc="events";');
                }
            }

        if ( TemplateHelper::isMobile() && isset($event_id)) {
            $person_model = new PeopleModel;
            $person_model->set('event_id',$event_id);
            $person_model->set('recent',false);
            $person_model->set('_id',null);
            $this->people = $person_model->getPeople();
        }

        $document->addScriptDeclaration('var layout="'.$layout.'"');

        //assign results to view
        $this->events = $events;
        $this->member_role = UsersHelper::getRole();
        $this->user_id = UsersHelper::getUserId();
        $this->team_id = UsersHelper::getTeamId();

        //display
        return parent::render();
    }

}
