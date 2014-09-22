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
    <h1><?php echo ucwords(TextHelper::_('COBALT_GOALS_HEADER')); ?></h1>
</div>
<div class="goals_columns row">
    <div class="goals_left_column span6">
        <ul class="list-unstyled">
            <li>
                <div class="goal_container media">
                    <div class="goal_img pull-left"><img src="<?php echo JURI::base(); ?>src/Cobalt/media/images/win_more_cash.png" /></div>
                    <div class="goal_info_container media-body">
                        <h4><a data-target="#editModal" data-toggle="modal" href="<?php echo RouteHelper::_('index.php?view=goals&layout=edit&type=win_cash&format=raw&tmpl=component'); ?>"><?php echo ucwords(TextHelper::_('COBALT_WIN_MORE_CASH')); ?></a></h4>
                        <div class="goal_info"><?php echo JText::sprintf('COBALT_CREATE_GOAL_TRACK_CASH',ConfigHelper::getConfigValue('currency')); ?></div>
                    </div>
                </div>
                <hr />
            </li>
            <li>
                <div class="goal_container media">
                    <div class="goal_img pull-left"><img src="<?php echo JURI::base(); ?>src/Cobalt/media/images/win_more_deals.png" /></div>
                    <div class="goal_info_container media-body">
                        <h4><a data-target="#editModal" data-toggle="modal" href="<?php echo RouteHelper::_('index.php?view=goals&layout=edit&type=win_deals&format=raw&tmpl=component'); ?>"><?php echo ucwords(TextHelper::_('COBALT_WIN_MORE_DEALS')); ?></a></h4>
                        <div class="goal_info"><?php echo TextHelper::_('COBALT_CREATE_GOAL_TRACK_DEALS'); ?></div>
                    </div>
                </div>
                <hr />
            </li>
            <li>
                <div class="goal_container media">
                    <div class="goal_img pull-left"><img src="<?php echo JURI::base(); ?>src/Cobalt/media/images/move_deals_forward.png" /></div>
                    <div class="goal_info_container media-body">
                        <h4><a data-target="#editModal" data-toggle="modal" href="<?php echo RouteHelper::_('index.php?view=goals&layout=edit&type=move_deals&format=raw&tmpl=component'); ?>"><?php echo ucwords(TextHelper::_('COBALT_MOVE_DEALS_FORWARD')); ?></a></h4>
                        <div class="goal_info"><?php echo TextHelper::_('COBALT_CREATE_GOAL_TRACK_DEAL_STAGES'); ?></div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="goals_right_column span6">
        <ul class="list-unstyled">
            <li>
                <div class="goal_container media">
                    <div class="goal_img pull-left"><img src="<?php echo JURI::base(); ?>src/Cobalt/media/images/complete_more_tasks.png" /></div>
                    <div class="goal_info_container media-body">
                        <h4><a data-target="#editModal" data-toggle="modal" href="<?php echo RouteHelper::_('index.php?view=goals&layout=edit&type=complete_tasks&format=raw&tmpl=component'); ?>"><?php echo ucwords(TextHelper::_('COBALT_COMPLETE_TASKS')); ?></a></h4>
                        <div class="goal_info"><?php echo TextHelper::_('COBALT_CREATE_GOAL_TRACK_TASKS'); ?></div>
                    </div>
                </div>
                <hr />
            </li>
            <li>
                <div class="goal_container media">
                    <div class="goal_img pull-left"><img src="<?php echo JURI::base(); ?>src/Cobalt/media/images/write_more_notes.png" /></div>
                    <div class="goal_info_container media-body">
                        <h4><a data-target="#editModal" data-toggle="modal" href="<?php echo RouteHelper::_('index.php?view=goals&layout=edit&type=write_notes&format=raw&tmpl=component'); ?>"><?php echo ucwords(TextHelper::_('COBALT_WRITE_NOTES')); ?></a></h4>
                        <div class="goal_info"><?php echo TextHelper::_('COBALT_CREATE_GOAL_TRACK_NOTES'); ?></div>
                    </div>
                </div>
                <hr />
            </li>
            <li>
                <div class="goal_container media">
                    <div class="goal_img pull-left"><img src="<?php echo JURI::base(); ?>src/Cobalt/media/images/create_deals.png" /></div>
                    <div class="goal_info_container media-body">
                        <h4><a data-target="#editModal" data-toggle="modal" href="<?php echo RouteHelper::_('index.php?view=goals&layout=edit&type=create_deals&format=raw&tmpl=component'); ?>"><?php echo ucwords(TextHelper::_('COBALT_CREATE_DEALS')); ?></a></h4>
                        <div class="goal_info"><?php echo TextHelper::_('COBALT_CREATE_GOAL_TRACK_DEALS_CREATED'); ?></div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<script>
    //clear modal data when close
    $('#editModal').on('hidden.bs.modal', function (e) {
        $('#editModal').removeData('bs.modal');
    })
</script>