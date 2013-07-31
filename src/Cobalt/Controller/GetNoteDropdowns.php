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

use Cobalt\Model\People as PeopleModel;
use Cobalt\Model\Deal as DealModel;
use Cobalt\Model\Note as NoteModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class GetNoteDropdowns extends DefaultController
{
    //ajax function to populate note dropdowns
    public function execute()
    {
        //grab people
        $model = new PeopleModel;
        $people = $model->getPeopleList();

        //grab deals
        $model = new DealModel;
        $deals = $model->getDealList();

        //grab notes categories
        $model = new NoteModel;
        $categories = $model->getNoteCategories();

        //construct data obj
        $data = array('people'=>$people,'deals'=>$deals,'categories'=>$categories);

        //encode and return results
        echo json_encode($data);

    }

}
