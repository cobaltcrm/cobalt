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
$app = \Cobalt\Factory::getApplication();
$report = $this->report[0]; ?>

<table class="com_cobalt_table table table-hover table-striped">
    <thead>
        <tr>
            <?php $fields = unserialize($report['fields']); ?>
            <?php $rows = array(); ?>
                <?php foreach ($fields as $id => $text) { ?>
                    <?php $rows[] = $id; ?>
                    <?php $primary_contact = strstr($id,"primary_contact_"); ?>
                    <?php $custom_field = strstr($id,"custom_field_"); ?>
                    <?php if (!$primary_contact && !$custom_field) { ?>
                        <th><div class="sort_order"><a class="d.<?php echo $id; ?>" onclick="sortTable('d.<?php echo $id; ?>',this)"><?php echo $text; ?></a></div></th>
                    <?php } ?>
                    <?php if ($primary_contact) { ?>
                        <?php switch ($id) {
                        case "primary_contact_name": ?>
                            <th><div class="sort_order"><a class="p.last_name" onclick="sortTable('p.last_name',this)"><?php echo $text; ?></a></div></th>
                            <?php break;
                        case "primary_contact_email": ?>
                            <th><div class="sort_order"><a class="p.email" onclick="sortTable('p.email',this)"><?php echo $text; ?></a></div></th>
                        <?php break;
                        case "primary_contact_phone": ?>
                            <th><div class="sort_order"><a class="p.phone" onclick="sortTable('p.phone',this)"><?php echo $text; ?></a></div></th>
                        <?php break;
                        case "primary_contact_company_name": ?>
                            <th><div class="sort_order"><a class="pc.name" onclick="sortTable('pc.name',this)"><?php echo $text; ?></a></div></th>
                        <?php break;
                        case "primary_contact_city": ?>
                            <th><div class="sort_order"><a class="p.city" onclick="sortTable('p.city',this)"><?php echo $text; ?></a></div></th>
                        <?php break;
                        case "primary_contact_state": ?>
                            <th><div class="sort_order"><a class="p.state" onclick="sortTable('p.state',this)"><?php echo $text; ?></a></div></th>
                        <?php break; ?>
                         <?php } ?>
                    <?php } ?>
                    <?php if ($custom_field) { ?>
                        <th><div class="sort_order"><a class="" onclick="sortTable('',this)"><?php echo $text; ?></a></div></th>
                    <?php } ?>
                <?php } ?>
        </tr>
        <?php if ( $app->input->get('view') != "print" ) { ?>
        <tr>
            <?php foreach ($fields as $id => $text) { ?>
                    <?php $primary_contact = strstr($id,"primary_contact_"); ?>
                    <?php $custom_field = strstr($id,"custom_"); ?>
                    <?php if (!$primary_contact && !$custom_field) { ?>
                        <?php switch ($id) {
                        case "name": ?>
                            <?php $deal_filter = $this->state->get('Report.'.$report['id'].'_custom_report_name'); ?>
                            <th><input class="input input-small filter_input" name="deal_name" type="text" value="<?php echo $deal_filter; ?>"  /></th>
                        <?php break; ?>
                        <?php case "owner_id": ?>
                        <th>
                            <select class="span1 filter_input" name="owner_id" id="owner_id">
                                <?php $user_filter = $this->state->get('Report.'.$report['id'].'_custom_report_owner_id'); ?>
                                <?php if ( UsersHelper::getRole() != 'basic' ) { ?>
                                    <?php   $all = array();
                                        $all[] = JHTML::_('select.option','all',TextHelper::_('COBALT_ALL'));
                                        echo JHtml::_('select.options',$all,'value','text',$user_filter,true);
                                    ?>
                                <?php } ?>
                                 <optgroup label="<?php echo TextHelper::_('COBALT_MEMBERS'); ?>" class="member" id="member" >
                                    <?php   $member = array();
                                            $member[] = JHTML::_('select.option',UsersHelper::getUserId(),TextHelper::_('COBALT_ME'));
                                            echo JHtml::_('select.options',$member,'value','text',$user_filter,true);
                                    ?>
                                    <?php echo JHtml::_('select.options', $this->user_names, 'value', 'text', $user_filter, true); ?>
                                </optgroup>
                                <?php if ( UsersHelper::getRole() == 'exec' ) { ?>
                                <optgroup label="<?php echo TextHelper::_('COBALT_TEAM'); ?>" class="team" id="team" >
                                    <?php echo JHtml::_('select.options', $this->team_names, 'value', 'text', $user_filter, true); ?>
                                </optgroup>
                                <?php } ?>
                            </select>
                        </th>
                        <?php break; ?>
                        <?php case "summary" : ?>
                        <th></th>
                        <?php break; ?>
                        <?php case "amount" : ?>
                        <th>
                            <select class="span1 filter_input" name="deal_amount">
                                <option value="all"><?php echo TextHelper::_('COBALT_ALL'); ?></option>
                                <?php $amount_filter = $this->state->get('Report.'.$report['id'].'_custom_report_amount'); ?>
                                <?php echo JHtml::_('select.options', $this->deal_amounts, 'value', 'text', $amount_filter, true); ?>
                            </select>
                        </th>
                        <?php break; ?>
                        <?php case "source_id" : ?>
                        <th>
                            <select class="span1 filter_input" name="source_id">
                                <option value="all"><?php echo TextHelper::_('COBALT_ALL'); ?></option>
                                <?php $source_filter = $this->state->get('Report.'.$report['id'].'_custom_report_source_id'); ?>
                                <?php echo JHtml::_('select.options', $this->deal_sources, 'value', 'text', $source_filter, true); ?>
                            </select>
                        </th>
                        <?php break; ?>
                        <?php case "stage_id" : ?>
                        <th>
                            <select class="span1 filter_input" name="stage_id">
                                <?php $stage_filter = $this->state->get('Report.'.$report['id'].'_custom_report_stage_id'); ?>
                                <?php echo JHtml::_('select.options', $this->deal_stages, 'value', 'text', $stage_filter, true); ?>
                            </select>
                        </th>
                        <?php break; ?>
                        <?php case "status_id" : ?>
                        <th>
                            <select class="span1 filter_input" name="status_id">
                                <option value="all"><?php echo TextHelper::_('COBALT_ALL'); ?></option>
                                <?php $status_filter = $this->state->get('Report.'.$report['id'].'_custom_report_status_id'); ?>
                                <?php echo JHtml::_('select.options', $this->deal_statuses, 'value', 'text', $status_filter, true); ?>
                            </select>
                        </th>
                        <?php break; ?>
                        <?php case "created" : ?>
                        <th>
                            <select class="span1 filter_input" name="created">
                                <?php $created_filter = $this->state->get('Report.'.$report['id'].'_custom_report_created'); ?>
                                <option value="all"><?php echo TextHelper::_('COBALT_ALL'); ?></option>
                                <?php echo JHtml::_('select.options', $this->created_dates, 'value', 'text', $created_filter, true); ?>
                            </select>
                        </th>
                        <?php break; ?>
                        <?php case "modified" : ?>
                        <th>
                            <select class="span1 filter_input" name="modified">
                                <?php $modified_filter = $this->state->get('Report.'.$report['id'].'_custom_report_modified'); ?>
                                <?php echo JHtml::_('select.options', $this->modified_dates, 'value', 'text', $modified_filter, true); ?>
                            </select>
                        </th>
                        <?php break; ?>
                        <?php case "probability" :?>
                        <th></th>
                        <?php break; ?>
                        <?php case "expected_close" :?>
                        <th>
                            <select class="span1 filter_input" name="expected_close">
                                 <?php $expected_close_filter = $this->state->get('Report.'.$report['id'].'_custom_report_expected_close'); ?>
                                <?php echo JHtml::_('select.options', $this->deal_close_dates, 'value', 'text', $expected_close_filter, true); ?>
                            </select>
                        </th>
                        <?php break;
                        case "actual_close" :?>
                        <th>
                            <select class="span1 filter_input" name="actual_close">
                                 <?php $actual_close_filter = $this->state->get('Report.'.$report['id'].'_custom_report_actual_close'); ?>
                                <?php echo JHtml::_('select.options', $this->deal_close_dates, 'value', 'text', $actual_close_filter, true); ?>
                            </select>
                        </th>
                        <?php break;
                        case "company_name" :?>
                        <th>
                            <?php $company_filter = $this->state->get('Report.'.$report['id'].'_custom_report_company_name'); ?>
                            <input class="filter_input" name="company_name" type="text" value="<?php echo $company_filter; ?>"  />
                        </th>
                        <?php break; ?>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($primary_contact) { ?>
                        <?php switch ($id) {
                        case "primary_contact_name": ?>
                            <?php $primary_contact_name = $this->state->get('Report.'.$report['id'].'_custom_report_primary_contact_name'); ?>
                            <th><input class="input input-small filter_input" name="primary_contact_name" type="text" value="<?php echo $primary_contact_name; ?>"  /></th>
                            <?php break;
                        case "primary_contact_email": ?>
                            <?php $primary_contact_email = $this->state->get('Report.'.$report['id'].'_custom_report_primary_contact_email'); ?>
                            <th><input class="input input-small filter_input" name="primary_contact_email" type="text" value="<?php echo $primary_contact_email; ?>"  /></th>
                        <?php break;
                        case "primary_contact_phone": ?>
                            <?php $primary_contact_phone = $this->state->get('Report.'.$report['id'].'_custom_report_primary_contact_phone'); ?>
                            <th><input class="input input-small filter_input" name="primary_contact_phone" type="text" value="<?php echo $primary_contact_phone; ?>"  /></th>
                        <?php break;
                        case "primary_contact_company_name": ?>
                            <?php $primary_contact_company_name = $this->state->get('Report.'.$report['id'].'_custom_report_primary_contact_company_name'); ?>
                            <th><input class="input input-small filter_input" name="primary_contact_company_name" type="text" value="<?php echo $primary_contact_company_name; ?>"  /></th>
                        <?php break;
                        case "primary_contact_city": ?>
                            <?php $primary_contact_city = $this->state->get('Report.'.$report['id'].'_custom_report_primary_contact_city'); ?>
                            <th><input class="input input-small filter_input" name="primary_contact_city" type="text" value="<?php echo $primary_contact_city; ?>"  /></th>
                        <?php break;
                        case "primary_contact_state": ?>
                            <?php $primary_contact_state = $this->state->get('Report.'.$report['id'].'_custom_report_primary_contact_state'); ?>
                            <th><input class="input input-small filter_input" name="primary_contact_state" type="text" value="<?php echo $primary_contact_state; ?>"  /></th>
                        <?php break; ?>
                         <?php } ?>
                    <?php } ?>
                    <?php if ($custom_field) { ?>
                        <?php
                        //get the custom field type
                        $custom_field_info = DealHelper::getUserCustomFields(str_replace('custom_','',$id));
                        $info = $custom_field_info[0];
                        $custom_field_filter = '';

                        //determine type of input
                        switch ($info['type']) {
                            case "text": ?>
                            <th><input class="input input-small filter_input" name="custom_<?php echo $info['id']; ?>" type="text" value="<?php echo $custom_field_filter; ?>"  /></th>
                            <?php break;
                            case "picklist": ?>
                            <th>
                                <select class="span1 filter_input" name="custom_<?php echo $info['id']; ?>">
                                    <?php $all = array('all'=>JHTML::_('select.option','all',TextHelper::_('COBALT_ALL'))); ?>
                                    <?php echo JHtml::_('select.options', $all+json_decode($info['values']), 'value', 'text', $custom_field_filter, true); ?>
                                </select>
                            </th>
                            <?php break;
                            case "number": ?>
                            <th><input class="input input-small filter_input" name="custom_<?php echo $info['id']; ?>" type="text" value="<?php echo $custom_field_filter; ?>"  /></th>
                            <?php break;
                            case "currency": ?>
                            <th><input class="input input-small filter_input" name="custom_<?php echo $info['id']; ?>" type="text" value="<?php echo $custom_field_filter; ?>"  /></th>
                            <?php break;
	                        case "forecast": ?>
                            <th><input class="input input-small filter_input" name="custom_<?php echo $info['id']; ?>" type="text" value="<?php echo $custom_field_filter; ?>"  /></th>
                            <?php break;
                            case "date": ?>
                            <!-- make this a custom date picker -->
                            <?php $custom_dates = DealHelper::getDealFilters(); ?>
                            <th>
                                <select class="span1 filter_input" name="custom_<?php echo $info['id']; ?>">
                                    <?php echo JHtml::_('select.options', $custom_dates, 'value', 'text', $custom_field_filter, true); ?>
                                </select>
                            </th>
                            <?php break; ?>
                    <?php } ?>
            <?php } ?>
            <?php } ?>
        </tr>
        <?php } ?>
    </thead>
    <tbody class="results">
