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
<?php if ($this->help_type != "launch_default") { if (!$this->launch_default) { ?>
    <div class="panel panel-warning">
        <div class="panel-heading">
            <h4 class="panel-title"><?php echo JText::_('COBALT_QUICK_START'); ?></h4>
        </div>
        <div class="panel-body">
        <ul class="help_menu nav nav-pills">
            <?php if ( count ( $this->help_menu_links ) > 0 ){ foreach ($this->help_menu_links as $link) { ?>
                        <li class="<?php echo $link['completed_status']!=0 ? 'completed' : 'uncompleted'; ?>" id="<?php echo $link['config']; ?>">
                            <a href="<?php echo $link['link']; ?>">
                                <i class="<?php echo $link['completed_status']==1 ? 'glyphicon glyphicon-ok icon' : $link['class']; ?>"></i>
                                <span><?php echo $link['text']; ?></span>
                            </a>
                            <span class="<?php echo $link['completed_status']==1 ? 'completed' : 'uncompleted'; ?>">
                                <span class="<?php echo $link['completed_status']==1 ? 'completed' : 'uncompleted'; ?>-inner"></span>
                            </span>
                        </li>
            <?php } } ?>
        </ul>
        </div>
    </div>
<?php } ?>
<?php if ($this->show_update_buttons || $this->show_help) {  ?>
    <div id="help_description_action" class="help_description_action">
        <form action="" method="post">
            <div class="alert alert-info">
                <?php if (!$this->show_update_buttons) { ?>
                    <div class="pull-right">
                        <a class="btn-mini btn btn-danger" data-toggle="modal" href="#disable_help_hidden" onclick="disableHelp();" id="disable_help_button"><?php echo JText::_('COBALT_DISABLE_HELP'); ?></a>
                    </div>
                <?php } ?>
                <h4><?php echo JText::_('COBALT_HELP_'.strtoupper($this->help_type).'_TITLE'); ?></h4>
                <?php echo JText::_('COBALT_HELP_'.strtoupper($this->help_type).'_DESC'); ?>
            </div>
            <?php if (!$this->step_completed && $this->show_update_buttons) { ?>
                <div id="help_actions" class="pull-right btn-group">
                    <a href="javascript:void(0);" onclick="updateConfig('<?php echo $this->help_type; ?>',1);" class="btn-primary btn-large" ><i class="glyphicon glyphicon-check icon-white"></i><?php echo JText::_('COBALT_COMPLETE'); ?></a>
                    <a href="javascript:void(0);" onclick="updateConfig('<?php echo $this->help_type; ?>',0);" class="btn laterButton" ><i class="glyphicon glyphicon-repeat"></i><?php echo JText::_('COBALT_COMPLETE_LATER'); ?></a>
                </div>
            <?php } ?>
        </form>
    </div>
    <div class="modal hide fade in" id="disable_help_hidden">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">×</button>
            <h3><?php echo JText::_('COBALT_DISABLE_HELP_TITLE'); ?></h3>
        </div>
        <div class="modal-body">
            <p><?php echo JText::_('COBALT_DISABLE_HELP_DESC'); ?></p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-primary" data-dismiss="modal"><?php echo JText::_('COBALT_CLOSE'); ?></a>
        </div>
    </div>
<?php } }
