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
use Cobalt\Helper\DealHelper;
use Cobalt\Helper\UsersHelper;

use Joomla\Registry\Registry;

defined('_CEXEC') or die;

class Report extends DefaultModel
{

    public $published = 1;

	/**
	 * Method to store a record
	 *
	 * @return  boolean  True on success
	 */
	public function store()
	{
		// Load Tables
		$row    = $this->getTable('Report');
		$oldRow = $this->getTable('Report');

		$data = $this->app->input->post->getArray();

		//date generation
		$date = DateHelper::formatDBDate(date('Y-m-d H:i:s'));
		if (!array_key_exists('id', $data))
		{
			$data['created'] = $date;
			$status          = "created";
		}
		else
		{
			$row->load($data['id']);
			$oldRow->load($data['id']);
			$status = "updated";
		}

		//modified date
		$data['modified'] = $date;

		//assign owner id
		$data['owner_id'] = UsersHelper::getUserId();

		//insert custom field data
		$data['fields'] = serialize($data['fields']);

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

	/**
	 * Get Custom Reports
	 *
	 * @param  int $id specific report to search for
	 *
	 * @return mixed $results reports matched
	 */
	public function getCustomReports($id = null)
	{
		// Load database
		$db    = $this->getDb();
		$query = $db->getQuery(true);

		// Gen query string
		$query->select("report.*")
			->from("#__reports as report")
			->leftJoin("#__users AS u ON u.id = report.owner_id");

		// Search for reports
		if ($id != null)
		{
			$query->where("report.id=$id");
		}

		// Filter based on member access roles
		$user_id     = UsersHelper::getUserId();
		$member_role = UsersHelper::getRole();
		$team_id     = UsersHelper::getTeamId();

		if ($member_role != 'exec')
		{

			if ($member_role == 'manager')
			{
				$query->where("u.team_id=$team_id");
			}
			else
			{
				$query->where("report.owner_id=$user_id");
			}
		}

		/**
		 * Set our sorting direction if set via post
		 */
		$layout = str_replace("_filter", "", $this->app->input->get('layout'));

		//default deals view
		if ($layout == "custom_reports")
		{
			$query->order($this->getState()->get('Report.filter_order') . ' ' . $this->getState()->get('Report.filter_order_Dir'));
		}

		//return results
		return $db->setQuery($query)->loadAssocList();
	}

	/**
	 * Get data for custom reports
	 *
	 * @param   integer  $id  ID of the custom data to retrieve
	 *
	 * @return  array
	 */
	public function getCustomReportData($id = null)
	{
		//get db
		$db    = $this->getDb();
		$query = $db->getQuery(true);

		//get the custom report so we know what data to filter and select
		$custom_report        = $this->getCustomReports($id);
		$custom_report        = $custom_report[0];
		$custom_report_fields = unserialize($custom_report['fields']);

		//gen query
		//construct query string
		$queryString = 'd.*,SUM(d.amount) AS filtered_total,';
		$queryString .= 'c.name as company_name,';
		$queryString .= 'stat.name as status_name,';
		$queryString .= 'source.name as source_name,';
		$queryString .= 'stage.name as stage_name,stage.percent,';
		$queryString .= 'user.first_name, user.last_name,';
		$queryString .= 'p.first_name as primary_contact_first_name,p.last_name as primary_contact_last_name,';
		$queryString .= "p.email as primary_contact_email,p.phone as primary_contact_phone,";
		$queryString .= "pc.name as primary_contact_company_name";

		//select
		$query->select($queryString);
		$query->from("#__deals AS d");

		//left join
		$query->leftJoin('#__companies AS c ON c.id = d.company_id && c.published>0');
		$query->leftJoin('#__deal_status AS stat ON stat.id = d.status_id');
		$query->leftJoin('#__sources AS source ON source.id = d.source_id');
		$query->leftJoin('#__stages AS stage on stage.id = d.stage_id');
		$query->leftJoin('#__users AS user ON user.id = d.owner_id');
		$query->leftJoin("#__people AS p ON p.id = d.primary_contact_id && p.published>0");
		$query->leftJoin("#__companies AS pc ON pc.id = p.company_id && pc.published>0");

		//group results
		$query->group("d.id");

		//filter data with user state requests
		$layout = str_replace("_filter", "", $this->app->input->get('layout'));
		$view   = $this->app->input->get('view');

		if ($view == "print")
		{
			$layout = "custom_report";
			$id     = $this->app->input->get('custom_report');
		}

		$filter_order     = $this->getState()->get('Report.' . $id . '_' . $layout . '_filter_order');
		$filter_order_Dir = $this->getState()->get('Report.' . $id . '_' . $layout . '_filter_order_Dir');
		$filter_order     = (strstr($filter_order, "custom_")) ? str_replace("d.", "", $filter_order) : $filter_order;
		$query->order($filter_order . ' ' . $filter_order_Dir);

		//assign defaults
		$close    = null;
		$modified = null;
		$created  = null;
		$status   = null;
		$source   = null;
		$stage    = null;

		//filter by deal names
		$deal_filter = $this->getState()->get('Report.' . $id . '_' . $layout . '_name');

		if ($deal_filter != null)
		{
			$query->where("d.name LIKE " . $db->quote('%' . $deal_filter . '%'));
		}

		//owner
		$owner_filter = $this->getState()->get('Report.' . $id . '_' . $layout . '_owner_id');

		if ($owner_filter != null && $owner_filter != 'all')
		{
			$owner_type = $this->getState()->get('Report.' . $id . '_' . $layout . '_owner_type');

			if ($owner_type == 'member')
			{
				$query->where("d.owner_id=" . $owner_filter);
			}
			elseif ($owner_type == 'team')
			{
				//get team members
				$team_members = UsersHelper::getTeamUsers($owner_filter, true);
				$query->where('d.owner_id IN (0, ' . implode(', ', $team_members) . ')');
			}
		}

		//amount
		$amount_filter = $this->getState()->get('Report.' . $id . '_' . $layout . '_amount');

		if ($amount_filter != null && $amount_filter != 'all')
		{
			if ($amount_filter == 'small')
			{
				$query->where("d.amount <= 50");
			}
			elseif ($amount_filter == 'medium')
			{
				$query->where("d.amount > 50 && d.amount <= 400");
			}
			elseif ($amount_filter == 'large')
			{
				$query->where("d.amount > 400");
			}
		}

		//source
		$source_filter = $this->getState()->get('Report.' . $id . '_' . $layout . '_source_id');

		if ($source_filter != null && $source_filter != 'all')
		{
			$source = $source_filter;
		}

		//stage
		$stage_filter = $this->getState()->get('Report.' . $id . '_' . $layout . '_stage_id');

		if ($stage_filter != null && $stage_filter != 'all')
		{
			$stage = $stage_filter;
		}

		//status
		$status_filter = $this->getState()->get('Report.' . $id . '_' . $layout . '_status_id');

		if ($status_filter != null && $status_filter != 'all')
		{
			$status = $status_filter;
		}

		//expected close
		$expected_close_filter = $this->getState()->get('Report.' . $id . '_' . $layout . '_expected_close');

		if ($expected_close_filter != null && $expected_close_filter != 'all')
		{
			$close = $expected_close_filter;
		}

		//modified
		$modified_filter = $this->getState()->get('Report.' . $id . '_' . $layout . '_modified');

		if ($modified_filter != null && $modified_filter != 'all')
		{
			$modified = $modified_filter;
		}

		//created
		$created_filter = $this->getState()->get('Report.' . $id . '_' . $layout . '_created');

		if ($created_filter != null && $created_filter != 'all')
		{
			$created = $created_filter;
		}

		//filter by primary contact name
		$primary_contact_name = $this->getState()->get('Report.' . $id . '_' . $layout . '_primary_contact_name');

		if ($primary_contact_name != null)
		{
			$query->where("(p.first_name LIKE " . $db->quote('%' . $primary_contact_name . '%') . " OR p.last_name LIKE " . $db->quote('%' . $primary_contact_name . '%'));
		}

		//filter by primary contact email
		$primary_contact_email = $this->getState()->get('Report.' . $id . '_' . $layout . '_primary_contact_email');

		if ($primary_contact_email != null)
		{
			$query->where("p.email LIKE " . $db->quote('%' . $primary_contact_email . '%'));
		}

		//filter by primary contact phone
		$primary_contact_phone = $this->getState()->get('Report.' . $id . '_' . $layout . '_primary_contact_phone');

		if ($primary_contact_phone != null)
		{
			$query->where("p.phone LIKE " . $db->quote('%' . $primary_contact_phone . '%'));
		}

		//get current date to use for all date filtering
		$date = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));

		/** --------------------------------------------
		 * Search for closing deal filters
		 */
		if ($close != null && $close != "any")
		{
			if ($close == "this_week")
			{
				$this_week = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));
				$next_week = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+7 days"));
				$query->where("d.expected_close >= " . $db->quote($this_week));
				$query->where("d.expected_close < " . $db->quote($next_week));
			}
			elseif ($close == "next_week")
			{
				$next_week       = date('Y-m-d 00:00:00', strtotime(DateHelper::formatDBDate(date("Y-m-d", strtotime($date))) . "+7 days"));
				$week_after_next = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+14 days"));
				$query->where("d.expected_close >= " . $db->quote($next_week));
				$query->where("d.expected_close < " . $db->quote($week_after_next));
			}
			elseif ($close == "this_month")
			{
				$this_month = DateHelper::formatDBDate(date('Y-m-0 00:00:00'));
				$next_month = date('Y-m-0 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+1 month"));
				$query->where("d.expected_close >= " . $db->quote($this_month));
				$query->where("d.expected_close < " . $db->quote($next_month));
			}
			elseif ($close == "next_month")
			{
				$next_month      = date("Y-m-0 00:00:00", strtotime(date("Y-m-d 00:00:00", strtotime($date)) . "+1 month"));
				$next_next_month = date("Y-m-0 00:00:00", strtotime(date("Y-m-d 00:00:00", strtotime($date)) . "+2 months"));
				$query->where("d.expected_close >= " . $db->quote($next_month));
				$query->where("d.expected_close < " . $db->quote($next_next_month));
			}
		}

		/** --------------------------------------------
		 * Search for modified deal filters
		 */
		if ($modified != null && $modified != "any")
		{
			if ($modified == "this_week")
			{
				$this_week = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));
				$last_week = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "-7 days"));
				$query->where("d.modified >= " . $db->quote($last_week));
				$query->where("d.modified < " . $db->quote($this_week));
			}
			elseif ($modified == "last_week")
			{
				$last_week        = DateHelper::formatDBDate(date("Y-m-d", strtotime("-7 days")));
				$week_before_last = DateHelper::formatDBDate(date("Y-m-d", strtotime("-14 days")));
				$query->where("d.modified >= " . $db->quote($week_before_last));
				$query->where("d.modified < " . $db->quote($last_week));
			}
			elseif ($modified == "this_month")
			{
				$this_month = DateHelper::formatDBDate(date('Y-m-1 00:00:00'));
				$next_month = date('Y-m-1 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+1 month"));
				$query->where("d.modified >= " . $db->quote($this_month));
				$query->where("d.modified < " . $db->quote($next_month));
			}
			elseif ($modified == "last_month")
			{
				$this_month = DateHelper::formatDBDate(date('Y-m-1 00:00:00'));
				$last_month = date('Y-m-1 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "-1 month"));
				$query->where("d.modified >= " . $db->quote($last_month));
				$query->where("d.modified < " . $db->quote($this_month));
			}
		}

		/** --------------------------------------------
		 * Search for created deal filters
		 */
		if ($created != null && $created != "any")
		{
			if ($created == "this_week")
			{
				$this_week = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));
				$last_week = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date) . "-7 days")));
				$query->where("d.created >= " . $db->quote($last_week));
				$query->where("d.created < " . $db->quote($this_week));
			}
			elseif ($created == "last_week")
			{
				$last_week        = DateHelper::formatDBDate(date("Y-m-d", strtotime("-7 days")));
				$week_before_last = DateHelper::formatDBDate(date("Y-m-d", strtotime("-14 days")));
				$query->where("d.created >= " . $db->quote($week_before_last));
				$query->where("d.created < " . $db->quote($last_week));
			}
			elseif ($created == "this_month")
			{
				$this_month = DateHelper::formatDBDate(date('Y-m-1 00:00:00'));
				$next_month = date('Y-m-1 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+1 month"));
				$query->where("d.created >= " . $db->quote($this_month));
				$query->where("d.created < " . $db->quote($next_month));
			}
			elseif ($created == "last_month")
			{
				$this_month = DateHelper::formatDBDate(date('Y-m-1 00:00:00'));
				$last_month = date('Y-m-1 00:00:00', strtotime(date("Y-m-d", strtotime($date) . "-1 month")));
				$query->where("d.created >= " . $db->quote($last_month));
				$query->where("d.created < " . $db->quote($this_month));
			}
			elseif ($created == "today")
			{
				$today    = DateHelper::formatDBDate(date("Y-m-d 00:00:00"));
				$tomorrow = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+1 day"));
				$query->where("d.created >= " . $db->quote($today));
				$query->where("d.created < " . $db->quote($tomorrow));
			}
			elseif ($created == "yesterday")
			{
				$today     = DateHelper::formatDBDate(date("Y-m-d 00:00:00"));
				$yesterday = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "-1 day"));
				$query->where("d.created >= " . $db->quote($yesterday));
				$query->where("d.created < " . $db->quote($today));
			}
		}

		/** ------------------------------------------
		 * Search for status
		 */
		if ($status != null && $status != 'all')
		{
			$query->where("d.status_id=" . $status);
		}

		/** -------------------------
		 * Search for sources
		 */
		if ($source != null && $source != 'all')
		{
			$query->where('d.source_id=' . $source);
		}

		/** ----------------------------------------------------------------
		 * Filter for stage id associations
		 */
		if ($stage != null && $stage != 'all')
		{
			//if we want active deals we must retrieve the active stage ids to filter by
			if ($stage == 'active')
			{
				//get stage ids
				$stage_ids = DealHelper::getActiveStages();
				//filter by results having team ids
				$stages = "";
				for ($i = 0; $i < count($stage_ids); $i++)
				{
					$stage = $stage_ids[$i];
					$stages .= $stage['id'] . ",";
				}
				$stages = substr($stages, 0, -1);
				$query->where("d.stage_id IN(" . $stages . ")");
			}
			else
			{
				// else filter by the stage id
				$query->where("d.stage_id=" . $db->quote($stage));
			}
		}

		/** ---------------------------------------------------------------------------------------------------------------
		 * Field for custom field user states
		 */
		//Get custom filters
		$custom_fields = DealHelper::getUserCustomFields();
		//If the user has defined any custom fields we will left join the associated data here
		if (count($custom_fields) > 0)
		{
			foreach ($custom_fields as $row)
			{
				//Join different data based on type
				switch ($row['type'])
				{
					//If the type is forecast we want to calculate the amount
					case "forecast":
						$query->select("( d.amount * ( d.probability / 100 )) as custom_" . $row['id']);
						break;
					//Else join the associated value from the database
					default :
						$query->select("custom_" . $row['id'] . ".value as custom_" . $row['id']);
						$query->leftJoin(
							"#__deal_custom_cf as custom_" . $row['id'] . " on " . "custom_" . $row['id'] . ".deal_id = d.id && "
							. "custom_" . $row['id'] . ".custom_field_id = " . $row['id']
						);
						break;
				}

				//If the user has any associated user state requests set in the model we set the filters here
				$custom_field_filter = $this->getState()->get('Report.' . $id . '_' . $layout . '_' . $row['id']);

				if ($custom_field_filter != null && $custom_field_filter != 'all')
				{
					switch ($row['type'])
					{
						case "forecast":
							$query->where("( d.amount * ( d.probability / 100 )) LIKE " . $db->quote('%' . $custom_field_filter . '%'));
							break;

						case "date":
							if ($custom_field_filter == "this_week")
							{
								$this_week = DateHelper::formatDBDate(date('Y-m-d 00:00:00'));
								$next_week = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+7 days"));
								$query->where("custom_" . $row['id'] . ".value >= " . $db->quote($this_week));
								$query->where("custom_" . $row['id'] . ".value < " . $db->quote($next_week));
							}
							elseif ($custom_field_filter == "next_week")
							{
								$next_week       = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+7 days"));
								$week_after_next = date('Y-m-d 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+14 days"));
								$query->where("custom_" . $row['id'] . ".value >= " . $db->quote($next_week));
								$query->where("custom_" . $row['id'] . ".value < " . $db->quote($week_after_next));
							}
							elseif ($custom_field_filter == "this_month")
							{
								$this_month = DateHelper::formatDBDate(date('Y-m-0 00:00:00'));
								$next_month = date('Y-m-0 00:00:00', strtotime(date("Y-m-d", strtotime($date)) . "+1 month"));
								$query->where("custom_" . $row['id'] . ".value >= " . $db->quote($this_month));
								$query->where("custom_" . $row['id'] . ".value < " . $db->quote($next_month));
							}
							elseif ($custom_field_filter == "next_month")
							{
								$next_month      = date("Y-m-0 00:00:00", strtotime(date("Y-m-d 00:00:00", strtotime($date)) . "+1 month"));
								$next_next_month = date("Y-m-0 00:00:00", strtotime(date("Y-m-d 00:00:00", strtotime($date)) . "+2 months"));
								$query->where("custom_" . $row['id'] . ".value >= " . $db->quote($next_month));
								$query->where("custom_" . $row['id'] . ".value < " . $db->quote($next_next_month));
							}

							break;

						default:
							$query->where("custom_" . $row['id'] . ".value LIKE " . $db->quote('%' . $custom_field_filter . '%'));
							break;
					}
				}
			}
		}

		//filter based on member access roles
		$user_id     = UsersHelper::getUserId();
		$member_role = UsersHelper::getRole();
		$team_id     = UsersHelper::getTeamId();

		if ($member_role != 'exec')
		{
			if ($member_role == 'manager')
			{
				$query->where("user.team_id=$team_id");
			}
			else
			{
				$query->where("d.owner_id=$user_id");
			}
		}

		$query->where("d.published=" . $this->published);
		$query->where("d.archived=0");

		//return results
		return $db->setQuery($query)->loadAssocList();
	}

	/**
	 * Method to delete a record
	 *
	 * @param   integer  $id  Report ID to delete
	 *
	 * @return  boolean  True on success
	 */
	public function deleteReport($id)
	{
		$table = $this->getTable('Report');

		return $table->delete($id);
	}

	/**
	 * Populate user state requests
	 */
	function populateState()
	{
		//determine view so we set correct states
		$view   = $this->app->input->get('view');
		$layout = str_replace("_filter", "", $this->app->input->get('layout'));
		$id     = $this->app->input->get('id') ? $this->app->input->get('id') : $this->app->input->get('custom_report');
		//set layout for filter pages

		if ($view == "print")
		{
			$id     = $this->app->input->get('custom_report');
			$layout = "custom_report";
		}

		$state = new Registry;

		/** --------------------------------------
		 * Filter data for different views
		 */
		switch ($layout)
		{
			case "custom_reports"    :
				//set default filter states for reports
				$filter_order     = $this->app->getUserStateFromRequest('Report.filter_order', 'filter_order', 'report.name');
				$filter_order_Dir = $this->app->getUserStateFromRequest('Report.filter_order_Dir', 'filter_order_Dir', 'asc');
				//set states for reports
				$state->set('Report.filter_order', $filter_order);
				$state->set('Report.filter_order_Dir', $filter_order_Dir);
				break;

			case "custom_report"    :
				//set default filter states for reports
				$filter_order          = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_filter_order', 'filter_order', 'd.name');
				$filter_order_Dir      = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_filter_order_Dir', 'filter_order_Dir', 'asc');
				$deal_filter           = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_name', 'deal_name', null);
				$owner_filter          = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_owner_id', 'owner_id', UsersHelper::getUserId());
				$owner_type_filter     = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_owner_type', 'owner_type', 'member');
				$amount_filter         = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_amount', 'deal_amount', null);
				$source_filter         = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_source_id', 'source_id', null);
				$stage_filter          = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_stage_id', 'stage_id', null);
				$status_filter         = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_status_id', 'status_id', null);
				$expected_close_filter = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_expected_close', 'expected_close', null);
				$modified_filter       = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_modified', 'modified', null);
				$created_filter        = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_created', 'created', null);
				$primary_contact_name  = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_primary_contact_name', 'primary_contact_name', null);
				$primary_contact_phone = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_primary_contact_phone', 'primary_contact_phone', null);
				$primary_contact_email = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . 'primary_contact_email', 'primary_contact_email', null);

				//get custom filters
				$custom_fields = DealHelper::getUserCustomFields();

				if (count($custom_fields) > 0)
				{
					foreach ($custom_fields as $row)
					{
						$custom_field_value = $this->app->getUserStateFromRequest('Report.' . $id . '_' . $layout . '_' . $row['id'], 'custom_' . $row['id'], null);
						$state->set('Report.' . $id . '_' . $layout . '_' . $row['id'], $custom_field_value);
					}
				}

				//set states for reports
				$state->set('Report.' . $id . '_' . $layout . '_filter_order', $filter_order);
				$state->set('Report.' . $id . '_' . $layout . '_filter_order_Dir', $filter_order_Dir);
				$state->set('Report.' . $id . '_' . $layout . '_name', $deal_filter);
				$state->set('Report.' . $id . '_' . $layout . '_owner_id', $owner_filter);
				$state->set('Report.' . $id . '_' . $layout . '_owner_type', $owner_type_filter);
				$state->set('Report.' . $id . '_' . $layout . '_amount', $amount_filter);
				$state->set('Report.' . $id . '_' . $layout . '_source_id', $source_filter);
				$state->set('Report.' . $id . '_' . $layout . '_stage_id', $stage_filter);
				$state->set('Report.' . $id . '_' . $layout . '_status_id', $status_filter);
				$state->set('Report.' . $id . '_' . $layout . '_expected_close', $expected_close_filter);
				$state->set('Report.' . $id . '_' . $layout . '_modified', $modified_filter);
				$state->set('Report.' . $id . '_' . $layout . '_created', $created_filter);
				$state->set('Report.' . $id . '_' . $layout . '_primary_contact_phone', $primary_contact_phone);
				$state->set('Report.' . $id . '_' . $layout . '_primary_contact_name', $primary_contact_name);
				$state->set('Report.' . $id . '_' . $layout . '_primary_contact_phone', $primary_contact_phone);
				$state->set('Report.' . $id . '_' . $layout . '_primary_contact_email', $primary_contact_email);
				break;
		}

		$this->setState($state);
	}
}
