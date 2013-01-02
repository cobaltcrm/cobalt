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
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th><?php echo ucwords(CRMText::_('COBALT_GOAL_NAME')); ?></th>
            <th><?php echo CRMText::_('COBALT_GOAL_RESPONSIBLE'); ?></th>
            <th><?php echo CRMText::_('COBALT_GOAL_DUE_DATE'); ?></th>
            <th><?php echo CRMText::_('COBALT_GOAL_ACTIONS'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
            for($i=0;$i<count($this->goals);$i++){
                $goal = $this->goals[$i]; ?>
                  <tr id="<?php echo $goal['id']; ?>">
                      <td><?php echo $goal['name']; ?></td>
                      <?php if ( $goal['assigned_type'] == 'member' ){ ?>
                          <td><?php echo $goal['first_name']." ".$goal['last_name']; ?></td>
                      <?php } ?>
                      <?php if ( $goal['assigned_type'] == 'team' ){ ?>
                          <td><?php echo $goal['first_name']." ".$goal['last_name']; ?><?php echo CRMText::_('COBALT_S_TEAM'); ?></td>
                      <?php } ?>
                      <?php if ( $goal['assigned_type'] == 'company' ){ ?>
                          <td><?php echo ucwords(CRMText::_('COBALT_THE_COMPANY')); ?></td>
                      <?php } ?>
                      <td><?php echo date("n/j/y",strtotime($goal['end_date'])); ?></td>
                      <td><a href="javascript:void(0);" onclick="deleteGoalEntry(this)" class="delete_goal"><i class="icon-trash"></i><?php echo CRMText::_('COBALT_DELETE'); ?></a></td>
                  </tr>
        <?php }
        ?>
    </tbody>
</table>
