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
        <div class="col-sm-12" id="content">
            <div id="system-message-container"></div>
            <div class="row-fluid">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="col-md-9">
                    <form action="<?php echo RouteHelper::_('index.php'); ?>" data-ajax="1" method="post" name="adminForm" id="adminForm" class="form-horizontal" >

                        <div class="page-header">
                            <?php echo $this->toolbar->render(); ?>
                            <h3><?php echo TextHelper::_('COBALT_EDITING_STATUS'); ?></h3>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="name">
                                <?php echo TextHelper::_('COBALT_NAME'); ?>
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" value="<?php echo $this->status->name; ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="name">
                                <?php echo TextHelper::_('COBALT_HEADER_STATUS_COLOR'); ?>
                            </label>
                            <div class="col-sm-10">
                                <input 
                                    data-placement="right" 
                                    id="color" 
                                    type="color" 
                                    class="form-control"  
                                    name="color" 
                                    value="<?php echo $this->status->color ? '#' . $this->status->color : '#00b725'; ?>" />
                            </div>
                        </div>
                        
                        <div>
                            <input type="hidden" name="id" value="<?php echo $this->status->id; ?>" />
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden" name="model" value="statuses" />

                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php echo $this->menu['quick_menu']->render(); ?>
    </div>
</div>
