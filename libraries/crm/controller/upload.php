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
defined( '_CEXEC' ) or die( 'Restricted access' );

class CobaltControllerUpload extends CobaltControllerDefault
{

function execute()
{
        $app = JFactory::getApplication();

        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.folder');

        $fieldName = 'document';

        //any errors the server registered on uploading
        $fileError = $_FILES[$fieldName]['error'];
        if ($fileError > 0) {
                switch ($fileError) {
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
        if ($fileSize > 2000000) {
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
        foreach ($validFileExts as $key => $value) {
            if ( preg_match("/$value/i", $uploadedFileExtension ) ) {
                $extOk = true;
            }
        }

        if ($extOk == false) {
            echo JText::_( 'INVALID EXTENSION' );

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
        $uploadPath = JPATH_SITE.'/uploads/'.$hash;

        if (!JFile::upload($fileTemp, $uploadPath)) {
            $msg = JText::_('COBALT_DOC_UPLOAD_FAIL');
            $app->redirect('index.php?view=documents',$msg);
        } else {
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
                        'is_image'          =>  is_array(getimagesize($uploadPath)) ? true : false,
                        'association_id'    =>  $app->input->get('association_id'),
                        'association_type'  =>  $app->input->get('association_type'),
                        'owner_id'          =>  CobaltHelperUsers::getUserId()
                        );

           $model = new CobaltModelDocuments();
           $session = JFactory::getSession();

           if ($id=$model->store($data)) {
            echo '<script type="text/javascript">window.top.window.uploadSuccess('.$id.');</script>';
               // $msg = JText::_('COBALT_DOC_UPLOAD_SUCCESS');
               // $app->redirect('index.php?view=documents&layout=upload&tmpl=component',$msg);
               // $session->set("upload_success", true);
           } else {
               // $msg = JText::_('COBALT_DOC_UPLOAD_FAIL');
               // $app->redirect('index.php?view=documents&layout=upload&tmpl=component',$msg);
               // $session->set("upload_success", false);
           }
        }
    }
}
