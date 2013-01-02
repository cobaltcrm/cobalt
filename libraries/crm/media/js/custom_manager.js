$(document).ready(function(){
	
	//assign type
	if ( typeof(type) !== 'undefined' ){
		changeCustomType(type);
	}
	
	//bind custom type dropdown
	$("#select-custom-type").bind('change',function(){
		changeCustomType(this.value);
	});
	
	//bind button to add more choices for picklists
	bindPicklistAdd();
	//bind remove value button
	bindPicklistRemove();
	// 
	Joomla.submitbutton = function(task){
		if ( task == "Custom.cancel" ){
			Joomla.submitform(task);
			return;
		}
		if ( save() ){
			Joomla.submitform(task);
		}
	}
	
});

function save(){
	var valid = true;

	var choiceCount = 0;
	var form = $("#adminForm :input");
	$(form).each(function(index,ele){
		if ( ele.name == "values[]" ){
			choiceCount++;
		}
		if ( $(this).hasClass('required') && ( $(this).val() == "" || $(this).val() == 0 ) ){
			$(this).focus();
			valid = false;
		}
	});

	if ( $("input[name=type]").val() == "picklist" && choiceCount == 0 ){
		valid = false;
	}

	return valid;
}

//change the custom field type
function changeCustomType(area){
	//change the html data on the page to reflect the correct type selected
	$("#custom_field_data").empty().html($("#custom_field_"+area).html());
	
	//if we select a picklist we must bind the links for custom choices
	if ( area == 'picklist' ){
		bindPicklistAdd();
		bindPicklistRemove();
	}
}

//bind add to picklist
function bindPicklistAdd(){
	$("#add_values").unbind();
	$("#add_values").bind('click',function(){
		addValue();
	});
}

//bind picklist areas
function bindPicklistRemove(){
	var ele = $("#choices").children('.choices');
	$(ele).each(function(index,ele){
		$(this).find('.remove_values:last').unbind();
	});
	$(ele).each(function(index,element){
		$(this).find('.remove_values:last').bind('click',function(){
			//assign function to link
			removeValue($(this).parentsUntil('.choices').parent('.choices'));
		})
	});
}

//add choices to the picklist
function addValue(){
	//append template
	$("#choices").append($("#choice_template").html());
	//get the new entry
	bindPicklistRemove();
}

//remove entry choices
function removeValue(element){
	//remove the element
	element.remove();
}
