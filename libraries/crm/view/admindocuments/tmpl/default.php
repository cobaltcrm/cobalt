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
                    <legend><h3><?php echo JText::_('COBALT_SHARED_DOCUMENTS'); ?></h3></legend>
                    <div class="alert alert-info"><?php echo JText::_('COBALT_SHARED_DOCS_DESC'); ?></div>
                    <form action="index.php?view=documents" method="post" name="adminForm" id="adminForm">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="1%">
                                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(this)" />
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_DOCUMENT_TYPE', 'd.filetype', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_DOCUMENT_FILENAME', 'd.name', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_DOCUMENT_SIZE', 'd.size', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort',  'COBALT_HEADER_DOCUMENT_UPLOADED', 'd.created', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ( count($this->documents) ) { foreach ($this->documents as $key=>$document) { ?>
                                    <tr class="">
                                        <td class="center"><?php echo JHtml::_('grid.id', $key, $document['id']); ?></td>
                                        <td class="order"><?php echo '<img width="30px" height="30px" src="'.JURI::base().'libraries/crm/media/images/'.$document['filetype'].'.png'.'" /><br /><b>'.strtoupper($document['filetype']).'<b></td>'; ?></td>
                                        <td class="order"><?php echo JHtml::_('link','index.php?view=documents&layout=download&document='.$document['filename'],$document['name'],array('target'=>'_blank')); ?></td>
                                        <td class="order"><?php echo $document['size']; ?>kb</td>
                                        <td class="order"><?php echo date("F j, Y",strtotime($document['created'])); ?></td>
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
                            <input type="hidden" name="task" value="" />
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
    <?php $this->menu['quick_menu']->render(); ?>
</div>
