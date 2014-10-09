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

use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Model\Documents as DocumentsModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Upload extends DefaultController
{
    public function execute()
    {
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

        //check for filesize
        $fileSize = $_FILES[$fieldName]['size'];
        if ($fileSize > 2000000) {
            echo TextHelper::_( 'FILE BIGGER THAN 2MB' );
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
        foreach ($validFileExts as $key => $value) {
            if ( preg_match("/$value/i", $uploadedFileExtension ) ) {
                $extOk = true;
            }
        }

        if ($extOk == false) {
            echo TextHelper::_( 'INVALID EXTENSION' );

                return;
        }

        //the name of the file in PHP's temp directory that we are going to move to our folder
        $fileTemp = $_FILES[$fieldName]['tmp_name'];

        //for security purposes, we will also do a getimagesize on the temp file (before we have moved it
        //to the folder) to check the MIME type of the file, and whether it has a width and height
        $imageinfo = getimagesize($fileTemp);

        //lose any special characters in the filename
        $fileName = preg_replace("[^A-Za-z0-9.]", "-", $fileName);
        $hash = md5($fileName).".".$uploadedFileExtension;

        //always use constants when making file paths, to avoid the possibilty of remote file inclusion
        $uploadPath = JPATH_UPLOADS . '/' . $hash;

        $associationID = $this->getInput()->getInt('association_id');
        $associationType = $this->getInput()->getString('association_type');
        $filePath = $hash;
        switch ($associationType) {
            case 'company':
                $associationFolder = 'companies';
                break;
            case 'deal':
                $associationFolder = 'deals';
                break;
            case 'person':
                $associationFolder = 'people';
                break;
        }
        if (isset($associationFolder)) {
            $tmpPath = JPATH_UPLOADS . '/' . $associationFolder;
            if (is_dir($tmpPath)) {
                //check association_id folder
                if (intval($associationID)) {
                    $tmpPathID = $tmpPath.'/'.$associationID;
                    //create if not exists
                    if (!is_dir($tmpPathID)) {
                        if (Folder::create($tmpPathID)) {
                            //set as upload destiny
                            $tmpPath = $tmpPathID;
                            $associationFolder .= '/'.$associationID;
                        }
                    }
                }
                $uploadPath = $tmpPath.'/'.$hash;
                $filePath = $associationFolder.'/'.$hash;
            }
        }

        if (!File::upload($fileTemp, $uploadPath)) {
            $msg = TextHelper::_('COBALT_DOC_UPLOAD_FAIL');
            $this->getApplication()->redirect('index.php?view=documents',$msg);
        } else {
            $return_uri = base64_decode($this->getInput()->getBase64('return'));
            if (empty($return_uri)) {
                $return_uri = 'index.php?view=documents&layout=upload&tmpl=component';
            }
           //update the database
           //date generation
           $date = date('Y-m-d H:i:s');
           $data = array (
                'name'              =>  $fileName,
                'filename'          =>  $filePath,
                'filetype'          =>  $uploadedFileExtension,
                'size'              =>  $fileSize/1024,
                'created'           =>  $date,
                'shared'            =>  1,
                'is_image'          =>  is_array(getimagesize($uploadPath)) ? true : false,
                'association_id'    =>  $associationID,
                'association_type'  =>  $associationType,
                'owner_id'          =>  UsersHelper::getUserId()
           );

           $model = new DocumentsModel;
           //$session = $this->container->resolve('session');

           if ($id=$model->store($data)) {
            //echo '<script type="text/javascript">Cobalt.modalMessage(Joomla.JText._("COBALT_UPLOADING"),Joomla.JText._("COBALT_DOC_UPLOAD_SUCCESS"),"success");</script>';
               $msg = TextHelper::_('COBALT_DOC_UPLOAD_SUCCESS');
               $this->getApplication()->redirect($return_uri,$msg);
               // $session->set("upload_success", true);
           } else {
               //echo '<script type="text/javascript">Cobalt.modalMessage(Joomla.JText._(\'COBALT_UPLOADING\'),Joomla.JText._(\'COBALT_DOC_UPLOAD_FAIL\'),\'danger\');</script>';
               $msg = TextHelper::_('COBALT_DOC_UPLOAD_FAIL');
               $this->getApplication()->redirect($return_uri,$msg);
               // $session->set("upload_success", false);
           }
        }
    }
}
