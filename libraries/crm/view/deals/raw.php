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
defined( '_JEXEC' ) or die( 'Restricted access' );

class CobaltViewDealsRaw extends JViewHtml
{
    public function render($tpl = null)
    {
        $app = JFactory::getApplication();

        $id = $app->input->get('id') ? $app->input->get('id') : null;
        $company_id = $app->input->get('company_id');
        $person_id = $app->input->get('person_id');

        //get deals
        $model = new CobaltModelDeal();

        if ($company_id) {
                $model->set('company_id',$company_id);
        } elseif ($person_id) {
                $model->set('person_id',$person_id);
        } elseif ($id) {
                $model->set('_id',$id);
        }

        $layout = $this->getLayout();

        $total = $model->getTotal();
        $this->total = $total;

        $pagination = $model->getPagination();
        $this->pagination = $pagination;

        //assign references
        switch ($layout) {
            case "entry":
                $this->stages = CobaltHelperDeal::getStages(null,TRUE,FALSE);
                $this->statuses = CobaltHelperDeal::getStatuses(null,true);
                $this->sources = CobaltHelperDeal::getSources(null);
                $this->users = CobaltHelperUsers::getUsers(null,TRUE);
                $this->k = 0;
                $this->deal = $model->getDeal();
            break;
            case "deal_dock_list":
                    $this->deals = $model->getDeals();
            break;
            case "add":
            case "edit":
                    $this->deal = $model->getDeal();
                    $this->edit_custom_fields_view = CobaltHelperView::getView('custom','edit','phtml',array('type'=>'deal','item'=>$this->deal));
            break;
            case "edit_conversation":
                    $model = new CobaltModelConversation();
                    $conversation = $model->getConversation($id);
                    $this->conversation = $conversation[0];
            break;
            case "conversation_entry":
                    $model = new CobaltModelConversation();
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
