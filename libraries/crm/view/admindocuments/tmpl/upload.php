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
<form action="index.php?view=admindocuments&format=raw" method="post" name="adminForm" id="adminForm" class="inline-form form-validate" enctype="multipart/form-data" >
    <input type="file" class="input-file" name="document" />
    <input type="submit" class="btn btn-primary" value="<?php echo CRMText::_('COBALT_UPLOAD'); ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>
