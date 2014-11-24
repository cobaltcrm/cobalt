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
        <div class="col-sm-12" id="content">
            <div id="system-message-container"></div>
            <div class="row">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="col-md-9">
                    <form action="<?php echo RouteHelper::_('index.php'); ?>" data-ajax="1" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
                        
                        <legend>
                            <div class="col-sm-9">
                                <h2><?php echo TextHelper::_("COBALT_EDITING_STAGE"); ?></h2>
                            </div>
                            <div class="col-sm-3">
                                <?php echo $this->toolbar->render(); ?>
                            </div>
                            <div class="clearfix"></div>
                        </legend>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="name">
                                <?php echo JText::_('COBALT_NAME'); ?>
                            </label>
                            <div class="col-sm-10">
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="name" 
                                    id="name" 
                                    value="<?php echo $this->stage->name; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="percent">
                                <?php echo JText::_('COBALT_HEADER_PERCENT'); ?>
                            </label>
                            <div class="col-sm-8">
                                <input 
                                    type="range" 
                                    name="percent_slider" 
                                    id="percent_slider" 
                                    value="<?php echo $this->stage->percent; ?>" 
                                    onchange="jQuery('#percent').val(jQuery(this).val());"
                                    min="0" 
                                    max="100"/>
                            </div>
                            <div class="col-sm-2 text-right input-group">
                                <input 
                                    type="number" 
                                    name="percent" 
                                    id="percent"
                                    class="form-control"
                                    value="<?php echo $this->stage->percent; ?>" 
                                    onchange="jQuery('#percent_slider').val(jQuery(this).val());"
                                    min="0" 
                                    max="100"/>
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="color">
                                <?php echo JText::_("COBALT_COLOR"); ?>
                            </label>
                            <div class="col-sm-10">
                                <input 
                                    data-placement="right" 
                                    id="color" 
                                    type="color" 
                                    class="form-control"  
                                    name="color" 
                                    value="<?php echo $this->stage->color ? '#' . $this->stage->color : '#00b725'; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                <?php echo JText::_("COBALT_WON_STAGE"); ?>
                            </label>
                            <div class="col-sm-10">
                                <input <?php if (isset($this->stage->won) && $this->stage->won == 1) echo "checked='checked'"; ?> type="checkbox" name="won" value="1">
                            </div>
                        </div>

                        <div>
                            <input type="hidden" name="id" value="<?php echo isset($this->stage->id) ? $this->stage->id : ''; ?>" />
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden" name="model" value="stages" />
                            <?php echo JHtml::_('form.token'); ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php echo $this->menu['quick_menu']->render(); ?>
    </div>
</div>
