<?php
/**
 * @package    Cobalt.CRM
 *
 * @copyright  Copyright (C) 2012 Cobalt. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Cobalt\Authentication;

/**
 * AuthenticationException
 *
 * @since  1.0
 */
class AuthenticationException extends \Exception
{
	/**
	 * Constructor.
	 *
	 * @param   string  $message  The optional message to throw.
	 *
	 * @since   1.0
	 */
	public function __construct($message = '')
	{
		parent::__construct($message, 403);
	}
}
