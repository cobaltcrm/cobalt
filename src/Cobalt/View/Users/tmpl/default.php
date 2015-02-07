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
                            <?php echo TextHelper::_('COBALT_ADD_TO_ACCOUNT'); ?>
                        </h3>
                    </div>

                    <table class="table table-striped data-table">
                        <thead>
                            <tr>
                                <th width="1%">
                                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo TextHelper::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Cobalt.selectAll(this)" />
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_USERS_HEADER_NAME'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_USERS_HEADER_USERNAME'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_USERS_HEADER_TEAM'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_USERS_HEADER_EMAIL'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_('COBALT_USERS_HEADER_ROLE'); ?>
                                </th>
                                <th>
                                    <?php echo TextHelper::_( 'COBALT_USERS_HEADER_LOGIN'); ?>
                                </th>
                            </tr>
                        </thead>
                    </table>
                    <div class="clearfix"></div>
                    <div class="alert alert-info clearfix">
                        <h3><?php echo TextHelper::_("COBALT_USER_ROLES_VISIBILITY"); ?></h3>
                        <b><?php echo TextHelper::_('COBALT_EXECUTIVE'); ?></b> - <?php echo TextHelper::_('COBALT_EXECUTIVE_DESC'); ?><br />
                        <b><?php echo TextHelper::_('COBALT_MANAGERS'); ?></b> - <?php echo TextHelper::_('COBALT_MANAGERS_DESC'); ?><br />
                        <b><?php echo TextHelper::_('COBALT_BASIC_USERS'); ?></b> - <?php echo TextHelper::_('COBALT_BASIC_USERS_DESC'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $this->menu['quick_menu']->render(); ?>
</div>
