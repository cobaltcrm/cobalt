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
                    <legend><h3><?php echo JText::_('COBALT_DEAL_STAGES'); ?></h3></legend>
                    <div class="alert alert-info"><?php echo JText::_('COBALT_DEAL_STAGES_DESC_1'); ?><br /><?php echo JText::_('COBALT_DEAL_STAGES_DESC_2'); ?></div>
                    <form action="index.php?view=stages" method="post" name="adminForm" id="adminForm">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="1%">
                                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(this)" />
                                    </th>
                                    <th style="text-align:left;" >
                                        <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_DEAL_STAGE', 's.name', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                    <th width="10%">
                                        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 's.ordering', $this->listDirn, $this->listOrder); ?>
                                        <?php if ($this->saveOrder) :?>
                                            <?php echo JHtml::_('grid.order',  $this->stages, 'filesave.png', 'stages.saveorder'); ?>
                                        <?php endif; ?>
                                    </th>
                                    <th width="1%"><?php echo JText::_('COBALT_COLOR'); ?></th>
                                    <th width="1%">
                                        <?php echo JHtml::_('grid.sort', 'COBALT_HEADER_PERCENT', 's.percent', $this->listDirn, $this->listOrder); ?>
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
                            <tfoot>
                                    <tr>
                                        <td colspan="13">
                                           <?php echo $this->pagination->getListFooter(); ?>
                                        </td>
                                    </tr>
                            </tfoot>
                        </table>
                        <div>
                            <input type="hidden" name="model" value="stages" />
                            <input type="hidden" name="controller" value="" />
                            <input type="hidden" name="boxchecked" value="0" />
                            <input type="hidden" name="filter_order" value="<?php echo $this->listOrder; ?>" />
                            <input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirn; ?>" />
                            <?php echo JHtml::_('form.token'); ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $this->menu['quick_menu']->render(); ?>
</div>
