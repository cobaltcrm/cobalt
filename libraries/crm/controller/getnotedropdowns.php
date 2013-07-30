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

class CobaltControllerGetNoteDropdowns extends CobaltControllerDefault
{

    //ajax function to populate note dropdowns
    public function execute()
    {
        //grab people
        $model = new CobaltModelPeople();
        $people = $model->getPeopleList();

        //grab deals
        $model = new CobaltModelDeal();
        $deals = $model->getDealList();

        //grab notes categories
        $model = new CobaltModelNote();
        $categories = $model->getNoteCategories();

        //construct data obj
        $data = array('people'=>$people,'deals'=>$deals,'categories'=>$categories);

        //encode and return results
        echo json_encode($data);

    }

}
