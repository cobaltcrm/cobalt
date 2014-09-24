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

<script type="text/javascript">
    var loc = "dashboard";
    var graphData = <?php echo json_encode($this->graph_data); ?>;
</script>
<div class="page-header">
    <h1><?php echo TextHelper::_('COBALT_DASHBOARD_HEADER'); ?></h1>
</div>

<iframe name="hidden" style="display:none;width:0px;height:0px;border:0px;"></iframe>

<div class="row-fluid">
    <div class="col-md-4">
        <ul class="dash_float_list list-unstyled" id="dash_floats_left">
            <li class="widget">
                <div class="dash_float" id="com_cobalt_tasks_events">
                        <?php echo $this->eventDock->render(); ?>
                </div>
           </li>
           <li class="widget">
                <div class="panel panel-default" id="deals_container">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?php echo ucwords(TextHelper::_('COBALT_RECENT_DEALS')); ?></h4>
                    </div>
                        <table class="table table-striped table-hover table-bordered" id="deal_list">
                            <thead>
                                <tr>
                                    <th><?php echo TextHelper::_('COBALT_DEAL_NAME'); ?></th>
                                    <th><?php echo TextHelper::_('COBALT_DEAL_STATUS'); ?></th>
                                    <th class="right"><?php echo TextHelper::_('COBALT_DEAL_AMOUNT'); ?></th>
                                </tr>
                            </thead>
                            <?php $i = 0; ?>
                            <?php foreach ($this->recentDeals as $deal) : ?>
                                <?php $k = $i%2; ?>
                                <tr class="cobalt_row_'<?php echo $k; ?>">
                                    <td>
                                        <a href="<?php echo RouteHelper::_('index.php?view=deals&layout=deal&id=' . $deal->id); ?>">
                                            <?php echo $deal->name; ?>
                                        </a>
                                    </td>
                                    <td><div class="deal-status-'<?php echo strtolower($deal->status_name); ?>"></div></td>
                                    <td><span class="amount"><?php echo ConfigHelper::getConfigValue('currency').$deal->amount; ?></span></td>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        </table>
                </div>
            </li>
       </ul>
    </div>
    <div class="col-md-8">
        <ul class="dash_float_list list-unstyled" id="dash_floats_right">
            <li class="widget">
                <div class="dash_float" id="sales_container">
                    <div class="dash_float_header">
                        <div class="btn-group pull-right">
                            <a class="btn btn-default btn-sm" id="chart_select_prev"><i class="glyphicon glyphicon-chevron-left"></i></a><a class="btn btn-default btn-sm" id="chart_select_next"><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>
                        <h3><?php echo TextHelper::_('COBALT_SALES_HEADER'); ?></h3>
                    </div>
                    <div id="sales_graphs_container">
                        <div id="sales_graphs"></div>
                    </div>
                </div>
           </li>

            <li class='widget'>
                <div class="dash_float" id="inbox_container">
                    <div class="dash_float_header">
                        <a id="email_loader" class="ajax_loader_static fltrt" onclick="getEmails();"></a>
                        <h3><?php echo TextHelper::_('COBALT_INBOX_HEADER'); ?></h3>
                    </div>
                    <div id="inbox">
                        <table class="table table-striped table-hover table-bordered" id="mail_table">
                            <thead>
                                <tr>
                                    <th><?php echo TextHelper::_("COBALT_SUBJECT"); ?></th>
                                    <th><?php echo TextHelper::_('COBALT_FROM'); ?></th>
                                    <th><?php echo TextHelper::_('COBALT_TO'); ?></th>
                                    <th><?php echo TextHelper::_("COBALT_RECEIVED"); ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="mail_table_entries">
                            </tbody>
                        </table>
                    </div>
                </div>
            </li>
            <li class="widget">
                <div class="dash_float" id="latest_container">
                    <div class="dash_float_header">
                       <a class="minify"></a><h3><?php echo TextHelper::_('COBALT_LATEST_HEADER'); ?></h3>
                    </div>
                    <div id="latest">
                        <table class="table table-striped table-hover table-bordered" id="latest_activity">
                            <thead>
                                <tr>
                                    <th><?php echo TextHelper::_('COBALT_ITEM'); ?></th>
                                    <th><?php echo TextHelper::_('COBALT_ACTION'); ?></th>
                                    <th><?php echo TextHelper::_('COBALT_BY'); ?></th>
                                    <th><?php echo TextHelper::_('COBALT_OCCURRED'); ?></th>
                                </tr>
                            </thead>
                            <?php echo $this->latest_activities->render(); ?>
                        </table>
                    </div>
                </div>
           </li>
        </ul>
    </div>
