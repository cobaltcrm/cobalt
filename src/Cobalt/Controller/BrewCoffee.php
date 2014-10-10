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

defined( '_CEXEC' ) or die( 'Restricted access' );

use Cobalt\Helper\MailHelper;

class BrewCoffee extends DefaultController
{
    public function execute()
    {
        $db = $this->container->get('db');
        $query = $db->getQuery(true);
        $query->select('u.id, u.first_name, u.last_name, user.email');
        $query->from('#__users AS u');
        $query->leftJoin('#__users AS user ON u.id = user.id');
        $query->where('u.morning_coffee=1');

        $db->setQuery($query);
        $people = $db->loadObjectList();

        $p = count($people);
        for ($i=0;$i<$p;$i++) {
            $person = $people[$i];
            $layout = MailHelper::loadStats($person->id);
            $layout->user = $person;

            MailHelper::sendMail($layout, $person->email);
        }

    }

}
