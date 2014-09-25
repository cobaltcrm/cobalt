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
                    <legend><h3><?php echo JText::_('COBALT_FORM_WIZARD_HEADER'); ?></h3></legend>
                    <form action="index.php?view=formwizard" method="post" name="adminForm" id="adminForm">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="1%">
                                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(this)" />
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort',  'COBALT_FORM_HEADER_NAME', 'f.name', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                    <th><?php echo JText::_('COBALT_FORM_HEADER_DESCRIPTION'); ?></th>
                                    <th><?php echo JText::_('COBALT_FORM_HEADER_HTML'); ?></th>
                                    <th><?php echo JText::_('COBALT_FORM_HEADER_SHORTCODE'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ( count($this->forms) ) { $i=0; foreach ($this->forms as $key=>$form) { ?>

                                    <tr class="row<?php echo $i % 2; ?>">
                                        <td class="center">
                                            <?php echo JHtml::_('grid.id', $key, $form['id']); ?>
                                        </td>
                                        <td class="order"><?php echo JHtml::_('link','index.php?view=formwizard&layout=edit&id='.$form['id'],$form['name']); ?></td>
                                        <td><?php echo $form['description']; ?></td>
                                        <td>
                                            <input onclick="selectTextarea('html_text_<?php echo $form['id']; ?>')" type="button" class="btn-mini btn-primary" data-toggle="modal" href="#form_<?php echo $form['id']; ?>" id="show_fields_button" value="<?php echo JText::_('COBALT_VIEW_HTML'); ?>" />
                                            <div class="modal hide" id="form_<?php echo $form['id'];?>">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">×</button>
                                                    <h3><?php echo JText::_('COBALT_FORM_HTML'); ?></h3>
                                                </div>
                                                <div class="modal-body">
                                                    <textarea rel="tooltip" data-original-title="<?php echo JText::_('COBALT_FORM_HTML_TOOLTIP'); ?>" wrap="off" cols="20" rows="15" style="width:500px !important;" onclick="selectTextarea(this);" rel="" id="html_text_<?php echo $form['id']; ?>"><?php echo $form['html']; ?></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>[cobaltform<?php echo $form['id']; ?>]</td>
                                    </tr>

                                <?php $i++; } } ?>
                            </tbody>
                        </table>
                        <div>
                            <input type="hidden" name="controller" value="" />
                            <input type="hidden" name="model" value="formwizard" />
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
