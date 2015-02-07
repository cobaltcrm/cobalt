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
                        <h3><?php echo TextHelper::_('COBALT_TASK_PEOPLE_TEMPLATES'); ?></h3>
                    </div>
                    <div class="alert alert-info"><?php echo TextHelper::_('COBALT_TASK_PEOPLE_TEMPLATES_DESC_1'); ?></div>
                    <table class="table table-striped data-table">
                        <thead>
                            <tr>
                                <th width="1%">
                                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo TextHelper::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Cobalt.selectAll(this)" />
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_TEMPLATE_NAME'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_TEMPLATE_TYPE'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_TEMPLATE_CREATED'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_TEMPLATE_MODIFIED'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_TEMPLATE_DEFAULT'); ?>
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
