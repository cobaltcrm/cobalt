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
use Cobalt\Helper\DealHelper;
use Cobalt\Model\People as PeopleModel;
use Cobalt\Helper\TextHelper;

defined( '_CEXEC' ) or die( 'Restricted access' );

class Raw extends AbstractHtmlView
{
    public function render($tpl = null)
    {
        $app = \Cobalt\Container::fetch('app');

        $deal_id = $app->input->get('deal_id');
        $event_id = $app->input->get('event_id');
        $companyId = $app->input->get('company_id');

        $model = new PeopleModel;
        $model->set('deal_id',$deal_id);
        $model->set('event_id',$event_id);
        $model->set('company_id',$companyId);

        $contacts = $model->getContacts();
        $this->contacts = $contacts;

        if ($deal_id) {
            $primary_contact_id = DealHelper::getPrimaryContact($deal_id);
            $this->primary_contact_id = $primary_contact_id;
        }

        //display view
        echo parent::render();
    }

}
