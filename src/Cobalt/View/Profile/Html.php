<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Profile;

use Joomla\View\AbstractHtmlView;
use Cobalt\Factory;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render()
    {
        $app = Factory::getApplication();

        //get user data and pass to view
        $this->user = $app->getUser();
        $this->user_id = $this->user->get('id');
        $this->user->getEmails();

        //display
        return parent::render();
    }

}
