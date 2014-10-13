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
                        <h3>
                            <?php echo $this->toolbar->render(); ?>
                            <?php echo TextHelper::_('COBALT_NOTE_CATEGORIES'); ?>
                        </h3>
                    </div>

                    <div class="alert alert-info">
                        <?php echo TextHelper::_('COBALT_NOTES_DESC_1'); ?>
                        <?php echo TextHelper::_('COBALT_NOTES_DESC_2'); ?>
                    </div>
                    
                    <form action="index.php?view=categories" method="post" name="adminForm" id="adminForm">
                        <table class="table table-striped data-table">
                            <thead>
                                <tr>
                                    <th width="1%">
                                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo TextHelper::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(this)" />
                                    </th>
                                    <th>
                                        <?php echo TextHelper::_('COBALT_HEADER_NOTE_CATEGORY'); ?>
                                    </th>
                                </tr>
                            </thead>
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
