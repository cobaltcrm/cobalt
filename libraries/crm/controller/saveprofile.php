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

class CobaltControllerSaveProfile extends CobaltControllerDefault
{

    public function execute()
    {
        //set error
        $error = true;

        $app = JFactory::getApplication();

        $data['id'] = CobaltHelperUsers::getUserId();

        //get model and store data
        $model = new CobaltModelUser();
        if ( $model->store() ) {
            $error = false;
        }

        //return results
        $results = array ( 'error' => $error );

        if ( array_key_exists('fullscreen',$data) ) {
            $append = CobaltHelperUsers::isFullscreen() ? "/?&tmpl=component" : "" ;
            $results['url'] = JRoute::_($data['url'].$append);
        }

        echo json_encode($results);

    }

}
