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
$app = \Cobalt\Factory::getApplication();
$view = $app->input->get('view');
$layout = $app->input->get('layout');

defined('_CEXEC') or die('Restricted access'); ?>
<script type="text/javascript">
    order_url = "<?php echo 'index.php?view=reports&layout=notes_filter&tmpl=component&format=raw'; ?>";
    order_dir = "<?php echo $this->state->get('Note.'.$view.'.'.$layout.'.filter_order_Dir'); ?>";
    order_col = "<?php echo $this->state->get('Note.'.$view.'.'.$layout.'.filter_order'); ?>";
</script>

<div class="page-header">
    <div class="btn-group pull-right">
        <a class="btn btn-default" href="javascript:void(0)" onclick="Cobalt.printItems('#list_form');"><?php echo TextHelper::_('COBALT_PRINT'); ?></a>
        <?php if ( UsersHelper::canExport() ) {?>
            <a class="btn btn-default" href="javascript:void(0)" onclick="Cobalt.exportCsv()"><?php echo TextHelper::_('COBALT_EXPORT_CSV'); ?></a>
        <?php } ?>
    </div>
    <div class="col-xs-5 col-sm-6 col-md-5 va-m"><h3><?php echo ucwords(TextHelper::_('COBALT_NOTES_REPORT')); ?></h3></div>
</div>
<?php echo $this->menu; ?>
<form id="list_form" class="print_form" method="post" target="_blank" action="<?php echo RouteHelper::_('index.php?view=printFriendly'); ?>">
<input type="hidden" name="layout" value="report" />
<input type="hidden" name="model" value="note" />
<input type="hidden" name="report" value="notes" />

<?php echo $this->notes_header->render(); ?>
<?php echo $this->notes_list->render(); ?>
<?php echo $this->notes_footer->render(); ?>
<input type="hidden" name="list_type" value="notes" />
</form>
