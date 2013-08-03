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
use Cobalt\Helper\TextHelper;
?>

<script type="text/javascript">
    var graphData = <?php echo json_encode($this->graph_data); ?>;
</script>
<div class="page-header">
    <h1><?php echo TextHelper::_('COBALT_SALES_DASHBOARD'); ?></h1>
</div>

<?php echo $this->menu; ?>
<?php if ($this->member_role != 'basic') { ?>
<div class="alert">
    <?php echo TextHelper::_("COBALT_SHOWING_SALES_DASHBOARD_FOR"); ?>
        <span class="dropdown">
                <a class="dropdown-toggle update-toggle-text" href="#" data-toggle="dropdown" role="button" href="javascript:void(0);" id="sales_dashboard_filter_link" ><span class="dropdown-label"><?php echo TextHelper::_('COBALT_ME'); ?></span><span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="sales_dashboard_filter_link">
                    <li><a class="filter_member_<?php echo $this->user_id; ?> dropdown_item" onclick="salesDashboardFilter(<?php echo $this->user_id; ?>,null)"><?php echo TextHelper::_('COBALT_ME'); ?></a></li>
                <?php if ($this->member_role == 'manager') { ?>
                    <li><a class="filter_team_<?php echo $this->team_id; ?> dropdown_item" onclick="salesDashboardFilter(null,<?php echo $this->team_id; ?>)"><?php echo TextHelper::_('COBALT_MY_TEAM'); ?></a></li>
                <?php } ?>
                <?php if ($this->member_role == 'exec') { ?>
                    <li><a class="filter_company dropdown_item" onclick="salesDashboardFilter(null,null)"><?php echo TextHelper::_("COBALT_THE_COMPANY"); ?></a></li>
                <?php foreach ($this->teams as $title => $text) { ?>
                    <li><a class="filter_team_<?php echo $text['team_id']; ?> dropdown_item" onclick="salesDashboardFilter(null,<?php echo $text['team_id']; ?>)"><?php echo $text['team_name'].TextHelper::_('COBALT_TEAM_APPEND'); ?></a></li>
                <?php }} ?>
                <?php foreach ($this->users as $title => $text) {
                     echo "<li><a class='filter_member_".$text['id']." dropdown_item' onclick=\"salesDashboardFilter(".$text['id'].",null)\">".$text['first_name'].' '.$text['last_name']."</a></li>";
                }?>
            </ul>
        </span>
    <?php } ?>
</div>
<div class="row" id="sales_dashboard_graphs">
    <div class="col-lg-6">
        <ul class="dash_float_list unstyled" id="dash_floats_right">
            <li class="graph widget">
                <div class="btn-group pull-right">
                    <a class="btn" href="javascript:void(0);" onclick="showChart('dealStagePie');"><i class="icon-adjust"></i></a>
                    <a class="btn" href="javascript:void(0);" onclick="showChart('dealStageBar');"><i class="icon-align-left"></i></a>
                </div>
                <div id="deal_stage"></div>
            </li>
            <li class="graph widget">
                <div class="btn-group pull-right">
                    <a class="btn" href="javascript:void(0);" onclick="showChart('dealStatusPie');"><i class="icon-adjust"></i></a>
                    <a class="btn" href="javascript:void(0);" onclick="showChart('dealStatusBar');"><i class="icon-align-left"></i></a>
                </div>
                <div id="deal_status"></div>
            </li>
        </ul>
    </div>
    <div class="col-lg-6">
        <ul class="dash_float_list unstyled" id="dash_floats_right">
            <li class="graph widget">
                <div id="yearly_commissions"></div>
            </li>
            <li class="graph widget">
                <div id="yearly_revenue"></div>
            </li>
            <li class="graph widget">
                <div id="monthly_commissions"></div>
            </li>
            <li class="graph widget">
                <div id="monthly_revenue"></div>
            </li>
        </ul>
    </div>
</div>
