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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class CobaltModelAvatar extends JModelBase
{


    var $image;
    var $image_type;


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


    /**
     * Save user avatars
     * @return [type] [description]
     */
    public function saveAvatar(){

        $app = JFactory::getApplication();

        //this is the name of the field in the html form, filedata is the default name for swfupload
        //so we will leave it as that
        $fieldName = 'avatar';

        //any errors the server registered on uploading
        $fileError = $_FILES[$fieldName]['error'];
        if ($fileError > 0)
        {
                switch ($fileError)
            {
                case 1:
                echo CRMText::_( 'FILE TO LARGE THAN PHP INI ALLOWS' );
                return;

                case 2:
                echo CRMText::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' );
                return;

                case 3:
                echo CRMText::_( 'ERROR PARTIAL UPLOAD' );
                return;

                case 4:
                echo CRMText::_( 'ERROR NO FILE' );
                return;
                }
        }

        //check the file extension is ok
        $fileName = $_FILES[$fieldName]['name'];
        $fileTemp = $_FILES[$fieldName]['tmp_name'];

        $uploadedFileNameParts = explode('.',$fileName);
        $uploadedFileExtension = array_pop($uploadedFileNameParts);

        $validFileExts = explode(',', 'jpeg,jpg,png,gif,bmp');

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
            echo CRMText::_( 'INVALID EXTENSION' );
                return;
        }

        //data generation
        $date = CobaltHelperDate::formatDBDate(date('Y-m-d H:i:s'));
        $hashFilename = md5($fileName.$date).".".$uploadedFileExtension;

        //lose any special characters in the filename
        $fileName = preg_replace("[^A-Za-z0-9.]", "-", $fileName);

        //always use constants when making file paths, to avoid the possibilty of remote file inclusion
        $uploadPath = JPATH_SITE.'//media/avatars/'.$hashFilename;

        if(!JFile::upload($fileTemp,$uploadPath))
        {
            echo CRMText::_( 'ERROR MOVING FILE' );
            return;
        }

        $image = new JImage();
        $image->loadFile($uploadPath);
        $image->resize(50,50,FALSE);
        $image->toFile($uploadPath);

        $item_type = $app->input->get('item_type');
        $item_id = $app->input->get('item_id');

        $data = array('id'=>$item_id,'avatar'=>$hashFilename);

        $this->deleteOldAvatar($item_id,$item_type);

        switch($item_type){
            case "people":
                $model_name = "people";
            break;
            case "companies":
                $model_name = "company";
            break;
        }

        $modelClass = "CobaltModel".ucwords($model_name);
        $model = new $modelClass();
        $model->store($data);

        return JURI::base().'libraries/crm/media/avatars/'.$hashFilename;

    }

    public function deleteOldAvatar($item_id,$item_type){

        $avatar = $this->getAvatar($item_id,$item_type);
        if ( $avatar ){
            echo JPATH_SITE.'//media/avatars/'.$avatar;
            JFile::delete(JPATH_SITE.'//media/avatars/'.$avatar);
        }

    }

    public function getAvatar($item_id,$item_type){

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->clear();
        $query->select("avatar")->from("#__".$item_type)->where("id=".$item_id);

        $db->setQuery($query);

        return $db->loadResult();

    }
 }