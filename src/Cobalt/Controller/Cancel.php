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

use JFactory;
use JText;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Cancel extends DefaultController
{
    public function execute()
    {
        $app = JFactory::getApplication();
        $view = $app->input->get('view');

        $msg = JText::_('Entry cancelled!');
        $app->redirect('index.php?view='.$view,$msg);
    }
}
