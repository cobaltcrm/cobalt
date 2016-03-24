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

<div class="page-header">
    <div class="btn-group pull-right">
        <a class="btn btn-default" href="javascript:void(0)" onclick="Cobalt.printItems('#list_form')"><?php echo TextHelper::_('COBALT_PRINT'); ?></a>
        <?php if ( UsersHelper::canExport() ) {?>
            <a class="btn btn-default" href="javascript:void(0)" onclick="Cobalt.exportCsv()"><?php echo TextHelper::_('COBALT_EXPORT_CSV'); ?></a>
        <?php } ?>
    </div>
    <div class="col-xs-5 col-sm-6 col-md-5 va-m"><h3><?php echo ucwords(TextHelper::_('COBALT_ROI_REPORT')); ?></h3></div>
</div>

<?php echo $this->menu; ?>
<script type="text/javascript">
    order_url = "<?php echo 'index.php?view=reports&layout=roi_report_filter&tmpl=component&format=raw'; ?>";
    order_dir = "<?php echo $this->state->get('Source.filter_order_Dir'); ?>";
    order_col = "<?php echo $this->state->get('Source.filter_order'); ?>";
</script>
<form id="list_form" class="print_form" method="post" target="_blank" action="<?php echo RouteHelper::_('index.php?view=printFriendly'); ?>">
<input type="hidden" name="layout" value="report" />
<input type="hidden" name="model" value="source" />
<input type="hidden" name="report" value="roi_report" />
    <?php echo $this->roi_report_header->render(); ?>
    <?php echo $this->roi_report_list->render(); ?>
    <?php echo $this->roi_report_footer->render(); ?>
<input type="hidden" name="list_type" value="roi_report" />
</form>
