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
            <div class="row-fluid">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="col-md-9">
                    <form action="<?php echo RouteHelper::_('index.php'); ?>" data-ajax="1" method="post" name="adminForm" id="adminForm" class="form-horizontal" >
                        <legend>
                            <div class="col-sm-9">
                                <h2><?php echo TextHelper::_("COBALT_EDITING_SOURCE"); ?></h2>
                            </div>
                            <div class="col-sm-3">
                                <?php echo $this->toolbar->render(); ?>
                            </div>
                            <div class="clearfix"></div>
                        </legend>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="name">
                                <?php echo TextHelper::_('COBALT_NAME'); ?>
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" value="<?php echo $this->source->name; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="cost">
                                <?php echo TextHelper::_('COBALT_HEADER_SOURCE_COST'); ?>
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="cost" id="cost" value="<?php echo $this->source->cost; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="type">
                                <?php echo TextHelper::_('COBALT_HEADER_SOURCE_TYPE'); ?>
                            </label>
                            <div class="col-sm-10">
                                <select class="form-control" name="type" id="type">
                                    <option value=""><?php echo TextHelper::_('COBALT_SELECT_SOURCE_TYPE'); ?></option>
                                    <?php echo JHtml::_('select.options', $this->source_types, 'value', 'text', $this->source->type, true);?>
                                </select>
                            </div>
                        </div>

                        <div>
                            <input type="hidden" name="id" value="<?php echo $this->source->id; ?>" />
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden" name="model" value="sources" />

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php echo $this->menu['quick_menu']->render(); ?>
    </div>
</div>
