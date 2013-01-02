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

<div class="container-fluid">
    <?php echo $this->side_menu['help_menu']->render(); ?>
    <?php echo $this->side_menu['quick_menu']->render(); ?>
    <div class="row-fluid">
        <div class="span12" id="content">
            <div id="system-message-container"></div>
            <div class="row-fluid">
                <?php echo $this->side_menu['menu']->render(); ?>
                <div class="span9">
                    <form action="index.php?view=cobalt" method="post" name="adminForm" id="adminForm" class="form-validate"  >
                        <div class="row-fluid">
                            <div class="rocket"></div>
                            <div class="launch_text">
                                <h1><?php echo JText::_('COBALT_CONGRATULATIONS'); ?></h1>
                                <p><?php echo JText::_('COBALT_LAUNCH_TEXT_DESC'); ?></p>
                                <input type="submit" value="<?php echo JText::_('COBALT_LAUNCH_BUTTON'); ?>" class="btn btn-success btn-large" />
                            </div>
                            <input type="hidden" name="id" value="1" />
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden" name="controller" value="config" />
                            <input type="hidden" name="launch_default" value="1" />
                            <input type="hidden" name="show_launch_message" value="1" />
                            <input type="hidden" name="view" value="cobalt" />
                            <?php echo JHtml::_('form.token'); ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>