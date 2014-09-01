<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Deals;

use Joomla\View\AbstractHtmlView;
use JFactory;
use Cobalt\Helper\DealHelper;
use Cobalt\Helper\ViewHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Model\Deal as DealModel;
use Cobalt\Model\Event as EventModel;
use Cobalt\Model\Conversation as ConversationModel;

defined( '_CEXEC' ) or die( 'Restricted access' );

class Raw extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        $app = JFactory::getApplication();

        $id = $app->input->get('id', null);
        $company_id = $app->input->get('company_id');
        $person_id = $app->input->get('person_id');

        //get deals
        $model = new DealModel;

        if ($company_id) {
            $model->set('company_id',$company_id);
        } elseif ($person_id) {
            $model->set('person_id',  $person_id);
        } elseif ($id) {
            $model->set('_id', $id);
        }

        $layout = $this->getLayout();

        $total = $model->getTotal();
        $this->total = $total;

        $pagination = $model->getPagination();
        $this->pagination = $pagination;

        //assign references
        switch ($layout) {
            case "entry":
                $this->stages = DealHelper::getStages(null, true, false);
                $this->statuses = DealHelper::getStatuses(null, true);
                $this->sources = DealHelper::getSources(null);
                $this->users = UsersHelper::getUsers(null, true);
                $this->k = 0;
                $this->deal = $model->getDeal();
            break;
            case "deal":
                $this->deal = $model->getDeal($id);
                $this->dealList = $model->getDeals();
                $primary_contact_id = DealHelper::getPrimaryContact($this->deal->id);
                $this->closed_stages = DealHelper::getClosedStages();
                $model = new EventModel;
                $events = $model->getEvents("deal", null, $app->input->get('id'));
                $this->event_dock = ViewHelper::getView('events','event_dock','phtml', array('events' => $events));
                $this->document_list = ViewHelper::getView('documents', 'document_row', 'phtml', array('documents' => $this->deal->documents));
                $this->custom_fields_view = ViewHelper::getView('custom', 'default', 'phtml', array('type' => 'deal', 'item' => $this->deal));
                $this->contact_info = ViewHelper::getView('contacts', 'default', 'phtml', array('contacts' => $this->deal->people, 'primary_contact_id' => $primary_contact_id));
            break;
            case "deal_dock_list":
                    $this->deals = $model->getDeals();
            break;
            case "add":
            case "edit":
                    $this->deal = $model->getDeal($id);
                    $this->edit_custom_fields_view = ViewHelper::getView('custom','edit','phtml',array('type'=>'deal','item'=>$this->deal));
            break;
            case "edit_conversation":
                    $model = new ConversationModel;
                    $conversation = $model->getConversation($id);
                    $this->conversation = $conversation[0];
            break;
            case "conversation_entry":
                    $model = new ConversationModel;
                    $conversation = $model->getConversation($id);
                    $this->conversation = $conversation[0];
            break;
            default:
                    $this->dealList = $model->getDeals();
                    $state = $model->getState();
                    $this->state = $state;
            break;
        }

        //display view
        echo parent::render();
    }

}
