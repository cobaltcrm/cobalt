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
defined( '_CEXEC' ) or die( 'Restricted access' );?>

<script type="text/javascript">
    var loc = 'companies';
    order_url = "<?php echo 'index.php?view=companies&layout=list&format=raw&tmpl=component'; ?>";
    order_dir = "<?php echo $this->state->get('Company.filter_order_Dir'); ?>";
    order_col = "<?php echo $this->state->get('Company.filter_order'); ?>";
</script>

<div class="page-header">
    <div class="btn-group pull-right">



        <?php if ( UsersHelper::canExport() ): ?>
        <button type="button" href="index.php?view=companies&layout=edit&format=raw&tmpl=component" data-target="#CobaltAjaxModal" data-toggle="modal" class="btn btn-success">
            <i class="glyphicon glyphicon-plus icon-white"></i>
            <?php echo TextHelper::_('COBALT_ADD_COMPANY'); ?>
        </button>
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a href="<?php echo RouteHelper::_('index.php?view=import&import_type=companies'); ?>">
                    <i class="glyphicon glyphicon-arrow-up"></i> <?php echo TextHelper::_('COBALT_IMPORT_COMPANIES'); ?>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" onclick="Cobalt.exportCSV()">
                    <i class="glyphicon glyphicon glyphicon-arrow-down"></i> <?php echo TextHelper::_('COBALT_EXPORT_COMPANIES'); ?>
                </a>
            </li>
        </ul>
        <?php else: ?>
        <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_ADD_COMPANY'); ?>" data-placement="bottom" class="btn btn-success" role="button" href="index.php?view=companies&layout=edit&format=raw&tmpl=component" data-target="#CobaltAjaxModal" data-toggle="modal">
            <i class="glyphicon glyphicon-plus icon-white"></i>
        </a>
        <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_IMPORT_COMPANIES'); ?>" data-placement="bottom"  class="btn btn-default" href="<?php echo RouteHelper::_('index.php?view=import&import_type=companies'); ?>"><i class="glyphicon glyphicon-circle-arrow-up"></i></a>
        <?php endif; ?>


    </div>

    <h1><?php echo ucwords(TextHelper::_('COBALT_COMPANIES')); ?></h1>
</div>
<ul class="list-inline filter-sentence">
    <li><span><?php echo TextHelper::_('COBALT_SHOW'); ?></span></li>
    <li class="dropdown">
        <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="company_type_link" href="javascript:void(0);"><span class="dropdown-label"><?php echo $this->company_type; ?></span></a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="company_type_link" data-filter="item">
            <?php foreach ($this->company_types as $title => $text) { ?>
            <li>
                <a href="#" class="filter_<?php echo $title ?>" data-filter-value="<?php echo $title; ?>">
                    <?php echo $text ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </li>
    <li>
        <span><?php echo TextHelper::_('COBALT_NAMED'); ?></span>
    </li>
    <li>
        <input class="form-control datatable-searchbox" name="company_name" type="text" placeholder="<?php echo TextHelper::_('COBALT_ANYTHING'); ?>" value="<?php echo $this->company_filter; ?>">
    </li>
    <li>
        <div class="ajax_loader"></div>
    </li>
</ul>

<?php echo TemplateHelper::getListEditActions(); ?>
<form method="post" id="list_form" action="<?php echo RouteHelper::_('index.php?view=companies'); ?>">
    <table class="table table-hover table-striped data-table table-bordered" id="deals">
        <?php echo $this->company_list->render(); ?>
    </table>
<input type="hidden" name="list_type" value="companies" />
</form>
<?php /*
<div id="templates" style="display:none;">
    <div id="edit_task" style="display:none;"></div>
    <div id="note_modal" style="display:none;"></div>
    <div id="edit_event" style="display:none;"></div>
    <div id="edit_button"><a class="edit_button_link" id="edit_button_link" href="javascript:void(0)"></a></div>
    <div id="edit_list_modal" style="display:none;" ></div>
</div>
*/
