<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\View\Contacts;

use Joomla\View\AbstractHtmlView;
use JFactory;
use Cobalt\Helper\DealHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class Html extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        $app = JFactory::getApplication();
        $deal_id = $app->input->get('deal_id');
        if ($deal_id) {
            $primary_contact_id = DealHelper::getPrimaryContact($deal_id);
            $this->primary_contact_id = $primary_contact_id;
        }

        //display
        return parent::render();
    }

}
