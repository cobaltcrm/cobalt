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

class CobaltModelGraphs extends CobaltModelDefault
{

    /**
     * Here we use multiple models to gather data for displaying graphs
     * @param $type type to filter by 'member','team','company'
     * @param $id id of type to filter by
     * @return $graph_data gathered graph data
     */
    public function getGraphData($type=null,$id=null)
    {
            //set default search data
            if ($type == null) {
                $type = 'member';
                $id = CobaltHelperUsers::getUserId();
            }

            //deal data
            $model = new CobaltModelDeal();
            $model->set('archived',0);
            $deals_by_stage = $model->getGraphDeals('stage',$type,$id);
            $deals_by_status = $model->getGraphDeals('status',$type,$id);
            $lead_sources = $model->getLeadSources($type,$id);
            $stage_names = array();
            $stage_totals = array();
            $status_names = array();
            $status_totals = array();
            $lead_source_names = array();
            $lead_totals = array();

            //revenue data
            $model = new CobaltModelRevenue();
            $monthly_revenue = $model->getMonthlyRevenue($type,$id);
            $yearly_revenue = $model->getYearlyRevenue($type,$id);

            //commission data
            $model = new CobaltModelCommission();
            $monthly_commissions = $model->getMonthlyCommission($type,$id);
            $yearly_commissions = $model->getYearlyCommission($type,$id);

            //get stage names
            if ( count($deals_by_stage) > 0 ) {
                foreach ($deals_by_stage as $stage) {
                    $stage_names[] = $stage['name'];
                    $stage_totals[] = $stage['y'];
                }
            }
            //get status names
            if ( count($deals_by_status) > 0 ) {
                foreach ($deals_by_status as $status) {
                    $status_names[] = CRMText::_('COBALT_'.strtoupper($status['name']).'_STATUS');
                    $status_totals[] = $status['y'];
                }
            }
            //get lead source names
            if ( count($lead_sources) > 0 ) {
                foreach ($lead_sources as $lead) {
                    $lead_source_names[] = $lead['name'];
                    $lead_totals[] = $lead['y'];
                }
            }
            //get weeks
            $weeks = array();
            $count = 0;
            if ($monthly_revenue) {
                foreach ($monthly_revenue as $week) {
                    $count++;
                    $weeks[] = "Week ".$count;
                }
            }
            //get months
            $months = CobaltHelperDate::getMonthNamesShort();

            //generate graph data
            $graph_data = array(
                'deal_stage'            => $deals_by_stage,
                'stage_names'           => $stage_names,
                'stage_totals'          => $stage_totals,
                'deal_status'           => $deals_by_status,
                'status_names'          => $status_names,
                'status_totals'         => $status_totals,
                'lead_sources'          => $lead_sources,
                'lead_source_names'     => $lead_source_names,
                'lead_totals'           => $lead_totals,
                'monthly_revenue'       => $monthly_revenue,
                'yearly_revenue'        => $yearly_revenue,
                'monthly_commissions'   => $monthly_commissions,
                'yearly_commissions'    => $yearly_commissions,
                'months'                => $months,
                'weeks'                 => $weeks
            );

            return $graph_data;
    }

}
