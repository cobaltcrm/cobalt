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

use Cobalt\Factory;
use Cobalt\Helper\TextHelper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

class UploadAvatar extends DefaultController
{
    public function execute()
    {
        $model = Factory::getModel('Avatar');

        /** @var \Joomla\Registry\Registry $state */
        $state = $model->getState();
        $state->set('item_type', $app->input->get('item_type'));
        $state->set('item_id', $app->input->get('item_id'));

        $model->setState($state);

        $avatar = $model->saveAvatar();

        if (empty($avatar)) {
            echo '<script type="text/javascript">
                    window.top.window.modalMessage("'.TextHelper::_('COBALT_ERROR').'","'.TextHelper::_('COBALT_AVATAR_UPLOAD_ERROR').'");
                  </script>';
        } else {
            echo '<script type="text/javascript">
                    window.top.window.modalMessage("'.TextHelper::_('COBALT_SUCCESS_MESSAGE').'","'.TextHelper::_('COBALT_AVATAR_UPLOAD_SUCCESS').'");
                    window.top.window.updateAvatar('.$state->get('item_id').',"'.$avatar.'");
                  </script>';
        }
    }

}
