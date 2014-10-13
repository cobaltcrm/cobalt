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

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class RemovePersonFromDeal extends DefaultController
{
    public function execute()
    {
        $person_id = $this->getInput()->get('person_id');
        $deal_id = $this->getInput()->get('deal_id');

        $db = $this->getContainer()->get('db');
        $query = $db->getQuery(true);

        $query->select("COUNT(*)")
                ->from("#__people")
                ->where("id=".$person_id);

        $count = $db->setQuery($query)->loadResult();

        if ($count) {
            $query->clear()
                ->delete("#__people_cf")
                ->where("association_id=".$deal_id)
                ->where("association_type='deal'")
                ->where("person_id=".$person_id);
            $db->setQuery($query);
            if ( $db->execute() ) {
                $success = true;
            } else {
                $success = false;
            }
        } else {
            $success = false;
        }

        echo json_encode(array('success' => $success));
   }

}
