/**
 * Globals
 */

var convoid = null;
var noteid = null;

jQuery(document).ready(function(){

	if ( typeof company_names != 'undefined' ){
		jQuery("input[name=company]").autocomplete({
			source: function(request, response) {
	        var results = jQuery.ui.autocomplete.filter(company_names, request.term);
	        	response(results.slice(0, 10));
	    	},
			select:function(event,ui){
				jQuery("#company_name").val(ui.item.label);
				jQuery("#company_id").val(ui.item.value);
				jQuery("#company_message").html('');
				return false;
			},
			search:function(){

			},
			open: function(){
        		jQuery(this).autocomplete('widget').css('z-index', 100);
        		return false;
		    }
		});
	}

	if ( typeof people_names != 'undefined' ){
		jQuery("input[name=primary_contact_name]").autocomplete({
			source: function(request, response) {
	        var results = jQuery.ui.autocomplete.filter(people_names, request.term);
	        	response(results.slice(0, 10));
	    	},
			select:function(event,ui){
				jQuery("#primary_contact_name").val(ui.item.label);
				jQuery("#primary_contact_id").val(ui.item.value);
				jQuery("#person_message").html('');
				return false;
			},
			search:function(){

			},
			open: function(){
        		jQuery(this).autocomplete('widget').css('z-index', 100);
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
				deal_update(form_name);
			}
	});
	
	//bind summary area
	jQuery("#editable_summary").bind('click',function(){
		jQuery("#editable_summary").hide();
		jQuery("#editable_summary_area").show();
	});	
	
	
	/*
	 * bind actions to actions dropdown menu items
	 */
	jQuery("#archive").bind('click',function(){
		
		//form data string
		archived = (archived==0)?1:0;
		var dataString = "id="+id+"&archived="+archived;
		
		//make ajax
		jQuery.ajax({
			
			type	:	"POST",
			url		:	'index.php?controller=save&model=deal&format=raw&tmpl=component',
			data	:	dataString,
			dataType:	'json',
			success	:	function(data){
				//display results
				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
				
				if ( archived )
				jQuery("#archive").text("Unarchive")
				if ( !archived )
				jQuery("#archive").text("Archive")
				actions();	
			}
		
		});
		
	});
	
});

//main function to make all ajax calls
//pass this the FORM name you want submitted
function deal_update(loc){

	if ( typeof id !== 'undefined' ){
	
		//generate data string for ajax call
		var dataString = '';
		var $form = jQuery('form[name='+loc+'] :input');
		$form.each(function(){
			dataString += "&"+this.name+"="+jQuery(this).val();
		});
		
		//make ajax call
		jQuery.ajax({
			type	:	"POST",
			url		:	'index.php?controller=save&model=deal&format=raw&tmpl=component',
			data	:	'id='+id+'&'+dataString,
			dataType:	'json',
			success	:	function(data){
					
					//determine location to update on page
					
					//amount area
					if(loc=="amount") {
						jQuery.when(jQuery("span.amount").fadeOut("slow"))
						.then(function(){
							jQuery("span.amount").empty().html('$'+data.amount);
						})
						.then(function(){
							jQuery("span.amount").fadeIn('slow');
						})
						.then(function(){
							
							jQuery("span.amount").bind('click',function(){
					
								jQuery("span.amount").unbind();
								jQuery.when(jQuery('span.amount').fadeOut("slow"))
								.then(function(){
									jQuery("span.amount").html('<form onsubmit="deal_update()" ><input class="inputbox" type="text" name="amount" /><input class="button" type="submit" value="'+COBALT_CHANGE_BUTTON+'" /></form>');
								})
								.then(function(){
									jQuery("span.amount").fadeIn("slow");
								});
								
							});
							
						});
					}
					
					//summary area
					if(loc=="summary") { 
						
						var summary = jQuery("#summary textarea[name=summary]").val();
						
						jQuery.when(jQuery("#summary").fadeOut('slow'))
						.then(function(){
							jQuery("#summary").html(nl2br(summary));
						})
						.then(function(){
							jQuery("#summary").fadeIn("slow");
						})
						.then(function(){
							jQuery("div.summary_edit").fadeIn("slow");
						});
					
					}
					
					modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
				
			}
			
		});

	}
	
}