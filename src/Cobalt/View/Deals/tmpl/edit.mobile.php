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

$deal = $this->deal;
$app = JFactory::getApplication();
?>
<h1><?php echo ucwords($deal['header']); ?></h1>
<form id="deal_form" method="post" name="new_deal" action="<?php echo JRoute::_('index.php?controller=save&model=deal&return=deals'); ?>" target="hidden" onsubmit="save()">
    <div id="editForm">
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_DEAL_NAME'); ?></div>
            <div class="cobaltValue wide"><input class="inputbox" type="text" name="name" value="<?php if(count($deal)>0) echo $deal['name']; ?>" /></div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_DEAL_SUMMARY'); ?></div>
            <div class="cobaltValue wide"><textarea class="inputbox" name="summary" cols="50" rows="5"><?php if(count($deal)>0) echo $deal['summary']; ?></textarea></div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_DEAL_COMPANY'); ?></div>
            <div class="cobaltValue">
                    <?php
                        if ( $app->input->get('company_id') ) {
                            echo $deal['company_name'];
                        } else {
                            echo DropdownHelper::generateDropdown('company',$deal['company_id']);
                        }
                    ?>
            </div>
        </div>
            <?php if ( array_key_exists('person_id',$deal) && !is_null($deal['person_id']) ) { ?>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_PERSON')); ?></div>
                <div class="cobaltValue"><?php echo $deal['person_name']; ?></div>
            </div>
            <?php } ?>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_DEAL_AMOUNT'); ?></div>
                <div class="cobaltValue"><input class="inputbox" type="text" name="amount" value="<?php if(count($deal)>0) echo $deal['amount']; ?>" /></div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_DEAL_STAGE'); ?></div>
                <div class="cobaltValue">
                    <?php echo DropdownHelper::generateDropdown('stage',$deal['stage_id']); ?>
                </div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_DEAL_SOURCE'); ?></div>
                <div class="cobaltValue">
                    <?php echo DropdownHelper::generateDropdown('source',$deal['source_id']); ?>
                </div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_DEAL_PROBABILITY'); ?></div>
                <div class="cobaltValue">
                    <input type="text" id="probability" class="inputbox" name="probability" />
                    <div id="slider"></div>
                </div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_DEAL_STATUS'); ?></div>
                <div class="cobaltValue">
                    <?php echo DropdownHelper::generateDropdown('deal_status',$deal['status_id']); ?>
                </div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_DEAL_CLOSE'); ?></div>
                <div class="cobaltValue"><input class="inputbox" type="text" name="expected_close" value="<?php if(count($deal)>0) echo $deal['expected_close']; ?>"></div>
            </div>
            <?php
                $custom = DropdownHelper::generateCustom('deal',$deal['id']);
                $custom_data = ( array_key_exists('id',$deal) ) ? DealHelper::getCustomData($deal['id'],"deal") : array();
                foreach ($custom as $field => $value) {
                    if ($value['type'] != 'forecast') {
                        $custom_field_filter = ( count($custom_data) != 0 ) ? $custom_data[$value['id']] : '';
                        echo '<div class="cobaltRow">';
                        echo '<div class="cobaltField">'.$value['name'].'</div>';
                        echo '<div class="cobaltValue">';
                            //determine type of input
                            switch ($value['type']) {
                                case "text": ?>
                                <input class="inputbox" name="custom_<?php echo $value['id']; ?>" value="<?php echo $custom_field_filter; ?>" />
                                <?php break;
                                case "picklist": ?>
                                        <select id="custom_<?php echo $value['id']; ?>" class="inputbox" name="custom_<?php echo $value['id']; ?>">
                                            <?php echo JHtml::_('select.options', $value['values'], 'value', 'text', $custom_field_filter, true); ?>
                                        </select>
                                <?php break;
                                case "number": ?>
                                <input class="inputbox" name="custom_<?php echo $value['id']; ?>" value="<?php echo $custom_field_filter; ?>" />
                                <?php break;
                                case "currency": ?>
                                <input class="inputbox" name="custom_<?php echo $value['id']; ?>" value="<?php echo $custom_field_filter; ?>" />
                                <?php break;
                                case "date": ?>
                                <!-- make this a custom date picker -->
                                    <input class="inputbox" name="custom_<?php echo $value['id']; ?>" class="filter_input date_input" name="" type="text" value="<?php echo $custom_field_filter; ?>"  />
                                <?php break; ?>
                            <?php }
                        echo '</div>';
                        echo '</div>';
                } }
            ?>
            <span class="actions"><input class="button" type="submit" value="<?php echo TextHelper::_('COBALT_SAVE_BUTTON'); ?>"> <a href="javascript:void(0);" onclick="window.history.back()"><?php echo TextHelper::_('COBALT_CANCEL_BUTTON'); ?></a></span>
    <?php
        if ( array_key_exists('id',$deal) ) {
            echo '<input class="inputbox" type="hidden" name="id" value="'.$deal['id'].'" />';
        }
        if ( array_key_exists('person_id',$deal) AND $app->input->get('person_id') ) {
            echo '<input class="inputbox" type="hidden" name="person_id" value="'.$deal['person_id'].'" />';
        }
        if ( array_key_exists('company_id',$deal) AND $app->input->get(';company_id') ) {
            echo '<input class="inputbox" type="hidden" name="company_id" value="'.$deal['company_id'].'" />';
        }
    ?>
    </div>
</form>
