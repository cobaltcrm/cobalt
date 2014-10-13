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

$n = count($this->deals);
for ($i=0; $i<$n; $i++) {
    $deal = $this->deals[$i];
    $k = $i%2;
    $view = \Cobalt\Factory::getView('deals','deal_dock_entry','phtml',array('deal'=>$deal,'offset'=>$k));
    echo $view->render();
}
