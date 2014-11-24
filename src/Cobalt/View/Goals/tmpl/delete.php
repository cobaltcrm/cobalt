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
?>

<div class="modal-header">
    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
    <h1><?php echo ucwords($this->header); ?></h1>
</div>
<div class="modal-body">
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th><?php echo ucwords(TextHelper::_('COBALT_GOAL_NAME')); ?></th>
            <th><?php echo TextHelper::_('COBALT_GOAL_RESPONSIBLE'); ?></th>
            <th><?php echo TextHelper::_('COBALT_GOAL_DUE_DATE'); ?></th>
            <th><?php echo TextHelper::_('COBALT_GOAL_ACTIONS'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            for ($i=0;$i<count($this->goals);$i++) {
                $goal = $this->goals[$i]; ?>
                  <tr id="<?php echo $goal['id']; ?>">
                      <td><?php echo $goal['name']; ?></td>
                      <?php if ($goal['assigned_type'] == 'member') { ?>
                          <td><?php echo $goal['first_name']." ".$goal['last_name']; ?></td>
                      <?php } ?>
                      <?php if ($goal['assigned_type'] == 'team') { ?>
                          <td><?php echo $goal['first_name']." ".$goal['last_name']; ?><?php echo TextHelper::_('COBALT_S_TEAM'); ?></td>
                      <?php } ?>
                      <?php if ($goal['assigned_type'] == 'company') { ?>
                          <td><?php echo ucwords(TextHelper::_('COBALT_THE_COMPANY')); ?></td>
                      <?php } ?>
                      <td><?php echo date("n/j/y",strtotime($goal['end_date'])); ?></td>
                      <td><a href="javascript:void(0);" onclick="Goal.delete(this)" class="delete_goal"><i class="glyphicon glyphicon-trash"></i><?php echo TextHelper::_('COBALT_DELETE'); ?></a></td>
                  </tr>
        <?php }
        ?>
    </tbody>
</table>
</div>
<div class="modal-footer">
    <div class="actions"><a aria-hidden="true" data-dismiss="modal" href="javascript:void(0);"><?php echo TextHelper::_('COBALT_CANCEL_BUTTON'); ?></a></div>
</div>

