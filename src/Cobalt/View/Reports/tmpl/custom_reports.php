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

<script type="text/javascript">
    order_url = "<?php echo RouteHelper::_('index.php?view=reports&layout=custom_reports_filter&tmpl=component&format=raw'); ?>";
    order_dir = "<?php echo $this->state->get('Report.filter_order_Dir'); ?>";
    order_col = "<?php echo $this->state->get('Report.filter_order'); ?>";
</script>

<div class="page-header">
    <div class="btn-group pull-right">
        <a class="btn" href="<?php echo RouteHelper::_('index.php?view=reports&layout=edit_custom_report'); ?>"><i class="glyphicon glyphicon-plus-sign"></i> <?php echo TextHelper::_('COBALT_NEW_CUSTOM_REPORT'); ?></a>
    </div>
    <h1><?php echo TextHelper::_('COBALT_CUSTOM_REPORTS'); ?></h1>
</div>

<?php echo $this->menu; ?>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th><div class="sort_order"><a class="report.name" onclick="sortTable('report.name',this)"><?php echo TextHelper::_('COBALT_CUSTOM_REPORT_NAME'); ?></a></div></th>
            <th><div class="sort_order"><a class="report.modified" onclick="sortTable('report.modified',this)"><?php echo TextHelper::_('COBALT_CUSTOM_REPORT_MODIFIED'); ?></a></div></th>
            <th><div class="sort_order"><a class="report.created" onclick="sortTable('report.created',this)"><?php echo TextHelper::_('COBALT_CUSTOM_REPORT_CREATED'); ?></a></div></th>
            <th><?php echo TextHelper::_('COBALT_CUSTOM_REPORT_ACTIONS'); ?></th>
        </tr>
    </thead>
    <tbody class="results">
        <?php echo $this->custom_reports_list->render(); ?>
    </tbody>
</table>
