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
defined( '_CEXEC' ) or die( 'Restricted access' ); ?>
<style type="text/css">
    body {
        padding-top: 0;
        background-color: #0047ab;
        height:100%;
    }
    .container {
        width: 300px;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-top: -206px;
        margin-left: -150px;
    }
    .well {
        padding-bottom: 0;
    }
    h1 {
        margin-top:0;
    }
</style>

<div class="container">
    <div class="well">
        <h1 class="text-center">Cobalt</h1>
        <form action="<?php echo RouteHelper::_('index.php?view=login'); ?>" method="post" role="form">
            <fieldset>
                <div class="form-group">
                    <label id="username-lbl" for="username" class="control-label"><?php echo TextHelper::_('COBALT_USER_NAME'); ?></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon glyphicon glyphicon-user"></i></span>
                        <input type="text" name="username" id="username" value="" class="form-control validate-username" size="25">
                    </div>
                </div>
                <div class="form-group">
                    <label id="password-lbl" for="password" class="control-label"><?php echo TextHelper::_('COBALT_PASSWORD'); ?></label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="icon glyphicon glyphicon-lock"></i></span>
                        <input type="password" name="password" id="password" value="" class="form-control validate-password" size="25">
                    </div>
                </div>
                <div class="form-group">
                    <div class="controls">
                        <button type="submit" class="btn btn-primary"><?php echo TextHelper::_('COBALT_LOG_IN'); ?></button>
                    </div>
                </div>
                <input type="hidden" name="return" value="<?php echo base64_encode('index.php?view=dashboard'); ?>">
            </fieldset>
        </form>
    </div>
</div>
