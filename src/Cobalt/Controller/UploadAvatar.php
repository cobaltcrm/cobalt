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

use Cobalt\Helper\TextHelper;
use Cobalt\Model\Avatar as AvatarModel;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class UploadAvatar extends DefaultController
{
    public function execute()
    {
        $model = new AvatarModel;
        $item_id = $this->input->get('item_id');

        if ( $avatar = $model->saveAvatar() ) {

            echo '<script type="text/javascript">
                    window.top.window.modalMessage("'.TextHelper::_('COBALT_SUCCESS_MESSAGE').'","'.TextHelper::_('COBALT_AVATAR_UPLOAD_SUCCESS').'");
                    window.top.window.updateAvatar('.$item_id.',"'.$avatar.'");
                    </script>';
        } else {
            echo '<script type="text/javascript">
                        window.top.window.modalMessage("'.TextHelper::_('COBALT_ERROR').'","'.TextHelper::_('COBALT_AVATAR_UPLOAD_ERROR').'");
                        </script>';
        }
    }

}
