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
    <div class="row-fluid">
        <div class="span12" id="content">
            <div id="system-message-container"></div>
            <div class="row-fluid">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="span9">
                    <form action="index.php?view=stages" method="post" name="adminForm" id="adminForm" class="form-validate"  >
                        <legend><h3><?php echo TextHelper::_('COBALT_EDITING_STAGE'); ?></h3></legend>
                            <label><b><?php echo JText::_('COBALT_NAME'); ?></b></label>
                            <input type="text" class="inputbox" name="name" value="<?php echo $this->stage['name']; ?>" />
                            <label><b><?php echo JText::_('COBALT_HEADER_PERCENT'); ?></b></label>
                            <span class="cobaltfield">
                                <input type="hidden" name="percent" value="<?php echo $this->stage['percent']; ?>"/>
                                <div class="" id="percent"></div>
                                <div id="percent_value"><?php echo $this->stage['percent']."%"; ?></div>
                            </span>
                            <label><b><?php echo JText::_("COBALT_COLOR"); ?></b></label>
                            <input class="inputbox hascolorpicker" type="text" name="color" value="<?php echo $this->stage['color']; ?>"><div class="colorwheel"></div>
                            <label><b><?php echo JText::_("COBALT_WON_STAGE"); ?></b></label>
                            <input <?php if ( isset($this->stage) && array_key_exists('won',$this->stage) && $this->stage['won'] == 1 ) echo "checked='checked'"; ?> type="checkbox" name="won" value="1">
                            <div>
                                <?php if ($this->stage['id']) { ?>
                                    <input type="hidden" name="id" value="<?php echo $this->stage['id']; ?>" />
                                <?php } ?>
                                <input type="hidden" name="controller" value="" />
                                <input type="hidden" name="model" value="stages" />
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
