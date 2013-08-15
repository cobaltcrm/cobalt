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
$raw = $app->input->get('format'); ?>
<form id="edit_form" method="post" name="new_deal" action="<?php echo JRoute::_('index.php?task=save'); ?>" onsubmit="return save(this)">
    <?php if (!$raw) { ?>
    <div class="page-header">
        <div class="btn-group pull-right">
            <button type="button" class="btn btn-success" onclick="jQuery('#edit_form').submit();"><?php echo TextHelper::_('COBALT_SAVE_BUTTON'); ?></button>
            <button type="button" class="btn btn-default" onclick="window.history.back()"><?php echo TextHelper::_('COBALT_CANCEL_BUTTON'); ?></button>
        </div>
        <h1><?php echo ucwords($deal['header']); ?></h1>
    </div>
<?php } ?>
    <div class="tabbable"> <!-- Only required for left/right tabs -->
        <ul class="nav nav-tabs">
            <li class='active'><a href="#basic" data-toggle="tab"><?php echo TextHelper::_('COBALT_BASIC'); ?></a></li>
            <li><a href="#dealinfo" data-toggle="tab"><?php echo TextHelper::_('COBALT_DEAL_INFO'); ?></a></li>
            <li><a href="#customfields" data-toggle="tab"><?php echo TextHelper::_('COBALT_CUSTOM_FIELDS'); ?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="basic">
                <div class="control-group">
                    <label class="control-label" for="name"><?php echo TextHelper::_('COBALT_DEAL_NAME'); ?><span class="required">*</span></label>
                    <div class="controls"><input type="text" class="form-control" name="name" placeholder="<?php echo TextHelper::_('COBALT_DEAL_NAME_NULL'); ?>" value="<?php echo isset($deal['name']) ? $deal['name'] : ''; ?>" /></div>
                </div>

                    <div class="control-group">
                        <label class="control-label" for="summary"><?php echo TextHelper::_('COBALT_DEAL_SUMMARY'); ?></label>
                        <div class="controls"><textarea class="form-control" name="summary" cols="50" placeholder="<?php echo TextHelper::_('COBALT_DEAL_SUMMARY_NULL'); ?>" rows="5"><?php echo isset($deal['summary']) ? $deal['summary'] : ''; ?></textarea></div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="company"><?php echo ucwords(TextHelper::_('COBALT_DEAL_COMPANY')); ?></label>
                        <div class="controls">
                                <input type="text" onkeyup="checkCompanyName(this);" class="form-control" name="company" id="company_name" value="<?php if ( array_key_exists('company_name',$deal) ) echo $deal['company_name']; ?>" />
                                <input type="hidden" name="company_id" id="company_id" value="<?php echo isset($deal['company_id']) ? $deal['company_id'] : ''; ?>" />
                                <div class="alert" style="display: none;" id="company_message"></div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="primary_contact_name"><?php echo ucwords(TextHelper::_('COBALT_PRIMARY_CONTACT')); ?></label>
                        <div class="controls">
                                <input type="text" onkeyup="checkPersonName(this);" class="form-control" name="primary_contact_name" id="primary_contact_name" value="<?php if ( array_key_exists('primary_contact_id',$deal) && $deal['primary_contact_id'] > 0 ) echo $deal['primary_contact_first_name'].' '.$deal['primary_contact_last_name']; ?>" />
                                <input type="hidden" name="primary_contact_id" id="primary_contact_id" value="<?php if ( array_key_exists('primary_contact_id',$deal) ) echo $deal['primary_contact_id']; ?>" />
                                <div id="person_message"></div>
                        </div>
                    </div>
                        <?php if ( array_key_exists('person_id',$deal) && !is_null($deal['person_id']) ) {
                            echo '<div class="controls">';
                            echo	'<label class="control-label" for="person">'.ucwords(TextHelper::_('COBALT_PERSON')).'</label>';
                            echo	'<div class="controls">'.$deal['person_name'].'</div>';
                            echo '</div>';
                        } ?>
            </div>
            <div class="tab-pane" id="dealinfo">
                <div class="control-group">
                    <label class="control-label" for="amount"><?php echo TextHelper::_('COBALT_DEAL_AMOUNT'); ?></label>
                    <div class="controls">
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo ConfigHelper::getConfigValue('currency'); ?></span>
                            <input class="form-control required" type="text" name="amount" value="<?php echo isset($deal['amount']) ? $deal['amount'] : ''; ?>" />
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="stage"><?php echo TextHelper::_('COBALT_DEAL_STAGE'); ?></label>
                    <div class="controls">
                        <?php echo DropdownHelper::generateDropdown('stage', isset($deal['stage_id']) ? $deal['stage_id'] : ''); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="source"><?php echo TextHelper::_('COBALT_DEAL_SOURCE'); ?></label>
                    <div class="controls">
                        <?php echo DropdownHelper::generateDropdown('source', isset($deal['source_id']) ? $deal['source_id'] : ''); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="probability"><?php echo TextHelper::_('COBALT_DEAL_PROBABILITY'); ?></label>
                    <div class="controls">
                        <div class="input-group">
                            <input type="text" class="form-control" name="probability" value="<?php if ( array_key_exists('probability', $deal) ) echo $deal['probability']; ?>" />
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="status"><?php echo TextHelper::_('COBALT_DEAL_STATUS'); ?></label>
                    <div class="controls">
                        <?php echo DropdownHelper::generateDealStatuses(isset($deal['status_id']) ? $deal['status_id'] : ''); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="expected_close"><?php echo TextHelper::_('COBALT_DEAL_CLOSE'); ?></label>
                    <div class="controls">
                        <input class="form-control date_input" type="text" id="expected_close" name="expected_close_input" value="<?php echo DateHelper::formatDate(isset($deal['expected_close']) ? $deal['expected_close'] : ''); ?>">
                        <input type="hidden" id="expected_close_hidden" name="expected_close" value="<?php if ( array_key_exists('expected_close',$deal) && !is_null($deal['expected_close']) && $deal['expected_close'] != "" && $deal['expected_close'] != "0000-00-00"   ) { echo $deal['expected_close']; } else { echo date("Y-m-d"); } ?>" />
                    </div>
                </div>
                <?php if ( array_key_exists('actual_close',$deal) && $deal['actual_close'] != "0000-00-00 00:00:00" && $deal['actual_close'] != "") { ?>
                <div class="control-group">
                    <label class="control-label" for="actual_close"><?php echo TextHelper::_('COBALT_DEAL_ACTUAL_CLOSE'); ?></label>
                    <div class="controls">
                        <input class="form-control date_input required" type="text" id="actual_close" name="actual_close_input" value="<?php echo DateHelper::formatDate(isset($deal['actual_close']) ? $deal['actual_close'] : ''); ?>">
                        <input type="hidden" id="actual_close_hidden" name="actual_close" value="<?php echo isset($deal['actual_close']) ? $deal['actual_close'] : ''; ?>" />
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="tab-pane" id="customfields">
                <?php echo $this->edit_custom_fields_view->render(); ?>
            </div>
        </div>
    </div>
    <?php
        if ( array_key_exists('id',$deal) ) {
            echo '<input class="form-control" type="hidden" name="id" value="'.$deal['id'].'" />';
        }
        if ( array_key_exists('person_id',$deal) AND $app->input->get('person_id') ) {
            echo '<input class="form-control" type="hidden" name="person_id" value="'.$deal['person_id'].'" />';
        }
        if ( array_key_exists('company_id',$deal) AND $app->input->get('company_id') ) {
            echo '<input class="form-control" type="hidden" name="company_id" value="'.$deal['company_id'].'" />';
        }
    ?>
    <input type="hidden" name="model" value="deal" />
</form>
