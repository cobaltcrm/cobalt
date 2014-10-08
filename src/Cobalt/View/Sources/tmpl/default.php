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
                            <?php echo JText::_('COBALT_SOURCES'); ?>
                        </h3>
                    </div>

                    <div class="alert alert-info">
                        <?php echo JText::_('COBALT_SOURCES_DESC_1'); ?><br />
                        <?php echo JText::_('COBALT_SOURCES_DESC_2'); ?>
                    </div>

                    <table class="table table-striped data-table">
                        <thead>
                            <tr>
                                <th width="1%">
                                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(this)" />
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_SOURCE_NAME'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_SOURCE_COST'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_HEADER_SOURCE_TYPE'); ?>
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
