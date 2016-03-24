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
defined( '_CEXEC' ) or die( 'Restricted access' );  ?>

<script type="text/javascript">
    var order_url = "<?php echo RouteHelper::_('index.php?view=reports&layout=custom_reports_filter&tmpl=component&format=raw'); ?>";
</script>
<div class="col-xs-5 col-sm-6 col-md-5 va-m"><h3><?php echo ucwords(TextHelper::_('COBALT_CUSTOM_REPORTS')); ?></h3></div>
<?php echo $this->menu; ?>
<h2><?php echo TextHelper::_('COBALT_SELECT_CUSTOM_COLUMNS'); ?></h2>
<p><?php echo TextHelper::_('COBALT_CUSTOM_COLUMNS_MESSAGE'); ?></p>
<div id="custom_field_columns">
    <ul class="columns">
        <?php $this->columns = (array_key_exists('report',$this)) ? array_diff($this->columns,unserialize($this->report[0]['fields'])) : $this->columns; ?>
        <?php foreach ($this->columns as $key=>$text) { ?>
            <li class="data" id="<?php echo $key; ?>" name="<?php echo $text; ?>"><?php echo $text; ?></li>
        <?php } ?>
    </ul>
</div>
<div id="custom_field_holders">
    <ul class="holders">
        <?php $count = ( array_key_exists('report',$this) ) ? count($this->report) : 0; ?>
        <?php $field_count = 0; ?>
        <?php if ($count > 0) { ?>
            <?php $fields = unserialize($this->report[0]['fields']); ?>
            <?php foreach ($fields as $id => $text) { ?>
                <?php $field_count++; ?>
                <li class="added_data" id="<?php echo $id; ?>" name="<?php echo $text; ?>"><?php echo $text; ?><div class="remove"><a onclick="remove(this)" class="remove"></a></div></li>
            <?php } ?>
        <?php } ?>
        <?php for ($i=0; $i<10-$field_count; $i++) { ?>
            <li class="holder"><?php echo TextHelper::_('COBALT_DRAG_FIELD_HERE'); ?></li>
        <?php } ?>
    </ul>
</div>
<form action="index.php?task=editReport" onsubmit="return validateCustomForm(this);" method="post">
    <div class="custom_report_inputs">
        <p><?php echo TextHelper::_('COBALT_NAME_REPORT'); ?>:</p><input class="form-control required" type="text" name="name" value="<?php if ( array_key_exists('report',$this) ) echo $this->report[0]['name']; ?>">
        <p><input class="btn btn-success" type="submit" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>"> - <?php echo TextHelper::_('COBALT_OR'); ?> - <a href="javascript:void(0);" onclick="window.history.back()"><?php echo TextHelper::_('COBALT_CANCEL_BUTTON'); ?></a></p>
    </div>
    <div id="post">
        <?php if ( array_key_exists('report',$this) ) { ?>
            <input type="hidden" name="id" value="<?php echo $this->report[0]['id']; ?>">
        <?php } ?>
    </div>
</form>
