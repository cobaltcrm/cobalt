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

use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Cancel extends DefaultController
{
    public function execute()
    {
        $view = $this->input->get('view');

        $msg = TextHelper::_('Entry cancelled!');
        $this->app->redirect('index.php?view='.$view,$msg);
    }
}
