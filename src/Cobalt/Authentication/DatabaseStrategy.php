<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2012 Cobalt. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt\Authentication;

use Cobalt\Container;
use Cobalt\Table\UserTable;

use Joomla\Authentication\AuthenticationStrategyInterface;
use Joomla\Authentication\Authentication;
use Joomla\Input\Input;

/**
 * Authentication strategy which pulls data from the database
 *
 * @since  1.0
 */
class DatabaseStrategy implements AuthenticationStrategyInterface
{
	/**
	 * The Input object
	 *
	 * @var    Input  $input  The input object from which to retrieve the username and password.
	 * @since  1.0
	 */
	private $input;

	/**
	 * The credential store.
	 *
	 * @var    array  $credentialStore  An array of username/hash pairs.
	 * @since  1.0
	 */
	private $credentialStore;

	/**
	 * The last authentication status.
	 *
	 * @var    integer  $status  The last status result (use constants from Authentication)
	 * @since  1.0
	 */
	private $status;

	/**
	 * Strategy Constructor
	 *
	 * @param   Input  $input  The input object from which to retrieve the request credentials.
	 *
	 * @since   1.0
	 */
	public function __construct(Input $input)
	{
		$this->input = $input;

		$usersTable = new UserTable(Container::fetch('db'));
		$this->credentialStore = $usersTable->getUserPasswords();
	}

	/**
	 * Attempt to authenticate the username and password pair.
	 *
	 * @return  string|boolean  A string containing a username if authentication is successful, false otherwise.
	 *
	 * @since   1.0
	 */
	public function authenticate()
	{
		$method = $this->input->getMethod();

		$username = $this->input->$method->get('username', false, 'username');
		$password = $this->input->$method->get('password', false, 'raw');

		if (!$username || !$password)
		{
			$this->status = Authentication::NO_CREDENTIALS;

			return false;
		}

		if (!isset($this->credentialStore[$username]))
		{
			$this->status = Authentication::NO_SUCH_USER;

			return false;
		}

		$hash = $this->credentialStore[$username];

		if (!password_verify($password, $hash))
		{
			$this->status = Authentication::INVALID_CREDENTIALS;

			return false;
		}

		$this->status = Authentication::SUCCESS;

		return $username;
	}

	/**
	 * Get the status of the last authentication attempt.
	 *
	 * @return  integer  Authentication class constant result.
	 *
	 * @since   1.0
	 */
	public function getResult()
	{
		return $this->status;
	}
}
