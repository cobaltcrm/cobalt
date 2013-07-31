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

class CobaltViewContactsRaw extends JViewHtml
{
    public function render($tpl = null)
    {
        $app = JFactory::getApplication();

        $deal_id = $app->input->get('deal_id');
        $event_id = $app->input->get('event_id');
        $companyId = $app->input->get('company_id');

        $model = new CobaltModelPeople();
        $model->set('deal_id',$deal_id);
        $model->set('event_id',$event_id);
        $model->set('company_id',$companyId);

        $contacts = $model->getContacts();
        $this->contacts = $contacts;

        if ($deal_id) {
            $primary_contact_id = CobaltHelperDeal::getPrimaryContact($deal_id);
            $this->primary_contact_id = $primary_contact_id;
        }

        //display view
        echo parent::render();
    }

}
