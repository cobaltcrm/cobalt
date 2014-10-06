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
                        <tbody>
                            <?php
                            $i = 0;
                            $ordering   = ($this->listOrder == 's.ordering');
                            if ( count($this->stages) ) { foreach ($this->stages as $key=>$stage) { ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="center">
                                        <?php echo JHtml::_('grid.id', $key, $stage['id']); ?>
                                    </td>
                                    <td style="text-align:left;" class="order"><?php echo JHtml::_('link','index.php?view=stages&layout=edit&id='.$stage['id'],$stage['name']); ?></td>
                                    <td class="order">
                                        <?php if ($this->saveOrder) :?>
                                            <?php if ($this->listDirn == 'asc') : ?>
                                                <span><?php echo $this->pagination->orderUpIcon($i, TRUE, 'stages.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, TRUE, 'stages.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                            <?php elseif ($this->listDirn == 'desc') : ?>
                                                <span><?php echo $this->pagination->orderUpIcon($i, TRUE, 'stages.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, TRUE, 'stages.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php $disabled = $this->saveOrder ?  '' : 'disabled="disabled"'; ?>
                                        <input type="text" name="order[]" size="5" value="<?php echo $stage['ordering'];?>" <?php echo $disabled ?> class="text-area-order" />
                                    </td>
                                    <td><div class="status-dot" style="background-color: #<?php echo $stage['color']; ?>;"></div></td>
                                    <td class="order"><?php echo $stage['percent']; ?>%</td>
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php echo $this->menu['quick_menu']->render(); ?>
</div>
