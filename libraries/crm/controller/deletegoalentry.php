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

class CobaltControllerDeleteGoalEntry extends CobaltControllerDefault
{

    public function execute()
    {
        //get id to delete
        $app = JFactory::getApplication();
        $goal_id = $app->input->get('goal_id');

        //get db
        $db =& JFactory::getDBO();
        $query = $db->getQuery(true);

        //form query
        $query->delete("#__goals")->where("id=$goal_id");

        //set query
        $db->setQuery($query);
        $results = array();
        if ( $db->query() ) {
            $results['error'] = 0;
        } else {
            $results['error'] = 1;
            print_r($db);
        }

        //return success
        echo json_encode($results);
    }

}
