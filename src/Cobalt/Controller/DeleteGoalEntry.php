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

class DeleteGoalEntry extends DefaultController
{
    public function execute()
    {
        $goal_id = $this->getInput()->get('goal_id');

        //get db
        $db = $this->getContainer()->get('db');
        $query = $db->getQuery(true);

        //form query
        $query->delete("#__goals")->where("id=".$db->quote($goal_id));

        //set query
        $db->setQuery($query);
        $results = array();
        if ( $db->execute() ) {
            $results['error'] = 0;
        } else {
            $results['error'] = 1;
        }

        //return success
        echo json_encode($results);
    }

}
