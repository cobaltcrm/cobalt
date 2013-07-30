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

class CobaltControllerToggleMailingList extends CobaltControllerDefault
{

    public function execute()
    {
        $app = JFactory::getApplication();
        $data = $app->input->getRequest('post');
        $subscribe = $data['subscribe'];
        $success = FALSE;
        if (!$subscribe) {
            if ( CobaltHelperMailinglists::addMailingList($data) ) {
                $success = TRUE;
            }
        } else {
            if ( CobaltHelperMailinglists::removeMailingList($data) ) {
                $success = TRUE;
            }
        }
        $return = array('success'=>$success);
        echo json_encode($return);
       }

}
