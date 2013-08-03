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

<h1><?php echo ucwords(TextHelper::_('COBALT_GOALS_HEADER')); ?></h1>
<div class="goals_columns row">
    <div class="goals_left_column col-lg-6">
        <ul class="list-unstyled">
            <li class="alert alert-success">
                <div class="goal_container media">
                    <div class="goal_img pull-left"><img src="<?php echo JURI::base(); ?>src/Cobalt/media/images/win_more_cash.png" /></div>
                    <div class="goal_info_container media-body">
                        <h2><a href="<?php echo JRoute::_('index.php?view=goals&layout=edit&type=win_cash'); ?>"><?php echo ucwords(TextHelper::_('COBALT_WIN_MORE_CASH').' '.ConfigHelper::getConfigValue('currency')); ?></a></h2>
                        <div class="goal_info"><?php echo JText::sprintf('COBALT_CREATE_GOAL_TRACK_CASH',ConfigHelper::getConfigValue('currency')); ?></div>
                    </div>
                </div>
            </li>
            <li class="alert">
                <div class="goal_container media">
                    <div class="goal_img pull-left"><img src="<?php echo JURI::base(); ?>src/Cobalt/media/images/win_more_deals.png" /></div>
                    <div class="goal_info_container media-body">
                        <h2><a href="<?php echo JRoute::_('index.php?view=goals&layout=edit&type=win_deals'); ?>"><?php echo ucwords(TextHelper::_('COBALT_WIN_MORE_DEALS')); ?></a></h2>
                        <div class="goal_info"><?php echo TextHelper::_('COBALT_CREATE_GOAL_TRACK_DEALS'); ?></div>
                    </div>
                </div>
            </li>
            <li class="alert alert-info">
                <div class="goal_container media">
                    <div class="goal_img pull-left"><img src="<?php echo JURI::base(); ?>src/Cobalt/media/images/move_deals_forward.png" /></div>
                    <div class="goal_info_container media-body">
                        <h2><a href="<?php echo JRoute::_('index.php?view=goals&layout=edit&type=move_deals'); ?>"><?php echo ucwords(TextHelper::_('COBALT_MOVE_DEALS_FORWARD')); ?></a></h2>
                        <div class="goal_info"><?php echo TextHelper::_('COBALT_CREATE_GOAL_TRACK_DEAL_STAGES'); ?></div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="goals_right_column col-lg-6">
        <ul class="list-unstyled">
            <li class="alert">
                <div class="goal_container media">
                    <div class="goal_img pull-left"><img src="<?php echo JURI::base(); ?>src/Cobalt/media/images/complete_more_tasks.png" /></div>
                    <div class="goal_info_container media-body">
                        <h2><a href="<?php echo JRoute::_('index.php?view=goals&layout=edit&type=complete_tasks'); ?>"><?php echo ucwords(TextHelper::_('COBALT_COMPLETE_TASKS')); ?></a></h2>
                        <div class="goal_info"><?php echo TextHelper::_('COBALT_CREATE_GOAL_TRACK_TASKS'); ?></div>
                    </div>
                </div>
            </li>
            <li class="alert-info alert">
                <div class="goal_container media">
                    <div class="goal_img pull-left"><img src="<?php echo JURI::base(); ?>src/Cobalt/media/images/write_more_notes.png" /></div>
                    <div class="goal_info_container media-body">
                        <h2><a href="<?php echo JRoute::_('index.php?view=goals&layout=edit&type=write_notes'); ?>"><?php echo ucwords(TextHelper::_('COBALT_WRITE_NOTES')); ?></a></h2>
                        <div class="goal_info"><?php echo TextHelper::_('COBALT_CREATE_GOAL_TRACK_NOTES'); ?></div>
                    </div>
                </div>
            </li>
            <li class="alert alert-success">
                <div class="goal_container media">
                    <div class="goal_img pull-left"><img src="<?php echo JURI::base(); ?>src/Cobalt/media/images/create_deals.png" /></div>
                    <div class="goal_info_container media-body">
                        <h2><a href="<?php echo JRoute::_('index.php?view=goals&layout=edit&type=create_deals'); ?>"><?php echo ucwords(TextHelper::_('COBALT_CREATE_DEALS')); ?></a></h2>
                        <div class="goal_info"><?php echo TextHelper::_('COBALT_CREATE_GOAL_TRACK_DEALS_CREATED'); ?></div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
