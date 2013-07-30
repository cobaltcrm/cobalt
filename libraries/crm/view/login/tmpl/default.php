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
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<form action="index.php?controller=login" method="post" class="form-horizontal">
    <fieldset class="well">
        <div class="control-group">
            <div class="control-label">
                <label id="username-lbl" for="username" class=""><?php echo CRMText::_('COBALT_USER_NAME'); ?></label>
            </div>
            <div class="controls">
                <input type="text" name="username" id="username" value="" class="validate-username" size="25">
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <label id="password-lbl" for="password" class=""><?php echo CRMText::_('COBALT_PASSWORD'); ?></label>
            </div>
            <div class="controls">
                <input type="password" name="password" id="password" value="" class="validate-password" size="25">
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <button type="submit" class="btn btn-primary"><?php echo CRMText::_('COBALT_LOG_IN'); ?></button>
            </div>
        </div>
        <input type="hidden" name="return" value="<?php echo base64_encode('index.php?view=dashboard'); ?>">
    </fieldset>
</form>
