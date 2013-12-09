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

use JRoute;
use Cobalt\Model\People as PeopleModel;
use Cobalt\Model\Deal as DealModel;
use Cobalt\Model\Company as CompanyModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class GetTaskAssociations extends DefaultController
{

    public function execute()
    {
        //open model
        $model = new PeopleModel;
        //retrieve all people
        $people = $model->getPeopleList();
        if ( count($people) ) {
            foreach ($people as $index => $row) {

                $people[$index]['type'] = "person";
                $people[$index]['association_link'] = JRoute::_('index.php?view=people&layout=person&id='.$row['id']);
            }
        }

        //open model
        $model = new DealModel;
        //retrieve all people
        $deals = $model->getDealList();
        if ( count($deals) ) {
            foreach ($deals as $index => $row) {
                $deals[$index]['type'] = 'deal';
                $deals[$index]['association_link'] = JRoute::_('index.php?view=deals&layout=deal&id='.$row['id']);
            }
        }

        //open model
        $model = new CompanyModel;
        //retrieve all people
        $companies = $model->getCompanyList();
        if ( count($companies) ) {
            foreach ($companies as $index => $row) {
                $companies[$index]['type'] = 'company';
                $companies[$index]['association_link'] = JRoute::_('index.php?view=companies&layout=company&id='.$row['id']);
            }
        }

        //merge our results to a grand object
        $results = array_merge($people,$deals,$companies);

        //return results as json object
        echo json_encode($results);

    }

}
