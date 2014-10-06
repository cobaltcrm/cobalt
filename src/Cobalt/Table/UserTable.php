<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Table;

use Joomla\Database\DatabaseDriver;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class UserTable extends AbstractTable
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  A database connector object
	 *
	 * @since   1.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__users', 'id', $db);
	}

	/**
	 * Fetches the list of users and their password hashes
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getUserPasswords()
	{
		$data = $this->db->setQuery(
			$this->db->getQuery(true)
				->select('username, password')
				->from($this->getTableName())
		)->loadAssocList();

		$users = array();

		foreach ($data as $row)
		{
			$users[$row['username']] = $row['password'];
		}

		return $users;
	}

	/**
	 * Load a user by username
	 *
	 * @param   string  $username  The username of the user to load
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function loadByUserName($username)
	{
		$check = $this->db->setQuery(
			$this->db->getQuery(true)
				->select('*')
				->from($this->getTableName())
				->where('username = ' . $this->db->quote($username))
		)->loadObject();

		return ($check) ? $this->bind($check) : $this;
	}
}
