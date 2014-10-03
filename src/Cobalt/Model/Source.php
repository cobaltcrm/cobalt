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
use Cobalt\Helper\UsersHelper;

// no direct access
defined('_CEXEC') or die('Restricted access');

class Source extends DefaultModel
{

	public $_id = null;

	/**
	 * Get sources for return on investment reports
	 *
	 * @param none
	 *
	 * @return mixed $results
	 */
	public function getRoiSources()
	{
		$query = $this->db->getQuery(true);

		//construct query string
		$query->select("s.id,s.name,count(d.id) as number_of_deals,sum(d.amount) as revenue,s.type,s.cost")
			->select("IF ( s.type <> " . $this->db->quote('per') . ", ( ( ( ( sum(d.amount) - s.cost ) / s.cost ) * 100 ) ), ( ( sum(d.amount) - ( s.cost * count(d.id) ) ) / ( s.cost * count(d.id) ) * 100 ) ) AS roi")
			->from("#__sources AS s");

		//left join data
		$won_stage_ids = DealHelper::getWonStages();
		$query->leftJoin("#__deals AS d ON d.source_id = s.id AND d.stage_id IN (" . implode(',', $won_stage_ids) . ") AND d.published=1 AND d.archived=0");
		$query->leftJoin("#__users AS u ON u.id = d.owner_id");

		//set our sorting direction if set via post
		$query->order($this->getState()->get('Source.filter_order') . ' ' . $this->getState()->get('Source.filter_order_Dir'));

		//group data
		$query->group("s.id");

		if ($this->_id)
		{
			if (is_array($this->_id))
			{
				$query->where("s.id IN (" . implode(',', $this->_id) . ")");
			}
			else
			{
				$query->where("s.id=$this->_id");
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
				$query->where("u.team_id=$team_id");
			}
			else
			{
				$query->where("(d.owner_id=$user_id)");
			}
		}

		return $this->db->setQuery($query)->loadAssocList();
	}

	/*
	 * Populate the model based on user requests
	 */
	public function populateState()
	{
		//get states
		$filter_order     = $this->app->getUserStateFromRequest('Source.filter_order', 'filter_order', 's.name');
		$filter_order_Dir = $this->app->getUserStateFromRequest('Source.filter_order_Dir', 'filter_order_Dir', 'asc');

		//set states
		$this->getState()->set('Source.filter_order', $filter_order);
		$this->getState()->set('Source.filter_order_Dir', $filter_order_Dir);
	}
}
