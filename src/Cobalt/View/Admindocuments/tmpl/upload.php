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
defined( '_CEXEC' ) or die( 'Restricted access' );
?>
<form id="upload_form" action="<?php echo RouteHelper::_('index.php?task=upload'); ?>" method="post" enctype="multipart/form-data">


    <div class="btn-group">
        <div class="btn btn-default btn-file">
            <i class="glyphicon glyphicon-plus"></i>  <?php echo TextHelper::_('COBALT_UPLOAD_FILE'); ?> <input type="file" id="upload_input_invisible" name="document" />
        </div>
    </div>



    <input type="hidden" name="association_id" value="<?php echo $company['id']; ?>" />
    <input type="hidden" name="association_type" value="company">
    <input type="hidden" name="return" value="<?php echo base64_encode(JUri::current()); ?>" />
</form>