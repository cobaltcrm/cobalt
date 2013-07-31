<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Helper;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

 class ChartsHelper;
 {

    public static function getDashboardCharts()
    {
        return  array( 'deals_by_status_pie_chart' =>  TextHelper::_('COBALT_DEALS_BY_STATUS_PIE_CHART'),
                        'deals_by_status_bar_chart' =>  TextHelper::_('COBALT_DEALS_BY_STATUS_BAR_CHART'),
                        'deals_by_stage_pie_chart'  =>  TextHelper::_('COBALT_DEALS_BY_STAGE_PIE_CHART'),
                        'deals_by_stage_bar_chart'  =>  TextHelper::_('COBALT_DEALS_BY_STAGE_BAR_CHART'),
                        'revenue_from_lead_sources' =>  TextHelper::_('COBALT_REVENUE_FROM_LEAD_SOURCES'),
                        'year_to_date_commissions'  =>  TextHelper::_('COBALT_YEAR_TO_DATE_COMMISSIONS'),
                        'commissions_this_month'    =>  TextHelper::_('COBALT_COMMISSIONS_THIS_MONTH'),
                        'year_to_date_revenue'      =>  TextHelper::_('COBALT_YEAR_T0_DATE_REVENUE'),
                        'revenue_this_month'        =>  TextHelper::_('COBALT_REVENUE_THIS_MONTH'));
    }

 }
