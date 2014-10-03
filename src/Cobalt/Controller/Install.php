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

class Install extends DefaultController
{
    public function execute()
    {
	    ini_set('display_errors', 0);

	    /** @var \Cobalt\Model\Install $model */
	    $model = $this->getModel('Install');

	    if (!$model->install())
	    {
		    // Error handler
	    }

	    // Success
	    $this->getApplication()->redirect($this->getApplication()->get('uri.host.base'));
    }
}
