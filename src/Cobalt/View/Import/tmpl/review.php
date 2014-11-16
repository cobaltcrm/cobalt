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

$memoryFlag = false;
?>

<h1><?php echo TextHelper::_('COBALT_REVIEW_YOUR_IMPORT'); ?></h1>
<?php if ( count($this->import_data) > 0 ) { ?>
    <p><?php echo TextHelper::_('COBALT_REVIEW_IMPORT_MESSAGE'); ?></p>
<?php } else { ?>
    <p><?php echo TextHelper::_('COBALT_REVIEW_IMPORT_MESSAGE_ERROR'); ?></p>
<?php } ?>

<form action="<?php echo RouteHelper::_('index.php?task=import'); ?>" method="post" class="form-horizontal" role="form" id="editForm">

    <?php if ( isset($this->import_data) && count($this->import_data) > 0 ) { try { foreach ($this->import_data as $key => $data) { ?>
    <?php if ($key > 0) { $style = "style='display:none;'"; } else { $style = ""; } ?>
    <div <?php echo $style; ?> id="import_entry_<?php echo $key; ?>" class="imported_row">
            <?php foreach ($data as $field => $value) { ?>
                <?php
                    $scriptMemory = memory_get_peak_usage(true);
                    $serverMemory = CobaltHelper::getBytes(ini_get('memory_limit'));
                    if ($scriptMemory >= $serverMemory) {
                        $memoryFlag = true;
                        ?>
                        <p><?php echo TextHelper::_('COBALT_REVIEW_IMPORT_MESSAGE_FILE_TOO_LARGE'); ?></p>
                        <?php

                        return;
                    }
                 ?>
                <?php $header = ( $array_key = array_search($field,$this->headers) ) ? $this->headers[$array_key] : $field; ?>
                <div class="form-group">
                <label class="col-sm-2 control-label">
                    <?php echo ucwords(str_replace('_',' ',str_replace('id','Name',$header))); ?>
                </label>
                <?php if (is_array($value)) { ?>
                <?php if ( array_key_exists('dropdown',$value) ) { ?>
                    <div class="col-sm-10"><?php echo $value['dropdown']; ?></div>
                <?php } elseif ( array_key_exists('value',$value)) { ?>
                    <div class="col-sm-10">
                            <input type="hidden" name="import_id[<?php echo $key; ?>][<?php echo $field; ?>]" value="<?php echo $value['value']; ?>" >
                            <input class="form-control" name="import_id[<?php echo $key; ?>][<?php echo str_replace('id','name',$field); ?>]" value="<?php echo $value['label']; ?>" /></div>
                <?php } else { ?>
                    <div class="col-sm-10"><?php echo TextHelper::_('COBALT_NO_RESULTS_FOUND'); ?></div>
                <?php } ?>
                <?php } else { ?>
                    <div class="col-sm-10"><input class="form-control" type="text" name="import_id[<?php echo $key; ?>][<?php echo $field; ?>]" value="<?php echo $value; ?>" /></div>
                <?php } ?>
                </div>
                <?php } ?>
                </div>
        <?php }  ?>
    <?php } catch (Exception $e) { ?>

    <?php } }?>
    <?php if ($memoryFlag) { ?>

    <?php } ?>
    <?php if ( count($this->import_data) > 1 ) { ?>
        <div id="import_seek" class="text-center">
            <a href="#" onclick="Cobalt.seekImport(-1);"><?php echo TextHelper::_('COBALT_PREVIOUS'); ?></a> -
            <a href="#" onclick="Cobalt.seekImport(1);"><?php echo TextHelper::_('COBALT_NEXT'); ?></a>
            <br />
            <span>
            <?php echo TextHelper::_('COBALT_VIEWING_ENTRY'); ?>
            <span id="viewing_entry">1</span>
            <?php echo TextHelper::_('COBALT_OF'); ?>
            <?php echo ' '.count($this->import_data); ?>
        </div>
    <?php } ?>
    <div class="import_buttons">
        <input class="btn btn-primary" type="submit" value="<?php echo TextHelper::_('COBALT_SUBMIT'); ?>" />
        <a href="<?php echo RouteHelper::_('index.php?view='.$this->import_type); ?>">
            <?php echo TextHelper::_('COBALT_CANCEL'); ?>
        </a>
    </div>
    <br />
    <input type="hidden" name="import_type" value="<?php echo $this->import_type; ?>" />
</form>
