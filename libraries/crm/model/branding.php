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

class CobaltModelBranding extends JModelBase
{
    /**
     *
     *
     * @access  public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    public function store()
    {
        //Load Tables
        $app = JFactory::getApplication();
        $row = JTable::getInstance('branding','Table');
        $data = $app->input->getRequest( 'post' );

        //date generation
        $date = CobaltHelperDate::formatDBDate(date('Y-m-d H:i:s'));
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
                echo CRMText::_( 'INVALID EXTENSION' );

                    return;
            }

            //data generation
            $hashFilename = md5($fileName.$date).".".$uploadedFileExtension;

            //lose any special characters in the filename
            $fileName = preg_replace("[^A-Za-z0-9.]", "-", $fileName);

            //always use constants when making file paths, to avoid the possibilty of remote file inclusion
            $uploadPath = JPATH_SITE.'/libraries/crm/media/logos/'.$hashFilename;

            if (!JFile::upload($fileTemp, $uploadPath)) {
                echo CRMText::_( 'ERROR MOVING FILE' );

                return;
            }

            $fileSize = filesize($uploadPath);

            $this->updateSiteLogo($hashFilename);
            unset($data['site_logo']);

        }

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

    public function updateSiteLogo($logo)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update("#__branding")->set("site_logo=".$db->Quote($logo));
        $db->setQuery($query);
        $db->query();
    }

    /**
     * Get list of themes
     * @param  int   $id specific search id
     * @return mixed $results results
     */
    public function getThemes($id=null)
    {
        //database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("b.*");
        $query->from("#__branding AS b");

        //return results
        $db->setQuery($query);

        return $db->loadAssocList();

    }

    /**
     * Get list of themes
     * @param  int   $id specific search id
     * @return mixed $results results
     */
    public function getDefaultTheme()
    {
        //database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //query
        $query->select("b.*");
        $query->from("#__branding AS b");
        $query->where("assigned=1");

        //return results
        $db->setQuery($query);

        return $db->loadAssocList();

    }

    /**
     * Change the default template
     * @param  int  $id id to assign to default
     * @return void
     */
    public function changeDefault($id)
    {
        //database
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        //unassign default
        $queryString = "UPDATE #__branding SET assigned=0 WHERE id <> $id";
        $db->setQuery($queryString);
        $db->query();

        //assign default
        $queryString = "UPDATE #__branding SET assigned=1 WHERE id=$id";
        $db->setQuery($queryString);
        $db->query();

    }

}
