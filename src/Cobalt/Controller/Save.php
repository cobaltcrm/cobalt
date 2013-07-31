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
use JRoute;
use Cobalt\Model\Company as CompanyModel;
use Cobalt\Model\Deal as DealModel;
use Cobalt\Model\People as PeopleModel;
use Cobalt\Model\Conversation as ConversationModel;
use Cobalt\Model\Note as NoteModel;
use Cobalt\Model\Event as EventModel;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Save extends DefaultController
{
    public function execute()
    {
        $app = JFactory::getApplication();
        $modelName = "Cobalt\\Model\\".ucwords($app->input->get('model'));
        $model = new $modelName();

        //if we are requesting a return redirect set up redirect link
        if ( $app->input->get('view') ) {
            $link = JRoute::_('index.php?view='.$app->input->get('view'));
        }

        if ( $db_id = $model->store() ) {

            $msg = TextHelper::_('COBALT_SUCCESSFULLY_SAVED');

            //redirect if set else return json
            if ( $app->input->get('view') ) {

                $app->redirect($link, $msg);

            } else {

                //companies
                if ( $app->input->get('model') == "company") {
                    $model = new CompanyModel;
                    $id = $app->input->get('id') ? $app->input->get('id') : null;
                    if ($id) {
                        $return = $model->getCompany($id);
                    } else {
                        $company = $db_id;
                    }
                    $return = $company;
                }

                //if deal information is being edited
                if ( $app->input->get('model') == 'deal' ) {
                    $model = new DealModel;
                    $id = $app->input->get('id') ? $app->input->get('id') : $db_id;
                    $deal = $model->getDeals($id);
                    $return = $deal[0];
                }

                //if people information is being edited
                if ( $app->input->get('model') == 'people' ) {
                    $model = new PeopleModel;
                    $id = $app->input->get('id') ? $app->input->get('id') : $db_id;
                    $person = $model->getPerson($id);
                    $return = $person;
                }

                //if conversation information is being edited
                if ( $app->input->get('model') == 'conversation') {
                    $model = new ConversationModel;
                    $db = JFactory::getDBO();
                    $conversation = $model->getConversation($db_id);
                    $return = $conversation[0];
                }

                //if note information is being edited
                if ( $app->input->get('model') == 'note' ) {
                    $model = new NoteModel;
                    $db = JFactory::getDBO();
                    $note = $model->getNote($db_id);
                    $return = $note[0];
                }

                //if event information is being edited
                if ( $app->input->get('model') == 'event' ) {

                    //get model
                    $model = new EventModel;
                    $db = JFactory::getDBO();

                    //determine whether we are inserting a new entry or editing an entry
                    $event_id = $db_id;

                    //get and return event
                    $return = $model->getEvent($event_id);

                }

                if ( isset($return) ) {
                    echo json_encode($return);
                }

            }

        } else {

              $msg = TextHelper::_('COBALT_ERROR_SAVING');

            //redirect if set else return json info
            if ( $app->input->get('return') ) {

                $app->redirect($link, $msg);

            } else {

                $return = $app->input->getRequest('post');
                echo json_encode($return);

            }

        }

    }
}
