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

class CobaltControllerUploadAvatar extends CobaltControllerDefault
{

    public function execute()
    {
        $app = JFactory::getApplication();
         $model = new CobaltModelAvatar();
        $item_id = $app->input->get('item_id');
        if ( $avatar = $model->saveAvatar() ) {

            echo '<script type="text/javascript">
                    window.top.window.modalMessage("'.CRMText::_('COBALT_SUCCESS_MESSAGE').'","'.CRMText::_('COBALT_AVATAR_UPLOAD_SUCCESS').'");
                    window.top.window.updateAvatar('.$item_id.',"'.$avatar.'");
                    </script>';
        } else {
            echo '<script type="text/javascript">
                        window.top.window.modalMessage("'.CRMText::_('COBALT_ERROR').'","'.CRMText::_('COBALT_AVATAR_UPLOAD_ERROR').'");
                        </script>';
        }
    }

}
