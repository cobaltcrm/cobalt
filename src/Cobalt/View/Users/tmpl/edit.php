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
    <div class="row">
        <div class="col-sm-12" id="content">
            <div id="system-message-container"></div>
            <div class="row">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="col-md-9">
                    <form action="index.php?view=users" method="post" name="adminForm" id="adminForm" class="form-horizontal" autocomplete="off">
                        <legend><h2><?php echo TextHelper::_("COBALT_MANAGING_USER"); ?></h2></legend>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="uid_name">
                                <?php echo JText::_('COBALT_SELECT_USER_TO_ADD'); ?>
                            </label>
                            <div class="col-sm-10">
                                <input 
                                    class="form-control" 
                                    data-placement="right" 
                                    type="text" 
                                    id="uid_name" 
                                    name="username" 
                                    rel="tooltip" 
                                    data-original-title="<?php echo JText::_('COBALT_START_TYPING_JOOMLA_NAME'); ?>" 
                                    value="<?php if ( isset($this->user) ) echo $this->user['username']; ?>"
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="uid_name">
                                <?php echo JText::_('COBALT_PASSWORD'); ?>
                            </label>
                            <div class="col-sm-10">
                                <input 
                                    class="form-control" 
                                    data-placement="right" 
                                    type="password" 
                                    id="uid_name" 
                                    name="password" 
                                    rel="tooltip" 
                                    data-original-title="<?php echo JText::_('COBALT_START_TYPING_JOOMLA_PASSWORD'); ?>" 
                                    value=""
                                    autocomplete="off" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="first_name">
                                <?php echo JText::_('COBALT_FIRST_NAME'); ?>
                            </label>
                            <div class="col-sm-10">
                                <input 
                                    data-placement="right" 
                                    class="form-control required" 
                                    type="text" 
                                    rel="tooltip" 
                                    data-original-title="<?php echo JText::_('COBALT_ENTER_FIRST_NAME_HERE'); ?>" 
                                    name="first_name" 
                                    id="first_name" 
                                    value="<?php echo $this->user['first_name']; ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="last_name">
                                <?php echo JText::_('COBALT_LAST_NAME'); ?>
                            </label>
                            <div class="col-sm-10">
                                <input 
                                    data-placement="right" 
                                    class="form-control" 
                                    type="text" 
                                    name="last_name" 
                                    id="last_name" 
                                    rel="tooltip" 
                                    data-original-title="<?php echo JText::_('COBALT_ENTER_LAST_NAME_HERE'); ?>" 
                                    value="<?php echo $this->user['last_name']; ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="email">
                                <?php echo JText::_('COBALT_EMAIL'); ?>
                            </label>
                            <div class="col-sm-10">
                                <input 
                                    data-placement="right" 
                                    type="text" 
                                    id="email" 
                                    class="form-control" 
                                    name="email" 
                                    rel="tooltip" 
                                    data-original-title="<?php echo JText::_('COBALT_EDIT_USER_EMAIL'); ?>" 
                                    value="<?php echo $this->user['email']; ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="role_type">
                                <?php echo JText::_('COBALT_MEMBER_ROLE'); ?>
                            </label>
                            <div class="col-sm-10">
                                <select 
                                    data-placement="right" 
                                    class="form-control" 
                                    name="role_type" 
                                    id="role_type" 
                                    rel="tooltip" 
                                    data-original-title="<?php echo JText::_('COBALT_ASSIGN_USER_ROLE'); ?>" 
                                    onchange="updateRole(this.value)" >
                                    <?php echo JHtml::_('select.options', $this->member_roles, 'value', 'text', $this->user['role_type'], true);?>
                                </select>
                                <div id="team_name" <?php if ($this->user['role_type'] != "manager") { ?> style="display:none;" <?php } ?> >
                                    <label class="col-sm-2 control-label" for="team_name">
                                        <?php echo JText::_('COBALT_TEAM_NAME'); ?>
                                    </label>
                                    <div class="col-sm-10">
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            name="team_name" 
                                            id="team_name"
                                            value="<?php if ( isset($this->user) ) echo $this->user['team_name']; ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($this->user['role_type'] == 'basic') {  ?>
                        <div class="form-group" id="team_assignment">
                        <?php } else { ?>
                        <div class="form-group" style="display:none;" id="team_assignment">
                        <?php } ?>
                            <label class="col-sm-2 control-label" for="team_id">
                                <?php echo JText::_('COBALT_USERS_HEADER_TEAM'); ?>
                            </label>
                            <div class="col-sm-10">
                                <select 
                                    class="form-control" 
                                    id="team_id" 
                                    name="team_id" 
                                    rel="tooltip" 
                                    data-original-title="<?php echo JText::_('COBALT_ASSIGN_USER_TEAM'); ?>" >
                                    <option value="0"><?php echo JText::_("COBALT_NONE"); ?></option>
                                    <?php echo JHtml::_('select.options', $this->teams, 'value', 'text', $this->user['team_id'], true);?>
                                </select>
                            </div>
                        </div>
                        <?php if ($this->user['role_type'] == 'manager') { ?>
                        <div class="form-group" style="display:none;" id="manager_assignment">
                            <label class="col-sm-2 control-label" for="manager_id">
                                <?php echo JText::_('COBALT_ASSIGN_NEW_MANAGER'); ?><span class="required">*</span>
                            </label>
                            <div class="col-sm-10">
                                <select 
                                    class="form-control" 
                                    id="manager_id" 
                                    name="manager_assignment" 
                                    rel="tooltip" 
                                    data-original-title="<?php echo JText::_('COBALT_ASSIGN_MANAGER'); ?>" >
                                    <option value=""><?php echo JText::_('COBALT_NEW_MANAGER'); ?></option>
                                    <?php echo JHtml::_('select.options', $this->managers, 'value', 'text', '', true);?>
                                </select>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="user_color">
                                <?php echo JText::_('COBALT_USER_COLOR'); ?>
                            </label>
                            <div class="col-sm-10">
                                <input 
                                    data-placement="right" 
                                    id="user_color" 
                                    type="color" 
                                    class="required" 
                                    rel="tooltip" 
                                    data-original-title="<?php echo JText::_('COBALT_USER_COLOR'); ?>" 
                                    name="color" 
                                    value="<?php echo $this->user['color']; ?>" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="checkbox col-sm-10 pull-right">
                                <input 
                                    value="1" 
                                    data-placement="left" 
                                    type="checkbox" 
                                    name="admin" 
                                    rel="tooltip" 
                                    data-original-title="<?php echo JText::_('COBALT_IF_CHECKED_ADMINISTRATOR'); ?>" 
                                    <?php echo ($this->user['admin'] ? 'checked' : ''); ?> />
                                <?php echo JText::_("COBALT_ADMNISTRATOR"); ?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="checkbox col-sm-10 pull-right">
                                <input 
                                    data-placement="left" 
                                    type="checkbox" 
                                    name="can_delete" 
                                    rel="tooltip" 
                                    data-original-title="<?php echo JText::_('COBALT_IF_CHECKED_DELETE'); ?>" 
                                    <?php echo ($this->user['can_delete'] ? 'checked' : ''); ?> />
                                <?php echo JText::_('COBALT_ALLOWED_TO_DELETE'); ?>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="checkbox col-sm-10 pull-right">
                                <input 
                                    data-placement="left" 
                                    type="checkbox" 
                                    name="exports" 
                                    rel="tooltip" 
                                    data-original-title="<?php echo JText::_('COBALT_IF_CHECKED_EXPORT'); ?>" 
                                    <?php echo ($this->user['exports'] ? 'checked' : ''); ?> />
                                <?php echo JText::_('COBALT_ALLOWED_TO_EXPORT'); ?>
                            </label>
                        </div>
                        <?php if ($this->user['id']) { ?>
                            <input type="hidden" name="id" value="<?php echo $this->user['id']; ?>" />
                        <?php } ?>
                        <input type="hidden" name="controller" value="" />
                        <input type="hidden" name="model" value="users" />
                        <input type="hidden" name="view" value="users" />
                        <?php echo JHtml::_('form.token'); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php echo $this->menu['quick_menu']->render(); ?>
</div>
