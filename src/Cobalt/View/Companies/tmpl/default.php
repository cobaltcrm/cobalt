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

<div data-remote="index.php?view=companies&layout=edit&format=raw&tmpl=component" class="modal hide fade" id="companyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo ucwords(TextHelper::_('COBALT_ADD_COMPANY')); ?></h3>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo ucwords(TextHelper::_('COBALT_CANCEL')); ?></button>
                <button onclick="saveItem('edit_form')" class="btn btn-primary"><?php echo ucwords(TextHelper::_('COBALT_SAVE')); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="page-header">
    <div class="btn-group pull-right">
        <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_ADD_COMPANY'); ?>" data-placement="bottom" class="btn btn-success" role="button" href="#companyModal" data-toggle="modal"><i class="glyphicon glyphicon-plus icon-white"></i></a>
        <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_IMPORT_COMPANIES'); ?>" data-placement="bottom"  class="btn btn-default" href="<?php echo RouteHelper::_('index.php?view=import&import_type=companies'); ?>"><i class="glyphicon glyphicon-circle-arrow-up"></i></a>
        <?php if ( UsersHelper::canExport() ) { ?>
        <a rel="tooltip" title="<?php echo TextHelper::_('COBALT_EXPORT_COMPANIES'); ?>" data-placement="bottom" class="btn btn-default" href="javascript:void(0)" onclick="exportCsv()"><i class="glyphicon glyphicon-share"></i></a>
        <?php } ?>
    </div>

    <h1><?php echo ucwords(TextHelper::_('COBALT_COMPANIES')); ?></h1>
</div>
<ul class="inline filter-sentence">
    <li><span><?php echo TextHelper::_('COBALT_SHOW'); ?></span></li>
    <li class="dropdown">
        <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" id="company_type_link" href="javascript:void(0);"><span class="dropdown-label"><?php echo $this->company_type; ?></span></a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="company_type_link">
            <?php foreach ($this->company_types as $title => $text) {
            echo "<li><a href='javascript:void(0);' class='filter_".$title."' onclick=\"companyType('".$title."')\">".$text."</a></li>";
            }?>
        </ul>
    </li>
    <li>
        <span><?php echo TextHelper::_('COBALT_NAMED'); ?></span>
        <input class="inputbox filter_input" name="company_name" type="text" placeholder="<?php echo TextHelper::_('COBALT_ANYTHING'); ?>" value="<?php echo $this->company_filter; ?>">
    </li>
    <li>
        <div class="ajax_loader"></div>
    </li>
</ul>
<small>
    <span id="companies_matched"></span> <?php echo TextHelper::_('COBALT_COMPANIES_MATCHED'); ?> <?php echo TextHelper::_('COBALT_THERE_ARE'); ?> <?php echo $this->company_count; ?> <?php echo TextHelper::_('COBALT_COMPANIES_IN_ACCOUNT'); ?>
</small>
<?php echo TemplateHelper::getListEditActions(); ?>
<form method="post" id="list_form" action="<?php echo RouteHelper::_('index.php?view=companies'); ?>" >
<table class="table table-hover table-striped" id="deals">
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
