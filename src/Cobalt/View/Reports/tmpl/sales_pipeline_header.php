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
$app = JFactory::getApplication();
?>
<table class="table table-striped table-hover">
        <thead>
            <tr>
                <?php if ( $app->input->get('view') != "print" ) { ?>
                     <th class="checkbox_column"><input type="checkbox" onclick="selectAll(this);" /></th>
                 <?php } ?>
                <th><div class="sort_order"><a class="d.name" onclick="sortTable('d.name',this)"><?php echo ucwords(TextHelper::_('COBALT_DEAL_NAME')); ?></a></div></th>
                <th><div class="sort_order"><a class="d.owner_id" onclick="sortTable('d.owner_id',this)"><?php echo ucwords(TextHelper::_('COBALT_OWNER')); ?></a></div></th>
                <th><?php echo ucwords(TextHelper::_('COBALT_SUMMARY')); ?></th>
                <th><div class="sort_order"><a class="d.amount" onclick="sortTable('d.amount',this)"><?php echo ucwords(TextHelper::_('COBALT_AMOUNT')); ?></a></div></th>
                <th><div class="sort_order"><a class="d.stage_id" onclick="sortTable('d.stage_id',this)"><?php echo ucwords(TextHelper::_('COBALT_STAGE')); ?></a></div></th>
                <th><div class="sort_order"><a class="stage.percent" onclick="sortTable('stage.percent',this)"><?php echo ucwords(TextHelper::_('COBALT_PERCENTAGE')); ?></a></div></th>
                <th><div class="sort_order"><a class="d.status_id" onclick="sortTable('d.status_id',this)"><?php echo ucwords(TextHelper::_('COBALT_STATUS')); ?></a></div></th>
                <th><div class="sort_order"><a class="d.expected_close" onclick="sortTable('d.expected_close',this)"><?php echo ucwords(TextHelper::_('COBALT_EXPECTED_CLOSE')); ?></a></div></th>
                <th><div class="sort_order"><a class="d.modified" onclick="sortTable('d.modified',this)"><?php echo ucwords(TextHelper::_('COBALT_MODIFIED')); ?></a></div></th>
                <th><div class="sort_order"><a class="d.created" onclick="sortTable('d.created',this)"><?php echo ucwords(TextHelper::_('COBALT_CREATED')); ?></a></div></th>
            </tr>
            <?php if ( $app->input->get('view') != "print" ) { ?>
            <tr>
                <?php $deal_filter = $this->state->get('Deal.sales_pipeline_name'); ?>
                <th></th>
                <th><input class="input input-small filter_input" name="deal_name" type="text" value="<?php echo $deal_filter; ?>"  /></th>
                <th>
                    <select class="span1 filter_input" name="owner_id" id="owner_id">
                        <?php $user_filter = $this->state->get('Deal.sales_pipeline_owner_id'); ?>
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
                <th></th>
                <th>
                    <select class="span1 filter_input" name="deal_amount">
                        <option value="all"><?php echo TextHelper::_('COBALT_ALL'); ?></option>
                        <?php $amount_filter = $this->state->get('Deal.sales_pipeline_amount'); ?>
                        <?php echo JHtml::_('select.options', $this->deal_amounts, 'value', 'text', $amount_filter, true); ?>
                    </select>
                </th>
                <th>
                    <select class="span1 filter_input" name="stage_id">
                        <?php $stage_filter = $this->state->get('Deal.sales_pipeline_stage_id'); ?>
                        <?php $base = array('all'=>TextHelper::_('COBALT_ALL_ACTIVE_STAGES')); ?>
                        <?php $this->deal_stages = $base + $this->deal_stages; ?>
                        <?php echo JHtml::_('select.options', $this->deal_stages, 'value', 'text', $stage_filter, true); ?>
                    </select>
                </th>
                <th></th>
                <th>
                    <select class="span1 filter_input" name="status_id">
                        <option value="all"><?php echo TextHelper::_('COBALT_ALL'); ?></option>
                        <?php $status_filter = $this->state->get('Deal.sales_pipeline_status_id'); ?>
                        <?php echo JHtml::_('select.options', $this->deal_statuses, 'value', 'text', $status_filter, true); ?>
                    </select>
                </th>
                <th>
                    <select class="span1 filter_input" name="expected_close">
                         <?php $expected_close_filter = $this->state->get('Deal.sales_pipeline_expected_close'); ?>
                        <?php echo JHtml::_('select.options', $this->deal_close_dates, 'value', 'text', $expected_close_filter, true); ?>
                    </select>
                </th>
                <th>
                    <select class="span1 filter_input" name="modified">
                        <?php $modified_filter = $this->state->get('Deal.sales_pipeline_modified'); ?>
                        <?php echo JHtml::_('select.options', $this->modified_dates, 'value', 'text', $modified_filter, true); ?>
                    </select>
                </th>
                <th>
                    <select class="span1 filter_input" name="created">
                        <?php $created_filter = $this->state->get('Deal.sales_pipeline_created'); ?>
                        <option value="all"><?php echo TextHelper::_('COBALT_ALL'); ?></option>
                        <?php echo JHtml::_('select.options', $this->created_dates, 'value', 'text', $created_filter, true); ?>
                    </select>
                </th>
            </tr>
            <?php } ?>
        </thead>
        <tfoot>
            <tr>
                <?php if ( $app->input->get('view') != "print" ) { ?>
                    <td>&nbsp;</td>
                <?php } ?>
                <td><?php echo TextHelper::_('COBALT_FILTERED_TOTAL'); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>
                    <?php echo ConfigHelper::getCurrency(); ?>
                    <span id="filtered_amount">
                    <?php if ( count($this->reports) > 0 ) {
                        $total = 0;
                        foreach ($this->reports as $key=>$report) {
                            $total += $report->amount;
                        }
                       echo $total;
                    }?>
                    </span>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <?php if ( $app->input->get('view') != "print" ) { ?>
                    <td>&nbsp;</td>
                <?php } ?>
                <td><?php echo TextHelper::_('COBALT_SALES_PIPELINE'); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>
                    <?php echo ConfigHelper::getCurrency(); ?>
                    <span id="total_amount">
                    <?php if ( count($this->reports) > 0 ) {
                        $total = 0;
                        foreach ($this->reports as $key=>$report) {
                            $total += $report->amount;
                        }
                       echo $total;
                    }?>
                    </span>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tfoot>
        <tbody class="results" id="reports">
