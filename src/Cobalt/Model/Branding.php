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

use Cobalt\Helper\DateHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Table\BrandingTable;
use Joomla\Filesystem\File;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Branding extends DefaultModel
{
    public function store()
    {
        //Load Tables
        $app = \Cobalt\Container::get('app');
        $row = new BrandingTable;
        $data = $app->input->getRequest('post');

        //date generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));
        $data['modified'] = $date;
        $this->changeDefault($data['id']);

        $fieldName = 'site_logo';

        //any errors the server registered on uploading
        $fileError = $_FILES[$fieldName]['error'];
        if ($fileError > 0) {
           unset($data['site_logo']);

        } else {

            //check the file extension is ok
            $fileName = $_FILES[$fieldName]['name'];
            $fileTemp = $_FILES[$fieldName]['tmp_name'];

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
            $hashFilename = md5($fileName.$date).".".$uploadedFileExtension;

            //lose any special characters in the filename
            //$fileName = preg_replace("[^A-Za-z0-9.]", "-", $fileName);

            //always use constants when making file paths, to avoid the possibilty of remote file inclusion
            $uploadPath = JPATH_SITE.'/libraries/crm/media/logos/'.$hashFilename;

            if (!File::upload($fileTemp, $uploadPath)) {
                echo TextHelper::_( 'ERROR MOVING FILE' );

                return;
            }

            $fileSize = filesize($uploadPath);

            $this->updateSiteLogo($hashFilename);
            unset($data['site_logo']);
        }

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            $this->setError($this->db->getErrorMsg());

            return false;
        }

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

        return true;
    }

    public function updateSiteLogo($logo)
    {
        $query = $this->db->getQuery(true)
            ->update("#__branding")
            ->set("site_logo=".$this->db->quote($logo));

        $this->db->setQuery($query)->execute();
    }

    /**
     * Get list of themes
     * @param  int   $id specific search id
     * @return mixed $results results
     */
    public function getThemes($id=null)
    {
        $query = $this->db->getQuery(true)
            ->select("b.*")
            ->from("#__branding AS b");

        return $this->db->setQuery($query)->loadAssocList();
    }

    /**
     * Get list of themes
     * @param  int   $id specific search id
     * @return mixed $results results
     */
    public function getDefaultTheme()
    {
        $query = $this->db->getQuery(true)
            ->select("b.*")
            ->from("#__branding AS b")
            ->where("assigned=1");

        return $this->db->setQuery($query)->loadAssocList();
    }

    /**
     * Change the default template
     * @param  int  $id id to assign to default
     * @return void
     */
    public function changeDefault($id)
    {
        $query = $this->db->getQuery(true)
            ->update('#__branding')
            ->set('assigned=0')
            ->where('id <> '.(int) $id);

        // Clear Previous
        $this->db->setQuery($query)->execute();

        // Set Default
        $query->clear()
            ->update('#__branding')
            ->set('assigned=0')
            ->where('id = '.(int) $id);

        $this->db->setQuery($query)->execute();
    }

}
