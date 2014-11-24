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

use Cobalt\Factory;

// no direct access
defined('_CEXEC') or die('Restricted access');

class Install extends DefaultController
{
	public function execute()
	{
		ini_set('display_errors', 0);

		// Get the request data
		$data = $this->getInput()->getArray(array(
			'db_drive' => 'cmd',
		    'site_name' => 'string',
		    'database_host' => 'cmd',
		    'database_user' => 'username',
		    'database_password'=> 'raw',
		    'database_name'=> 'string',
		    'database_prefix'=> 'string',
		    'first_name' => 'string',
		    'last_name' => 'string',
		    'username' => 'username',
		    'password' => 'raw',
		    'email' => 'string'
		));

		/** @var \Cobalt\Model\Install $model */
		$model = Factory::getModel('Install');

		if (!$model->install($data))
		{
			// Error handler
		}

		// Success
		$this->getApplication()->redirect($this->getApplication()->get('uri.host.base'));
	}
}
