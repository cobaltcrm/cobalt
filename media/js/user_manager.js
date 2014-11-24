	/**
 * Globals
 */
var new_manager = false;
var user_id = 0;

//onready
$(document).ready(function(){

	$("#user_color").css({'backgroundColor':"#"+$("#user_color").val()});
	$("#user_color").ColorPicker({
		color	: "#"+$(this).val(),
		onChange : function(rgb,hex){
			$("#user_color").val(hex);
			$("#user_color").css({'backgroundColor':"#"+hex});
		},
		onSubmit : function(rgb,hex){
			$("#user_color").val(hex);
			$("#user_color").css({'backgroundColor':"#"+hex});
		}
	});

	Joomla.submitbutton = function(task){
		if ( task == "Users.cancel" ){
			Joomla.submitform(task);
			return;
		}
		if ( save() ){
			Joomla.submitform(task);
		}else{
			if ( user_id == 0 ){
				alert(Joomla.JText._('COBALT_PLEASE_SELECT_A_USER'));
			}
		}
	}

	$('#uid_name').autocomplete({
		source: function(request, response) {
        var results = $.ui.autocomplete.filter(user_list_names, request.term);
        	response(results.slice(0, 10));
    	},
    	select:function(event,ui){
    		user_id = getUserId(ui.item.value);
    		updateUser(user_id);
    		$("#id").val(user_id);
    	},
    	search:function(event,ui){
    		user_id = getUserId(event.currentTarget.value);
    		updateUser(user_id);
    		$("#id").val(user_id);
    	},
    	
		// autoFocus:true,

	});

});

function getUserId(name){
	user_id = 0;
	$.each(user_list,function(id,values){
		if ( values.name == name ){
			user_id = id;
		}
	})
	return user_id;
}

function save(){
	var valid = true;
	$("#adminForm :input").each(function(){
		if ( $(this).hasClass('required') && ( $(this).val() == "" || $(this).val() == 0 ) ){
			$(this).focus();
			valid = false;
		}
	});
	return valid;
}

//prefill user data on page
function updateUser(id){
	$("#id").val('');
	$("input[name=first_name]").val('');
	$("input[name=last_name]").val('');
	$("#email").val('');
	$("#username").val('');
	if ( id ){
		$("#id").val(id);
		$("input[name=first_name]").val(users[id].first_name);
		$("input[name=last_name]").val(users[id].last_name);
		$("#email").val(users[id].email);
		$("#username").val(users[id].username);
	}
}

//update member and team data depending on selection
function updateRole(id){

	if ( id == "manager" ){
		$("#team_name").show();
	}else{
		$("#team_name").hide();
	}
	
	//show team assignment
	if ( id == 'basic' ){
		$("#team_assignment").show().removeClass('hidden');
	}else{
		$("#team_assignment").hide().addClass('hidden');
	}
	
	//if we are downgrading a users access
	if ( role_type == 'manager' && id != 'manager' ){
		new_manager = true;
		$("#manager_id").addClass('required');
		$("#manager_assignment").show();
	}else{
		new_manager = false;
		$("#manager_id").removeClass('required');
		$("#manager_assignment").hide();
	}
	
	//reassign a team if we are changing role
	if ( role_type != id ){
		$("select[name=team_id]").val('');
	}
	
}