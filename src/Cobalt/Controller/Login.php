<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Controller;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

use Cobalt\Helper\RouteHelper;

class Login extends DefaultController
{
	/**
	 * Execute the controller
	 *
	 * @return  boolean  True if controller finished execution
	 *
	 * @since   1.0
	 */
	public function execute()
	{
		// If logged in, move on to the manager
		if ($this->getApplication()->getUser()->isAuthenticated())
		{
			$this->getApplication()->redirect(RouteHelper::_('index.php?view=dashboard'));
		}

		$method = $this->getInput()->getMethod();

		$username = $this->getInput()->$method->get('username', false, 'username');
		$password = $this->getInput()->$method->get('password', false, 'raw');

		if ($username && $password)
		{
			$this->getApplication()->login();
		}

		$this->getInput()->set('view', 'login');

		return parent::execute();
	}
}
