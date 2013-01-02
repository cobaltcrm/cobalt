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

class CobaltControllerRemovePersonFromDeal extends CobaltControllerDefault
{

    function execute(){

    	$app = JFactory::getApplication();

        $person_id = $app->input->get('person_id');
        $deal_id = $app->input->get('deal_id');

        $db =& JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select("COUNT(*)")
                ->from("#__people")
                ->where("id=".$person_id);
        $db->setQuery($query);
        $count = $db->loadResult();

        if ( $count ){
            $query->clear()
                ->delete("#__people_cf")
                ->where("association_id=".$deal_id)
                ->where("association_type='deal'")
                ->where("person_id=".$person_id);
            $db->setQuery($query);
            if ( $db->query() ){
                $success = true;
            }else{
                $success = false;
            }
        }else{
            $success = false;
        }

        $data = array('success'=>$success);
        echo json_encode($data);
   }

}