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

<h1><?php echo ucwords($this->import_header); ?></h1>
<div class="step">
	<div class="title">
		<?php echo CRMText::_('COBALT_STEP_ONE'); ?>
	</div>
	<div class="text">
		<h2><?php echo CRMText::_('COBALT_EXPORT_YOUR_FILE'); ?></h2>
		<p><?php echo CRMText::_('COBALT_EXPORT_YOUR_FILE_INSTRUCTIONS'); ?></p>
	</div>
</div>
<div class="step">
	<div class="title">
		<?php echo CRMText::_('COBALT_STEP_TWO'); ?>
	</div>
	<div class="text">
		<h2><?php echo CRMText::_('COBALT_ENSURE_YOUR_FILE_IS_FORMATTED'); ?></h2>
			<p><?php echo CRMText::_('COBALT_ENSURE_YOUR_FILE_IS_FORMATTED_INSTRUCTIONS'); ?>
			<form id="download_import_template" method="POST">
				<p><input class="button" onclick="downloadImportTemplate()" type="button" value="<?php echo CRMText::_('COBALT_DOWNLOAD_TEMPLATE'); ?>" /></p>
				<input type="hidden" name="template_type" value="<?php echo $this->import_type; ?>" />
			</form>
	</div>
</div>
<div class="step">
	<div class="title">
		<?php echo CRMText::_('COBALT_STEP_THREE'); ?>
	</div>
	<div class="text">
		<h2><?php echo CRMText::_('COBALT_UPLOAD_YOUR_FILE'); ?></h2>
		<p><?php echo CRMText::_('COBALT_SELECT_YOUR_CSV'); ?>
			<form id="upload_form" action="<?php echo JRoute::_('index.php?view=import&layout=review'); ?>" method="POST" enctype="multipart/form-data">
	        <div class="input_upload_button" >
	        	<input type="hidden" name="type" value="people" />
	            <input class="button" type="button" id="upload_button" value="<?php echo CRMText::_('COBALT_UPLOAD_FILE'); ?>" />
	            <input type="hidden" name="import_type" value="<?php echo $this->import_type; ?>" />
	            <input type="file" id="upload_input_invisible" name="document" />
	        </div>
	        </form>
    	</p>
	</div>
</div>
