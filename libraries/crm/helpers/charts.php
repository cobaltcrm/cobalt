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
defined( '_JEXEC' ) or die( 'Restricted access' ); 

 class CobaltHelperCharts extends JObject
 {
    
    function getDashboardCharts(){
        
        return  array( 'deals_by_status_pie_chart' =>  CRMText::_('COBALT_DEALS_BY_STATUS_PIE_CHART'),
                        'deals_by_status_bar_chart' =>  CRMText::_('COBALT_DEALS_BY_STATUS_BAR_CHART'),
                        'deals_by_stage_pie_chart'  =>  CRMText::_('COBALT_DEALS_BY_STAGE_PIE_CHART'),
                        'deals_by_stage_bar_chart'  =>  CRMText::_('COBALT_DEALS_BY_STAGE_BAR_CHART'),
                        'revenue_from_lead_sources' =>  CRMText::_('COBALT_REVENUE_FROM_LEAD_SOURCES'),
                        'year_to_date_commissions'  =>  CRMText::_('COBALT_YEAR_TO_DATE_COMMISSIONS'),
                        'commissions_this_month'    =>  CRMText::_('COBALT_COMMISSIONS_THIS_MONTH'),
                        'year_to_date_revenue'      =>  CRMText::_('COBALT_YEAR_T0_DATE_REVENUE'),
                        'revenue_this_month'        =>  CRMText::_('COBALT_REVENUE_THIS_MONTH'));
    }
        
        
 }
    