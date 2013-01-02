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

class CobaltModelImport extends JModelBase
{
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
     * Import a CSV File
     * @param  [String] $data
     * @param  [String] $model [ Model to import ]
     * @return [Boolean] $success
     */
    function importCSVData($data,$model,$returnIds=FALSE){

        if ( $returnIds ){
            $ids = array();
        }else{
            $success = false;
        }

        if ( count($data) > 0 && isset($model) ){
            $modelName = "CobaltModel".ucwords($model);
            $model = new $modelName();
            foreach ( $data as $import ){
                $id = $model->store($import);
                if ( is_array($id) ){
                    $id = $id['id'];
                }else if ( is_object($id) ){
                    $id = $id->id;
                }
                if ( $returnIds ) {
                    $ids[] = $id;
                }else{
                    $success = $id != FALSE || $id != 0 ? TRUE : FALSE;
                }
            }
        }

        if ( $returnIds ){
            return $ids;
        }else{
            return $success;
        }
    }

    /**
     * Read a CSV File
     * @param  [String] $file
     * @return [Mixed]  $data
     */
    function readCSVFile($file,$import_type=null,$generateHtml=TRUE){
        $app = JFactory::getApplication();
        ini_set("auto_detect_line_endings", "1");
        $data = array();
        $line = 1;
        $headers = array();
        $i = -2;
        $db =& JFactory::getDBO();
        $import_type = $import_type ? $import_type : $app->input->get('import_type');
        $table = $db->getTableColumns("#__".$import_type);
        $special_headers = array('company_id','company_name','stage_name','source_name','status_name','primary_contact_name','assignee_name','type');

        if (($handle = fopen($file, "r")) !== FALSE) {

            while (($read = fgetcsv($handle, 1000, ",")) !== FALSE) {

                $i++;
                $num = count($read);

                if ( $line == 1 ){

                    $headers = $read;
                    $data['headers'] = $headers;

                }else{

                    $line_data = array();

                    for ($c=0; $c < $num; $c++) {

                        $header_name = array_key_exists($c,$headers) ? $headers[$c] : FALSE;

                        if ( $header_name ){

                            if ( in_array($header_name,$special_headers) ){

                                $read[$c] = utf8_encode($read[$c]);

                                switch($header_name){

                                    case "company_id":
                                        $model = new CobaltModelCompany();
                                        $new_header = "company_id";
                                        $company_name = $model->getCompanyName($read[$c]);
                                        $name = "name=\"import_id[".$i."][".$new_header."]\"";
                                        if ( $company_name != "" ){
                                            $name = $company_name;
                                        }else{
                                            $name = "";
                                        }
                                        $special_data = array('label'=>$read[$c],'value'=>$name);
                                    break;

                                    case "company_name":

                                        $model = new CobaltModelCompany();
                                        $new_header = "company_id";
                                        $company_id = $model->getCompanyList($read[$c]);
                                        $name = "name=\"import_id[".$i."][".$new_header."]\"";
                                        if ( count($company_id) > 0 ){
                                            $name = $company_id[0]['name'];
                                        }else{
                                            $name = $read[$c];
                                        }
                                        $special_data = array('label'=>$read[$c],'value'=>$name);
                                    break;

                                    case "stage_name":

                                        $new_header = "stage_id";
                                        $stage_id = CobaltHelperDeal::getStages($read[$c]);
                                        $name = "name=\"import_id[".$i."][".$new_header."]\"";
                                        if ( count($stage_id) ){
                                            $keys = array_keys($stage_id);
                                            $stage_id = $keys[0];
                                        }
                                        if ( $generateHtml ){
                                            $special_data = array('dropdown' => CobaltHelperDropdown::generateDropdown('stage',$stage_id,$name));
                                        }else{
                                            $special_data = $stage_id;
                                        }

                                    break;

                                    case "source_name":

                                        $new_header = "source_id";
                                        $source_id = CobaltHelperDeal::getSources($read[$c]);
                                        $name = "name=\"import_id[".$i."][".$new_header."]\"";
                                        if ( count($source_id) ){
                                            $keys = array_keys($source_id);
                                            $source_id = $keys[0];
                                        }
                                        if ( $generateHtml ){
                                            $special_data = array('dropdown' => CobaltHelperDropdown::generateDropdown('source',$source_id,$name));
                                        }else{
                                            $special_data = $source_id;
                                        }

                                    break;

                                    case "status_name":

                                        $new_header = "status_id";
                                        $status_id = CobaltHelperDeal::getStatuses($read[$c]);
                                        $name = "name=\"import_id[".$i."][".$new_header."]\"";
                                        if ( count($status_id) ){
                                            $keys = array_keys($status_id);
                                            $status_id = $keys[0];
                                        }
                                        if ( $generateHtml ){
                                            $special_data = array('dropdown'=>CobaltHelperDropdown::generateDropdown('deal_status',$status_id,$name));
                                        }else{
                                            $special_data = $source_id;
                                        }

                                    break;

                                    case "primary_contact_name":

                                        $new_header = "primary_contact_id";
                                        $model = new CobaltModelPeople();
                                        $contact = $model->searchForContact($read[$c]);
                                        if ( $contact ){
                                            $special_data = array('label'=>$contact[0]->label,'value'=>$contact[0]->value);
                                        }else{
                                            $special_data = array();
                                        }

                                    break;

                                    case "assignee_name":

                                        $new_header = "assignee_id";
                                        $model = new CobaltModelPeople();
                                        $contact = $model->searchForContact($read[$c]);

                                    break;

                                    case "type":

                                        $new_header = "type";
                                        if ( $generateHtml ){
                                            $special_data = array('dropdown' => CobaltHelperDropdown::getContactTypes($read[$c]));
                                        }else{
                                            $special_data = CobaltHelperDropdown::getContactTypes($read[$c]);
                                        }

                                    break;
                                }

                                $line_data[$new_header] = $special_data;

                            }else{

                                if ( array_key_exists($header_name,$table) ){

                                    $line_data[$header_name] = utf8_encode($read[$c]);

                                }
                            }

                        }

                    }

                    if ( count($line_data) > 0 ){

                        $data[] = $line_data;
                    }

                }

                $line++;

            }

            fclose($handle);

        }

        return $data;

    }



}