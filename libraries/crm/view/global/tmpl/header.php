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
/*
if (CobaltHelperUsers::getLoggedInUser()) {
  if ( CobaltHelperUsers::isFullscreen() && !(JFactory::getApplication()->input->get('view')=="print") ) {
?>
    <div id="logged_in_user"><?php echo CobaltHelperConfig::getConfigValue('welcome_message'); ?> <?php CobaltHelperUsers::getFirstName()."!";
                    echo self::displayLogout();
                echo '</div>';
            }

            echo '<script type="text/javascript">var userDateFormat = "'.CobaltHelperUsers::getDateFormat(FALSE).'";</script>';
            echo '<script type="text/javascript">var user_id = "'.CobaltHelperUsers::getUserId().'";</script>';
        }

  }
}
*/
?>
<div id="com_cobalt">
    <div id="message" style="display:none;"></div>
    <div id="google-map" style="display:none;"></div>
    <div id="edit_note_entry" style="display:none;"></div>
    <div id="edit_convo_entry" style="display:none;"></div>
    <div id="document_preview_modal" style="display:none;"></div>
    <?php echo CobaltHelperTemplate::getEventDialog(); ?>
    <?php echo CobaltHelperTemplate::getAvatarDialog(); ?>
    <?php if ($this->isMobile) { ?>
      <div class='page' data-role='page' data-theme='b' id=''>
    <?php }
