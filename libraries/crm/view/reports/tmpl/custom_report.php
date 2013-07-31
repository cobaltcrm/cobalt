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

<?php $report = $this->report[0]; ?>
<script type="text/javascript">
    order_url = "<?php echo 'index.php?view=reports&layout=custom_report_filter&format=raw&tmpl=component&id='.$report['id']; ?>";
    order_dir = "<?php echo $this->state->get('Report.'.$report['id'].'_'.$this->layout.'_filter_order_Dir'); ?>";
    order_col = "<?php echo $this->state->get('Report.'.$report['id'].'_'.$this->layout.'_filter_order'); ?>";
</script>
<div class="page-header">
    <div class="btn-group pull-right">
        <a class="btn" href="javascript:void(0)" onclick="printItems(this)"><i class="icon-print"></i> <?php echo CRMText::_('COBALT_PRINT'); ?></a>
        <?php if ( CobaltHelperUsers::canExport() ) {?>
            <a class="btn" href="javascript:void(0)" onclick="exportCsv()"><i class="icon-share"></i> <?php echo CRMText::_('COBALT_EXPORT_CSV'); ?></a>
        <?php } ?>
    </div>
    <h1><?php echo $report['name']; ?></h1>
</div>
<?php echo $this->menu; ?>
<form id="list_form" class="print_form" method="post" target="_blank" action="<?php echo JRoute::_('index.php?view=print'); ?>">
<input type="hidden" id="list_form_layout" name="layout" value="report" />
<input type="hidden" name="model" value="source" />
<input type="hidden" name="report" value="custom_report" />
<input type="hidden" name="custom_report" value="<?php echo $report['id']; ?>" />
<?php echo $this->custom_report_header->render(); ?>
<?php echo $this->custom_report_list->render(); ?>
<?php echo $this->custom_report_footer->render(); ?>
<input type="hidden" name="list_type" value="custom_report" />
<input type="hidden" name="report_id" value="<?php echo $report['id']; ?>" />
</form>
