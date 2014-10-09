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

use Cobalt\Helper\TextHelper;
use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\FileHelper;
use Cobalt\Helper\DateHelper;
use Joomla\Filesystem\File;
use Joomla\Registry\Registry;
use JUri;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Documents extends DefaultModel
{
    public $_view = "admindocuments";

    public function store($data = null)
    {
        //Load Tables
        $row = $this->getTable('Documents');

        if ($data == null)
        {
            $data = $this->app->input->getRequest( 'post' );
        }

        //date generation
        $date = date('Y-m-d H:i:s');

        if (!array_key_exists('id', $data))
        {
            $data['created'] = $date;
        }

        $data['modified'] = $date;
        $data['shared'] = 1;

        // Bind the form fields to the table
	    try
	    {
		    $row->save($data);
	    }
	    catch (\Exception $exception)
	    {
		    $this->app->enqueueMessage($exception->getMessage(), 'error');

		    return false;
	    }

        return true;
    }

    public function _buildQuery()
    {
        $query = $this->db->getQuery(true);
        $query->select("d.*,".
                       "c.name as company_name,".
                       "deal.name as deal_name,".
                       "p.first_name as person_first_name, p.last_name as person_last_name,".
                       "CONCAT(u.first_name,' ',u.last_name) AS owner_name");
        $query->from("#__documents AS d");
        $query->leftJoin("#__companies AS c ON d.association_type = 'company' AND d.association_id = c.id");
        $query->leftJoin("#__deals AS deal ON d.association_type = 'deal' AND d.association_id = deal.id");
        $query->leftJoin("#__people AS p ON d.association_type ='person' AND d.association_id = p.id");
        $query->leftJoin("#__users AS u ON u.id = d.owner_id");
        $query->where("d.shared = 1");

        return $query;

    }

    /**
     * Get list of stages
     * @param  int   $id specific search id
     * @return mixed $results results
     */
    public function getDocuments($id = null)
    {
        $query = $this->_buildQuery();

        //sort
        $query->order($this->getState('Documents.filter_order') . ' ' . $this->getState('Documents.filter_order_Dir'));

        if ($id)
        {
            $query->where("d.id = $id");
        }

        /** ------------------------------------------
         * Set query limits/ordering and load results
         */
        $limit = $this->getState($this->_view . '_limit');
        $limitStart = $this->getState($this->_view . '_limitstart');

        if ($limit != 0)
        {
            if ($limitStart >= $this->getTotal())
            {
                $limitStart = 0;
                $limit = 10;
                $limitStart = ($limit != 0) ? (floor($limitStart / $limit) * $limit) : 0;
                $this->state->set($this->_view . '_limit', $limit);
                $this->state->set($this->_view . '_limitstart', $limitStart);
            }
        }

        //return results
        $this->db->setQuery($query, $limitStart, $limit);
        $documents = $this->db->loadAssocList();

        return $documents;

    }

    public function populateState()
    {
        //get states
        $filter_order = $this->app->getUserStateFromRequest('Documents.filter_order', 'filter_order','d.filename');
        $filter_order_Dir = $this->app->getUserStateFromRequest('Documents.filter_order_Dir', 'filter_order_Dir','asc');

        //set states
        $state = new Registry;
        $state->set('Documents.filter_order', $filter_order);
        $state->set('Documents.filter_order_Dir', $filter_order_Dir);

        // Get pagination request variables
        $limit = $this->app->getUserStateFromRequest($this->_view . '_limit', 'limit', 10);
        $limitstart = $this->app->getUserStateFromRequest($this->_view . '_limitstart', 'limitstart', 0);

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $state->set($this->_view . '_limit', $limit);
        $state->set($this->_view . '_limitstart', $limitstart);

        $this->setState($state);
    }

    public function delete($id)
    {
	    return $this->getTable('Documents')->delete($id);
    }

    public function upload()
    {
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

        if ($fileSize > 2000000)
        {
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
        foreach ($validFileExts as $key => $value)
        {
            if (preg_match("/$value/i", $uploadedFileExtension))
            {
                $extOk = true;
            }
        }

        if ($extOk == false)
        {
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
        $hash = md5($fileName) . "." . $uploadedFileExtension;

        //always use constants when making file paths, to avoid the possibilty of remote file inclusion
        $uploadPath = JPATH_UPLOADS . '/' . $hash;

        if (!File::upload($fileTemp, $uploadPath))
        {
            $msg = TextHelper::_('COBALT_DOC_UPLOAD_FAIL');
            $this->app->redirect('index.php?view=admindocuments',$msg);
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
                'size'              =>  $fileSize / 1024,
                'created'           =>  $date,
                'shared'            =>  1,
                'is_image'          =>  is_array(getimagesize($uploadPath)) ? true : false
            );

            $model = new static;
            $session = $this->app->getSession();

            if ($model->store($data))
            {
               $msg = TextHelper::_('COM_CRMERY_DOC_UPLOAD_SUCCESS');
               $this->app->redirect('index.php?view=admindocuments&layout=upload_success&format=raw',$msg);
               $session->set("upload_success", true);
            }
            else
            {
               $msg = TextHelper::_('COM_CRMERY_DOC_UPLOAD_FAIL');
               $this->app->redirect('index.php?view=admindocuments&layout=upload_success&format=raw',$msg);
               $session->set("upload_success", false);
            }
        }
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

        if ($this->app->input->getString('loc','documents') == 'documents')
        {
            $columns[] = array('data' => 'association', 'ordering' => 'd.association_type');
        }

        $columns[] = array('data' => 'owner', 'ordering' => 'u.last_name');
        $columns[] = array('data' => 'size', 'ordering' => 'd.size');
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
                $template = '<input type="checkbox" class="export" name="ids[]" value="' . $item->id . '" />';
                break;
            case 'type':
                $file_path = sprintf('%s/images/%s.png', JPATH_MEDIA, $item->filetype);

                if (file_exists($file_path)) {
                    $file_src = sprintf('%simages/%s.png', $this->app->get('uri.media.full'), $item->filetype);
                    $template = '<img src="' . $file_src . '" >';
                }
                else
                {
                    $file_src = sprintf('%simages/file.png', $this->app->get('uri.media.full'));
                    $template = '<img src="' . $file_src . '" >';
                }

                break;
            case 'name':
                $template = '<div class="dropdown"><span class="caret"></span><a id="'.$item->id.'" class="document_edit dropdown-toggle" data-toggle="dropdown" role="button" href="javascript:void(0);"> '.$item->name.'</a>';

                $template .= '<ul class="dropdown-menu" role="menu">';
                $template .= '<li><a href="'.RouteHelper::_('index.php?task=PreviewDocument&format=raw&tmpl=component&document='.$item->filename).'" target="_blank" class="document_preview" id="preview_'.$item->id.'"><i class="glyphicon glyphicon-eye-open"></i> '.TextHelper::_('COBALT_PREVIEW').'</a></li>';
                $template .= '<li><a href="'.RouteHelper::_('index.php?task=DownloadDocument&format=raw&tmpl=component&document='.$item->filename).'" target="_blank" class="document_download" id="download_'.$item->id.'"><i class="glyphicon glyphicon-download"></i> '.TextHelper::_('COBALT_DOWNLOAD').'</a></li>';

                if ($item->owner_id == UsersHelper::getLoggedInUser()->id)
                {
                    $template .= '<li><a href="#" class="document_delete" id="delete_' . $item->id . '"><i class="glyphicon glyphicon-remove"></i> ' . TextHelper::_('COBALT_DELETE') . '</a></li>';
                }

                $template .= '</ul></div>';
                break;
            case 'association':
                $association_type = $item->association_type;

                //assign association link
                switch ($association_type)
                {
                    case "deal":
                        $view = 'deals';
                        $item->association_name = $item->deal_name;
                        break;
                    case "person":
                        $view = "people";
                        $item->association_name = $item->person_first_name . " " . $item->person_last_name;
                        break;
                    case "company";
                        $view = "companies";
                        $item->association_name = $item->company_name;
                        break;
                }

                if (isset($item->association_name))
                {
                    $template = '<a href="'.RouteHelper::_('index.php?view='.$view.'&layout='.$association_type.'&id='.$item->association_id).'" >'.$item->association_name;
                }
                else
                {
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
