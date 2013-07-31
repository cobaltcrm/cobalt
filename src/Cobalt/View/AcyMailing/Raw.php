<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\AcyMailing;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

use Joomla\View\AbstractHtmlView;
use Cobalt\Helper\MailinglistsHelper;

class Raw extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        $layout = $this->getLayout();

        switch ($layout) {
            case "manage":
                $this->lists = MailinglistsHelper::getMailingLists(TRUE);
            break;
            case "links":
                $this->links = MailinglistsHelper::getLinks();
            break;
        }

        //display
        echo parent::render($tpl);
    }

}
