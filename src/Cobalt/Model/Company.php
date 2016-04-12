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

use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\DateHelper;
use Cobalt\Helper\CobaltHelper;
use Cobalt\Helper\ActivityHelper;
use Cobalt\Helper\TweetsHelper;
use Cobalt\Helper\UsersHelper;
use Cobalt\Helper\TemplateHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Company extends DefaultModel
{
    public $_view      = null;
    public $_layout    = null;
    public $_user      = null;
    public $_team      = null;
    public $_id        = null;
    public $_type      = null;
    public $published  = 1;

	/**
	 * Instantiate the model.
	 *
	 * @param   DatabaseDriver  $db     The database adapter.
	 * @param   Registry        $state  The model state.
	 *
	 * @since   1.0
	 */
	public function __construct(DatabaseDriver $db = null, Registry $state = null)
    {
        parent::__construct($db, $state);
        $this->_view = $this->app->input->get('view');
        $this->_layout = str_replace('_filter','',$this->app->input->get('layout'));
    }

    /**
     * Method to store a record
     *
     * @return boolean True on success
     */
    public function store($data = null)
    {
        //Load Tables
        $row    = $this->getTable('Company');
        $oldRow = $this->getTable('Company');

        if ($data == null)
        {
            $data = $this->app->input->post->getArray();
        }

        //date generation
        $date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));

        if ( !array_key_exists('id',$data) || ( array_key_exists('id',$data) && $data['id'] <= 0 ) ) {
            $data['created'] = $date;
            $status = 'created';
        } else {
            $row->load($data['id']);
            $oldRow->load($data['id']);
            $status = 'updated';
        }

        $data['modified'] = $date;
        $data['owner_id'] = UsersHelper::getUserId();

        //generate custom field string
        $customArray = array();
        foreach ($data as $name => $value) {
            if ( strstr($name,'custom_') && !strstr($name,'_input') && !strstr($name,"_hidden") ) {
                $id = str_replace('custom_','',$name);
                $customArray[] = array('custom_field_id'=>$id,'custom_field_value'=>$value);
                unset($data[$name]);
            }
        }

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

        $id = !empty($data['id']) ? $data['id'] : $this->db->insertId();

        ActivityHelper::saveActivity($oldRow, $row, 'company', $status);

        //if we receive no custom post data do not modify the custom fields
        if (count($customArray) > 0) {
            CobaltHelper::storeCustomCf($id,$customArray,'company');
        }

        //$this->app->triggerEvent('onAfterCompanySave', array(&$row));

        return $row->id;
    }

    /**
     * Build our db query object
     */
    public function _buildQuery()
    {
		if ($this->db->name=='mysqli')
		{
			$this->db->setQuery("SET SQL_BIG_SELECTS=1")->execute();
		}

        $user = $this->_user;
        $team = $this->_team;
        $id = $this->_id;
        $type = $this->_type;
        $view = $this->app->input->get('view');

        if (!$id) {

            $session = $this->app->getSession();

            //determine whether we are searching for a team or user
            if ($user) {
                $session->set('company_team_filter', null);
            }
            if ($team) {
                $session->set('company_user_filter', null);
            }

            //set user session data
            if ($type != null) {
                $session->set('company_type_filter',$type);
            } else {
                $sess_type = $session->get('company_type_filter');
                $type = $sess_type;
            }
            if ($user != null) {
                $session->set('company_user_filter',$user);
            } else {
                $sess_user = $session->get('company_user_filter');
                $user = $sess_user;
            }
            if ($team != null) {
                $session->set('company_team_filter',$team);
            } else {
                $sess_team = $session->get('company_team_filter');
                $team = $sess_team;
            }

        }

        //generate query for base companies
        $query = $this->db->getQuery(true);
        $export = $this->app->input->get('export');

        if ($export) {

            $select_string  = 'c.name,c.description,c.address_1,c.address_2,c.address_city,';
            $select_string .= 'c.address_state,c.address_zip,c.address_country,c.website,c.created,c.modified';

            $query
                ->select($select_string)
                ->from("#__companies as c")
                ->leftJoin("#__users AS u on u.id = c.owner_id");
        } else {
            $query
                ->select('c.*')
                ->from("#__companies as c")
                ->leftJoin("#__users AS u on u.id = c.owner_id");
        }

        if (!$id) {

            //get current date
            $date = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));

            $type = $this->getState('Company.item_filter', $type);

            //filter for type
            if ($type != null && $type != "all") {
				// Filter for get companies with published status of -1
				if($type == 'unpublished')
				{
					$query->where("c.published='-1'");
				}

                //filter for companies with tasks due today
                if ($type == 'today') {
                    $query->leftJoin("#__events_cf as event_company_cf on event_company_cf.association_id = c.id AND event_company_cf.association_type=" . $this->db->quote('company'));
                    $query->join('INNER',"#__events as event on event.id = event_company_cf.event_id");
                    $query->where("event.due_date=" . $this->db->quote($date));
                    $query->where("event.published>0");
                }

                //filter for companies and deals//tasks due tomorrow
                if ($type == "tomorrow") {
                    $tomorrow = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() + (1*24*60*60)));
                    $query->leftJoin("#__events_cf as event_company_cf on event_company_cf.association_id = c.id AND event_company_cf.association_type=" . $this->db->quote('company'));
                    $query->join('INNER',"#__events as event on event.id = event_company_cf.event_id");
                    $query->where("event.due_date=" . $this->db->quote($tomorrow));
                    $query->where("event.published>0");
                }

                //filter for companies updated in the last 30 days
                if ($type == "updated_thirty") {
                    $last_thirty_days = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() - (30*24*60*60)));
                    $query->where("c.modified >" . $this->db->quote($last_thirty_days));
                }

                 //filter for past companies// last contacted 30 days ago or longer
                if ($type == "past") {
                    $last_thirty_days = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() - (30*24*60*60)));
                    $query->where("c.modified <" . $this->db->quote($last_thirty_days));
                }

                //filter for recent companies
                if ($type == "recent") {
                    $last_thirty_days = DateHelper::formatDBDate(date('Y-m-d 00:00:00',time() - (30*24*60*60)));
                    $query->where("c.modified >" . $this->db->quote($last_thirty_days));
                }

                 $query->group("c.id");

            }
			else
			{
				$query->where("c.published=1");
			}

            /** company name filter **/
            $company_name = $this->getState()->get('Company.'.$view.'_name');
            if ($company_name != null) {
                $query->where("( c.name LIKE " . $this->db->quote('%'.$company_name.'%') . " )");
            }

        }

        //search for specific companies
        if ($id != null) {
            if ( is_array($id) ) {
                $query->where("c.id IN (".implode(',', $id).")");
            } else {
                $query->where("c.id=$id");
            }
        }

        //filter based on member access roles
        $user_id = UsersHelper::getUserId();
        $member_role = UsersHelper::getRole();
        $team_id = UsersHelper::getTeamId();

        //filter based on specified user
        if ($user && $user != 'all') {
            $query->where("c.owner_id = ".$user);
        }

        //filter based on team
        if ($team) {
            $team_members = UsersHelper::getTeamUsers($team, true);
            $query->where("c.owner_id IN (".implode(',', $team_members).")");
        }

        //set user state requests
        $query
            ->order($this->getState()->get('Company.filter_order').' '.$this->getState()->get('Company.filter_order_Dir'));

        return $query;
    }

    /*
     * Method to access companies
     *
     * @return mixed
     */
    public function getCompanies($id = null, $type = null, $user = null, $team = null)
    {
        $this->_id = $id;
        $this->_type = $type;
        $this->_user = $user;
        $this->_team = $team;

        //get query string
        $query = $this->_buildQuery();

        /** ------------------------------------------
         * Set query limits and load results
         */

        if (!TemplateHelper::isMobile())
        {
            $limit = $this->getState()->get($this->_view.'_limit');
            $limitStart = $this->getState()->get($this->_view.'_limitstart');

            if (!$this->_id && $limit != 0)
            {
                if ($limitStart >= $this->getTotal())
                {
                    $limitStart = 0;
                    $limit = 10;
                    $limitStart = ($limit != 0) ? (floor($limitStart / $limit) * $limit) : 0;
                    $this->getState()->set($this->_view.'_limit', $limit);
                    $this->getState()->set($this->_view.'_limitstart', $limitStart);
                }
            }
        }

        //run query and grab results of companies
        $companies = $this->db->setQuery($query, $limitStart, $limit)->loadObjectList();

        //generate query to join people
        if (count($companies))
        {
            $export = $this->app->input->get('export');

            if (!$export)
            {
                foreach ($companies as $key => $company)
                {
                    /* Tweets */
                    if ($company->twitter_user != "" && $company->twitter_user != " ")
                    {
                        $companies[$key]->tweets = TweetsHelper::getTweets($company->twitter_user);
                    }

                    //generate people query
                    $peopleModel = new People;
                    $peopleModel->set('company_id', $company->id);
                    $companies[$key]->people = $peopleModel->getContacts();

                    //generate deal query
                    $dealModel = new Deal;
                    $dealModel->set('company_id', $company->id);
                    $deals = $dealModel->getDeals();
                    $companies[$key]->pipeline = 0;
                    $companies[$key]->won_deals = 0;

                    for ($i = 0; $i < count($deals); $i++)
                    {
                        $deal = $deals[$i];
                        $companies[$key]->pipeline += $deal->amount;

                        if ($deal->percent == 100)
                        {
                            $companies[$key]->won_deals += $deal->amount;
                        }
                    }

                    $companies[$key]->deals = $deals;

                    //Get Associated Notes
                    $notesModel = new Note;
                    $companies[$key]->notes = $notesModel->getNotes($company->id, 'company');

                    // Get Associated Documents
                    $documentModel = new Document;
                    $documentModel->set('company_id', $company->id);
                    $companies[$key]->documents  = $documentModel->getDocuments();

                    $companies[$key]->address_formatted = ( strlen($company->address_1) > 0 ) ? $company->address_1.
                         $company->address_2.", ".
                         $company->address_city.' '.$company->address_state.', '.$company->address_zip.
                         ' '.$company->address_country : "";
                }

            }

        }

        //$this->app->triggerEvent('onCompanyLoad',array(&$companies));

        return $companies;
    }

    public function getCompany($id = null)
    {
        $id = $id ? $id : $this->app->input->getInt('id');

        if ($id > 0)
        {
            $company = $this->getCompanies($id);

            if ( is_array($company) && count($company) >= 1 )
            {
                return $company[0];
            }
            else
            {
                return $this->getTable('Company');
            }
        }
        else
        {
            return $this->getTable('Company');
        }
    }

    /**
     * method to get list of companies
     */
    public function getCompanyList($company_name = null)
    {
        //gen query
        $query = $this->db->getQuery(true)
            ->select("name,id FROM #__companies");

        if ($company_name) {
            $company_name = ucwords($company_name);
            $query->where('LOWER(name) LIKE ' . $this->db->quote('%' . $company_name . '%'));
        }

        $query->where("published=".$this->published);

        return $this->db->setQuery($query)->loadAssocList();
    }

    public function getCompanyNames($json=FALSE)
    {
        $names = $this->getCompanyList();
        $return = array();
        if ( count($names) > 0 ) {
            foreach ($names as $key => $name) {
                $return[] = array('label'=>$name['name'],'value'=>$name['id']);
            }
        }

        return $json ? json_encode($return) : $return;
    }

    /**
     * Checks for existing company by name
     * @param  [var] $name company name to check
     * @return [int] ID of existing company
     */
    public function checkCompanyName($name)
    {
        $query = $this->db->getQuery(true)
            ->select('c.id')
            ->from('#__companies AS c')
            ->where('LOWER(c.name) = ' . $this->db->quote(strtolower($name)) );

        return $this->db->setQuery($query)->loadResult();
    }

    public function getCompanyName($idOrName)
    {
        $query = $this->db->getQuery(true)
            ->select('c.name')
            ->from('#__companies AS c')
            ->where('c.id=' . $this->db->quote($idOrName) . ' OR c.name=' . $this->db->quote($idOrName));

        return $this->db->setQuery($query)->loadResult();
    }

    /**
     * Populate user state requests
     */
    public function populateState()
    {
        //determine view so we set correct states
        $view = $this->app->input->get('view');

        // Get pagination request variables
        $limit = $this->app->getUserStateFromRequest($view.'_limit','limit',10);
        $limitstart = $this->app->getUserStateFromRequest($view.'_limitstart','limitstart',0);

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

	    $state = new Registry;

        $state->set($view.'_limit', $limit);
        $state->set($view.'_limitstart', $limitstart);

        //set default filter states for reports
        $filter_order           = $this->app->getUserStateFromRequest('Company.filter_order','filter_order','c.name');
        $filter_order_Dir       = $this->app->getUserStateFromRequest('Company.filter_order_Dir','filter_order_Dir','asc');
        $company_filter         = $this->app->getUserStateFromRequest('Company.'.$view.'_name','company_name',null);

        //set states for reports
        $state->set('Company.filter_order', $filter_order);
        $state->set('Company.filter_order_Dir', $filter_order_Dir);
        $state->set('Company.'.$view.'_name', $company_filter);

        // filters
        $item_filter = $this->app->input->getString('item', null);
        $state->set('Company.item_filter', $item_filter);

	    $this->setState($state);
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
    public function getDataTableColumns()
    {
        $columns = array();
        $columns[] = array('data' => 'id', 'orderable' => false, 'sClass' => 'text-center');
        $columns[] = array('data' => 'name', 'ordering' => 'c.name');
        $columns[] = array('data' => 'contact_info', 'orderable' => false);
        $columns[] = array('data' => 'created', 'ordering' => 'c.created');
        $columns[] = array('data' => 'modified', 'ordering' => 'c.modified');
        $columns[] = array('data' => 'action', 'orderable' => false);

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
            $items = $this->getCompanies();
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
                $template = '<input type="checkbox" class="export" name="ids[]" value="'.$item->id.'" />';
                break;
            case 'name':
                $template = '<div class="title_holder">';
                $template .= '<a href="'.RouteHelper::_('index.php?view=companies&layout=company&company_id='.$item->id).'">'.$item->name.'</a>';
                $template .= '</div>';
                if ($item->address_formatted != ''):
                    $template .= '<address>'.$item->address_formatted.'</address>';
                endif;
                $template .= '<div class="hidden"><small>'.$item->description.'</small></div>';
                break;
            case 'contact_info':
                $template = $item->phone.'<br>'.$item->email;
                break;
            case 'modified':
                $template = DateHelper::formatDate($item->modified);
                break;
            case 'created':
                $template = DateHelper::formatDate($item->created);
                break;
            case 'action':
                $template = '<div class="btn-group">';
                // @TODO: make these 2 buttons work
                // $template .= ' <a rel="tooltip" title="'.TextHelper::_('COBALT_VIEW_CONTACTS').'" data-placement="bottom" class="btn" href="#" onclick="showCompanyContactsDialogModal('.$item->id.')"><i class="glyphicon glyphicon-user"></i></a>';
                // $template .= ' <a rel="tooltip" title="'.TextHelper::_('COBALT_VIEW_NOTES').'" data-placement="bottom" class="btn" href="#" onclick="openNoteModal('.$item->id.',\'company\');"><i class="glyphicon glyphicon-file"></i></a>';
                $template .= '</div>';
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
