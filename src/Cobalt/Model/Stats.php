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
use Cobalt\Helper\UsersHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;

// no direct access
defined('_CEXEC') or die('Restricted access');

class Stats extends DefaultModel
{

	public $person_id;

	public $access;

	public $users;

	public $today;

	public $previousDay;

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

		$this->previousDay = DateHelper::formatDBDate(date('Y-m-d') . " - 1 day");
		$this->today       = DateHelper::formatDBDate(date('Y-m-d'));
		$this->access      = UsersHelper::getRole($this->person_id);
		$this->users       = $this->getUsers($this->person_id, $this->access);
	}

	public function getDistinctEntries($type, $field)
	{
		// $query->where("(h.date >= '".$this->previousDay."' AND h.date < '".$this->today."')");
		$query = $this->db->getQuery(true)
			->select("DISTINCT h.type_id")
			->from("#__history AS h")
			->where("h.field=" . $this->db->quote($field) . " AND h.type=" . $this->db->quote($type))
			->where("h.user_id IN(" . implode(',', $this->users) . ")");

		return $this->db->setQuery($query)->loadColumn();
	}

	public function joinField($ids, $type, $field)
	{
		$results = array();

		$query = $this->db->getQuery(true);

		if (count($ids) > 0)
		{
			foreach ($ids as $id)
			{
				// $query->where("(h.date >= '".$this->previousDay."' AND h.date < '".$this->today."')");
				$query->clear()
					->select("h.type_id," . $type . ".*,h.new_value")
					->from("#__history AS h")
					->leftJoin("#__" . $type . " AS " . $type . " ON " . $type . ".id = h.type_id")
					->where("h.type_id=" . $id)
					->where("h.field=" . $this->db->quote($field))
					->order("h.date DESC LIMIT 1");

				$results[] = $this->db->setQuery($query)->loadObject();
			}
		}

		return $results;
	}

	public function getUsers($user_id, $user_role)
	{
		if ($user_role != 'basic')
		{
			$query = $this->db->getQuery(true)
				->select("id")
				->from("#__users");

			//if manager
			if ($user_role == "manager")
			{
				$team_id = UsersHelper::getTeamId($user_id);
				$query->where('team_id=' . $team_id);
			}
			//if exec there is no where clause, load all users

			//load results
			$results = $this->db->setQuery($query)->loadColumn();
		}
		else
		{
			$results = array($user_id);
		}

		return $results;
	}

	public function getActiveDealsAmount()
	{
		$query = $this->db->getQuery(true);

		/** get unique history **/
		$deal_ids = $this->getDistinctEntries('deal', 'stage_id');

		$query->clear()
			->select("SUM(d.amount)")
			->from("#__deals AS d")
			->where("d.id IN(" . implode(',', $deal_ids) . ')');
		// $query->where("(h.date >= '".$this->previousDay."' AND h.date < '".$this->today."')");

		return $this->db->setQuery($query)->loadResult();
	}

	public function getStages()
	{
		$query = $this->db->getQuery(true);

		/** Select distinct history entries **/
		$results = $this->getDistinctEntries('deal', 'stage_id');

		/** Get most recent entry from the above **/
		$deals = $this->joinField($results, 'deals', 'stage_id');

		/** Merge with all possible stages **/
		$query->clear()
			->select("s.name,s.color,s.id,0 AS amount")
			->from("#__stages AS s");

		$stages = $this->db->setQuery($query)->loadAssocList('id');

		/** Sum amounts from above **/
		if (count($deals) > 0)
		{
			foreach ($deals as $deal)
			{
				if (array_key_exists($deal->new_value, $stages))
				{
					$stages[$deal->new_value]['amount'] += $deal->amount;
				}
			}
		}

		usort($stages, array($this, 'sortAmount'));

		return $stages;
	}

	public function sortAmount($a, $b)
	{
		return $a['amount'] < $b['amount'];
	}

	public function getLeads()
	{
		/** person ids **/
		$person_ids = $this->getDistinctEntries('person', 'type');
		$people     = $this->joinField($person_ids, 'people', 'type');
		$leads      = array('lead' => 0, 'contact' => 0);

		if (count($people) > 0)
		{
			foreach ($people as $person)
			{
				$leads[$person->type]++;
			}
		}

		return $leads;
	}

	public function getNotes()
	{

		$note_ids = $this->getDistinctEntries('note', 'id');

		$query = $this->db->getQuery(true)
			->select("c.*")
			->from("#__notes_categories AS c");

		$categories = $this->db->setQuery($query)->loadAssocList();

		$totals = array();

		if (count($categories) > 0)
		{
			foreach ($categories as $category)
			{
				$query->clear()
					->select("COUNT(n.id)")
					->from("#__notes AS n")
					->where("n.category_id = " . $category['id'])
					->where("n.id IN(" . implode(',', $note_ids) . ")");

				$totals[$category['name']] = $this->db->setQuery($query)->loadResult();
			}
		}

		return $totals;
	}

	public function getTodos()
	{
		$events = $this->getDistinctEntries('event', 'id');

		$query = $this->db->getQuery(true)
			->select("c.*")
			->from("#__events_categories AS c");

		$categories = $this->db->setQuery($query)->loadAssocList();

		$totals = array();

		if (count($categories) > 0)
		{
			foreach ($categories as $category)
			{
				$query->clear()
					->select("COUNT(e.id) AS total,SUM(e.completed) AS completed")
					->from("#__events AS e")
					->where("e.category_id = " . $category['id'])
					->where("e.id IN(" . implode(',', $events) . ")");

				$totals[$category['name']] = $this->db->setQuery($query)->loadObject();
			}
		}

		return $totals;
	}

	public function getDealActivity()
	{
	}
}
