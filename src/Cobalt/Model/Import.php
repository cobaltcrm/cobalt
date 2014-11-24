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

use Cobalt\Helper\DealHelper;
use Cobalt\Helper\DropdownHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Import extends DefaultModel
{
    /**
     * Import a CSV File
     * @param  [String]  $data
     * @param  [String]  $model [ Model to import ]
     * @return [Boolean] $success
     */
    public function importCSVData($data, $model)
    {
        $success = false;
        if ( count($data) > 0 && isset($model) ) {
            $modelName = "Cobalt\\Model\\".ucwords($model);
            $model = new $modelName();
            foreach ($data as $import) {
                $model->store($import);
            }
            $success = true;
        }

        return $success;
    }

    /**
     * Read a CSV File
     * @param  [String] $file
     * @return [Mixed]  $data
     */
    public function readCSVFile($file, $table = null)
    {
        ini_set("auto_detect_line_endings", "1");
        $data = array();
        $line = 1;
        $headers = array();
        $i = -2;
        $db = $this->getDb();
        $table = $db->getTableColumns("#__" . $this->app->input->get('import_type', $table));
        $special_headers = array('company_id','company_name','stage_name','source_name','status_name','primary_contact_name','assignee_name','type');

        if (($handle = fopen($file, "r")) !== false)
        {
            while (($read = fgetcsv($handle, 1000, ",")) !== false)
            {
                $i++;
                $num = count($read);

                if ($line == 1)
                {
                    $headers = $read;
                    $data['headers'] = $headers;
                }
                else
                {
                    $line_data = array();

                    for ($c = 0; $c < $num; $c++)
                    {
                        $header_name = array_key_exists($c, $headers) ? $headers[$c] : false;

                        if ($header_name)
                        {
                            if (in_array($header_name, $special_headers))
                            {
                                $read[$c] = utf8_encode($read[$c]);

                                switch ($header_name)
                                {
                                    case "company_id":
                                        $model = new Company;
                                        $new_header = "company_id";
                                        $company_name = $model->getCompanyName($read[$c]);
                                        $name = "name=\"import_id[".$i."][".$new_header."]\"";

                                        if ($company_name != "")
                                        {
                                            $name = $company_name;
                                        }
                                        else
                                        {
                                            $name = "";
                                        }

                                        $special_data = array('label'=>$read[$c],'value'=>$name);
                                    break;

                                    case "company_name":

                                        $model = new Company;
                                        $new_header = "company_id";
                                        $company_id = $model->getCompanyList($read[$c]);
                                        $name = "name=\"import_id[".$i."][".$new_header."]\"";

                                        if (count($company_id) > 0)
                                        {
                                            $name = $company_id[0]['name'];
                                        }
                                        else
                                        {
                                            $name = $read[$c];
                                        }

                                        $special_data = array('label'=>$read[$c],'value'=>utf8_encode($name));
                                    break;

                                    case "stage_name":

                                        $new_header = "stage_id";
                                        $stage_id = DealHelper::getStages($read[$c]);
                                        $name = "name=\"import_id[".$i."][".$new_header."]\"";

                                        if (count($stage_id))
                                        {
                                            $keys = array_keys($stage_id);
                                            $stage_id = $keys[0];
                                        }

                                        $special_data = array('dropdown' => DropdownHelper::generateDropdown('stage',$stage_id,$name));

                                    break;

                                    case "source_name":

                                        $new_header = "source_id";
                                        $source_id = DealHelper::getSources($read[$c]);
                                        $name = "name=\"import_id[".$i."][".$new_header."]\"";

                                        if (count($source_id))
                                        {
                                            $keys = array_keys($source_id);
                                            $source_id = $keys[0];
                                        }

                                        $special_data = array('dropdown' => DropdownHelper::generateDropdown('source',$source_id,$name));

                                    break;

                                    case "status_name":

                                        $new_header = "status_id";
                                        $status_id = DealHelper::getStatuses($read[$c]);
                                        $name = "name=\"import_id[".$i."][".$new_header."]\"";

                                        if (count($status_id))
                                        {
                                            $keys = array_keys($status_id);
                                            $status_id = $keys[0];
                                        }

                                        $special_data = array('dropdown'=>DropdownHelper::generateDropdown('deal_status',$status_id,$name));

                                    break;

                                    case "primary_contact_name":

                                        $new_header = "primary_contact_id";
                                        $model = new People;
                                        $contact = $model->searchForContact($read[$c]);

                                        if ($contact)
                                        {
                                            $special_data = array('label'=>$contact[0]->label,'value'=>$contact[0]->value);
                                        }
                                        else
                                        {
                                            $special_data = array();
                                        }

                                    break;

                                    case "assignee_name":

                                        $new_header = "assignee_id";
                                        $model = new People;
                                        $contact = $model->searchForContact($read[$c]);

                                    break;

                                    case "type":

                                        $new_header = "type";
                                        $special_data = array('dropdown' => ucwords(DropdownHelper::getContactTypes($read[$c])));

                                    break;
                                }

                                $line_data[$new_header] = $special_data;
                            }
                            else
                            {
                                if (array_key_exists($header_name,$table))
                                {
                                    $line_data[$header_name] = utf8_encode($read[$c]);
                                }
                            }
                        }
                    }

                    if (count($line_data) > 0)
                    {
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
