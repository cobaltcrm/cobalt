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
    <?php echo $this->menu['quick_menu']->render(); ?>
    <div class="row-fluid">
        <div class="span12" id="content">
            <div id="system-message-container"></div>
            <div class="row-fluid">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="span9">
                    <form action="index.php?view=sources" method="post" name="adminForm" id="adminForm" class="form-validate"  >
                        <div class="row-fluid">
                            <legend><h3><?php echo CRMText::_('COBALT_EDITING_SOURCE'); ?></h3></legend>
                            <label><b><?php echo JText::_('COBALT_NAME'); ?></b></label>
                            <input type="text" class="inputbox" name="name" value="<?php echo $this->source['name']; ?>" />
                            <label><b><?php echo JText::_('COBALT_HEADER_SOURCE_COST'); ?></b></label>
                            <input type="text" class="inputbox" name="cost" value="<?php echo $this->source['cost']; ?>" />
                            <label><b><?php echo JText::_('COBALT_HEADER_SOURCE_TYPE'); ?></b></label>
                            <select class="inputbox" name="type">
                                <option value=""><?php echo JText::_('COBALT_SELECT_SOURCE_TYPE'); ?></option>
                                <?php echo JHtml::_('select.options', $this->source_types, 'value', 'text', $this->source['type'], true);?>
                            </select>
                            <div>
                                <?php if ($this->source['id']) { ?>
                                    <input type="hidden" name="id" value="<?php echo $this->source['id']; ?>" />
                                <?php } ?>
                                <input type="hidden" name="controller" value="" />
                                <input type="hidden" name="model" value="sources" />
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
