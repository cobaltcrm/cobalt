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

use Cobalt\Helper\UsersHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Mail extends DefaultModel
{
    // Get config settings
    private $hostname = null;
    private $username = null;
    private $password = null;

    /**
     * IMAP CONNECTION
     */
    private $imap = null;
    private $stucture = null;
    private $attachments = null;

	/**
	 * Instantiate the model.
	 *
	 * @param   DatabaseDriver  $db     The database adapter.
	 * @param   Registry        $state  The model state.
	 *
	 * @since   1.0
	 */
	public function __construct(DatabaseDriver $db = null, Registry $state = null)
    {
        parent::__construct($db, $state);
        /**Initialize Configurations**/
        $this->_getConfig();
    }

    /**
     * Method to store a record
     *
     * @return boolean True on success
     */
    public function store()
    {

    }

    /**
     * Get imap configuration
     */
    private function _getConfig()
    {
        $config = ConfigHelper::getImapConfig();
        $this->hostname = $config->imap_host;
        $this->username = $config->imap_user;
        $this->password = $config->imap_pass;
         // Validate config
        if (strlen($this->hostname) == 0 || strlen($this->username) == 0  || strlen($this->password) == 0 ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * CONNECT TO IMAP
     * @param  [String]  $hostname=null [ if we wish to override the default configurations in the constructor ]
     * @param  [String]  $username=null [ if we wish to override the default configurations in the constructor ]
     * @param  [String]  $password=null [ if we wish to override the default configurations in the constructor ]
     * @return [BOOlean] [success]
     */
    private function _connect($hostname=null,$username=null,$password=null)
    {
        if ($hostname) {
            $this->hostname = $hostname;
        }
        if ($username) {
            $this->username = $username;
        }
        if ($password) {
            $this->password = $password;
        }

        $connected = FALSE;

        if ( strlen($this->hostname) == 0 || strlen($this->username) == 0  || strlen($this->password) == 0) {
            return false;

        } else {

            /* try to connect to default ssl */
            $error = error_reporting();
            error_reporting(0);
            if ( $this->imap = @imap_open('{'.$this->hostname.':993/imap/ssl/novalidate-cert}INBOX',$this->username,$this->password) ) {
                $error = error_reporting($error);
                $connected = true;
            } else {
                $error = error_reporting($error);
                $connected = false;
            }

            /* try to connect to fallback */
            if (!$connected) {
                $error = error_reporting();
                error_reporting(0);
                if ( $this->imap = @imap_open('{'.$this->hostname.'/notls}INBOX',$this->username,$this->password) ) {
                    $error = error_reporting($error);
                    $connected = true;
                } else {
                    $error = error_reporting($error);
                    $connected = false;
                }
            }

            /** SUPPRESS IMAP ERRORS **/
            imap_errors();
            imap_alerts();

        }

        return $connected;
    }

    /**
     * CLOSE CONNECTION
     */
    private function _close()
    {
        if ($this->imap) {
            imap_expunge($this->imap);
            imap_close($this->imap, CL_EXPUNGE);
            imap_errors();
            imap_alerts();
        }
    }

    /**
     * Build search string for imap searches
     * @return [Mixed] [Searches]
     */
    private function _buildSearch()
    {
        //Associated emails
        $emails = UsersHelper::getEmails();

        $searchStrings = false;

        if ($emails) {
            $searchStrings = array();
            for ( $i=0; $i<count($emails); $i++ ) {
                $email = $emails[$i];
                $searchStrings[] = 'UNSEEN FROM "'.$email['email'].'"';
            }
        }

        // $searchString = "UNSEEN";
        return $searchStrings;

    }

    /**
     * SEARCH IMAP
     * @param  Mixed  $filter search filter as String or as Array of searches
     * @param  string $params option filter params
     * @return mixed  $data emails
     */
    private function _search($filter="ALL",$params=null)
    {
        if ( is_array($filter) ) {
            $emails = array();
            foreach ($filter as $string) {
                $search = imap_search($this->imap,$string);
                if ($search) {
                    $emails = array_merge($emails,$search);
                }
            }
        } else {
            $emails = imap_search($this->imap,$filter . " " . $params );
        }

        /* if emails are returned, cycle through each... */
        $data = array();

        if ($emails) {
          /* for every email... */
          foreach ($emails as $email_number) {
            /* get information specific to this email */
            $overview = imap_fetch_overview($this->imap,$email_number,0);
            $this->structure = imap_fetchstructure($this->imap,$email_number,0);

            /* commented out to speed up email retrieval */
            $headers = imap_rfc822_parse_headers(imap_fetchheader($this->imap,$email_number));

            switch ( strtolower($this->structure->subtype) ) {
                case "plain":
                    $partNum = 1;
                break;
                case "alternative":
                    $partNum = 1;
                break;
                case "mixed":
                    $partNum = 1.2;
                break;
                case "html":
                    $partNum = 1.2;
                break;
            }
            $partNum=1;
            $message =  quoted_printable_decode(imap_fetchbody($this->imap,$email_number,$partNum,FT_PEEK));

            /* get any possible attachments, commented out to speed up email retrieval, we do not want to do this unless we are automatically associating an email with a user */
           // $attachments = $this->getAttachments($email_number);

            $email = array(
                    'overview'      => $overview[0],
                    'structure'     => $this->structure,
                    'headers'       => $headers,
                    'message'       => $message,
                    // 'attachments'   => $attachments
                );
            $data[$email_number] = $email;
          }
        }

        return $data;
    }

    public function getAttachments($email_number)
    {
        $this->attachments = array();
        $email = @imap_fetchstructure($this->imap, $email_number,0);
        imap_errors();
        imap_alerts();
        if ( is_object($email) ) {
            $parts = $this->create_part_array($email);

            for ( $i=0; $i<count($parts); $i++ ) {
                $part = $parts[$i];
                if ( array_key_exists('part_object',$part) && $part['part_object']->ifdparameters ) {
                    for ( $i2=0; $i2<count($part['part_object']->dparameters); $i2++) {
                        $param = $part['part_object']->dparameters[$i2];
                        $param->encoding = $part['part_object']->encoding;
                        $param->attachment = @imap_fetchbody($this->imap, $email_number, $part['part_number'],FT_PEEK);
                        if ($param->encoding == 3) { // 3 = BASE64
                            $param->attachment = base64_decode($param->attachment);
                        } elseif ($param->encoding == 4) { // 4 = QUOTED-PRINTABLE
                            $param->attachment = quoted_printable_decode($param->attachment);
                        }
                        $this->attachments[]=$param;
                    }
                }
            }
        } else {
            return false;
        }

        return $this->attachments;
    }

    /**
     * Store email attachments to local device
     * @param [mixed]  $attachments [array of attachments]
     * @param [String] $location    [storage location]
     */
    private function _storeAttachments($attachments,$key,$location="person")
    {
        if ( is_array($attachments) && count($attachments) > 0 ) {
            $model = new Document;
            foreach ($attachments as $attachment_key => $attachment) {
                $attachment->association_id = $key;
                $attachment->association_type = $location;
                $attachment->email = 1;
                $model->store($attachment);
            }
        }
    }

    public function storeAttachments($email_id, $person_id,$location="person")
    {
        $this->structure = @imap_fetchstructure($this->imap,$email_id);
        $attachments = $this->getAttachments($email_id);
        $this->_storeAttachments($attachments,$person_id,$location);
        imap_errors();
        imap_alerts();
    }

    /**
     * Associate emails with correct users
     */
    private function _associateEmails($emails)
    {
        //Pull all IDS from #__people WHERE owner_id EQUALS current logged in user
        $people = UsersHelper::getPeopleEmails();

        //If any emails match up with our TO then we automatically insert them as notes and store any attachments
        if ( count($emails) > 0 ) {
            foreach ($emails as $email_key => $email) {

                $this->structure = $email['structure'];

                $address = $email['headers']->to[0]->mailbox."@".$email['headers']->to[0]->host;

                if ( $key = array_search($address,$people) ) {

                    $data = array();
                    $data['note'] = $email['message'];
                    $data['owner_id'] = UsersHelper::getUserId();
                    $data['person_id'] = $key;

                    $model = new Note;
                    $model->store($data);

                    $attachments = $this->getAttachments($email['overview']->msgno);
                    if ( count($attachments) > 0 ) {
                        $email['attachments'] = $attachments;
                        $this->_storeAttachments($email['attachments'],$key);
                    }

                    $this->_delete($email['overview']->msgno);
                    unset($emails[$email_key]);
                }
            }
        }

        return $emails;
    }

    /**
     * DELETE EMAILS
     * @param mixed $message_ids message id(s) that should be deleted
     */
    private function _delete($message_ids)
    {
        if ( is_array($message_ids) ) {
            foreach ($message_ids as $id) {
                imap_delete($this->imap,$id);
            }
        } else {
            imap_delete($this->imap,$message_ids);
        }
    }

    /**
     * Retrieve user emails // inbox
     * @return [type] [description]
     */
    public function getMail()
    {
        // Validate config
        if (strlen($this->hostname) == 0 || strlen($this->username) == 0  || strlen($this->password) == 0 ) {
            return false;
        }

        /* grab emails */
        if ( $this->_connect() ) {

            /** construct and perform imap search **/
            $where = $this->_buildSearch();
            $emails = array();
            if ($where) {
                $emails = $this->_search($where);
            }
            if ($emails) {
                /** Associate emails and autoinsert entries into database, returns nonassociated emails **/
                $emails = $this->_associateEmails($emails);
            }

            /* close the connection */
            $this->_close();

            return $emails;

        }
    }

    /**
     * GET INDIVIDUAL EMAILS
     * @param  [type] $email_id [description]
     * @return [type] [description]
     */
    public function getEmail($email_id,$msgOnly=TRUE)
    {
        $this->_connect();
        $email = @imap_fetchstructure($this->imap, $email_id,0);
        imap_errors();
        imap_alerts();

        if ( is_object($email) ) {

            switch ( strtolower($email->subtype) ) {
                    case "plain":
                        $partNum = 1;
                    break;
                    case "alternative":
                        $partNum = 1;
                    break;
                    case "mixed":
                        $partNum = 1.2;
                    break;
                    case "html":
                        $partNum = 1.2;
                    break;
                }

            $message =  quoted_printable_decode(@imap_fetchbody($this->imap,$email_id,$partNum,FT_PEEK));
            imap_errors();
            imap_alerts();

            if (!$msgOnly) {

                $overview = @imap_fetch_overview($this->imap,$email_id,0);
                imap_errors();
            imap_alerts();

                // $headers = imap_rfc822_parse_headers(imap_fetchheader($this->imap,$email_id));

                switch ( strtolower($email->subtype) ) {
                    case "plain":
                        $partNum = 1;
                    break;
                    case "alternative":
                        $partNum = 1;
                    break;
                    case "mixed":
                        $partNum = 1.2;
                    break;
                    case "html":
                        $partNum = 1.2;
                    break;
                }

               $attachments = $this->getAttachments($email_id);

                $emailInfo = array(
                        'overview'      => $overview[0],
                        'structure'     => $email,
                        // 'headers'       => $headers,
                        'message'       => $message,
                        'attachments'   => $attachments
                    );

            }

        } else {
            return false;
        }

        $this->_close();

        return $msgOnly ? $message : $emailInfo;
    }

    public function create_part_array($structure, $prefix="")
    {
       $part_array = array();

        if (sizeof($structure->parts) > 0) {
            foreach ($structure->parts as $count => $part) {
                $this->add_part_to_array($part, $prefix.($count+1), $part_array);
            }
        }

       return $part_array;
    }

    public function add_part_to_array($obj, $partno, & $part_array)
    {
        if ($obj->type == TYPEMESSAGE) {
            $this->parse_message($obj->parts[0], $partno.".");
        } else {
            if (array_key_exists('parts',$obj) && sizeof($obj->parts) > 0) {
                foreach ($obj->parts as $count => $p) {
                    $this->add_part_to_array($p, $partno.".".($count+1), $part_array);
                }
            }
        }

        $part_array[] = array('part_number' => $partno, 'part_object' => $obj);
    }

    public function parse_message($obj, $prefix="")
    {
    /* Here you can process the data of the main "part" of the message, e.g.: */
      // do_anything_with_message_struct($obj);

      if (sizeof($obj->parts) > 0)
        foreach ($obj->parts as $count=>$p)
          $this->parse_part($p, $prefix.($count+1));
    }

    public function parse_part($obj, $partno)
    {
    /* Here you can process the part number and the data of the parts of the message, e.g.: */
      // do_anything_with_part_struct($obj,$partno);

      if ($obj->type == TYPEMESSAGE)
        $this->parse_message($obj->parts[0], $partno.".");
      else
        if (sizeof($obj->parts) > 0)
          foreach ($obj->parts as $count=>$p)
            $this->parse_part($p, $partno.".".($count+1));
    }

    /**
     * Remove user emails from inbox
     */
    public function removeEmail($message_id=null)
    {
        if ( $this->_connect() ) {
            if (!$message_id) {
                $message_id = $this->app->input->get('id');
            }
            if ($message_id != null || $message_id != 0) {
                $this->_delete($message_id);
            }
            $this->_close();
        }
    }

    public function saveEmail($email_id=null)
    {
        if (!$email_id) {
            $email_id = $this->app->input->get('id');
        }

        $email = $this->getEmail($email_id,FALSE);

        $person_id = $this->app->input->get('person_id');
        $deal_id = $this->app->input->get('deal_id');
        $person_name = $this->app->input->get('person_name');
        $deal_name = $this->app->input->get('deal_name');

        $data = array(
                'deal_id'       => $deal_id,
                'person_id'     => $person_id,
                'note'          => $email['message'],
                'person_name'   => $person_name,
                'deal_name'     => $deal_name
            );

        $noteModel = new Note;
        $noteModel->store($data);

        $this->_connect();

        if ($person_id) {
            try {
                $this->storeAttachments($email_id,$person_id,"person");
            } catch (\Exception $e) {

            }
        }
        if ($deal_id) {
            try {
                $this->storeAttachments($email_id,$deal_id,"deal");
            } catch (\Exception $e) {

            }
        }

        $this->_delete($email_id);
        $this->_close();

    }

}
