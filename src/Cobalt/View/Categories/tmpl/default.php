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
                    <legend><h3><?php echo JText::_('COBALT_NOTE_CATEGORIES'); ?></h3></legend>
                    <div class="alert alert-info">
                        <?php echo JText::_('COBALT_NOTES_DESC_1'); ?>
                        <?php echo JText::_('COBALT_NOTES_DESC_2'); ?>
                    </div>
                    <form action="index.php?view=categories" method="post" name="adminForm" id="adminForm">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="1%">
                                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(this)" />
                                    </th>
                                    <th class="left">
                                        <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_NOTE_CATEGORY', 'c.name', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ( count($this->categories) ) { foreach ($this->categories as $key=>$category) { ?>
                                    <tr class="row<?php echo $i % 2; ?>">
                                        <td class="center">
                                            <?php echo JHtml::_('grid.id', $key, $category['id']); ?>
                                        </td>
                                        <td class="left">
                                            <?php echo JHtml::_('link','index.php?view=categories&layout=edit&id='.$category['id'],$category['name']); ?>
                                        </td>
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
                            <input type="hidden" name="model" value="" />
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
