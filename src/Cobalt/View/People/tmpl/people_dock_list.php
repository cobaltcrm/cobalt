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
defined( '_CEXEC' ) or die( 'Restricted access' );

$c = count($this->people);
    for ($i=0; $i<$c; $i++) {

        $person = $this->people[$i];
        $k=$i%2;
        $data = array(
            array('ref'=>'k','data'=>$k),
            array('ref'=>'person','data'=>$person)
            );
        $person_entry = \Cobalt\Factory::getView('people','people_dock_entry','phtml',array('person'=>$person,'k'=>$k));
        echo $person_entry->render();
    }
