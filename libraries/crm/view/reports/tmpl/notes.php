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
$app = JFactory::getApplication();
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
        <a class="btn" href="javascript:void(0)" onclick="printItems(this);"><?php echo CRMText::_('COBALT_PRINT'); ?></a>
        <?php if ( CobaltHelperUsers::canExport() ) {?>
            <a class="btn" href="javascript:void(0)" onclick="exportCsv()"><?php echo CRMText::_('COBALT_EXPORT_CSV'); ?></a>
        <?php } ?>
    </div>
    <h1><?php echo CRMText::_('COBALT_NOTES_REPORT'); ?></h1>
</div>
<?php echo $this->menu; ?>
<form id="list_form" class="print_form" method="post" target="_blank" action="<?php echo JRoute::_('index.php?view=print'); ?>">
<input type="hidden" name="layout" value="report" />
<input type="hidden" name="model" value="note" />
<input type="hidden" name="report" value="notes" />

<?php echo $this->notes_header->render(); ?>
<?php echo $this->notes_list->render(); ?>
<?php echo $this->notes_footer->render(); ?>
<input type="hidden" name="list_type" value="notes" />
</form>
