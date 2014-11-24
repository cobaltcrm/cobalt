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

use Cobalt\Model\Template as TemplateModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class CreateTemplate extends DefaultController
{
    public function execute()
    {
        $return = array();
        $return['success'] = FALSE;

        $model = new TemplateModel;

        if ( $model->createTemplate() ) {
            $return['success'] = TRUE;
        }

        echo json_encode($return);
    }

}
