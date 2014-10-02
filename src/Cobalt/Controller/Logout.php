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

use Cobalt\Helper\RouteHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

/**
 * Logout controller
 *
 * @since   1.0
 */
class Logout extends DefaultController
{
	/**
	 * Execute the controller
	 *
	 * @return  void  Redirects the application
	 *
	 * @since   1.0
	 */
	public function execute()
	{
		$this->getApplication()->setUser(null)->redirect(RouteHelper::_('index.php', false));
	}
}
