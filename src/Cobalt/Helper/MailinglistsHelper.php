<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Helper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class MailinglistsHelper
{

    public $listId 	= NULL;
    public $peopleIds 	= NULL;
    public $subscriber = NULL;

    public function __construct()
    {
        $app = JFactory::getApplication();
        $this->listId = $app->input->get('list_id');
        $this->peopleIds = $app->input->get('people_ids');
        $this->subscriber = $this->getSubscriberId();
    }

    public static function getSubscriberId()
    {
        $app = JFactory::getApplication();

        $person_id = $app->input->get('person_id') ? $app->input->get('person_id') : $app->input->get('id');
        $personModel = new CobaltModelPeople();
        $email = $personModel->getEmail($person_id);

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("subid")
            ->from("#__acymailing_subscriber")
            ->where("email='".$email."'");

        $db->setQuery($query);

        $id = $db->loadResult();

        return $id;

    }

    /**
     * Get Acymailing Mailing Lists
     * @param  [type] $listId=NULL [description]
     * @return [type] [description]
     */
    public static function getMailingLists($all=FALSE)
    {
        $subid = self::getSubscriberId();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("DISTINCT list.listid,list.name,list.description,list.color")
                ->from("#__acymailing_list AS list");

                if ($subid) {
                    $query->select("subscribed.listid AS isSubscribed");
                    $query->leftJoin("#__acymailing_listsub AS subscribed ON subscribed.listid = list.listid AND subscribed.status=1 AND subscribed.subid = ".$subid);
                }

                if (!$all) {
                    $query->where("subscribed.subid=".$subid);
                }

                $query->where("list.published=1");

        $db->setQuery($query);

        $lists = $db->loadObjectList();

        if ( count($lists) > 0 ) {
            foreach ($lists as $list) {
                if ( !isset($list->isSubscribed) ) {
                    $list->isSubscribed = 0;
                }
            }
        }

        return $lists;

    }

    /**
     * Get Acymailing List Newsletters
     * @param  [type] $listId=NULL [description]
     * @return [type] [description]
     */
    public static function getNewsletters($listId=NULL)
    {
        $listId = $listId ? $listId : $this->listId;
        $subId = self::getSubscriberId();

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("mail.mailid,mail.subject,mail.published,mail.senddate,user.open,user.opendate")
                ->from("#__acymailing_mail AS mail")
                ->leftJoin("#__acymailing_listmail AS listmail ON listmail.mailid = mail.mailid")
                ->leftJoin("#__acymailing_userstats AS user ON mail.mailid = user.mailid")
                ->where("listmail.listid=".$listId)
                ->where("user.subid=".$subId)
                ->where("mail.published=1");

        $db->setQuery($query);

        $newsletters = $db->loadObjectList();

        return $newsletters;

    }

    /**
     * Add Cobalt People to Acymailing Lists
     */
    public static function addMailingList($data)
    {
        $person_id = $data['person_id'];
        $listid = $data['listid'];

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $peopleModel = new CobaltModelPeople();
        $person = $peopleModel->getPerson($person_id);
        $person = $person[0];

        $time = time();

        $query->select("subid")
                ->from("#__acymailing_subscriber")
                ->where("email='".$person['email']."'");

        $db->setQuery($query);
        $subid = $db->loadResult();

        if ($subid) {

            $query->clear();
            $query->update("#__acymailing_listsub")
                ->set(array("subdate=$time","status=1"))
                ->where("subid=".$subid)
                ->where("listid=".$listid);

            $db->setQuery($query);
            $db->query();

        } else {

            $query->insert("#__acymailing_subscriber")
                ->columns("email,name,created,confirmed,enabled,accept,html")
                ->values($db->Quote($person['email']).','.$db->Quote($person['first_name'].' '.$person['last_name']).','.$time.','.'1,1,1,1');

            $db->setQuery($query);
            $db->query();

            $subid = $db->insertid();

            $query->clear();
            $query->insert("#__acymailing_listsub")
                ->columns("listid,subid,subdate,status")
                ->values($listid.','.$subid.','.$time.',1');

            $db->setQuery($query);
            $db->query();

        }

        return true;

    }

    /**
     * Remove Cobalt People from Acymailing Lists
     */
    public static function removeMailingList($data)
    {
        $person_id = $data['person_id'];
        $listid = $data['listid'];

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $time = time();

        $values = array("unsubdate=".$time,'status=0');

        $query->update("#__acymailing_listsub")->set($values)->where("listid=".$listid)->where("subid=".self::getSubscriberId());
        $db->setQuery($query);
        $db->query();

        return true;

    }

    public static function getLinks()
    {
        $app = JFactory::getApplication();

        $data = $app->input->getRequest('post');
        $person_id = array_key_exists('person_id',$data) ? $data['person_id'] : $data['id'];
        $mailid = $data['mailid'];
        $subid = self::getSubscriberId();

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("click.click,url.name,url.url")
                ->from("#__acymailing_urlclick AS click")
                ->leftJoin("#__acymailing_url AS url ON url.urlid = click.urlid")
                ->where("click.subid=".$subid)
                ->where("click.mailid=".$mailid);

        $db->setQuery($query);

        return $db->loadObjectList();

    }

}
