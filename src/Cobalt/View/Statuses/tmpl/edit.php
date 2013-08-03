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

<div class="container-fluid">
    <?php echo $this->menu['quick_menu']->render(); ?>
    <div class="row">
        <div class="col-lg-12" id="content">
            <div id="system-message-container"></div>
            <div class="row">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="col-lg-9">
                    <form action="index.php?view=statuses" method="post" name="adminForm" id="adminForm" class="form-validate"  >
                        <div class="row">
                            <legend><h3><?php echo TextHelper::_('COBALT_EDITING_STATUS'); ?></h3></legend>
                            <label><b><?php echo JText::_("COBALT_NAME"); ?></b></label>
                            <input type="text" class="inputbox" name="name" value="<?php echo $this->status['name']; ?>" />
                            <label><b><?php echo JText::_('COBALT_HEADER_STATUS_COLOR'); ?></b></label>
                            <span class="cobaltfield"><input type="text" class="inputbox hascolorpicker" name="color" value="<?php echo $this->status['color']; ?>" /><div id="colorwheel" class="colorwheel"></div></span>
                            <div>
                                <?php if ($this->status['id']) { ?>
                                    <input type="hidden" name="id" value="<?php echo $this->status['id']; ?>" />
                                <?php } ?>
                                <input type="hidden" name="controller" value="" />
                                <input type="hidden" name="model" value="statuses" />
                                <?php echo JHtml::_('form.token'); ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php echo $this->menu['quick_menu']->render(); ?>
    </div>
</div>
