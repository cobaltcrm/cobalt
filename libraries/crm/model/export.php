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

class CobaltModelExport extends JModelBase
{

	function __construct(){			
		parent::__construct();
	}

	/**
	 * Dynamically download CSV files based on type requested
	 * @return [type] [description]
	 */
	public function getCsv(){

		$app = JFactory::getApplication();

		//Determine request type
		$download_type = $app->input->get('list_type');

		//Generate CSV data based on request type
		$data = $this->getCsvData($download_type);

		//return csv file
		return($this->generateCsv($data['header'],$data['rows']));

	}


	/**
	 * Get CSV data
	 * @param  [type] $data_type [description]
	 * @return [type]            [description]
	 */
	public function getCsvData($data_type){

		$app = JFactory::getApplication();

		$data = array();

		$export_ids = $app->input->get('ids');

		switch($data_type){
			case "deals":
				$model = new CobaltModelDeal();
				$data = $model->getDeals($export_ids);
			break;
			case "companies":
				$model = new CobaltModelCompany();
				$data = $model->getCompanies($export_ids);
			break;
			case "people":
				$model = new CobaltModelPeople();
				$data = $model->getPeople($export_ids);
			break;
			case "sales_pipeline":
	            $model = new CobaltModelDeal();
    		    $data = $model->getReportDeals($export_ids);
			break;
			case "source_report":
				$model = new CobaltModelDeal();
        		$data = $model->getDeals($export_ids);
			break;
			case "roi_report":
				$model = new CobaltModelSource();
        		$data = $model->getRoiSources($export_ids);
			break;
			case "notes":
				$model = new CobaltModelNote();
        		$data = $model->getNotes(NULL,NULL,FALSE);
			break;
			case "custom_report":
        		$model = new CobaltModelReport();
        		$data = $model->getCustomReportData($app->input->get('report_id'));
			break;

		}	

		if ( count($data) ){
			$header = array_keys($data[0]);
		}

		return array('header'=>$header,'rows'=>$data);

	}

	/**
	 * Generate CSV
	 * @param  [type] $header [description]
	 * @param  [type] $data   [description]
	 * @return [type]         [description]
	 */
	public function generateCsv($header,$data){

		$str = "";

		if ( count($header) ){
			$str .= implode(',',$header)."\r\n";
		}

		if ( count($data) ){
			foreach ( $data as $row ){
				$str .= implode(',',$row)."\r\n";
			}
		}

		return $str;

	}

	/**
	 * Generate vcards for people
	 * @return [type] [description]
	 */
	public function getVcard(){

		$app = JFactory::getApplication();

		$person_id = $app->input->get('person_id');

		$model = new CobaltModelPeople();

		$person = $model->getPerson($person_id);
		$person = $person[0];

		$str = "";

		$str .= "BEGIN:VCARD\r\n";
		$str .= "VERSION:4.0\r\n";
		$str .= "N:".$person['last_name'].";".$person['first_name'].";;;\r\n";
		$str .= "FN: ".$person['first_name']." ".$person['last_name']."\r\n";
		$str .= "ORG:".$person['company_name']."\r\n";
		$str .= "TITLE:".$person['position']."\r\n";
		$str .= 'TEL;TYPE="work,voice";VALUE=uri:tel:+'.$person['phone']."\r\n";
		$str .= 'TEL;TYPE="mobile,voice";VALUE=uri:tel:+'.$person['mobile_phone']."\r\n";
		$str .= 'ADR;TYPE=work:;;'.$person['work_address_1']." ".$person['work_address_2'].';'.$person['work_city'].';'.$person['work_state'].';'.$person['work_zip'].';'.$person['work_country']."\r\n";
		$str .= "EMAIL:".$person['email']."\r\n";
		$str .= "END:VCARD\r\n";

		return $str;

	}

}