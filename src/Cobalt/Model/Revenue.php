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
use Cobalt\Helper\TextHelper;

// no direct access
defined('_CEXEC') or die;

class Revenue extends DefaultModel
{
	/**
	 * Get Monthly Revenue
	 *
	 * @param   string|null   $access_type  Filter by 'member', 'team', or 'company'; or null for no filter
	 * @param   integer|null  $access_id    ID of the $access_type we want to filter by
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getMonthlyRevenue($access_type = null, $access_id = null)
	{
		$db    = $this->getDb();
		$query = $db->getQuery(true);

		// Get current month
		$current_month = DateHelper::formatDBDate(date('Y-m-01 00:00:00'));

		// Get weeks in month
		$weeks = DateHelper::getWeeksInMonth($current_month);

		// Get stage id to filter deals by
		$won_stage_ids = DealHelper::getWonStages();

		$data           = new \stdClass;
		$data->labels   = array();
		$data->datasets = array();
		$totals         = array();

		foreach ($weeks as $week)
		{
			$start_date = $week['start_date'];
			$end_date   = $week['end_date'];

			$weekDate       = new \DateTime($start_date . ' +2 day');
			$data->labels[] = TextHelper::_('COBALT_WEEK') . ' ' . $weekDate->format('W');

			$query->clear()
				->select('SUM(d.amount) AS y')
				->from('#__deals AS d')
				->where('d.stage_id IN (' . implode(',', $won_stage_ids) . ')')
				->where('d.modified >= ' . $db->quote($start_date))
				->where('d.modified < ' . $db->quote($end_date))
				->where('d.modified IS NOT NULL')
				->where('d.published > 0');

			if (count($won_stage_ids))
			{
				$query->where('d.stage_id IN (' . implode(', ', $won_stage_ids) . ')');
			}

			// Filter by access type
			if ($access_type != 'company')
			{
				if ($access_type == 'team')
				{
					// Get team members
					$team_members = UsersHelper::getTeamUsers($access_id, true);
					$query->where('d.owner_id IN (0, ' . implode(', ', $team_members) . ')');
				}
				elseif ($access_type == 'member')
				{
					$query->where('d.owner_id = ' . $access_id);
				}
			}

			$total = (int) $db->loadResult();

			if (!$total)
			{
				$total = 0;
			}

			$totals[] = $total;
		}

		$data->datasets[0]                   = new \stdClass;
		$data->datasets[0]->data             = $totals;
		$data->datasets[0]->label            = '';
		$data->datasets[0]->fillColor        = "rgba(151,187,205,0.5)";
		$data->datasets[0]->strokeColor      = "rgba(151,187,205,0.8)";
		$data->datasets[0]->pointColor       = "rgba(151,187,205,0.75)";
		$data->datasets[0]->pointStrokeColor = "rgba(151,187,205,1)";

		return $data;
	}

	/**
	 * Get Yearly Revenue
	 *
	 * @param   string|null   $access_type  Filter by 'member', 'team', or 'company'; or null for no filter
	 * @param   integer|null  $access_id    ID of the $access_type we want to filter by
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getYearlyRevenue($access_type = null, $access_id = null)
	{
		$db    = $this->getDb();
		$query = $db->getQuery(true);

		// Get current year and months to loop through
		$current_year = DateHelper::formatDBDate(date('Y-01-01 00:00:00'));
		$month_names  = DateHelper::getMonthNames();
		$months       = DateHelper::getMonthDates();

		// Get stage id to filter deals by
		$won_stage_ids = DealHelper::getWonStages();

		$results = array();

		foreach ($months as $month)
		{
			$start_date = $month['date'];
			$end_date   = DateHelper::formatDBDate(date('Y-m-d 00:00:00', strtotime($start_date . ' + 1 months')));

			$query->clear()
				->select('d.modified, SUM(d.amount) AS y')
				->from('#__deals AS d')
				->where('d.modified >= ' . $db->quote($start_date))
				->where('d.modified < ' . $db->quote($end_date))
				->where('d.modified IS NOT NULL')
				->where('d.published > 0');

			if (count($won_stage_ids))
			{
				$query->where('d.stage_id IN (' . implode(', ', $won_stage_ids) . ')');
			}

			// Filter by access type
			if ($access_type != 'company')
			{
				if ($access_type == 'team')
				{
					$team_members = UsersHelper::getTeamUsers($access_id, true);
					$query->where('d.owner_id IN (0, ' . implode(', ', $team_members) . ')');
				}
				elseif ($access_type == 'member')
				{
					$query->where('d.owner_id = ' . $access_id);
				}
			}

			$totals = $db->setQuery($query)->loadAssoc();

			if (!$totals)
			{
				$totals = array('y' => 0);
			}

			$totals['y'] = (int) $totals['y'];
			$results[]   = $totals;
		}

		return $results;
	}
}
