/**
 * Globals
 */
var current_area = null;
var order_url = null;
var order_col = null;
var order_dir = null;
var header_refs = new Array();
var row_refs = new Array();

//document ready
jQuery(document).ready(function(){

	// bindDropdowns();
	bindDropdownItems();
	
	//bind column filters for page
	jQuery('#column_filter').children('ul').children('li').children('input[type=checkbox]').each(function(index,element){
		jQuery(element).bind('click',function(){
			column(jQuery(element).attr('id'));
		});
	});
	
	//assign the correct sort direction
	assignFilterOrder();
	
	//assign a company name search filter input box
	jQuery(".filter_input").bind('keyup',function(){
		filterTable(jQuery(this).val(),jQuery(this).attr('name'),jQuery(this));
	});
	
	jQuery(".filter_input").bind('change',function(){
		filterTable(jQuery(this).val(),jQuery(this).attr('name'),jQuery(this));
	});
	
	//determine which columns should be shown on the page
	if ( typeof(loc) !== 'undefined' ){
		if ( loc == 'deals' || loc == 'companies' || loc == 'people' ){
			showColumns();
		}
	}
	
});

function bindDropdownItems(){
	jQuery('.dropdown_item').live('click',function(){
		var id = jQuery(this).parentsUntil('div.filters').parent('div.filters').attr('id')+"_link";
		jQuery("#"+id).html(jQuery(this).html());
		if ( typeof jQuery(this).attr('data-value') !== 'undefined' ){
			ajaxSaveModal(jQuery(this));
		}
	});
}


function ajaxSaveModal(ele){
	var item_id = jQuery(ele).attr('data-item-id');
	var item_type = jQuery(ele).attr('data-item');
	var value_type = jQuery(ele).attr('data-field');
	var new_value = jQuery(ele).attr('data-value');

	dataString = "item_id="+item_id+"&item_type="+item_type+"&field="+value_type+"&value="+new_value;

	jQuery.ajax({
		url:'index.php?task=saveajax&format=raw&tmpl=component',
		type:'POST',
		data:dataString,
		dataType:'JSON',
		success:function(data){
			modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
			if ( item_type == "deal" && value_type == "stage_id" ){
				if ( data.closed == true ){
					actual_close = data.actual_close;
					actual_close_formatted = data.actual_close_formatted;
					jQuery("#actual_close").val(actual_close_formatted);
					jQuery("#actual_close_hidden").val(actual_close);
					jQuery("#expected_close_container").hide();
					jQuery("#actual_close_container").show();
				} else {
					expeced_close = data.expeced_close;
					expeced_close_formatted = data.expected_close_formatted;
					jQuery("#expeced_close").val(expeced_close_formatted);
					jQuery("#expected_close_hidden").val(expeced_close);
					jQuery("#actual_close_container").hide();
					jQuery("#expected_close_container").show();
				}
			}
			if ( loc == "deals" ){
				expected_close = data.expected_close_formatted;
				actual_close = data.actual_close_formatted;
				if ( data.closed == true ){
					jQuery("#expected_close_"+data.id).html(expected_close);
					jQuery("#actual_close"+data.id).html(actual_close);
				}else{
					jQuery("#actual_close"+data.id).html(Joomla.JText._('COBALT_ACTIVE_DEAL'));
				}
			}
		}
	});
}


function hideDropdown(current_area) {
	if ( current_area != null ){

		if (jQuery("#"+current_area).hasClass('editable') ){
			jQuery("#"+current_area).children('div.editable_info').hide();
			current_area = null;
		}else{

			//unbind area
			jQuery("#"+current_area).unbind();
			//hide area
			var div = current_area.replace('_link','');

			jQuery("#"+div).hide();

			//rebind area
			var bind_area = current_area;
			jQuery("#"+bind_area).bind('click',function(){
				showDropdown(bind_area);
			});

		}

	}
}

//function to bind filter dropdowns
function showDropdown(area){

	var link = area;
	var div = area.replace('_link','');

	// if there is already a filter box open we close it and reassign our bind functionalities
	if ( current_area != null ){

		if (jQuery("#"+current_area).hasClass('editable') ){

			jQuery("#"+current_area).children('div.editable_info').hide();	
			current_area = null;
			
		}else{

			//unbind area
			jQuery("#"+current_area).unbind();
			//hide area
			jQuery("#"+current_area.replace('_link','')).hide();
			
			//rebind area
			var bind_area = current_area;
			jQuery("#"+bind_area).bind('click',function(){
				showDropdown(bind_area);
			});

		}

	}
			
		//assign new area
		if ( current_area != div ) {

			//fade in area
			jQuery("#"+div).show();
			
			//unbind any prior functionalities
			jQuery("#"+link).unbind();
			
			//set css
			jQuery("#"+div).css('left',jQuery("#"+link).position().left+"px");
			jQuery("#"+div).css('top',jQuery("#"+link).position().top+parseInt(jQuery("#"+link).css('height'))+"px");
			// jQuery("#"+div).css('top','100px');`
			//TODO bind area to fade out when losing focus or other clicks
			jQuery("#"+div).focus();
			jQuery("#"+div).focusout(function(){

			});

			//bind area to hide when clicked
			jQuery("#"+link).bind('click',function(){
				//hide area
				jQuery("#"+div).hide();
				//rebind area
				jQuery("#"+link).bind('click',function(){
					showDropdown(link);
				});
			});
			
			//update new current area
			current_area = link;
			
		}else{
			//hide/show area
			if ( jQuery("#"+div).is(':visible') ) {
				//hide
				jQuery("#"+div).hide();
			}else{
				//fade in area
				jQuery("#"+div).show();
			}
			
		}
	
}

//function to show initial page load columns
function initColumn(area){

	//string replacement
	var show_area = area.replace('show_','');

	if(jQuery("#"+area).is(':checked')){
		//insert the new rule
		document.styleSheets[0].insertRule('div#com_cobalt th.'+show_area+'{ display:table-cell; }',0);
		//insert reference to the new rule to our global references object
		header_refs[show_area] = document.styleSheets[0].cssRules[0];
		
		//insert the new rule
		document.styleSheets[0].insertRule('div#com_cobalt td.'+show_area+'{ display:table-cell; }',0);
		//insert reference to the new rule to our global references object
		row_refs[show_area] = document.styleSheets[0].cssRules[0];
	}else{
		//insert the new rule
		document.styleSheets[0].insertRule('div#com_cobalt th.'+show_area+'{ display:none; }',0);
		//insert reference to the new rule to our global references object
		header_refs[show_area] = document.styleSheets[0].cssRules[0];
		
		//insert the new rule
		document.styleSheets[0].insertRule('div#com_cobalt td.'+show_area+'{ display:none; }',0);
		//insert reference to the new rule to our global references object
		row_refs[show_area] = document.styleSheets[0].cssRules[0];
	}


}

//function to bind clicks for displaying certain columns
function column(area){
	//string replacement
	var show_area = area.replace('show_','');
	if(jQuery("#"+area).is(':checked')){
		//insert the new rules
		header_refs[show_area].style['display']='table-cell';
		row_refs[show_area].style['display']='table-cell';
	}else{
		//insert the new rules
		header_refs[show_area].style['display']='none';
		row_refs[show_area].style['display']='none';
	}
	
	//make ajax call to update our database information and session information
	var dataString = "loc="+loc+"&column="+show_area;
	jQuery.ajax({
		url		:	'index.php?task=updateColumns&format=raw&tmpl=component',
		type	:	'post',
		data	:	dataString,
		success	:	function(data){
			
		}
	});
}

//show the respected columns relating to user session and database information
function showColumns(){
	
	//loop through the column filter inputs to determine which ones are checked or not to display the areas
	jQuery("#column_filter").children('ul').children('li').each(function(){
		initColumn(jQuery(this).children('input').attr('id'));
	});	
}

//filter through deal stages
function dealStage(stage){

	showAjaxLoader();
	
	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterDeals&view=deals&format=raw&tmpl=component',
		data	:	'stage='+stage,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();
			
			//assign new html
			jQuery.when(jQuery("#deals").empty())
			.then(function(){
				jQuery("#deals").html(data);
			});
			
			//update link message
			jQuery("#"+current_area).html(jQuery("#deal_stages a.filter_"+stage).text());
			
		}
	});
	
}

//filter through deal types
function dealType(type){

	showAjaxLoader();
	
	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterDeals&view=deals&format=raw&tmpl=component',
		data	:	'type='+type,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();
			
			//assign new html
			jQuery.when(jQuery("#deals").empty())
			.then(function(){
				jQuery("#deals").html(data);
			});
			
			//update link message
			jQuery("#"+current_area).html(jQuery("#deal_type a.filter_"+type).text());
			
		}
	});
	
}

//filter through deal users
function dealUser(user,team){

	showAjaxLoader();
	
	var link = current_area;
	var div = current_area.replace('_link','');

	var search_id = ( user ) ? user : team;
	var dataString = ( user ) ? 'user='+search_id : 'team_id='+search_id;
	
	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterDeals&view=deals&format=raw&tmpl=component',
		data	:	dataString,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();
			
			//assign new html
			jQuery.when(jQuery("#deals").empty())
			.then(function(){
				jQuery("#deals").html(data);
			});
			
			//update link message
			if ( user ){
				jQuery("#"+current_area).html(jQuery("#deal_user a.filter_user_"+search_id).text());
			}else{
				jQuery("#"+current_area).html(jQuery("#deal_user a.filter_team_"+search_id).text());
			}
			
		}
	});
	
}

//filter through deal closing times
function dealClose(time){

	showAjaxLoader();
	
	var link = current_area;
	var div = current_area.replace('_link','');

		//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterDeals&view=deals&format=raw&tmpl=component',
		data	:	'close='+time,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();
			
			//assign new html
			jQuery.when(jQuery("#deals").empty())
			.then(function(){
				jQuery("#deals").html(data);
			});
			
			//update link message
			jQuery("#"+link).html(jQuery("#deal_closing a.filter_"+time).text());
			
		}
	});

	
}

//filter through company types
function companyType(type){

	showAjaxLoader();
	
	var link = current_area;
	var div = current_area.replace('_link','');

	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterCompanies&view=companies&format=raw&tmpl=component',
		data	:	'type='+type,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();
			
			//assign new html
			jQuery.when(jQuery("#companies").empty())
			.then(function(){
				jQuery("#companies").html(data);
			});
			
			//update link message
			jQuery("#"+link).html(jQuery("#company_type a.filter_"+type).text());
			
		}
	});
	
}

//filter through company by user permissions
function companyUser(user,team){

	showAjaxLoader();
	
	var link = current_area;
	var div = current_area.replace('_link','');

	var search_id = ( user ) ? user : team;
	var dataString = ( user ) ? 'user='+user : 'team_id='+team;
	
	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterCompanies&view=companies&format=raw&tmpl=component',
		data	:	dataString,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();
			
			//assign new html
			jQuery.when(jQuery("#companies").empty())
			.then(function(){
				jQuery("#companies").html(data);
			});
			
			//update link message
			if ( user ){
				jQuery("#"+current_area).html(jQuery("#company_user a.filter_user_"+search_id).text());
			}else{
				jQuery("#"+current_area).html(jQuery("#company_user a.filter_team_"+search_id).text());
			}
			
		}
	});
	
}

//update event listings
function updateEventList(user,team){
	//make ajax call for new event listings
	var search_event_id = ( user ) ? user : team;
	var dataString = "";
	if ( user ){
		dataString += 'assignee_id='+search_event_id+"&assignee_filter_type=individual";
	}else{
		dataString += 'assignee_id='+search_event_id+"&assignee_filter_type=team";
	}
	if ( typeof loc !== 'undefined' && typeof id !== 'undefined' ){
		dataString += "&association_type="+loc+"&association_id="+id;
	}
	jQuery.ajax({
		
		type:'post',
		url:'index.php?view=events&layout=event_listings&tmpl=comp&format=raw&tmpl=component',
		data:dataString,
		dataType:'html',
		success:function(data){
			//assign new html
			jQuery.when(jQuery("#task_list").empty())
			.then(function(){
				jQuery("#task_list").html(data);
			});
			
			//update link message
			if ( user ){
				jQuery("#"+current_area).html(jQuery("#event_user a.filter_user_"+search_event_id).text());
			}else{
				jQuery("#"+current_area).html(jQuery("#event_user a.filter_team_"+search_event_id).text());
			}
		}
		
	});
}

//filter people types
function peopleType(type){

	showAjaxLoader();

	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterPeople&view=people&format=raw&tmpl=component',
		data	:	'type='+type,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();

			//assign new html
			jQuery.when(jQuery("#people").empty())
			.then(function(){
				jQuery("#people").html(data);
			});
			
			//update link message
			jQuery("#"+current_area).html(jQuery("#people_type a.filter_"+type).text());
		}
	});
}

//filter people by user
function peopleUser(user,team){

	showAjaxLoader();
	
	var search_id = ( user ) ? user : team;
	var dataString = ( user ) ? 'user='+search_id : 'team_id='+search_id;
	
	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterPeople&view=people&format=raw&tmpl=component',
		data	:	dataString,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();

			//assign new html
			jQuery.when(jQuery("#people").empty())
			.then(function(){
				jQuery("#people").html(data);
			});
			
			//update link message
			if ( user ){ 
				jQuery("#"+current_area).html(jQuery("#people_user a.filter_user_"+search_id).text());
			}else{
				jQuery("#"+current_area).html(jQuery("#people_user a.filter_team_"+search_id).text());
			}
		}
	});
}

//filter people by stage//updates
function peopleUpdated(stage){

	showAjaxLoader();

	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterPeople&view=people&format=raw&tmpl=component',
		data	:	'stage='+stage,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();

			//assign new html
			jQuery.when(jQuery("#people").empty())
			.then(function(){
				jQuery("#people").html(data);
			});
			
			//update link message
			jQuery("#"+current_area).html(jQuery("#people_stages a.filter_"+stage).text());
		}
	});
}

//filter people tags
function peopleTag(tag){

	showAjaxLoader();

	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterPeople&view=people&format=raw&tmpl=component',
		data	:	'tag='+tag,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();

			//assign new html
			jQuery.when(jQuery("#people").empty())
			.then(function(){
				jQuery("#people").html(data);
			});
			
			//update link message
			jQuery("#"+current_area).html(jQuery("#people_tags a.filter_"+tag).text());
		}
	});
}

//filter people status
function peopleStatus(status){

	showAjaxLoader();

	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterPeople&view=people&format=raw&tmpl=component',
		data	:	'status='+status,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();

			//assign new html
			jQuery.when(jQuery("#people").empty())
			.then(function(){
				jQuery("#people").html(data);
			});
			
			//update link message
			jQuery("#"+current_area).html(jQuery("#people_status a.filter_"+status).text());
		}
	});
}

//filter documents based on association
function documentAssoc(assoc){

	showAjaxLoader();
	
	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterDocuments&view=documents&format=raw&tmpl=component',
		data	:	'assoc='+assoc,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();

			//assign new html
			jQuery.when(jQuery("#documents").empty())
			.then(function(){
				jQuery("#documents").html(data);
			});
			
			//update link message
			jQuery("#"+current_area).html(jQuery("#document_assoc a.filter_"+assoc).text());
		}
	});
	
}


//filter documents based on ownership
function documentUser(user,team){

	showAjaxLoader();
	
	var search_id = ( user ) ? user : team;
	var dataString = ( user ) ? 'user='+search_id : 'team_id='+search_id;
	
	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterDocuments&view=documents&format=raw&tmpl=component',
		data	:	dataString,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();

			//assign new html
			jQuery.when(jQuery("#documents").empty())
			.then(function(){
				jQuery("#documents").html(data);
			});
			
			//update link message
			if ( user ){ 
				jQuery("#"+current_area).html(jQuery("#document_user a.filter_user_"+search_id).text());
			}else{
				jQuery("#"+current_area).html(jQuery("#document_user a.filter_team_"+search_id).text());
			}
		}
	});
	
}

//filter documents based on type
function documentType(type){

	showAjaxLoader();
	
	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	'index.php?task=filterDocuments&view=documents&format=raw&tmpl=component',
		data	:	'type='+type,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();

			//assign new html
			jQuery.when(jQuery("#documents").empty())
			.then(function(){
				jQuery("#documents").html(data);
			});
			
		}
	});
}

//function for filtering sales dashboard graphs
function salesDashboardFilter(member,team){
	var data = null;
	var type = null;
	//if we are searching for a specific member
	if ( member != null ){
		type = 'member_'+member;
		data = 'filter=member&id='+member;
	}
	//if we are searching for a specific team
	if ( team != null ){
		type = 'team_'+team;
		data = 'filter=team&id='+team;
	}
	//if we are searching for the company
	if ( member == null && team == null ){
		type = 'company';
		data = 'filter=company';
	}
	jQuery.ajax({
			type	:	'post',
			url		:	'index.php?task=graph&format=raw&tmpl=component',
			data	:	data,
			dataType:	'json',
			success	:	function(data){
				//update our graph data
				//redisplay the charts
				showAllCharts(data);
			}
		});
}

//sort all tables by ascending or descending
function sortTable(column,ele){

	showAjaxLoader();
	
	//hide the image for the last column sorted
	jQuery('div.sort_order').each(function(index,ele){
		jQuery(ele).removeClass('order_asc');
		jQuery(ele).removeClass('order_desc');
	});
	
	//set page globals
	if ( order_col == column ){
		order_dir = ( order_dir == 'desc' ) ? 'asc' : 'desc' ;
	}else{
		order_col = column;
		order_dir = 'asc';	
	}
	
	//construct sort string
	var order_string = "filter_order="+column+"&filter_order_Dir="+order_dir;
	
	//get the table we wish to display the results in
	var table_ele = typeof window.loc !== 'undefined' ? "#"+window.loc : jQuery('tbody.results');
	
	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	order_url,
		data	:	order_string,
		dataType:	'html',
		success	:	function(data){

			jQuery(table_ele).html(data);
			assignFilterOrder();
			hideAjaxLoader();

		}
	});
	
}

function assignFilterOrder(){
	jQuery('div.sort_order').children('a').each(function(){
		jQuery(this).parent('div').find("i").remove();
		if ( jQuery(this).attr('class') == order_col ){
			if ( order_dir == "asc" ){
				jQuery(this).parent('div').append('<i class="icon-chevron-up"></i>');
			}else{
				jQuery(this).parent('div').append('<i class="icon-chevron-down"></i>');
			}
		}
	});
}

//filter a table by certain values or dropdowns
function filterTable(val,col,ele){

	showAjaxLoader();

	//construct query string
	var order_string = col+"="+val;
	
	//get the table we wish to display the results in
	
	var table_ele = typeof window.loc !== 'undefined' ? "#"+window.loc : jQuery('.results');	
	
	//determine any other variables we might need to pass such as team or member types
	var input_type = jQuery(ele).attr('type');
	if ( input_type != 'text' ){ 
		var selected = jQuery("option:selected", ele);
	    if(jQuery(selected.parent()[0]).attr('class') == "member"){
	        order_string += "&owner_type=member";

	    } else if(jQuery(selected.parent()[0]).attr('class') == "team"){
	        order_string += "&owner_type=team";
	        
	    }
    }

    order_string += "&loc="+window.loc+"&format=raw&tmpl=component";
	
	//make ajax call
	jQuery.ajax({
		type	:	'post',
		url		:	order_url,
		data	:	order_string,
		dataType:	'html',
		success	:	function(data){

			hideAjaxLoader();

			//update table html
			jQuery(table_ele).html(data);
			
		}
	})
}