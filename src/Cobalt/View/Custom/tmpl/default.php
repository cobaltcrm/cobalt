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
defined( '_CEXEC' ) or die( 'Restricted access' );

    $custom = DropdownHelper::generateCustom($this->type,$this->item->id);
    $count = 0;
    echo '<div class="custom-fields">';
    echo '<table class="table table-striped table-hover table-bordered">';
    if ( count($custom) > 0 ) {
    foreach ($custom as $field => $value) {
        $count++;
        $k=$count%3;
        switch ($value['type']) {
            case "forecast":
                $custom_field_filter = array_key_exists('forecast',$this->item) ? $this->item['forecast'] : 0;
            break;
            case "text":
                $custom_field_filter = array_key_exists('selected',$value) && strlen(trim($value['selected'])) > 0 ? $value['selected'] : TextHelper::_('COBALT_CLICK_TO_EDIT');
            break;
            case "number":
                $custom_field_filter = array_key_exists('selected',$value) && strlen(trim($value['selected'])) > 0 ? $value['selected'] : TextHelper::_('COBALT_CLICK_TO_EDIT');
            break;
            case "currency":
                $custom_field_filter = array_key_exists('selected',$value) && strlen(trim($value['selected'])) > 0 ? $value['selected'] : TextHelper::_('COBALT_CLICK_TO_EDIT');
            break;
            case "date":
                $custom_field_filter = array_key_exists('selected',$value) && $value['selected'] != 0 && $value['selected'] != "0000-00-00 00:00:00" ? DateHelper::formatDate($value['selected']) : TextHelper::_('COBALT_CLICK_TO_EDIT');
            break;
            case "picklist":
                $custom_field_filter = ( is_array($value) && array_key_exists('values',$value) && is_array($value['values']) && array_key_exists('selected',$value) && array_key_exists($value['selected'],$value['values']) ) ? $value['values'][$value['selected']] : TextHelper::_('COBALT_CLICK_TO_EDIT');
            break;
        }
            echo '<tr>';
            echo '<th class="customFieldHead">'.$value['name'].'</th>';
            echo '<td>';
                //determine type of input
                switch ($value['type']) {

                    case "text":
                    case "number":
                    case "currency": ?>
                    <span class="editable parent" id="editable_custom_<?php echo $value['id']; ?>_container">
                        <div class="inline" id="editable_custom_<?php echo $value['id']; ?>">
                            <a href="javascript:void(0);" rel="popover" data-title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.$value['name']; ?>" data-html='true' data-content='<div class="clearfix input-append"><form id="<?php echo $value['id']; ?>_form">
                            <input placeholder="<?php echo TextHelper::_('COBALT_CLICK_TO_EDIT'); ?>" type="text" class="inputbox input-small" name="custom_<?php echo $value['id']; ?>" value="<?php echo $value['selected']; ?>" />
                            <a href="javascript:void(0);" class="btn" onclick="Cobalt.saveEditableModal(this);"><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                        </form></div>' ><?php echo $custom_field_filter; ?></a>
                        </div>
                    </span>
                    <?php break;

                    case "picklist": ?>

                    <div class='dropdown'>
                        <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id='custom_<?php echo $value['id']; ?>_field_link'><?php echo $custom_field_filter; ?></a>
                        <ul class="dropdown-menu" role="menu">
                        <?php if ( is_array($value) && array_key_exists('values',$value) && count($value['values']) > 0 ){ foreach ($value['values'] as $id => $name) { ?>
                            <li>
                                <a href="javascript:void(0)" class="dropdown_item" data-field="custom_<?php echo $value['id']; ?>" data-item="<?php echo $this->type; ?>" data-item-id="<?php echo $this->item['id']; ?>" data-value="<?php echo $id; ?>">
                                    <?php echo $name; ?>
                                </a>
                            </li>
                        <?php }} ?>
                        </ul>
                    </div>

                    <?php break;

                    case "forecast" ?>
                    <span id="custom_<?php echo $value['id']; ?>" value="<?php echo $custom_field_filter; ?>" class="forecast">
                        <?php echo ConfigHelper::getCurrency().$custom_field_filter; ?>
                    </span>
                    <?php break;

                    case "date": ?>
                    <!-- make this a custom date picker -->
                        <form name="custom_<?php echo $value['id'];?>_form" id="custom_<?php echo $value['id']; ?>_form">
                            <div class="input-append">
                                <input class="input-small inputbox-hidden date_input editable-modal-datepicker" id="custom_<?php echo $value['id']; ?>" name="custom_<?php echo $value['id']; ?>_hidden" type="text" placeholder="<?php echo TextHelper::_('COBALT_CLICK_TO_EDIT'); ?>"  value="<?php echo $custom_field_filter; ?>"  />
                                <input type="hidden" id="custom_<?php echo $value['id']; ?>_hidden" name="custom_<?php echo $value['id']; ?>" value="<?php echo $custom_field_filter; ?>"  />
                                <span class="input-append-addon"><i class="icon-calendar"></i></span>
                            </div>
                        </form>
                    <?php break; ?>

                <?php }
            echo '</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
    echo '</div>';
