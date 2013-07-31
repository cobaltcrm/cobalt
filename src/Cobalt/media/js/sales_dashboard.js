/**
 * Globals
 */
var chart = null;
var charts = [	'dealStagePie',
				'dealStageBar',
				'dealStatusPie',
				'dealStatusBar',
				// 'leadRevenue',
				// 'leadCloseTime',
				'yearlyCommissions',
				'monthlyCommissions',
				'yearlyRevenue',
				'monthlyRevenue'	];
				
var currentChart = null;
var colors = Highcharts.getOptions().colors;

jQuery(document).ready(function(){	
	showAllCharts(graphData);
});

//show all charts
function showAllCharts(new_data){

	graphData = new_data;
	
	if  ( typeof graphData !== 'undefined' && graphData != null ){

		//Loop through deal stages to assign certain values
		if ( graphData.deal_stage != null ){
			for ( i=0; i<graphData.deal_stage.length; i++ ){
				graphData.deal_stage[i].color = colors[i];
			}
		}
		//loop through deal statuss to assign color values
		if ( graphData.deal_status != null ){
			for ( i=0; i<graphData.deal_status.length; i++ ){
				graphData.deal_status[i].color = colors[i];
			}
		}
		//loop through commission months to assign colors
		if ( graphData.yearly_commissions != null ){
			for ( i=0; i<graphData.yearly_commissions.length; i++ ){
				graphData.yearly_commissions[i].color = colors[i];
			}
		}
		//loop through commission weeks to assign colors
		if ( graphData.monthly_commissions != null ){
			for ( i=0; i<graphData.monthly_commissions.length; i++ ){
				graphData.monthly_commissions[i].color = colors[i];
			}
		}
		//loop through revenue weeks to assign colors
		if ( graphData.monthly_revenue != null ){
			for ( i=0; i<graphData.monthly_revenue.length; i++ ){
				graphData.monthly_revenue[i].color = colors[i];
			}
		}
		//loop through revenue months to assign colors
		if ( graphData.yearly_revenue != null ){
			for ( i=0; i<graphData.yearly_revenue.length; i++ ){
				graphData.yearly_revenue[i].color = colors[i];
			}
		}
		
		//render all graphs
		for( chart in charts ){
			showChart(charts[chart]);
		}

	}
}

//shows different kinds of charts
function showChart(type){
		
		switch ( type ) {
	
			case "dealStagePie" :
				chart = new Highcharts.Chart({
			         chart: {
									renderTo: 'deal_stage',
									plotBackgroundColor: null,
									plotBorderWidth: null,
									plotShadow: true
								},
								title: {
									text: ucwords(Joomla.JText._('COBALT_DEALS_BY_STAGE'))
								},
								tooltip: {
									formatter: function() {
										return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
									}
								},
								plotOptions: {
									pie: {
										allowPointSelect: true,
										cursor: 'pointer',
										dataLabels: {
											enabled: true,
											color: '#000000',
											connectorColor: '#000000',
											formatter: function() {
												return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
											}
										}
									}
								},
				      series: [{
				      	type: 'pie',
									name:  ucwords(Joomla.JText._('COBALT_DEALS_BY_STAGE')),
									data:  graphData.deal_stage
				      }]
		      	});
	      	break;

			case 'dealStageBar' :
			 	chart = new Highcharts.Chart({
			     				chart: {
									renderTo: 'deal_stage',
									defaultSeriesType: 'column'
								},
								title: {
									text: ucwords(Joomla.JText._('COBALT_DEALS_BY_STAGE'))
								},
								xAxis: {
									categories:graphData.stage_names
								},
								yAxis: {
									min: 0,
									title: {
										text: ucwords(Joomla.JText._('COBALT_TOTAL'))
									}
								},
								legend: {
									enabled:false
								},
								tooltip: {
									formatter: function() {
										return ''+
											this.x +': '+ this.y;
									}
								},
								plotOptions: {
									column: {
										pointPadding: 0.2,
										borderWidth: 0
									}
								},
							        series: [{
							        	name:ucwords(Joomla.JText._('COBALT_DEALS_BY_STAGE')),
							        	data: graphData.deal_stage
							        }]
					});
				break;
				
			case 'dealStatusPie' :
				chart = new Highcharts.Chart({
			         chart: {
									renderTo: 'deal_status',
									plotBackgroundColor: null,
									plotBorderWidth: null,
									plotShadow: true
								},
								title: {
									text: ucwords(Joomla.JText._('COBALT_DEALS_BY_STATUS'))
								},
								tooltip: {
									formatter: function() {
										return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
									}
								},
								plotOptions: {
									pie: {
										allowPointSelect: true,
										cursor: 'pointer',
										dataLabels: {
											enabled: true,
											color: '#000000',
											connectorColor: '#000000',
											formatter: function() {
												return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
											}
										}
									}
								},
				      series: [{
				      	type: 'pie',
									name: ucwords(Joomla.JText._('COBALT_DEALS_BY_STATUS')),
									data:  graphData.deal_status
				      }]
		      	});
			break;
			
			case 'dealStatusBar' :
				chart = new Highcharts.Chart({
			     				chart: {
									renderTo: 'deal_status',
									defaultSeriesType: 'column'
								},
								title: {
									text: ucwords(Joomla.JText._('COBALT_DEALS_BY_STATUS'))
								},
								xAxis: {
									categories:graphData.status_names
								},
								yAxis: {
									min: 0,
									title: {
										text: ucwords(Joomla.JText._('COBALT_TOTAL'))									
									}
								},
								legend: {
									enabled:false
								},
								tooltip: {
									formatter: function() {
										return ''+
											this.x +': '+ this.y;
									}
								},
								plotOptions: {
									column: {
										pointPadding: 0.2,
										borderWidth: 0
									}
								},
							        series: [{
							        	name:ucwords(Joomla.JText._('COBALT_DEALS_BY_STATUS')),
							        	data: graphData.deal_status
							        }]
					});
			break;
			
			case 'leadRevenue' :
				chart = new Highcharts.Chart({
			         chart: {
									renderTo: 'lead_revenue',
									plotBackgroundColor: null,
									plotBorderWidth: null,
									plotShadow: true
								},
								title: {
									text: ucwords(Joomla.JText._('COBALT_REVENUE_FROM_LEAD_SOURCES'))
								},
								tooltip: {
									formatter: function() {
										return '<b>'+ this.point.name +'</b>: '+Joomla.JText._('COBALT_CURRENCY')+this.point.data;
									}
								},
								plotOptions: {
									pie: {
										allowPointSelect: true,
										cursor: 'pointer',
										dataLabels: {
											enabled: true,
											color: '#000000',
											connectorColor: '#000000',
											formatter: function() {
												return '<b>'+ this.point.name +'</b>: '+Joomla.JText._('COBALT_CURRENCY')+this.point.data;
											}
										}
									}
								},
				      series: [{
				      	type: 'pie',
									name: ucwords(Joomla.JText._('COBALT_REVENUE_FROM_LEAD_SOURCES')),
									data:  graphData.lead_sources
				      }]
		      	});
		      	
			break;
			
			//TODO
			case 'leadCloseTime' :
				
				
			break;
			
			case 'yearlyCommissions' :
			chart = new Highcharts.Chart({
			     				chart: {
									renderTo: 'yearly_commissions',
									defaultSeriesType: 'column'
								},
								title: {
									text: ucwords(Joomla.JText._('COBALT_YEARLY_COMMISSIONS'))
								},
								xAxis: {
									categories:graphData.months
								},
								yAxis: {
									min: 0,
									title: {
										text: Joomla.JText._('COBALT_TOTAL')
									},
									labels:{formatter:function(){
										return Joomla.JText._('COBALT_CURRENCY')+this.value;
									}}
								},
								legend: {
									enabled:false
								},
								tooltip: {
									formatter: function() {
										return ''+
											this.x +': '+Joomla.JText._('COBALT_CURRENCY')+ this.y;
									}
								},
								plotOptions: {
									column: {
										pointPadding: 0.2,
										borderWidth: 0
									}
								},
							        series: [{
							        	name:ucwords(Joomla.JText._('COBALT_YEARLY_COMMISSIONS')),
							        	data: graphData.yearly_commissions
							        }]
					});
			break;
			
			case 'monthlyCommissions' :
			chart = new Highcharts.Chart({
			     				chart: {
									renderTo: 'monthly_commissions',
									defaultSeriesType: 'column'
								},
								title: {
									text: ucwords(Joomla.JText._('COBALT_MONTHLY_COMMISSIONS'))
								},
								xAxis: {
									categories:graphData.weeks
								},
								yAxis: {
									min: 0,
									title: {
										text: Joomla.JText._('COBALT_TOTAL')
									},
									labels:{formatter:function(){
										return Joomla.JText._('COBALT_CURRENCY')+this.value;
									}}
								},
								legend: {
									enabled:false
								},
								tooltip: {
									formatter: function() {
										return ''+
											this.x +': '+Joomla.JText._('COBALT_CURRENCY')+ this.y;
									}
								},
								plotOptions: {
									column: {
										pointPadding: 0.2,
										borderWidth: 0
									}
								},
							        series: [{
							        	name: ucwords(Joomla.JText._('COBALT_MONTHLY_COMMISSIONS')),
							        	data: graphData.monthly_commissions
							        }]
					});
			break;
			
			case 'yearlyRevenue' :
			 chart = new Highcharts.Chart({
			     				chart: {
									renderTo: 'yearly_revenue',
									defaultSeriesType: 'column'
								},
								title: {
									text: ucwords(Joomla.JText._('COBALT_YEARLY_REVENUE'))
								},
								xAxis: {
									categories:graphData.months
								},
								yAxis: {
									min: 0,
									title: {
										text: Joomla.JText._('COBALT_TOTAL')
									},
									labels:{formatter:function(){
										return Joomla.JText._('COBALT_CURRENCY')+this.value;
									}}
								},
								legend: {
									enabled:false
								},
								tooltip: {
									formatter: function() {
										return ''+
											this.x +': '+Joomla.JText._('COBALT_CURRENCY')+ this.y;
									}
								},
								plotOptions: {
									column: {
										pointPadding: 0.2,
										borderWidth: 0
									}
								},
							        series: [{
							        	name: ucwords(Joomla.JText._('COBALT_YEARLY_REVENUE')),
							        	data: graphData.yearly_revenue
							        }]
					});
			break;
			
			case 'monthlyRevenue' :
			chart = new Highcharts.Chart({
			     				chart: {
									renderTo: 'monthly_revenue',
									defaultSeriesType: 'column'
								},
								title: {
									text: ucwords(Joomla.JText._('COBALT_MONTHLY_REVENUE'))
								},
								xAxis: {
									categories:graphData.weeks
								},
								yAxis: {
									min: 0,
									title: {
										text: Joomla.JText._('COBALT_TOTAL')
									},
									labels:{formatter:function(){
										return Joomla.JText._('COBALT_CURRENCY')+this.value;
									}}
								},
								legend: {
									enabled:false
								},
								tooltip: {
									formatter: function() {
										return ''+
											Joomla.JText._('COBALT_WEEK')+' '+this.x +': '+Joomla.JText._('COBALT_CURRENCY')+ this.y;
									}
								},
								plotOptions: {
									column: {
										pointPadding: 0.2,
										borderWidth: 0
									}
								},
							        series: [{
							        	name: ucwords(Joomla.JText._('COBALT_MONTHLY_REVENUE')),
							        	data: graphData.monthly_revenue
							        }]
					});
			break;
			
			}
}

//Loops through charts on dashboard
function chartLoop(position){
	//get next position
	if ( position == 'next' ){
		currentChart = ( currentChart == charts.length - 1 ) ? 0 : currentChart + 1;
	}
	if ( position == 'prev' ){
		currentChart = ( currentChart == 0 ) ? charts.length : currentChart - 1;
	}
	//show the next chart
	showChart(charts[currentChart]);
}

