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
defined( '_JEXEC' ) or die( 'Restricted access' );

class CobaltControllerBrewCoffee extends CobaltControllerDefault
{

    public function execute()
    {
        $db =& JFactory::getDBO();
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
            $layout = CobaltHelperMail::loadStats($person->id);
            $layout->user = $person;

            CobaltHelperMail::sendMail($layout, $person->email);
        }

    }

}
