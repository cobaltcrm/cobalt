jQuery(document).ready(function(){
	
	//bind the assignment areas
	jQuery("#assigned_id").bind('change',function(){
		updateAssignedType(jQuery(this));
	});
	
	//bind date assignment and date picker areas
	jQuery("#date_picker").bind('change',function(){
		updateGoalDate(jQuery(this));
	});
	
	//bind delete goals buttons
	jQuery(".delete_goals").each(function(){
		jQuery(this).bind('click',function(){deleteGoals(jQuery(this));});
	});

	assignDates('this_week');
	
});


//change the type of assignment we are taking to hidden fields
function updateAssignedType(ele){
	
	//get element value
	var value = jQuery(ele).val();
	var assigned_id = null;
	var assigned_type = null;
	
	//assign member data
	if ( value.indexOf('member_') != -1 ){
		assigned_id = value.replace('member_','');
		assigned_type = 'member';
	} 
	//assign company data
	if ( value.indexOf('company_') != -1 ){
		assigned_id = value.replace('company_','');
		assigned_type = 'company';
	}
	//assign team data
	if ( value.indexOf('team_') != -1 ){
		assigned_id = value.replace('team_','');
		assigned_type = 'team';		
	}
	
	//update hidden fields
	jQuery("input[name=assigned_id]").val(assigned_id);
	jQuery("input[name=assigned_type]").val(assigned_type);
	
	
}

function assignDates(date_interval){

	var current_date = new Date();
	var start_date = null;
	var end_date = null;

	switch ( date_interval ){

		case "this_week":

			var today = current_date.getDay();
			var days_to_add = 5 - today;
			var days_to_remove = 0 + today;

			current_date.setDate(current_date.getDate()-days_to_remove);
			var beginning_of_week = current_date.getDate();
			current_date.setDate(current_date.getDate()+days_to_add+days_to_remove);
			var end_of_week = current_date.getDate();

			var start_date = current_date.getFullYear() + "-" + (  current_date.getMonth() + 1 ) + "-" + ( beginning_of_week ) + " 00:00:00";
			var end_date = current_date.getFullYear() + "-" + (  current_date.getMonth() + 1 ) + "-" + ( end_of_week ) + " 23:59:59";

		break;


	}

	jQuery("#start_date_hidden").val(start_date);
	jQuery("#end_date_hidden").val(end_date);

}


//change the start and end date for goals
function updateGoalDate(ele){
	
	var val = jQuery(ele).val();
	var current_date = new Date();
	var start_date = null;
	var end_date = null;

	switch ( val ){

		case "this_week":

			assignDates('this_week');

		break;

		case "next_week":


			var today = current_date.getDay();
			var days_to_add = ( 5 - today );
			var days_to_remove = ( 0 + today );

			current_date.setDate(current_date.getDate()-days_to_remove+8);
			var beginning_of_week = current_date.getDate();
			current_date.setDate(current_date.getDate()+days_to_add+days_to_remove-1);
			var end_of_week = current_date.getDate();

			var this_month = ( current_date.getMonth()==11 ) ? 1 : current_date.getMonth() + 1;

			var start_date = current_date.getFullYear() + "-" + (  this_month ) + "-" + ( beginning_of_week ) + " 00:00:00";
			var end_date = current_date.getFullYear() + "-" + (  this_month ) + "-" + ( end_of_week ) + " 23:59:59";


		break;

		case "this_month":

			var this_month = ( current_date.getMonth()==11 ) ? 1 : current_date.getMonth() + 1;
			var next_month = ( this_month==12 ) ? 1 : this_month + 1;
			start_date = current_date.getFullYear() + "-" + this_month + "-00 00:00:00";
			end_date   = current_date.getFullYear() + "-" + next_month + "-00 23:59:59";

		break;

		case "next_month":

			var this_month = ( current_date.getMonth()==11 ) ? 1 : current_date.getMonth() + 1;
			var next_month = ( this_month==12 ) ? 1 : this_month + 1;
			var next_next_month = ( next_month==12 ) ? 1 : next_month + 1;
			start_date = current_date.getFullYear() + "-" + next_month + "-00 00:00:00";
			end_date   = current_date.getFullYear() + "-" + next_next_month + "-00 23:59:59";

		break;

		case "this_quarter":

			var quarter = Math.floor((current_date.getMonth() / 3));	
		  	
		  
	      	var firstDate = new Date(current_date.getFullYear(), quarter * 3, 1);
	      	var start_month = ( firstDate.getMonth()==11 ) ? 1 : firstDate.getMonth() + 1;
	      	start_date = firstDate.getFullYear() + "-" + start_month + "-" + firstDate.getDate() + "-00 00:00:00";

	     	 var secondDate = new Date(current_date.getFullYear(), quarter * 3 + 3, 0);
	     	 var end_month = ( secondDate.getMonth()==11 ) ? 1 : secondDate.getMonth() + 1;
	      	end_date = secondDate.getFullYear() + "-" + end_month + "-" + secondDate.getDate() + "-00 23:59:59";

		break;

		case "next_quarter":

			var quarter = Math.floor((current_date.getMonth() / 3));
			var this_month = ( current_date.getMonth()==11 ) ? 1 : current_date.getMonth() + 1;

			var firstDate = new Date(current_date.getFullYear(), quarter * 3 + 3, 1);
			var start_month = ( firstDate.getMonth()==11 ) ? 1 : firstDate.getMonth() + 1;
	      	start_date = firstDate.getFullYear() + "-" + start_month + "-" + firstDate.getDate() + "-00 00:00:00";

	      	var secondDate = new Date(current_date.getFullYear(), quarter * 3 + 6, 0);
	      	var end_month = ( secondDate.getMonth()==11 ) ? 12 : secondDate.getMonth() + 1;
	      	end_date = secondDate.getFullYear() + "-" + end_month + "-" + secondDate.getDate() + "-00 23:59:59";

		break;


		case "this_year":

			start_date = current_date.getFullYear() + "-00-00 00:00:00";
			end_date   = ( current_date.getFullYear() + 1 ) + "-00-00 23:59:59";

		break;

	}

	if ( val == 'custom' ){

		jQuery.when(jQuery("#date_selection_area").append(jQuery("#date_selection_area_template")))
		.then(function(){jQuery("#date_selection_area_template").show();})
		.then(function(){jQuery("#date_selection_area").slideDown('fast');})

	}else{

		jQuery("#date_selection_area").slideUp('fast');
		jQuery("#start_date_hidden").val(start_date);
		jQuery("#end_date_hidden").val(end_date);

	}
	
}

//filter through goals by individual ids
function changeIndividual(id){
	//make ajax call
	jQuery.ajax({
		type		:'post',
		url			:'index.php?task=getIndividualGoals&format=raw&tmpl=component',
		data		:'id='+id,
		dataType	:'html',
		success		:function(data){
			jQuery.when( jQuery("#individual_goals").empty() )
					.then(function () {
						jQuery("#individual_goals").html(data);
					});
		}
	});
}

//filter through goals by team ids
function changeTeam(id){
	//make ajax call
	jQuery.ajax({
		type		:'post',
		url			:'index.php?task=getTeamGoals&format=raw&tmpl=component',
		data		:'id='+id,
		dataType	:'html',
		success		:function(data){
			jQuery.when( jQuery("#team_goals").empty() )
					.then(function () {
						jQuery("#team_goals").html(data);
					});
		}
	});
}

//change a leaderboard entry
function changeLeaderBoard(id){
	//make ajax call
	jQuery.ajax({
		type		:'post',
		url			:'index.php?task=getLeaderBoard&format=raw&tmpl=component',
		data		:'id='+id,
		dataType	:'html',
		success		:function(data){
			jQuery.when( jQuery("#leaderboards").empty() )
					.then(function () {
						jQuery("#leaderboards").html(data);
					});
		}
	});
}

//delete goals with modal popup
function deleteGoals(obj){
	
	//make ajax call for modal information
	jQuery.ajax({
		url		: 'index.php?view=goals&layout=delete&format=raw&tmpl=component',
		type	: 'post',
		data	: 'goal_type='+jQuery(obj).attr('id').replace('goal_type_',''),
		dataType: 'html',
		success : function(data){
			jQuery("#CobaltAjaxModalBody").html(data);
		}
	});

	jQuery("#CobaltAjaxModalHeader").text(Joomla.JText._('COBALT_DELETE_GOALS'));
	jQuery("#CobaltAjaxModalFooter").hide();

	//open modal dialog
	jQuery('#CobaltAjaxModal').modal('show');
	
}


//delete individual goal entries
function deleteGoalEntry(area){
	//confirm our delete
	//if delete confirmed
	var confirmDelete = confirm(Joomla.JText._('COBALT_DELETE_GOALS'));
	//if delete confirmed
	if ( confirmDelete ){
		//get goal id to delete
		var goal =jQuery(area).parentsUntil('tr').parent('tr');
		var goal_id = jQuery(goal).attr('id');
		//make ajax call
		jQuery.ajax({
			url		: 'index.php?task=deleteGoalEntry&format=raw&tmpl=component',
			type	: 'post',
			data	: 'goal_id='+goal_id,
			dataType: 'json',
			success : function(data){
				if ( data.error == 0 ){
					//display success 
					modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
					//get goal area to fade out
					jQuery(goal).fadeOut('fast');
					jQuery("#goal_"+goal_id).fadeOut("fast");
					// jQuery('#delete_goals').dialog('close');
				}
			}
		});
	}	
}
