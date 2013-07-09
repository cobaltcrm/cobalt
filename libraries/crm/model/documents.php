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

class CobaltModelDocuments extends CobaltModelDefault
{

    var $_view = "admindocuments";

    /**
     *
     *
     * @access  public
     * @return  void
     */
    function __construct()
    {
        parent::__construct();

    }

    function store($data=null)
    {

        $app = JFactory::getApplication();

        //Load Tables
        $row = JTable::getInstance('documents','Table');
        if($data==null){
            $data = $app->input->getRequest( 'post' );
        }

        //date generation
        $date = date('Y-m-d H:i:s');

        if ( !array_key_exists('id',$data) ){
            $data['created'] = $date;
        }

        $data['modified'] = $date;
        $data['shared'] = 1;

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Make sure the record is valid
        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Store the web link table to the database
        if (!$row->store()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }

    function _buildQuery(){

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
     * Get list of stages
     * @param int $id specific search id
     * @return mixed $results results
     */
    function getDocuments($id=null){

        //database
        $db = JFactory::getDBO();
        $query = $this->_buildQuery();

        //sort
        $query->order($this->getState('Documents.filter_order') . ' ' . $this->getState('Documents.filter_order_Dir'));
        if( $id ){
            $query->where("d.id=$id");
        }

        //return results
        $db->setQuery($query);
        $documents = $db->loadAssocList();

        return $documents;

    }

    function populateState(){
        //get states
        $app = JFactory::getApplication();
        $filter_order = $app->getUserStateFromRequest('Documents.filter_order','filter_order','d.filename');
        $filter_order_Dir = $app->getUserStateFromRequest('Documents.filter_order_Dir','filter_order_Dir','asc');

        //set states
        $state = new JRegistry();
        $state->set('Documents.filter_order', $filter_order);
        $state->set('Documents.filter_order_Dir',$filter_order_Dir);
        $this->setState($state);
    }

    function remove($id){
        //get dbo
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //delete id
        $query->delete('#__documents')->where('id = '.$id);
        $db->setQuery($query);
        $db->query();
    }

    function upload(){
        //import joomlas filesystem functions, we will do all the filewriting with joomlas functions,
        //so if the ftp layer is on, joomla will write with that, not the apache user, which might
        //not have the correct permissions
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        //this is the name of the field in the html form, filedata is the default name for swfupload
        //so we will leave it as that
        $fieldName = 'document';

        //any errors the server registered on uploading
        $fileError = $_FILES[$fieldName]['error'];
        if ($fileError > 0)
        {
                switch ($fileError)
            {
                case 1:
                echo JText::_( 'FILE TO LARGE THAN PHP INI ALLOWS' );
                return;

                case 2:
                echo JText::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' );
                return;

                case 3:
                echo JText::_( 'ERROR PARTIAL UPLOAD' );
                return;

                case 4:
                echo JText::_( 'ERROR NO FILE' );
                return;
                }
        }

        //check for filesize
        $fileSize = $_FILES[$fieldName]['size'];
        if($fileSize > 2000000)
        {
            echo JText::_( 'FILE BIGGER THAN 2MB' );
        }

        //check the file extension is ok
        $fileName = $_FILES[$fieldName]['name'];
        $uploadedFileNameParts = explode('.',$fileName);
        $uploadedFileExtension = array_pop($uploadedFileNameParts);

        $validFileExts = explode(',', 'jpeg,jpg,png,gif,pdf,doc,docx,odt,rtf,ppt,xls,txt');

        //assume the extension is false until we know its ok
        $extOk = false;

        //go through every ok extension, if the ok extension matches the file extension (case insensitive)
        //then the file extension is ok
        foreach($validFileExts as $key => $value)
        {
            if( preg_match("/$value/i", $uploadedFileExtension ) )
            {
                $extOk = true;
            }
        }

        if ($extOk == false)
        {
            echo JText::_( 'INVALID EXTENSION' );
                return;
        }

        //the name of the file in PHP's temp directory that we are going to move to our folder
        $fileTemp = $_FILES[$fieldName]['tmp_name'];

        //for security purposes, we will also do a getimagesize on the temp file (before we have moved it
        //to the folder) to check the MIME type of the file, and whether it has a width and height
        $imageinfo = getimagesize($fileTemp);

        //lose any special characters in the filename
        $fileName = ereg_replace("[^A-Za-z0-9.]", "-", $fileName);
        $hash = md5($fileName).".".$uploadedFileExtension;

        //always use constants when making file paths, to avoid the possibilty of remote file inclusion
        $uploadPath = JPATH_SITE.'/uploads/'.$hash;

        $app = JFactory::getApplication();

        if(!JFile::upload($fileTemp, $uploadPath))
        {
            $msg = CRMText::_('COBALT_DOC_UPLOAD_FAIL');
            $app->redirect('index.php?view=admindocuments',$msg);
        }
        else
        {
           //update the database
           //date generation
           $date = date('Y-m-d H:i:s');
           $data = array (
                        'name'              =>  $fileName,
                        'filename'          =>  $hash,
                        'filetype'          =>  $uploadedFileExtension,
                        'size'              =>  $fileSize/1024,
                        'created'           =>  $date,
                        'shared'            =>  1,
                        'is_image'          =>  is_array(getimagesize($uploadPath)) ? true : false
                        );

           $model = new CobaltModelDocuments();
           $session = JFactory::getSession();

           if($model->store($data)){
               $msg = JText::_('COM_CRMERY_DOC_UPLOAD_SUCCESS');
               $app->redirect('index.php?view=admindocuments&layout=upload_success&format=raw',$msg);
               $session->set("upload_success", true);
           }else{
               $msg = JText::_('COM_CRMERY_DOC_UPLOAD_FAIL');
               $app->redirect('index.php?view=admindocuments&layout=upload_success&format=raw',$msg);
               $session->set("upload_success", false);
           }
        }
    }



}