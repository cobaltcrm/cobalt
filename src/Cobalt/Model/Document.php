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

use Cobalt\Table\DocumentTable;
use JFactory;
use Joomla\Registry\Registry;
use Joomla\Filesystem\File;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\ActivityHelper;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\FileHelper;
use JUri;
use Joomla\Filter\OutputFilter;
use Cobalt\Helper\RouteHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Document extends DefaultModel
{
    public $company_id = null;
    public $deal_id = null;
    public $person_id = null;

    /**
     * Method to store a record
     *
     * @return boolean True on success
     */
    public function store($data=null)
    {
        if ($data) {

            $data = (array) $data;

            $_FILES = array();
            $_FILES['document'] = $data;
            $_FILES['tmp_name'] = $data['attachment'];
            $fileName = $data['value'];
            $fileTemp = $data['attachment'];
            $association_id = $data['association_id'];
            $association_type = $data['association_type'];
            $uploadedFileExtension = substr(strrchr($fileName,'.'),1);
            $data['is_attachment'] = 1;
            $data['email'] = 1;

        } else {

            $association_id = $_POST['association_id'];
            $association_type = $_POST['association_type'];

            //this is the name of the field in the html form, filedata is the default name for swfupload
            //so we will leave it as that
            $fieldName = 'document';

            //any errors the server registered on uploading
            $fileError = $_FILES[$fieldName]['error'];
            if ($fileError > 0) {
                    switch ($fileError) {
                    case 1:
                    echo TextHelper::_( 'FILE TO LARGE THAN PHP INI ALLOWS' );

                    return;

                    case 2:
                    echo TextHelper::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' );

                    return;

                    case 3:
                    echo TextHelper::_( 'ERROR PARTIAL UPLOAD' );

                    return;

                    case 4:
                    echo TextHelper::_( 'ERROR NO FILE' );

                    return;
                    }
            }

            //check the file extension is ok
            $fileName = $_FILES[$fieldName]['name'];
            $fileTemp = $_FILES[$fieldName]['tmp_name'];

        }

        $uploadedFileNameParts = explode('.',$fileName);
        $uploadedFileExtension = array_pop($uploadedFileNameParts);

        $validFileExts = explode(',', 'jpeg,jpg,png,gif,pdf,doc,docx,odt,rtf,ppt,xls,txt');

        //assume the extension is false until we know its ok
        $extOk = false;

        //go through every ok extension, if the ok extension matches the file extension (case insensitive)
        //then the file extension is ok
        foreach ($validFileExts as $key => $value) {
            if ( preg_match("/$value/i", $uploadedFileExtension ) ) {
                $extOk = true;
            }
        }

        if ($extOk == false) {
            echo TextHelper::_( 'INVALID EXTENSION' );

                return;
        }

        //data generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));
        $hashFilename = md5($fileName.$date).".".$uploadedFileExtension;

        //lose any special characters in the filename
        $fileName = preg_replace("[^A-Za-z0-9.]", "-", $fileName);

        //always use constants when making file paths, to avoid the possibilty of remote file inclusion
        $uploadPath = JPATH_SITE.'//documents/'.$hashFilename;

        if ($data['is_attachment']) {
            if (!File::write($uploadPath,$fileTemp)) {
                echo TextHelper::_( 'ERROR MOVING FILE' );

                return;
            }
        } else {
            if (!File::upload($fileTemp, $uploadPath)) {
             echo TextHelper::_( 'ERROR MOVING FILE' );

                return;
            }
        }

        $fileSize = filesize($uploadPath);

       //update the database
       $newData = array(
                    'name'                  =>  $fileName,
                    'filename'              =>  $hashFilename,
                    'association_id'        =>  $association_id,
                    'association_type'      =>  $association_type,
                    'filetype'              =>  $uploadedFileExtension,
                    'size'                  =>  $fileSize/1024,
                    'created'               =>  $date
                    );

        if ( array_key_exists('email',$data) && $data['email'] ) {
            $newData['email'] = 1;
        }

        //Load Tables
        $row = new DocumentTable;
        $oldRow = new DocumentTable;

        //date generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));

        if ( !array_key_exists('id',$newData) ) {
            $newData['created'] = $date;
            $status = "created";
        } else {
            $row->load($data['id']);
            $oldRow->load($data['id']);
            $status = "updated";
        }

        $is_image = is_array(getimagesize($uploadPath)) ? true : false;

        $newData['modified'] = $date;
        $newData['owner_id'] = UsersHelper::getUserId();
        $newData['is_image'] = $is_image;

        // Bind the form fields to the table
        if (!$row->bind($newData)) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

       $app = \Cobalt\Container::fetch('app');
       //$app->triggerEvent('onBeforeDocumentSave', array(&$row));

        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

        $id = ( array_key_exists('id',$data) ) ? $data['id'] : $this->db->insertId();

        ActivityHelper::saveActivity($oldRow, $row,'document', $status);

        //$app->triggerEvent('onAfterDocumentSave', array(&$row));

        return $id;
    }

    /**
     * Method to retrieve documents
     * @param $id specific id to retrieve, if null all are returned
     */
    public function getDocuments($id=null)
    {
        $app = \Cobalt\Container::fetch('app');

        //get DBO
        $db = JFactory::getDBO();

        //gen query
        $query = $db->getQuery(true);
        $query->select("d.*,".
                       "c.name as company_name,".
                       "deal.name as deal_name,".
                       "p.first_name as owner_first_name, p.last_name as owner_last_name,".
                       "CONCAT(u.first_name,' ',u.last_name) AS owner_name");
        $query->from("#__documents AS d");
        $query->leftJoin("#__companies AS c ON d.association_type = 'company' AND d.association_id = c.id");
        $query->leftJoin("#__deals AS deal ON d.association_type = 'deal' AND d.association_id = deal.id");
        $query->leftJoin("#__people AS p ON d.association_type ='person' AND d.association_id = p.id");
        $query->leftJoin("#__users AS u ON u.id = d.owner_id");

        //get user data
        $member_type = UsersHelper::getRole();
        $member_id = UsersHelper::getUserId();
        $team_id = UsersHelper::getTeamId();

        //get session data
        $session = JFactory::getSession();

        //get post data
        $assoc  = $app->input->get('assoc');
        $user   = $app->input->get('user');
        $type   = $app->input->get('type');
        $team   = $app->input->get('team_id');
        $document_name = $app->input->getString('document_name');

        //determine if we are searching for a team or a user
        if ($team) {
            $session->set('document_user_filter',null);
        }

        if ($user) {
            $session->set('document_team_filter',null);
        }

        //set user session data
        if ($assoc != null) {
            $session->set('document_assoc_filter',$assoc);
        } else {
            $sess_assoc = $session->get('document_assoc_filter');
            $assoc = $sess_assoc;
        }
        if ($user != null) {
            $session->set('document_user_filter',$user);
            $session->set('document_team_filter',null);
        } else {
            $sess_user = $session->get('document_user_filter');
            $user = $sess_user;
        }
        if ($type != null) {
            $session->set('document_type_filter',$type);
        } else {
            $sess_type = $session->get('document_type_filter');
            $type = $sess_type;
        }
        if ($team != null) {
            $session->set('document_team_filter',$team);
            $session->set('document_user_filter',null);
        } else {
            $sess_team = $session->get('document_team_filter');
            $team = $sess_team;
        }

        //filter for team
        if ($team) {
            $query->where("u.team_id=$team");
        }
        //filter for user
        if ($user && $user != "all") {
            $query->where('d.owner_id='.$user);
        }

        //filter data
        if ($assoc AND $assoc != 'all') {
            switch ($assoc) {
                case "deals":
                    $query->where("d.association_type='deal'");
                    break;
                case "people":
                    $query->where("d.association_type='person'");
                    break;
                case "companies":
                    $query->where("d.association_type='company'");
                    break;
                case "emails":
                    $query->where("d.email=1");
                    break;
                case "shared":
                    $query->where("d.shared=1");
                    break;
            }
        }

        //set user filter states
        $query->order($this->getState('Document.filter_order') . ' ' . $this->getState('Document.filter_order_Dir'));

        //filter for types
        if ($type AND $type != 'all') {
            $doc_types = array();
            switch ($type) {
                case "spreadsheets":
                    $doc_types = array( 'xlr','xls','xlsx' );
                    break;
                case "images":
                    $doc_types = array( 'bmp','gif','jpg','jpeg','png', 'psd','pspimage','thm','tif','yuv' );
                    break;
                case "documents":
                    $doc_types = array(  );
                    break;
                case "pdfs":
                    $doc_types = array( 'pdf' );
                    break;
                case "presentations":
                    $doc_types = array( 'pps','ppt','pptx','key','odp' );
                    break;
                case "others":
                    $doc_types = array();
                    break;
            }
            if ( count($doc_types) ) {
                $queryString = '';
                foreach ($doc_types as $key => $type) {
                   if ($key) {
                        $queryString .= " OR d.filetype='".$type."'";
                   } else {
                        $queryString .= " d.filetype='".$type."'";
                   }
                }
                $query->where('('.$queryString.')');
            }
        }

        //sort depending on member role
        if ($member_type != 'exec') {
            if ($assoc != 'shared') {
                $shared = $assoc == "all" ? "OR d.shared=1" : "";
                if ($member_type == 'manager') {
                    $query->where('(u.team_id='.$team_id." $shared)");
                } else {
                    $query->where('(d.owner_id='.$member_id." $shared)");
                }
            }
        }

        if ($id) {
            $query->where("d.id=".$id);
        }

        if ($this->company_id) {
            $query->where("(d.association_type='company' AND d.association_id=".$this->company_id.')');
        }

        if ($this->deal_id) {
            $query->where("(d.association_type='deal' AND d.association_id=".$this->deal_id.')');
        }

        if ($this->person_id) {
            $query->where("(d.association_type='person' AND d.association_id=".$this->person_id.')');
        }

        if (!empty($document_name)) {
            $query->where("( d.name LIKE '%".$document_name."%' OR deal.name LIKE '%".$document_name."%' OR c.name LIKE '%".$document_name."%')");
        }

        //get results
        $offset = $app->input->getInt('start',0);
        $limit = $app->input->getInt('length',0);
        $db->setQuery($query, $offset, $limit);
        $results = $db->loadAssocList();

        //$app->triggerEvent('onDocumentLoad', array(&$results));

        //return results
        return $results;

    }

    public function getDocument($id=null)
    {
        $app = \Cobalt\Container::fetch('app');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("*")->from("#__documents");

        if (!$id) {
            $document_hash = $app->input->getString('document');
            $query->where("filename=".$db->quote($document_hash));
        } else {
            $query->where("id=".$id);
        }

        $db->setQuery($query);
        $document = $db->loadObjectList();
        $document = $document[0];

        $document->path = getcwd().'/uploads/'.$document->filename;

        //$app->triggerEvent('onDocumentLoad', array(&$document));

        return $document;

    }

    /**
     * Method to delete a record
     *
     * @return boolean True on success
     */
    public function deleteDocument($id)
    {
        //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //get filename to delete
        $query->select("d.filename FROM #__documents as d");
        $query->where("d.id=".$id);

        //get filename
        $db->setQuery($query);
        $filename = $db->loadResult();

        //delete
        $query->clear();
        $query->delete("#__documents");
        $query->where("id=".$id);
        $db->setQuery($query);
        $db->query();

        if ( !unlink(JPATH_COMPONENT.'/documents/'.$filename) ) return false;

        //return
        return true;
    }

    /**
     * Populate user state requests
     */
    public function populateState()
    {
        //get states
        $app = \Cobalt\Container::fetch('app');

        if ( $app->input->get('view') == "documents" ) {

            $filter_order = $app->getUserStateFromRequest('Document.filter_order','filter_order','d.created');
            $filter_order_Dir = $app->getUserStateFromRequest('Document.filter_order_Dir','filter_order_Dir','desc');

        } else {

            $filter_order = "d.created";
            $filter_order_Dir = "desc";

        }

        $state = new Registry;

        //set states
        $state->set('Document.filter_order',$filter_order);
        $state->set('Document.filter_order_Dir',$filter_order_Dir);

        $this->setState($state);
    }

    public function _buildQuery()
    {
        //database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("d.*");
        $query->from("#__documents AS d");
        $query->where("d.shared=1");

        return $query;

    }

    /**
     * Describe and configure columns for jQuery dataTables here.
     *
     * 'data'       ... column id
     * 'orderable'  ... if the column can be ordered by user or not
     * 'ordering'   ... name of the column in SQL query with table prefix
     * 'sClass'     ... CSS class applied to the column
     * (other settings can be found at dataTable documentation)
     *
     * @return array
     */
    public function getDataTableColumns() {
        $columns = array();
        $columns[] = array('data' => 'id', 'orderable' => false, 'sClass' => 'text-center');
        $columns[] = array('data' => 'type', 'ordering' => 'd.filetype');
        $columns[] = array('data' => 'name', 'ordering' => 'd.name');
        if ($this->app->input->getString('loc','documents') == 'documents') {
            $columns[] = array('data' => 'association', 'ordering' => false);
        }
        $columns[] = array('data' => 'owner', 'ordering' => 'u.last_name');
        $columns[] = array('data' => 'size', 'orderable' => false);
        $columns[] = array('data' => 'created', 'ordering' => 'd.created');

        return $columns;
    }

    /**
     * Method transforms items to the format jQuery dataTables needs.
     * Algorithm is available in parent method, just pass items array.
     *
     * @param   array of object of items from the database
     * @return  array in format dataTables requires
     */
    public function getDataTableItems($items = array())
    {
        if (!$items)
        {
            $items = $this->getDocuments();
        }

        return parent::getDataTableItems($items);
    }

    /**
     * Prepare HTML field templates for each dataTable column.
     *
     * @param   string column name
     * @param   object of item
     * @return  string HTML template for propper field
     */
    public function getDataTableFieldTemplate($column, $item)
    {
        switch ($column)
        {
            case 'id':
                $template = '<input type="checkbox" class="export" name="ids[]" value="'.$item->id.'" />';
                break;
            case 'type':
                $file_path = sprintf('%s/media/images/%s.png',JPATH_COBALT,$item->filetype);
                if (file_exists($file_path)) {
                    $file_src = sprintf('%s/src/Cobalt/media/images/%s.png',JUri::base(),$item->filetype);
                    $template = '<img src="'.$file_src.'" >';
                } else {
                    $file_src = sprintf('%s/src/Cobalt/media/images/file.png',JUri::base());
                    $template = '<img src="'.$file_src.'" >';
                }
                break;
            case 'name':
                $template = '<div class="dropdown"><span class="caret"></span><a id="'.$item->id.'" class="document_edit dropdown-toggle" data-toggle="dropdown" role="button" href="javascript:void(0);"> '.$item->name.'</a>';

                $template .= '<ul class="dropdown-menu" role="menu">';
                $template .= '<li><a href="'.RouteHelper::_('index.php?task=PreviewDocument&format=raw&tmpl=component&document='.$item->filename).'" target="_blank" class="document_preview" id="preview_'.$item->id.'"><i class="glyphicon glyphicon-eye-open"></i> '.TextHelper::_('COBALT_PREVIEW').'</a></li>';
                $template .= '<li><a href="'.RouteHelper::_('index.php?task=DownloadDocument&format=raw&tmpl=component&document='.$item->filename).'" target="_blank" class="document_download" id="download_'.$item->id.'"><i class="glyphicon glyphicon-download"></i> '.TextHelper::_('COBALT_DOWNLOAD').'</a></li>';
                if ($item->owner_id == UsersHelper::getLoggedInUser()->id) {
                    $template .= '<li><a href="javascript:void(0);" class="document_delete" id="delete_'.$item->id.'"><i class="glyphicon glyphicon-remove"></i> '.TextHelper::_('COBALT_DELETE').'</a></li>';
                }
                $template .= '</ul></div>';
                break;
            case 'association':
                $association_type = $item->association_type;
                //assign association link
                switch ($association_type) {
                    case "deal":
                        $view = 'deals';
                        $association_type = "deal";
                        $item->association_name = $item->deal_name;
                        break;
                    case "person":
                        $view = "people";
                        $association_type = "person";
                        $item->association_name = $item->owner_first_name." ".$item->owner_last_name;
                        break;
                    case "company";
                        $view = "companies";
                        $association_type = "company";
                        $item->association_name = $item->company_name;
                        break;
                }
                if (isset($item->association_name)) {
                    $template = '<a href="'.RouteHelper::_('index.php?view='.$view.'&layout='.$association_type.'&id='.$item->association_id).'" >'.$item->association_name;
                } else {
                    $template = "";
                }
                break;
            case 'owner':
                $template = $item->owner_name;
                break;
            case 'size':
                $template = FileHelper::sizeFormat($item->size);
                break;
            case 'created':
                $template = DateHelper::formatDate($item->created);
                break;
            case 'modified':
                $template = DateHelper::formatDate($item->modified);
                break;
            default:
                if (isset($column) && isset($item->{$column}))
                {
                    $template = $item->{$column};
                }
                else
                {
                    $template = '';
                }
                break;
        }
        return $template;
    }
}
