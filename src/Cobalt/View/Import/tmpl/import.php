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

<h1><?php echo ucwords($this->import_header); ?></h1>
<div class="row">
    <div class="col-sm-2">
        <h2><?php echo TextHelper::_('COBALT_STEP_ONE'); ?></h2>
    </div>
    <div class="col-sm-10">
        <h3 class="help-block"><?php echo TextHelper::_('COBALT_EXPORT_YOUR_FILE'); ?></h3>
        <p class="help-block"><?php echo TextHelper::_('COBALT_EXPORT_YOUR_FILE_INSTRUCTIONS'); ?></p>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-sm-2">
        <h2><?php echo TextHelper::_('COBALT_STEP_TWO'); ?></h2>
    </div>
    <div class="col-sm-10">
        <h3 class="help-block"><?php echo TextHelper::_('COBALT_ENSURE_YOUR_FILE_IS_FORMATTED'); ?></h3>
        <p class="help-block"><?php echo TextHelper::_('COBALT_ENSURE_YOUR_FILE_IS_FORMATTED_INSTRUCTIONS'); ?></p>
        <form id="download_import_template" method="post" action="index.php?task=downloadImportTemplate&tmpl=component&format=raw">
            <p>
                <input class="btn btn-primary" type="submit" value="<?php echo TextHelper::_('COBALT_DOWNLOAD_TEMPLATE'); ?>" />
            </p>
            <input type="hidden" name="template_type" value="<?php echo $this->import_type; ?>" />
        </form>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-sm-2">
        <h2><?php echo TextHelper::_('COBALT_STEP_THREE'); ?></h2>
    </div>
    <div class="col-sm-10">
        <h3 class="help-block"><?php echo TextHelper::_('COBALT_UPLOAD_YOUR_FILE'); ?></h3>
        <p class="help-block"><?php echo TextHelper::_('COBALT_SELECT_YOUR_CSV'); ?></p>
        <form id="upload_form" action="<?php echo RouteHelper::_('index.php?view=import&layout=review'); ?>" method="post" enctype="multipart/form-data">
            <div class="btn-group">

                <div class="btn btn-default btn-file">
                    <i class="glyphicon glyphicon-plus"></i>  Upload File <input type="file" name="document" id="upload_input_invisible">
                </div>
            </div>
            <input type="hidden" name="type" value="people" />
            <input type="hidden" name="import_type" value="<?php echo $this->import_type; ?>" />
        </form>
    </div>
</div>