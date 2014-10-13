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
?>
<script type="text/javascript">
    var order_url = "<?php echo 'index.php?view=reports&layout=deal_milestones_filter&format=raw'; ?>";
</script>
<div class="page-header">
    <h1><?php echo ucwords(TextHelper::_('COBALT_DEAL_MILESTONE_REPORT')); ?></h1>
</div>
<?php echo $this->menu; ?>
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th><?php echo TextHelper::_('COBALT_DEAL_NAME'); ?></th>
            <th><?php echo TextHelper::_('COBALT_OWNER'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php $deal_filter = $this->state->get('Deal.'.str_replace('_filter','',$app->input->get('layout')).'_name'); ?>
            <td><input class="form-control filter_input" name="deal_name" type="text" value="<?php echo $deal_filter; ?>"></td>
            <td>
                <select class="form-control filter_input" name="owner_id">
                    <?php if ( UsersHelper::getRole() != "basic" ) { ?>
                        <option value=""><?php echo TextHelper::_('COBALT_ALL'); ?></option>
                    <?php } ?>
                    <?php if ( UsersHelper::getRole() != "basic" ) { ?>
                        <?php $user_filter = $this->state->get('Deal.'.str_replace('_filter','',$app->input->get('layout')).'_owner_id'); ?>
                        <?php $team_filter = $this->state->get('Deal.'.str_replace('_filter','',$app->input->get('layout')).'_owner_id');; ?>
                        <optgroup label="<?php echo TextHelper::_('COBALT_TEAM'); ?>" class="team">
                            <?php echo JHtml::_('select.options', $this->team_names, 'value', 'text', $user_filter, true); ?>
                        </optgroup>
                        <optgroup label="<?php echo TextHelper::_('COBALT_MEMBERS'); ?>" class="member">
                            <option value="<?php echo UsersHelper::getUserId(); ?>"><?php echo TextHelper::_('COBALT_ME'); ?></option>
                            <?php echo JHtml::_('select.options', $this->user_names, 'value', 'text', $team_filter, true); ?>
                        </optgroup>
                    <?php } ?>
                </select>
            </td>
        </tr>
    </tbody>
</table>
<div class="results">
    <?php echo $this->deal_milestone_list->render(); ?>
</div>
<form method="post" action="<?php echo RouteHelper::_('index.php?view=reports&layout=deal_milestones'); ?>">
    <table class="com_cobalt_table">
        <tfoot>
        <tr>
           <td colspan="14"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
     </tfoot>
    </table>
</form>
