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
            <div class="row">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="col-md-9">
                    <div class="page-header">
                        <?php echo $this->toolbar->render(); ?>
                        <h3><?php echo JText::_('COBALT_SHARED_DOCUMENTS'); ?></h3>
                    </div>
                    
                    <div class="alert alert-info"><?php echo JText::_('COBALT_SHARED_DOCS_DESC'); ?></div>
                    <table class="table table-striped data-table">
                        <thead>
                            <tr>
                                <th width="1%">
                                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Cobalt.selectAll(this)" />
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_DOCUMENT_TYPE'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_DOCUMENT_FILENAME'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_DOCUMENT_OWNER'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_DOCUMENT_ASSOCIATION'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_DOCUMENT_SIZE'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_DOCUMENT_UPLOADED'); ?>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php $this->menu['quick_menu']->render(); ?>
</div>
<!-- Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="dealModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>