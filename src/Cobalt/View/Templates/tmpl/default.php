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
                    <legend><h3><?php echo JText::_('COBALT_TASK_PEOPLE_TEMPLATES'); ?></h3></legend>
                    <div class="alert alert-info"><?php echo JText::_('COBALT_TASK_PEOPLE_TEMPLATES_DESC_1'); ?></div>
                    <form action="index.php?view=templates" method="post" name="adminForm" id="adminForm">
                         <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="1%">
                                            <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(this)" />
                                        </th>
                                        <th>
                                            <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_TEMPLATE_NAME', 't.name', $this->listDirn, $this->listOrder); ?>
                                        </th>
                                        <th>
                                            <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_TEMPLATE_TYPE', 't.type', $this->listDirn, $this->listOrder); ?>
                                        </th>
                                        <th>
                                            <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_TEMPLATE_CREATED', 't.created', $this->listDirn, $this->listOrder); ?>
                                        </th>
                                        <th>
                                            <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_TEMPLATE_MODIFIED', 't.modified', $this->listDirn, $this->listOrder); ?>
                                        </th>
                                        <th>
                                            <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_TEMPLATE_DEFAULT', 't.default', $this->listDirn, $this->listOrder); ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ( count($this->templates) ) { foreach ($this->templates as $key=>$template) { ?>
                                        <tr class="row<?php echo $i % 2; ?>">
                                            <td class="center">
                                                <?php echo JHtml::_('grid.id', $key, $template['id']); ?>
                                            </td>
                                            <td class="order"><?php echo JHtml::_('link','index.php?view=templates&layout=edit&id='.$template['id'],$template['name']); ?></td>
                                            <td class="order"><?php echo ucwords($template['type']); ?></td>
                                            <td class="order"><?php echo $template['created']; ?></td>
                                            <td class="order"><?php echo $template['modified']; ?></td>
                                            <td class="order"><?php if($template['default']) echo "Default"; ?></td>
                                        </tr>
                                    <?php }} ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="13">
                                            <!-- pagination -->
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div>
                                <input type="hidden" name="controller" value="" />
                                <input type="hidden" name="model" value="templates" />
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
