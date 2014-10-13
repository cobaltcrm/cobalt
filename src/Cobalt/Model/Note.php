<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Model;

use Cobalt\Factory;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\ActivityHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\CobaltHelper;
use Cobalt\Helper\DateHelper;
use Joomla\Registry\Registry;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Note extends DefaultModel
{

    public $_id = null;
    public $published = 1;
    public $public_notes = null;

    /**
     * Method to store a record
     *
     * @return boolean True on success
     */
    public function store($data=null)
    {
        //Load Tables
        $row = $this->getTable('Note');
        $oldRow = $this->getTable('Note');

        if ($data == null) {
            $data = $this->app->input->getArray(array(
                'note' => 'string',
                'deal_id' => 'int',
                'person_id' => 'int',
                'name' => 'string',
                'category_id' => 'int',
                'company_id' => 'int',
                'note_id' => 'int',
                'event_id' => 'int'
            ));
            $data = array_filter($data);
        }

        if ( array_key_exists('note_id', $data) ) {
            $data['id'] = $data['note_id'];
        }

        if ( array_key_exists('is_email',$data) ) {
            $model = new Mail;
            $email = $model->getEmail($data['email_id']);
            $data['note'] = $email;
        }

        /** check for and automatically associate and create primary contacts or people **/
        if ( array_key_exists('person_name',$data) && $data['person_name'] != "" ) {
            $peopleModel = new People;
            $existingPerson = $peopleModel->checkPersonName($data['person_name']);

            if ($existingPerson=="") {
                $pdata = array();
                $name = explode(" ",$data['person_name']);
                $pdata['first_name'] = array_key_exists(0,$name) ? $name[0] : "";
                $pdata['last_name'] = array_key_exists(1,$name) ? $name[1] : "";
                $data['person_id'] = $peopleModel->store($pdata);
            } else {
                $data['person_id'] = $existingPerson;
            }

        }

        /** check for and automatically associate and create deals **/
        if ( array_key_exists('deal_name',$data) && $data['deal_name'] != "" && (!array_key_exists('deal_id', $data) || empty($data['deal_id']) || $data['deal_id'] == 0) ) {
            $dealModel = new Deal;
            $existingDeal = $dealModel->checkDealName($data['deal_name']);

            if ($existingDeal=="") {
                $pdata = array();
                $pdata['name'] = $data['deal_name'];
                $data['deal_id'] = $dealModel->store($pdata);
            } else {
                $data['deal_id'] = $existingDeal;
            }
        }

        //date generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));

        if ( empty($data['id']) ) {
            $data['created'] = $date;
            $status = "created";
        } else {
            $row->load($data['id']);
            $oldRow->load($data['id']);
            $status = "updated";
        }

        $data['modified'] = $date;
        $data['owner_id'] = UsersHelper::getUserId();

        // Bind the form fields to the table
	    try
	    {
		    $row->save($data);
	    }
	    catch (\Exception $exception)
	    {
		    $this->app->enqueueMessage($exception->getMessage(), 'error');

		    return false;
	    }

        if ( array_key_exists('id',$data) && intval($data['id']) ) {
            $id = $data['id'];
        } else {
            $id = $this->db->insertId();
        }

        ActivityHelper::saveActivity($oldRow, $row,'note', $status);

        //Store email attachments
        if ( array_key_exists('is_email',$data) ) {
            $model = new CobaltModelMail();
            $model->storeAttachments($data['email_id'], $data['person_id']);
        }

        //$this->app->triggerEvent('onAfterNoteSave', array(&$row));

        return $id;
    }

    /*
     * Method to access a note
     *
     * @return array
     */
    public function getNote($id)
    {
        //grab db
        $db = $this->getDb();

        //initialize query
        $query = $db->getQuery(true);

        //gen query string
        $query->select("n.*,cat.name as category_name,comp.name as company_name,deal.name as deal_name,"
                    ."person.first_name as person_first_name,person.last_name as person_last_name,"
                    ."owner.first_name as owner_first_name, owner.last_name as owner_last_name, author.email");
        $query->from("#__notes as n");
        $query->leftJoin("#__notes_categories AS cat ON cat.id = n.category_id");
        $query->leftJoin("#__companies AS comp ON comp.id = n.company_id AND comp.published>0");
        $query->leftJoin("#__deals AS deal ON deal.id = n.deal_id AND deal.published>0");
        $query->leftJoin("#__people AS person on person.id = n.person_id AND person.published>0");
        $query->leftJoin('#__users AS owner ON owner.id = n.owner_id');
        $query->leftJoin("#__users AS author ON author.id = owner.id");
        $query->where("n.id=".$id);
        $query->where("n.published=".$this->published);

        //load results
        $db->setQuery($query);
        $results = $db->loadAssocList();

        //clean results
        if ( count ( $results ) > 0 ) {
            foreach ($results as $key => $note) {
                $results[$key]['created_formatted'] = DateHelper::formatDate($note['created']);
                $results[$key]['owner_avatar'] = CobaltHelper::getGravatar($note['email']);
            }
        }

        //$this->app->triggerEvent('onNoteLoad', array(&$results));

        //return results
        return $results;
    }

    /*
     * Method to access notes
     *
     * @return array
     */
    public function getNotes($object_id = NULL,$type = NULL, $display = true)
    {
        //grab db
        $db = $this->getDb();

        //initialize query
        $query = $db->getQuery(true);

        //gen query string
        $query->select("n.*,cat.name as category_name,comp.name as company_name,
                        comp.id as company_id,deal.name as deal_name,deal.id as deal_id,
                        person.id as person_id,person.first_name as person_first_name,
                        person.last_name as person_last_name, owner.first_name as owner_first_name,
                        event.name as event_name, event.id as event_id,
                        owner.last_name as owner_last_name, author.email");
        $query->from("#__notes as n");
        $query->leftJoin("#__notes_categories AS cat ON cat.id = n.category_id");
        $query->leftJoin("#__companies AS comp ON comp.id = n.company_id AND comp.published>0");
        $query->leftJoin("#__events AS event ON event.id = n.event_id AND event.published>0");
        $query->leftJoin("#__deals AS deal ON deal.id = n.deal_id AND deal.published>0");
        $query->leftJoin("#__people AS person on n.person_id = person.id AND person.published>0");
        $query->leftJoin("#__users AS owner ON n.owner_id = owner.id");
        $query->leftJoin("#__users AS author ON author.id = owner.id");

        $company_filter = $this->getState('Note.company_name');
        if ($company_filter != null) {
            $query->where("comp.name LIKE " . $db->quote('%' . $company_filter . '%'));
        }
        //deal
        $deal_filter = $this->getState('Note.deal_name');
        if ($deal_filter != null) {
            $query->where("deal.name LIKE " . $db->quote('%' . $deal_filter . '%'));
        }
        //person
        $person_filter = $this->getState('Note.person_name');
        if ($person_filter != null) {

        }

        if ($object_id) {
            switch ($type) {
                case 'person':
                case 'people':
                    $query->where('n.person_id ='.$object_id);
                break;

                case 'company':
                    $query->where('(n.company_id ='.$object_id.' OR deal.company_id = '.$object_id.' OR person.company_id = '.$object_id.")");
                break;

                case 'deal':
                    $query->where('n.deal_id='.$object_id);
                break;
                case "event":
                    $query->where("n.event_id=$object_id");
                break;
            }
        }

        //owner
        $owner_filter = $this->getState('Note.owner_id');
        if ($owner_filter != null && $owner_filter != "all") {
            $owner_type = $this->getState('Note.owner_type');
            switch ($owner_type) {
                case "team":
                    $team_member_ids = UsersHelper::getTeamUsers($owner_filter,TRUE);
                    $query->where("n.owner_id IN (".implode(',',$team_member_ids).")");
                break;
                case "member":
                    $query->where("n.owner_id=".$owner_filter);
                break;
            }
        }
        //created
        $created_filter = $this->getState('Note.created');
        if ($company_filter != null) {

        }
        //category
        $category_filter = $this->getState('Note.category_id');
        if ($category_filter != null) {
            $query->where("n.category_id=".$category_filter);
        }

         if ($this->_id) {
            if ( is_array($this->_id) ) {
                $query->where("n.id IN (".implode(',',$this->_id).")");
            } else {
                $query->where("n.id=$this->_id");
            }
        }

        /** ---------------------------------------------------------------
         * Filter data using member role permissions
         */
        $member_id = UsersHelper::getUserId();
        $member_role = UsersHelper::getRole();
        $team_id = UsersHelper::getTeamId();
        if ($this->public_notes != true) {
            if ($member_role != 'exec') {
                 //manager filter
                if ($member_role == 'manager') {
                    $query->where('owner.team_id = '.$team_id);
                } else {
                //basic user filter
                    $query->where('n.owner_id = '.$member_id);
                }
            }
        }

        $query->where("n.published=".$this->published);
        $query->order("n.modified DESC");

        //load results
        $db->setQuery($query);
        $results = $db->loadAssocList();

        //clean results
        if (count($results) > 0) {
            foreach ($results as $key => $note) {
                $results[$key]['created_formatted'] = DateHelper::formatDate($note['created']);
                $results[$key]['owner_avatar'] = CobaltHelper::getGravatar($note['email']);
            }
        }

        //$this->app->triggerEvent('onNoteLoad', array(&$results));

        if (!$display) {
            //return results
            return $results;

        } else {
            $notesView = Factory::getView('note','default','phtml',array('notes'=>$results),$this);

            return $notesView;
        }
    }

    /*
     * method to get list of deals
     */

    public function getNoteCategories()
    {
        //db object
        $db = $this->getDb();
        //gen query
        $query = $db->getQuery(true);
        $query->select("name,id FROM #__notes_categories");
        //set query
        $db->setQuery($query);
        //load list
        $row = $db->loadAssocList();
        $blank = array(array('name'=>TextHelper::_('COBALT_NONE'),'id'=>0));
        $return = array_merge($blank,$row);
        //return results
        return $return;

    }

    public function populateState()
    {
        //get states
        $filter_order = $this->app->getUserStateFromRequest('Note.filter_order','filter_order','comp.name');
        $filter_order_Dir = $this->app->getUserStateFromRequest('Note.filter_order_Dir','filter_order_Dir','asc');

        //set default filter states
        $company_filter = $this->app->getUserStateFromRequest('Note.company_name','company_name',null);
        $deal_filter = $this->app->getUserStateFromRequest('Note.deal_name','deal_name',null);
        $person_filter = $this->app->getUserStateFromRequest('Note.person_name','person_name',null);
        $owner_filter = $this->app->getUserStateFromRequest('Note.owner_id','owner_id',null);
        $owner_type = $this->app->getUserStateFromRequest('Note.owner_type','owner_type',null);
        $created_filter = $this->app->getUserStateFromRequest('Note.created','created',null);
        $category_filter = $this->app->getUserStateFromRequest('Note.category_id','category_id',null);

        //set states
        //
        $state = new Registry;

        $state->set('Note.filter_order', $filter_order);
        $state->set('Note.filter_order_Dir',$filter_order_Dir);
        $state->set('Note.company_name',$company_filter);
        $state->set('Note.deal_name',$deal_filter);
        $state->set('Note.person_name',$person_filter);
        $state->set('Note.owner_id',$owner_filter);
        $state->set('Note.owner_type',$owner_type);
        $state->set('Note.created',$created_filter);
        $state->set('Note.category_id',$category_filter);

        $this->setState($state);

    }

    public function remove($id)
    {
        $table = $this->getTable('Note');
        $table->delete($id);

        return $table;
    }
}
