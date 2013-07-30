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

class CobaltControllerSaveCf extends CobaltControllerDefault
{

        function execute()
        {
            $return = array();
            $app = JFactory::getApplication();

            //get post data
            $data = $app->input->getRequest('post');
            //get db Object
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $table = $data['table'];
            $loc = $data['loc'];
            unset($data['table']);
            unset($data['loc']);

            //write to tables if there is no association already in cf tables
            $query->select('* FROM #__'.$table.'_cf');

            //loop to see if we have matches in database
            $overrides = array('tmpl');
            foreach ($data as $key => $value) {
                if ( !in_array($key,$overrides) ) {
                    $query->where($key . " = '" . $value . "'");
                }
            }

            $db->setQuery($query);
            $results = $db->loadAssocList();

            //determine if we found any results
            if ( count($results) == 0 ) {

                $query->insert('#__'.$table.'_cf');
                //timestamp
                $data['created'] = date('Y-m-d H:i:s');
                $date = CobaltHelperDate::formatDBDate(date('Y-m-d H:i:s'));
                //loop through data to get query string
                foreach ($data as $key => $value) {
                    if ( !in_array($key,$overrides) ) {
                        // determine key and key values
                        $query->set($key . " = '" . $value . "'");
                    }
                }
                //execute query
                $db->setQuery($query);
                $db->query();

                //if return data requested
                if ($table == 'people') {

                    //determine which page we want are wanting to send information back to
                    if ($loc == 'deal') {
                        $model = new CobaltModelPeople();
                        $return = $model->getPerson(array_key_exists('person_id',$data) ? $data['person_id']:"");
                        $return = $return[0];
                    }

                    if ($loc == 'person') {
                        $model = new CobaltModelDeal();
                        $return = $model->getDeals(array_key_exists('deal_id',$data) ? $data['deal_id']:"");
                        $return = $return[0];
                    }
                }

            } else {

                $return = array('error'=>true);

            }

            //return json data
            echo json_encode($return);

        }

}
