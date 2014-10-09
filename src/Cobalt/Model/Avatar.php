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

use Joomla\Filesystem\File;
use Joomla\Image\Image;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Avatar extends DefaultModel
{
    public $image;
    public $image_type;

    /**
     * Save user avatars
     * @return [type] [description]
     */
    public function saveAvatar()
    {
        //this is the name of the field in the html form, filedata is the default name for swfupload
        //so we will leave it as that
        $fieldName = 'avatar';

        //any errors the server registered on uploading
        $fileError = $_FILES[$fieldName]['error'];
        if ($fileError > 0) {
            switch ($fileError) {
                case 1:
                    echo TextHelper::_( 'FILE TO LARGE THAN PHP INI ALLOWS' );

                    return false;
                case 2:
                    echo TextHelper::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' );

                    return false;
                case 3:
                    echo TextHelper::_( 'ERROR PARTIAL UPLOAD' );

                    return false;
                case 4:
                    echo TextHelper::_( 'ERROR NO FILE' );

                    return false;
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
        foreach ($validFileExts as $key => $value) {
            if ( preg_match("/$value/i", $uploadedFileExtension ) ) {
                $extOk = true;
            }
        }

        if ($extOk == false) {
            echo TextHelper::_('INVALID EXTENSION');

            return false;
        }

        //data generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));
        $hashFilename = md5($fileName.$date).".".$uploadedFileExtension;

        //lose any special characters in the filename
        //$fileName = preg_replace("[^A-Za-z0-9.]", "-", $fileName);

        //always use constants when making file paths, to avoid the possibilty of remote file inclusion
        $uploadPath = JPATH_MEDIA . '/avatars/' . $hashFilename;

        if (!File::upload($fileTemp, $uploadPath)) {
            echo TextHelper::_('ERROR MOVING FILE');

            return false;
        }

        $image = new Image;
        $image->loadFile($uploadPath);
        $image->resize(50, 50, false);
        $image->toFile($uploadPath);

        $data = array(
            'id' => $this->state->get('item_id'),
            'avatar' => $hashFilename
        );

        $item_type = $this->state->get('item_type');

        $this->deleteOldAvatar($data['id'], $item_type);

        switch ($item_type) {
            case "people":
                $model_name = "people";
            break;
            case "companies":
                $model_name = "company";
            break;
        }

        $modelClass = "Cobalt\\Model\\".ucwords($model_name);
        $model = new $modelClass($this->db);
        $model->store($data);

        return $this->app->get('uri.media.full') . 'avatars/' . $hashFilename;

    }

    public function deleteOldAvatar($item_id,$item_type)
    {
        $avatar = $this->getAvatar($item_id,$item_type);
        if ($avatar) {
            echo JPATH_MEDIA . '/avatars/' . $avatar;
            File::delete(JPATH_MEDIA . '/avatars/' . $avatar);
        }

    }

    public function getAvatar($item_id, $item_type)
    {
        $query = $this->db->getQuery(true);

        $query->select("avatar")->from("#__".$item_type)->where("id=".$item_id);

        return $this->db->setQuery($query)->loadResult();

    }
 }
