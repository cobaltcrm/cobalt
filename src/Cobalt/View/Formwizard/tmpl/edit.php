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
                    <form action="index.php?view=formwizard" method="post" name="adminForm" id="adminForm" class="form-validate" >
                        <div class="row-fluid">
                            <legend><h2><?php echo TextHelper::_('COBALT_EDITING_CUSTOM_FORM'); ?></h2></legend>
                            <ul class="list-unstyled adminformlist cobaltadminlist">
                                <li>
                                    <label><b><?php echo JText::_('COBALT_FORM_TYPE'); ?></b></label>
                                    <?php echo $this->form_types; ?>
                                </li>
                                <li>
                                    <label><b><?php echo JText::_('COBALT_FORM_NAME'); ?></b></label>
                                    <input type="text" id="name" name="name" class="required" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_NAME_YOUR_FORM'); ?>" value="<?php if(isset($this->form)) echo $this->form['name']; ?>">
                                </li>
                                <li>
                                    <label><b><?php echo JText::_('COBALT_FORM_DESCRIPTION'); ?></b></label>
                                    <textarea id="description" name="description"><?php if(isset($this->form)) echo $this->form['description']; ?></textarea>
                                </li>
                                 <li>
                                    <label><b><?php echo JText::_('COBALT_FORM_FIELDS'); ?></b></label>
                                    <input onclick="showFieldCheckboxes()" type="button" class="btn btn-primary" data-toggle="modal" href="#show_fields_button_modal" id="show_fields_button" value="<?php echo JText::_('COBALT_SELECT_FIELDS'); ?>" />
                                </li>
                                 <li>
                                    <label><b><?php echo JText::_('COBALT_FORM_RETURN_URL'); ?></b></label>
                                    <input type="text" id="return_url" name="return_url" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_RETURN_URL_TOOLTIP'); ?>" value="<?php if(isset($this->form)) echo $this->form['return_url']; ?>">
                                </li>
                                <li>
                                    <label><b><?php echo JText::_('COBALT_OWNER'); ?></b></label>
                                    <input class="required" type="text" id="owner_id" name="owner_id_input" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_OWNER_TOOLTIP'); ?>" value="<?php if(isset($this->form)) echo $this->form['owner_name']; ?>">
                                    <input class="required" type="hidden" id="owner_id_hidden" name="owner_id" value="<?php if ( isset($this->form) ) echo $this->form['owner_id']; ?>" />
                                </li>
                                 <li>
                                    <label><b><?php echo JText::_('COBALT_FORM_HTML'); ?></b></label>
                                    <textarea wrap="off" cols="20" rows="15" style="width:300px !important;" id="fields" onclick="selectTextarea(this);" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_FORM_HTML_TOOLTIP'); ?>" name="html"><?php if(isset($this->form)) echo $this->form['html']; ?></textarea>
                                </li>
                            </ul>
                            <div>
                                <?php if ( isset($this->form) && array_key_exists('id',$this->form) ) { ?>
                                    <input type="hidden" name="id" id="form_id" value="<?php echo $this->form['id']; ?>" />
                                <?php } else { ?>
                                    <input type="hidden" name="temp_id" id="form_id" value="<?php echo $this->form_id; ?>" />
                                <?php } ?>
                                <input type="hidden" name="controller" value="" />
                                <input type="hidden" name="model" value="formwizard" />
                                <?php echo JHtml::_('form.token'); ?>
                            </div>
                        </div>
                        <div class="modal hide fade in" id="show_fields_button_modal">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">×</button>
                                <h3><?php echo JText::_('COBALT_SELECT_FIELDS'); ?></h3>
                            </div>
                            <div class="modal-body">
                                <?php if ( isset($this->fields) && count($this->fields) > 0 ) {
                                    foreach ($this->fields as $type => $fields) { ?>
                                    <div id="<?php echo $type; ?>_fields" class="field_checkbox_container">
                                        <div class="span5">
                                        <ul class="list-unstyled">
                                                <?php
                                                $i=0;
                                                foreach ($fields as $key => $field) { ?>
                                                <?php $row = $i%5; ?>
                                                <?php if ($row == 0 && $i != 0) { ?>
                                                </ul>
                                                </div>
                                                <div class="span5">
                                                <ul class="list-unstyled">
                                                <?php } ?>
                                                <?php $checked = isset($this->form) && is_array($this->form['fields']) && in_array($field['name'],$this->form['fields']) ? "checked='checked'" : ""; ?>
                                                <li><label class="checkbox"><input <?php echo $checked; ?> id="<?php echo $type.'_field_'.$key; ?>" type="checkbox" onclick="updateFields()" name="fields[]" value="<?php echo $field['name']; ?>" /><?php echo $field['display']; ?></label></li>
                                            <?php $i++; } ?>
                                        </ul>
                                        </div>
                                    </div>
                                <?php } } ?>
                            </div>
                            <div class="modal-footer">
                                <a href="#" class="btn btn-primary" data-dismiss="modal"><?php echo TextHelper::_('COBALT_CLOSE'); ?></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php echo $this->menu['quick_menu']->render(); ?>
    </div>
</div>
