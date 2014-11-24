<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Mail;

use Joomla\View\AbstractHtmlView;
use Cobalt\Model\Mail as MailModel;

defined( '_CEXEC' ) or die( 'Restricted access' );

class Raw extends AbstractHtmlView
{
    public function display($tpl = null)
    {
        $model = new MailModel;
        $this->mail = $model->getMail();

        //display
        echo parent::render();
    }

}
