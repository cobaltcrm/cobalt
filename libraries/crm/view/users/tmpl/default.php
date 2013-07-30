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
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="container-fluid">
    <?php echo $this->menu['quick_menu']->render(); ?>
    <div class="row-fluid">
        <div class="span12" id="content">
            <div id="system-message-container"></div>
            <div class="row-fluid">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="span9">
                    <legend><h3><?php echo JText::_('COBALT_ADD_TO_ACCOUNT'); ?></h3></legend>
                    <div class="alert alert-info">
                        <h3><?php echo JText::_("COBALT_USER_ROLES_VISIBILITY"); ?></h3>
                        <b><?php echo JText::_('COBALT_EXECUTIVE'); ?></b> - <?php echo JText::_('COBALT_EXECUTIVE_DESC'); ?><br />
                        <b><?php echo JText::_('COBALT_MANAGERS'); ?></b> - <?php echo JText::_('COBALT_MANAGERS_DESC'); ?><br />
                        <b><?php echo JText::_('COBALT_BASIC_USERS'); ?></b> - <?php echo JText::_('COBALT_BASIC_USERS_DESC'); ?>
                    </div>
                    <form action="index.php" method="post" name="adminForm" id="adminForm">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="1%">
                                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="checkAll(this)" />
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort',  'COBALT_USERS_HEADER_NAME', 'u.last_name', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort', 'COBALT_USERS_HEADER_USERNAME', 'ju.username', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort', 'COBALT_USERS_HEADER_TEAM', 'u.team_id', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort', 'COBALT_USERS_HEADER_EMAIL', 'ju.email', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort', 'COBALT_USERS_HEADER_ROLE', 'u.role_type', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                    <th>
                                        <?php echo JHtml::_('grid.sort',  'COBALT_USERS_HEADER_LOGIN', 'ju.lastvisitDate', $this->listDirn, $this->listOrder); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ( count($this->users) ) { foreach ($this->users as $key=>$user) { ?>

                                    <tr class="row<?php echo $key % 2; ?>">
                                        <td class="center">
                                            <?php echo JHtml::_('grid.id', $key, $user['id']); ?>
                                        </td>
                                        <td class="order"><?php echo JHtml::_('link','index.php?view=users&layout=edit&id='.$user['id'],$user['first_name'].' '.$user['last_name']); ?></td>
                                        <td class="order"><?php echo $user['username']; ?></td>
                                        <td class="order">
                                            <?php
                                            if ( array_key_exists('team_id',$user) && $user['team_id'] ) {
                                                echo $user['team_name'].JText::_("COBALT_TEAM_APPEND");
                                            }
                                            ?>
                                        </td>
                                        <td class="order"><?php echo $user['email']; ?></td>
                                        <td class="order"><?php echo ucwords($user['role_type']); ?></td>
                                        <td class="order"><?php echo date("F j,Y g:iA",strtotime($user['last_login'])); ?></td>
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
                            <input type="hidden" name="view" value="users" />
                            <input type="hidden" name="layout" value="" />
                            <input type="hidden" name="controller" value="" />
                            <input type="hidden" name="boxchecked" value="0" />
                            <input type="hidden" name="filter_order" value="<?php echo $this->listOrder; ?>" />
                            <input type="hidden" name="filter_order_Dir" value="<?php echo $this->listDirn; ?>" />
                            <input type="hidden" name="model" value="users" />
                            <?php echo JHtml::_('form.token'); ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $this->menu['quick_menu']->render(); ?>
</div>
