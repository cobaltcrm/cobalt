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

<h1><?php echo CRMText::_('COBALT_REVIEW_YOUR_IMPORT'); ?></h1>
<?php if ( count($this->import_data) > 0 ){ ?>
	<p><?php echo CRMText::_('COBALT_REVIEW_IMPORT_MESSAGE'); ?></p>
<?php } else { ?>
	<p><?php echo CRMText::_('COBALT_REVIEW_IMPORT_MESSAGE_ERROR'); ?></p>
<?php } ?>
<?php if ( count($this->import_data) > 1 ) { ?>
	<div id="import_seek">
			<span>
			<?php echo CRMText::_('COBALT_VIEWING_ENTRY'); ?>
			<span id="viewing_entry">1</span>
			<?php echo CRMText::_('COBALT_OF'); ?>
			<?php echo ' '.count($this->import_data); ?>
			<a href="javascript:void(0)" onclick="seekImport(-1);"><?php echo CRMText::_('COBALT_PREVIOUS'); ?></a> -
			<a href="javascript:void(0)" onclick="seekImport(1);"><?php echo CRMText::_('COBALT_NEXT'); ?></a>
	</div>
<?php } ?>
<form action="<?php echo JRoute::_('index.php?controller=import'); ?>" method="POST">
		<div id="editForm">
		<?php if ( isset($this->import_data) && count($this->import_data) > 0 ) { try { foreach ( $this->import_data as $key => $data ) { ?>
		<?php if ( $key > 0 ){ $style = "style='display:none;'"; } else { $style = ""; } ?>
		<div <?php echo $style; ?> id="import_entry_<?php echo $key; ?>">
				<?php $memoryFlag = false; ?>
				<?php foreach ( $data as $field => $value ){ ?>
					<?php 
						$scriptMemory = memory_get_peak_usage(true);
						$serverMemory = CobaltHelperCobalt::getBytes(ini_get('memory_limit'));
						if ( $scriptMemory >= $serverMemory) {
							$memoryFlag = true;
							?>
							<p><?php echo CRMText::_('COBALT_REVIEW_IMPORT_MESSAGE_FILE_TOO_LARGE'); ?></p> 
							<?php
							return;
						}
					 ?>
					<?php $header = ( $array_key = array_search($field,$this->headers) ) ? $this->headers[$array_key] : $field; ?>
					<div class="cobaltRow">
					<div class="cobaltField"><?php echo ucwords(str_replace('_',' ',str_replace('id','Name',$header))); ?></div>
					    <?php if (is_array($value)){ ?>
						<?php if ( array_key_exists('dropdown',$value) ){ ?>
							<div class="cobaltValue wide"><?php echo $value['dropdown']; ?></div>
						<?php } else if ( array_key_exists('value',$value)) { ?>
							<div class="cobaltValue wide">
									<input type="hidden" name="import_id[<?php echo $key; ?>][<?php echo $field; ?>]" value="<?php echo $value['value']; ?>" >
									<input class="inputbox" name="import_id[<?php echo $key; ?>][<?php echo str_replace('id','name',$field); ?>]" value="<?php echo $value['label']; ?>" /></div>
						<?php } else { ?>
							<div class="cobaltValue wide"><?php echo CRMText::_('COBALT_NO_RESULTS_FOUND'); ?></div>
						<?php } ?>
					<?php } else { ?>
						<div class="cobaltValue wide"><input class="inputbox" type="text" name="import_id[<?php echo $key; ?>][<?php echo $field; ?>]" value="<?php echo $value; ?>" /></div>
					<?php } ?>
					</div>
					<?php } ?>
					</div>
			<?php }  ?>
		<?php }catch(Exception $e){ ?>
				
		<?php } }?>
		<?php if ($memoryFlag){ ?>

		<?php } ?>
		</div>
	<div class="import_buttons">
		<input class="button" type="submit" value="<?php echo CRMText::_('COBALT_SUBMIT'); ?>" /><a onclick="window.location.href='<?php echo JRoute::_('index.php?view='.$this->import_type); ?>'"><?php echo CRMText::_('COBALT_CANCEL'); ?></a>
	</div>
	<input type="hidden" name="import_type" value="<?php echo $this->import_type; ?>" />
</form>