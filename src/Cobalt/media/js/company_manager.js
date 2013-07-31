jQuery(document).ready(function(){

	if ( typeof company_names != 'undefined' ){
		jQuery("input[name=company]").autocomplete({
			source:company_names,
			select:function(event,ui){
				jQuery("#company_name").val(ui.item.label);
				jQuery("#company_id").val(ui.item.value);
				return false;
			},
			search:function(){
				jQuery("#company_name").val(ui.item.label);
				jQuery("#company_id").val(ui.item.value);
				return false;
			}
		});
	}

	jQuery(".date_input").datepicker('destroy');
	jQuery(".date_input").datepicker({
			dateFormat:userDateFormat,
			changeYear:true,
			changeMonth:true, 
			onSelect:function(event,ui){
				jQuery("#"+jQuery(this).attr('id')+'_hidden').val(ui.selectedYear+"-"+("0"+(ui.selectedMonth+1)).slice(-2)+"-"+("0"+(ui.selectedDay)).slice(-2));
				var form_name = jQuery(this).attr('id')+"_form";
				company_update(form_name);
			}
	});
	
});

//bind work address slide down
	function bind_area(loc){
		jQuery("#"+loc+"_button").fadeOut('fast');
		jQuery("#"+loc+"_info").slideDown('fast');
	}


	//main function to make all ajax calls
//pass this the FORM name you want submitted
function company_update(loc){
	
	//generate data string for ajax call
	var dataString = '';
	var $form = jQuery('form[name='+loc+'] :input');
	$form.each(function(){
		dataString += "&"+this.name+"="+jQuery(this).val();
	});
	
	//make ajax call
	jQuery.ajax({
		type	:	"POST",
		url		:	'index.php?controller=save&model=company&format=raw&tmpl=component',
		data	:	'id='+id+'&'+dataString,
		dataType:	'json',
		success	:	function(data){

				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
			
		}
		
	});
	
}