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

class CobaltModelCompany extends CobaltModelDefault
{
		
        var $_view      = null;
        var $_layout    = null;
        var $_user      = null;
        var $_team      = null;
        var $_id        = null;
        var $_type      = null;
        var $published  = 1;
       
        /**
         * Constructor
         */
        function __construct() {

            parent::__construct();
            $app = JFactory::getApplication();
            $this->_view = $app->input->get('view');
            $this->_layout = str_replace('_filter','',$app->input->get('layout'));
        }

		/**
		 * Method to store a record
		 *
		 * @return    boolean    True on success
		 */
		function store($data=null)
		{
            $app = JFactory::getApplication();
            $db = JFactory::getDBO();

			//Load Tables
			$row =& JTable::getInstance('company','Table');
            $oldRow =& JTable::getInstance('company','Table');

            if ( $data == null ){
		      $data = $app->input->getRequest('post');
            }
			
			//date generation
			$date = CobaltHelperDate::formatDBDate(date('Y-m-d H:i:s'));
			
			if ( !array_key_exists('id',$data) || ( array_key_exists('id',$data) && $data['id'] <= 0 ) ){
				$data['created'] = $date;
                $status = 'created';
			} else {
                $row->load($data['id']);
                $oldRow->load($data['id']);
                $status = 'updated';
            }
			
			$data['modified'] = $date;
            $data['owner_id'] = CobaltHelperUsers::getUserId();

            //generate custom field string
            $customArray = array();
            foreach( $data as $name => $value ){
                if( strstr($name,'custom_') && !strstr($name,'_input') && !strstr($name,"_hidden") ){
                    $id = str_replace('custom_','',$name);
                    $customArray[] = array('custom_field_id'=>$id,'custom_field_value'=>$value);
                    unset($data[$name]);
                }
            }
			
		    // Bind the form fields to the table
		    if (!$row->bind($data)) {
		        $this->setError($db->getErrorMsg());
		        return false;
		    }

            $dispatcher = JEventDispatcher::getInstance();
            $dispatcher->trigger('onBeforeCompanySave', array(&$row));      


		    // Make sure the record is valid
		    if (!$row->check()) {
		        $this->setError($db->getErrorMsg());
		        return false;
		    }
	 
		    // Store the web link table to the database
		    if (!$row->store()) {
		        $this->setError($db->getErrorMsg());
		        return false;
		    }

            $id = array_key_exists('id',$data) && $data['id'] > 0 ? $data['id'] : $db->insertId();
		 
            CobaltHelperActivity::saveActivity($oldRow, $row,'company', $status);

            //if we receive no custom post data do not modify the custom fields
            if ( count($customArray) > 0 ){
                CobaltHelperCobalt::storeCustomCf($id,$customArray,'company');
            }

            $dispatcher = JEventDispatcher::getInstance();
            $dispatcher->trigger('onAfterCompanySave', array(&$row));

		    return $row;
		}

        /**
         * Build our db query object
         */
        function _buildQuery(){

            $app = JFactory::getApplication();

            /** Large SQL Selections **/
            $db =& JFactory::getDBO();
            $query = $db->getQuery(true);
            $db->setQuery("SET SQL_BIG_SELECTS=1");
            $db->query();
            
            $user = $this->_user;
            $team = $this->_team;
            $id = $this->_id;
            $type = $this->_type;
            $view = $app->input->get('view');

            if ( !$id ){

                $session = JFactory::getSession();

                //determine whether we are searching for a team or user
                if ( $user ){
                    $session->set('company_team_filter',null);
                }
                if ( $team ){
                    $session->set('company_user_filter',null);
                }
                
                //set user session data
                if ( $type != null ) {
                    $session->set('company_type_filter',$type);
                } else {
                    $sess_type = $session->get('company_type_filter');
                    $type = $sess_type;
                }
                if ( $user != null ) {
                    $session->set('company_user_filter',$user);
                } else {
                    $sess_user = $session->get('company_user_filter');
                    $user = $sess_user;
                }
                if ( $team != null ){
                    $session->set('company_team_filter',$team);
                }else{
                    $sess_team = $session->get('company_team_filter');
                    $team = $sess_team;
                }

            }
            
            $db =& JFactory::getDBO();
            
            //generate query for base companies
            $query = $db->getQuery(true);
            $export = $app->input->get('export');

            if ( $export ){

                $select_string  = 'c.name,c.description,c.address_1,c.address_2,c.address_city,';
                $select_string .= 'c.address_state,c.address_zip,c.address_country,c.website,c.created,c.modified';

                $query->select($select_string);
                $query->from("#__companies as c");
                $query->leftJoin("#__users AS u on u.id = c.owner_id");
            }else{
                $query->select('c.*');
                $query->from("#__companies as c");
                $query->leftJoin("#__users AS u on u.id = c.owner_id");
            }

            if ( !$id ){

                //get current date
                $date = CobaltHelperDate::formatDBDate(date('Y-m-d 00:00:00'));
                
                //filter for type
                if ( $type != null && $type != "all" ){
                    
                    //filter for companies with tasks due today
                    if ( $type == 'today' ){
                        $query->leftJoin("#__events_cf as event_company_cf on event_company_cf.association_id = c.id AND event_company_cf.association_type='company'");
                        $query->join('INNER',"#__events as event on event.id = event_company_cf.event_id");
                        $query->where("event.due_date='$date'");
                        $query->where("event.published>0");
                    }
                    
                    //filter for companies and deals//tasks due tomorrow
                    if ( $type == "tomorrow" ){
                        $tomorrow = CobaltHelperDate::formatDBDate(date('Y-m-d 00:00:00',time() + (1*24*60*60)));
                        $query->leftJoin("#__events_cf as event_company_cf on event_company_cf.association_id = c.id AND event_company_cf.association_type='company'");
                        $query->join('INNER',"#__events as event on event.id = event_company_cf.event_id");
                        $query->where("event.due_date='$tomorrow'");
                        $query->where("event.published>0");
                    }
                    
                    //filter for companies updated in the last 30 days
                    if ( $type == "updated_thirty" ){
                        $last_thirty_days = CobaltHelperDate::formatDBDate(date('Y-m-d 00:00:00',time() - (30*24*60*60)));
                        $query->where("c.modified >'$last_thirty_days'");
                    }
                    
                     //filter for past companies// last contacted 30 days ago or longer
                    if ( $type == "past" ){
                        $last_thirty_days = CobaltHelperDate::formatDBDate(date('Y-m-d 00:00:00',time() - (30*24*60*60)));
                        $query->where("c.modified <'$last_thirty_days'");
                    }
                    
                    //filter for recent companies
                    if ( $type == "recent" ) {
                        $last_thirty_days = CobaltHelperDate::formatDBDate(date('Y-m-d 00:00:00',time() - (30*24*60*60)));
                        $query->where("c.modified >'$last_thirty_days'");
                    }
                    
                     $query->group("c.id");
                    
                }

                /** company name filter **/
                $company_name = $this->getState('Company.'.$view.'_name');
                if ( $company_name != null ){
                    $query->where("( c.name LIKE '%".$company_name."%' )");
                }               

            }
            
            //search for specific companies
            if ( $id != null ){
                if ( is_array($id) ){
                    $query->where("c.id IN (".implode(',',$id).")");
                }else{
                    $query->where("c.id=$id");
                }
            }

            //filter based on member access roles
            $user_id = CobaltHelperUsers::getUserId();
            $member_role = CobaltHelperUsers::getRole();
            $team_id = CobaltHelperUsers::getTeamId();

            //filter based on specified user
            if ( $user AND $user != 'all' ){
                $query->where("c.owner_id = ".$user);
            }
            
            //filter based on team
            if ( $team ){
                $team_members = CobaltHelperUsers::getTeamUsers($team,TRUE);
                $query->where("c.owner_id IN (".implode(',',$team_members).")");
            }
         
            //set user state requests
            $query->order($this->getState('Company.filter_order') . ' ' . $this->getState('Company.filter_order_Dir'));

            $query->where("c.published=".$this->published);
            
            //return query object
            return $query;
                        
        }
		
		/*
		 * Method to access companies
		 * 
		 * @return mixed	 
		 */
		function getCompanies($id=null,$type=null,$user=null,$team=null){
			
            $this->_id = $id;
            $this->_type = $type;
            $this->_user = $user;
            $this->_team = $team;
            
            //get session data
            $session = JFactory::getSession();
            $db =& JFactory::getDBO();
            
            //get query string
            $query = $this->_buildQuery();
            
            /** ------------------------------------------
             * Set query limits and load results
             */
            
            if(!CobaltHelperTemplate::isMobile()) {
                $limit = $this->getState($this->_view.'_limit');
                $limitStart = $this->getState($this->_view.'_limitstart');
                if (  !$this->_id && $limit != 0 ){
                    if ( $limitStart >= $this->getTotal() ){
                        $limitStart = 0;
                        $limit = 10;
                        $limitStart = ($limit != 0) ? (floor($limitStart / $limit) * $limit) : 0;
                        $this->state->set($this->_view.'_limit', $limit);
                        $this->state->set($this->_view.'_limitstart', $limitStart);
                    }
                    $query .= " LIMIT ".($limit)." OFFSET ".($limitStart);
                }
            }
            
            //run query and grab results of companies
            $db->setQuery($query);
            $companies = $db->loadAssocList();
            
            //generate query to join people
            if ( count($companies) ){
                $app = JFactory::getApplication();
                $export = $app->input->get('export');

                if ( !$export ){

                    foreach ( $companies as $key => $company ) {

                        /* Tweets */
                        if($company['twitter_user']!="" && $company['twitter_user']!=" ") {
                            $companies[$key]['tweets'] = CobaltHelperTweets::getTweets($company['twitter_user']);
                        }

                        //generate people query
                        $peopleModel = new CobaltModelPeople();
                        $peopleModel->set('company_id',$company['id']);
                        $companies[$key]['people'] = $peopleModel->getContacts();
                        
                        //generate deal query
                        $dealModel = new CobaltModelDeal();
                        $dealModel->set('company_id',$company['id']);
                        $deals = $dealModel->getDeals();
                        $companies[$key]['pipeline'] = 0;
                        $companies[$key]['won_deals'] = 0;
                        for($i=0;$i<count($deals);$i++) {
                            $deal = $deals[$i];
                            $companies[$key]['pipeline'] += $deal['amount'];
                            if($deal['percent']==100) {
                                $companies[$key]['won_deals'] += $deal['amount'];                            
                            }
                        }   
                        $companies[$key]['deals'] = $deals;

                        //Get Associated Notes
                        $notesModel = new CobaltModelNote();
                        $companies[$key]['notes'] = $notesModel->getNotes($company['id'],'company');
                    
                        // Get Associated Documents
                        $documentModel = new CobaltModelDocument();
                        $documentModel->set('company_id',$company['id']);
                        $companies[$key]['documents']  = $documentModel->getDocuments();
                        
                        $companies[$key]['address_formatted'] = ( strlen($company['address_1']) > 0 ) ? $company['address_1'].
                                                                 $company['address_2'].", ".
                                                                 $company['address_city'].' '.$company['address_state'].', '.$company['address_zip'].
                                                                 ' '.$company['address_country'] : "";
                    }

                }
                
            } 

            $dispatcher = JEventDispatcher::getInstance();
            $dispatcher->trigger('onCompanyLoad',array(&$companies));      

            //return results
            return $companies;            
		}

        function getCompany($id=null){
            $app = JFactory::getApplication();
            $id = $id ? $id : $app->input->get('id');

            if ( $id > 0 ){
                $company = $this->getCompanies($id);
                if ( is_array($company) && count($company) >= 1 ){
                    return $company[0];
                }else{
                    return (array)JTable::getInstance('Company','Table');
                }
            }else{
                return (array)JTable::getInstance('Company','Table');
            }
        }

        /**
         * method to get list of companies
         */
        
        function getCompanyList($company_name=null){
            
            //db object
            $db =& JFactory::getDBO();
            //gen query
            $query = $db->getQuery(true);
            $query->select("name,id FROM #__companies");

            if ( $company_name ){
                $company_name = ucwords($company_name);
                $query->where("LOWER(name) LIKE '%".$company_name."%'");
            }

            $query->where("published=".$this->published);

            //set query
            $db->setQuery($query);
            //load list
            $row = $db->loadAssocList();

            //return results
            return $row;
            
        }

        function getCompanyNames($json=FALSE){
            $names = $this->getCompanyList();
            $return = array();
            if ( count($names) > 0 ){
                foreach ( $names as $key => $name ){
                    $return[] = array('label'=>$name['name'],'value'=>$name['id']);
                }   
            }
            return $json ? json_encode($return) : $return;
        }

        /**
         * Checks for existing company by name
         * @param  [var] $name company name to check
         * @return [int]       ID of existing company
         */
        function checkCompanyName($name) 
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('c.id');
            $query->from('#__companies AS c');
            $query->where('LOWER(c.name) = "'.strtolower($name).'"');
            $db->setQuery($query);
            $existingCompany = $db->loadResult();

            return $existingCompany;
        }

        function getCompanyName($idOrName){
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('c.name');
            $query->from('#__companies AS c');
            $query->where("c.id='".$idOrName."' OR c.name='".$idOrName."'");
            $db->setQuery($query);
            $company_name = $db->loadResult();

            return $company_name;
        }

        /**
         * Populate user state requests
         */
        function populateState(){
            //get states
            $app = JFactory::getApplication();
            
            //determine view so we set correct states
            $view = $app->input->get('view');
            $layout = str_replace("_filter","", $app->input->get('layout'));
            
            // Get pagination request variables
            $limit = $app->getUserStateFromRequest($view.'_limit','limit',10);
            $limitstart = $app->getUserStateFromRequest($view.'_limitstart','limitstart',0);
            
            // In case limit has been changed, adjust it
            $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
            
            $this->state->set($view.'_limit', $limit);
            $this->state->set($view.'_limitstart', $limitstart);
            
            //set default filter states for reports
            $filter_order           = $app->getUserStateFromRequest('Company.filter_order','filter_order','c.name');
            $filter_order_Dir       = $app->getUserStateFromRequest('Company.filter_order_Dir','filter_order_Dir','asc');
            $company_filter         = $app->getUserStateFromRequest('Company.'.$view.'_name','company_name',null);
            
            //set states for reports
            $this->state->set('Company.filter_order',$filter_order);
            $this->state->set('Company.filter_order_Dir',$filter_order_Dir);
            $this->state->set('Company.'.$view.'_name',$company_filter);

        }
		
}