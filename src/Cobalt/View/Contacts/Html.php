<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class CobaltViewContactsHtml extends JViewHtml
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
