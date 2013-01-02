var user_id = 0;

$(document).ready(function(){

	$("#type").change(function(){
		updateFields();
	});

	$("#return_url").blur(function(){
		updateFields();
	});

	$('#owner_id').autocomplete({
		source: function(request, response) {
	    var results = $.ui.autocomplete.filter(user_list, request.term);
	    	response(results.slice(0, 10));
		},
		select:function(event,ui){
			user_id = 0;
			user_id = ui.item.value;
			$("#owner_id_hidden").val(user_id);
			$("#owner_id").val(ui.item.label);
			updateFields();
			return false;
		},
		search:function(event,ui){
			user_id = 0;
			$("#owner_id_hidden").val('');
			updateFields();
			return;
		},
		change:function(event,ui){
			if ( user_id == 0 ){
				$("#owner_id_hidden").val('');
				$("#owner_id").val('');
			}
			updateFields();
		},
		close:function(event,ui){
			updateFields();
		}
	});

})

function showFieldCheckboxes(){
	$("div.field_checkbox_container").hide();
	var type = $("#type").val();
	$("#"+type+"_fields").show();
}

function updateFields(){
	var html = "<form action='"+base_url+"index.php?controller=saveWizardForm&format=raw&tmpl=component' method='POST'>\n";
	var type = $("#type").val();
	$.each(fields[type],function(fieldIndex,field){
		if ( $("#"+type+"_field_"+fieldIndex).is(":checked") ){
			switch(field.type){
				case "text":
				case "number":
				case "currency":
					html += "\t<div class='row'>\n\t\t<label>"+field.display+"</label>\n\t\t<input type='text' name='"+field.name+"' />\n\t</div>\n";
				break;
				case "picklist":
					html += "\t<div class='row'>\n\t\t<label>"+field.display+"</label>\n\t\t<select name='"+field.name+"'>\n";
					$.each(field.values,function(valueIndex,value){
						html += "\t\t\t<option value='"+valueIndex+"'>"+value+'</option>\n';
					});
					html += "\t\t</select>\n\t</div>\n";
				break;
			}
		}
	});
	var return_url = $.base64.encode($("#return_url").val());
	var owner_id = $("#owner_id_hidden").val();
	var form_id = $("#form_id").val();

	html += '\t<input type="hidden" name="owner_id" value="'+owner_id+'" />\n';
	html += '\t<input type="submit" value="Submit" />\n';
	html += '\t<input type="hidden" name="save_type" value="'+type+'" />\n';
	html += '\t<input type="hidden" name="return" value="'+return_url+'" />\n';
	html += '\t<input type="hidden" name="form_id" value="'+form_id+'" />\n';
	html += '</form>\n';
	$("#fields").val(html);
}	

