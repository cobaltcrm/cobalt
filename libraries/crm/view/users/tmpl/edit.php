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
defined( '_CEXEC' ) or die( 'Restricted access' );  ?>

<div class="container-fluid">
    <?php echo $this->menu['quick_menu']->render(); ?>
    <div class="row-fluid">
        <div class="span12" id="content">
            <div id="system-message-container"></div>
            <div class="row-fluid">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="span9">
                    <form action="index.php?view=users" method="post" name="adminForm" id="adminForm" class="form-validate" >
                        <legend><h2><?php echo CRMText::_("COBALT_MANAGING_USER"); ?></h2></legend>
                        <ul class="unstyled adminformlist cobaltadminlist">
                            <li>
                                <label><b><?php echo JText::_('COBALT_SELECT_USER_TO_ADD'); ?></b></label>
                                <input data-placement="right" type="text" id="uid_name" name="username" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_START_TYPING_JOOMLA_NAME'); ?>" value="<?php if ( isset($this->user) ) echo $this->user['username']; ?>">
                            </li>
                            <li>
                                <label><b><?php echo JText::_('COBALT_PASSWORD'); ?></b></label>
                                <input data-placement="right" type="password" id="uid_name" name="password" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_START_TYPING_JOOMLA_PASSWORD'); ?>" value="" >
                            </li>
                            <li>
                                <label><b><?php echo JText::_('COBALT_FIRST_NAME'); ?></b></label>
                                <input data-placement="right" class="inputbox required" type="text" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_ENTER_FIRST_NAME_HERE'); ?>" name="first_name" value="<?php echo $this->user['first_name']; ?>" />
                            </li>
                            <li>
                                <label><b><?php echo JText::_('COBALT_LAST_NAME'); ?></b></label>
                                <input data-placement="right" class="inputbox" type="text" name="last_name" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_ENTER_LAST_NAME_HERE'); ?>" value="<?php echo $this->user['last_name']; ?>" />
                            </li>
                            <li>
                                <label><b><?php echo JText::_('COBALT_EMAIL'); ?></b></label>
                                <input data-placement="right" type="text" id="email" class="inputbox" name="email" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_EDIT_USER_EMAIL'); ?>" value="<?php echo $this->user['email']; ?>" />
                            </li>
                            <li>
                                <label><b><?php echo JText::_('COBALT_MEMBER_ROLE'); ?></b></label>
                                <select data-placement="right" class="inputbox" name="role_type" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_ASSIGN_USER_ROLE'); ?>" onchange="updateRole(this.value)" >
                                    <?php echo JHtml::_('select.options', $this->member_roles, 'value', 'text', $this->user['role_type'], true);?>
                                </select>
                                <div id="team_name" <?php if ($this->user['role_type'] != "manager") { ?> style="display:none;" <?php } ?> >
                                    <label><b><?php echo JText::_('COBALT_TEAM_NAME'); ?></b></label>
                                    <input type="text" class="inputbox" name="team_name" value="<?php if ( isset($this->user) ) echo $this->user['team_name']; ?>" />
                                </div>
                            </li>
                            <?php if ($this->user['role_type'] == 'basic') {  ?>
                            <li id="team_assignment">
                            <?php } else { ?>
                            <li style="display:none;" id="team_assignment">
                            <?php } ?>
                                <label><b><?php echo JText::_('COBALT_USERS_HEADER_TEAM'); ?></b></label>
                                <select class="inputbox" id="team_id" name="team_id" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_ASSIGN_USER_TEAM'); ?>"  >
                                        <option value="0"><?php echo JText::_("COBALT_NONE"); ?></option>
                                        <?php echo JHtml::_('select.options', $this->teams, 'value', 'text', $this->user['team_id'], true);?>
                                </select>
                            </li>
                            <?php if ($this->user['role_type'] == 'manager') { ?>
                            <li style="display:none;" id="manager_assignment">
                                <label><b><?php echo JText::_('COBALT_ASSIGN_NEW_MANAGER'); ?><span class="required">*</span></b></label>
                                <select class="inputbox" id="manager_id" name="manager_assignment" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_ASSIGN_MANAGER'); ?>"  >
                                        <option value=""><?php echo JText::_('COBALT_NEW_MANAGER'); ?></option>
                                        <?php echo JHtml::_('select.options', $this->managers, 'value', 'text', '', true);?>
                                </select>
                            </li>
                            <?php } ?>
                            <li>
                                <label class="checkbox" >
                                    <input value="1" data-placement="left" type="checkbox" name="admin" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_IF_CHECKED_ADMINISTRATOR'); ?>" <?php echo ($this->user['admin'] ? 'checked' : ''); ?> />
                                    <?php echo JText::_("COBALT_ADMNISTRATOR"); ?>
                                </label>
                            </li>
                            <li>
                                <label class="checkbox" >
                                      <input data-placement="left" type="checkbox" name="can_delete" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_IF_CHECKED_DELETE'); ?>" <?php echo ($this->user['can_delete'] ? 'checked' : ''); ?> />
                                      <?php echo JText::_('COBALT_ALLOWED_TO_DELETE'); ?>
                                </label>
                            </li>
                            <li>
                                <label class="checkbox" >
                                    <input data-placement="left" type="checkbox" name="exports" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_IF_CHECKED_EXPORT'); ?>" <?php echo ($this->user['exports'] ? 'checked' : ''); ?> />
                                    <?php echo JText::_('COBALT_ALLOWED_TO_EXPORT'); ?>
                                </label>

                            </li>
                            <li>
                                <label>
                                    <b><?php echo JText::_('COBALT_USER_COLOR'); ?></b>
                                </label>
                                <input data-placement="right" id="user_color" type="text" class="required hascolorpicker" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_USER_COLOR'); ?>" name="color" value="<?php echo $this->user['color']; ?>" />
                            </li>
                        </ul>
                        <div>
                            <?php if ($this->user['id']) { ?>
                                <input type="hidden" name="id" value="<?php echo $this->user['id']; ?>" />
                            <?php } ?>
                            <input type="hidden" name="controller" value="" />
                            <input type="hidden" name="model" value="users" />
                            <input type="hidden" name="view" value="users" />
                            <?php echo JHtml::_('form.token'); ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $this->menu['quick_menu']->render(); ?>
</div>
