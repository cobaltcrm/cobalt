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
                    <legend><h3><?php echo JText::_('COBALT_DEAL_CUSTOM_FIELDS'); ?></h3></legend>
                    <div class="alert alert-info"><?php echo JText::_('COBALT_DEAL_CUSTOM_FIELDS_DESC'); ?></div>
                    <form action="index.php?view=dealcustom" method="post" name="adminForm" id="adminForm">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="1%">
                                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(this)" />
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_CUSTOM_NAME', 'c.name', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                    <th width="10%">
                                        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'c.ordering', $this->listDirn, $this->listOrder); ?>
                                        <?php if ($this->saveOrder) :?>
                                            <?php echo JHtml::_('grid.order',  $this->custom_fields, 'filesave.png', 'dealcustom.saveorder'); ?>
                                        <?php endif; ?>
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_CUSTOM_TYPE', 'c.type', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $ordering   = ($this->listOrder == 'c.ordering');
                                    $i=0;
                                ?>
                                <?php if ( count($this->custom_fields) > 0 ) {
                                    foreach ($this->custom_fields as $key=>$custom) { ?>
                                        <tr class="row<?php echo $i % 2; ?>">
                                            <td class="center">
                                                <?php echo JHtml::_('grid.id', $key, $custom['id']); ?>
                                            </td>
                                            <td class="order"><?php echo JHtml::_('link','index.php?view=dealcustom&layout=edit&id='.$custom['id'],$custom['name']); ?></td>
                                            <td class="order">
                                                <?php if ($this->saveOrder) :?>
                                                    <?php if ($this->listDirn == 'asc') : ?>
                                                        <span><?php echo $this->pagination->orderUpIcon($i, TRUE, 'dealcustom.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                        <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, TRUE, 'dealcustom.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                                    <?php elseif ($this->listDirn == 'desc') : ?>
                                                        <span><?php echo $this->pagination->orderUpIcon($i, TRUE, 'dealcustom.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                                        <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, TRUE, 'dealcustom.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php $disabled = $this->saveOrder ?  '' : 'disabled="disabled"'; ?>
                                                <input type="text" name="order[]" size="5" value="<?php echo $custom['ordering'];?>" <?php echo $disabled ?> class="text-area-order" />
                                            </td>
                                            <td class="order"><?php echo ucwords($custom['type']); ?></td>
                                        </tr>
                                <?php $i++; } } ?>
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
                            <input type="hidden" name="controller" value="" />
                            <input type="hidden" name="model" value="dealcustom" />
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
