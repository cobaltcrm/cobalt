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
use Cobalt\Helper\UsersHelper;
use Cobalt\Model\User as UserModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class SaveProfile extends DefaultController
{
    public function execute()
    {
        //set error
        $error = true;

        $data['id'] = UsersHelper::getUserId();

        //get model and store data
        $model = new UserModel;
        if ( $model->store() ) {
            $error = false;
        }

        //return results
        $results = array ( 'error' => $error );

        if ( array_key_exists('fullscreen',$data) ) {
            $append = UsersHelper::isFullscreen() ? "/?&tmpl=component" : "" ;
            $results['url'] = RouteHelper::_($data['url'].$append);
        }

        echo json_encode($results);

    }

}
