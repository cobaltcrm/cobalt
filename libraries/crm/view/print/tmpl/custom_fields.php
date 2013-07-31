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
    $custom = CobaltHelperDropdown::generateCustom($this->item_type,$this->item['id']);
    $count = 0;

      if ( count($custom) > 0 ) { ?>

    <h2><?php echo CRMText::_('COBALT_EDIT_CUSTOM'); ?></h2>

            <div class="columncontainer">
                <div class="twocolumn">
                    <table class="com_cobalt_table">

    <?php foreach ($custom as $field => $value) {
        $count++;
        $k=$count%3;
        switch ($value['type']) {
            case "forecast":
                $custom_field_filter = array_key_exists('forecast',$this->item) ? $this->item['forecast'] : 0;
            break;
            case "text":
                $custom_field_filter = array_key_exists('selected',$value) ? $value['selected'] : "";
            break;
            case "number":
                $custom_field_filter = array_key_exists('selected',$value) ? $value['selected'] : "";
            break;
            case "currency":
                $custom_field_filter = array_key_exists('selected',$value) ? $value['selected'] : "";
            break;
            case "date":
                $custom_field_filter = array_key_exists('selected',$value) && $value['selected'] != 0 && $value['selected'] != "0000-00-00 00:00:00" ? CobaltHelperDate::formatDate($value['selected']) : "";
            break;
            case "picklist":
                $custom_field_filter = ( is_array($value) && array_key_exists('values',$value) && is_array($value['values']) && array_key_exists('selected',$value) && array_key_exists($value['selected'],$value['values']) ) ? $value['values'][$value['selected']] : "";
            break;
        }
            echo '<tr>';
            echo '<th class="customFieldHead">'.$value['name'].'</th>';
            echo '<td>';
                //determine type of input
                switch ($value['type']) {

                    case "text":
                    case "number":
                    case "currency":
                    case "picklist":
                    case "date":
                        echo '<span>'.$custom_field_filter.'</span>';
                    break;
                    case "forecast":
                        echo '<span class="forecast">';
                            echo CobaltHelperConfig::getCurrency().$custom_field_filter;
                        echo '</span>';
                    break;
                }
            echo '</td>';
            echo '</tr>';
    }
    echo '</div>';
    echo '</table>';
      echo '</div>';
}
