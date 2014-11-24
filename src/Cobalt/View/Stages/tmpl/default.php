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

                    <div class="page-header">
                        <h3>
                            <?php echo $this->toolbar->render(); ?>
                            <?php echo JText::_('COBALT_DEAL_STAGES'); ?>
                        </h3>
                    </div>

                    <div class="alert alert-info">
                        <?php echo JText::_('COBALT_DEAL_STAGES_DESC_1'); ?><br />
                        <?php echo JText::_('COBALT_DEAL_STAGES_DESC_2'); ?>
                    </div>
                    
                    <table class="table table-striped data-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Cobalt.selectAll(this)" />
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_DEAL_STAGE'); ?>
                                </th>
                                <th><?php echo JText::_('COBALT_COLOR'); ?></th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_PERCENT'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('JGRID_HEADING_WON'); ?>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php echo $this->menu['quick_menu']->render(); ?>
</div>
