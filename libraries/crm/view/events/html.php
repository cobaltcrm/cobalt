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

class CobaltViewEventsHtml extends JViewHtml
{
    public function render($tpl = null)
    {
        $app = JFactory::getApplication();

        $document = JFactory::getDocument();

        //event model
        $model = new CobaltModelEvent();

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

                $this->event_statuses = CobaltHelperEvent::getEventStatuses();
                $this->event_types = CobaltHelperEvent::getEventTypes();
                $this->event_categories = CobaltHelperEvent::getCategories(TRUE);
                $this->event_due_dates = CobaltHelperEvent::getEventDueDates();
                $this->event_associations = CobaltHelperEvent::getEventAssociations();
                $this->event_users = CobaltHelperUsers::getUsers(NULL,TRUE);
                $this->event_teams = CobaltHelperUsers::getTeams();

                $this->state = $state;
            break;
        }

            if ($layout != 'edit_task' || $layout != "edit_event") {

                if (CobaltHelperTemplate::isMobile()) {
                    $model->set('current_events',true);
                    $document->addScriptDeclaration('loc="events";');
                }
            }

        if ( CobaltHelperTemplate::isMobile() && isset($event_id)) {
            $person_model = new CobaltModelPeople();
            $person_model->set('event_id',$event_id);
            $person_model->set('recent',false);
            $person_model->set('_id',null);
            $this->people = $person_model->getPeople();
        }

        $document->addScriptDeclaration('var layout="'.$layout.'"');

        //assign results to view
        $this->events = $events;
        $this->member_role = CobaltHelperUsers::getRole();
        $this->user_id = CobaltHelperUsers::getUserId();
        $this->team_id = CobaltHelperUsers::getTeamId();

        //display
        return parent::render();
    }

}
