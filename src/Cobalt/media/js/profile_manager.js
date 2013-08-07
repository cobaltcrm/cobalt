/**
 * Globals
 */
var cur_slide = null;

jQuery(document).ready(function(){
	
	//bind slides
	jQuery('.profile_slide_link').each(function(){
		jQuery(this).bind('click',function(){
			displaySlide(jQuery(this).attr('id')+'_slide');
		})
	})
	
	//assign cancel buttons
	jQuery('.cancel').each(function(){
		jQuery(this).bind('click',function(){
			jQuery(".cancel").parent('div').slideUp('fast');
			cur_slide = null;
		});
	});
	
	//assign save buttons
	jQuery('.save').each(function(){
		jQuery(this).bind('click',function(){
			saveForm(jQuery(this));
		});
	});

	//email check
	bindEmails();
	
});

function bindEmails(){
	jQuery("input[name=email\\[\\]]").keyup(function(){
		var ele = jQuery(this);
		jQuery(ele).removeClass('highlight_input');
		clearTimeout(jQuery(ele).attr('timeout'));
		jQuery(ele).attr('timeout',setTimeout(function(){checkEmail(jQuery(ele));},500));
	});
}

function checkEmail(ele){

	if ( jQuery(ele).val() != "" ){
		var dataString = "email="+jQuery(ele).val();
		jQuery.ajax({
			url: base_url+'index.php?task=ajax&task=checkEmailName&tmpl=component&format=raw',
			type:'POST',
			data:dataString,
			dataType:'JSON',
			success:function(data){
				var save_button =  jQuery(ele).parentsUntil('form').parent('form').next('.save');
				if(data.success) {	
					jQuery(ele).next('span.message').html(data.message);				
				}
				if(data.email_exists==true || data.email_exists==1){
					jQuery(ele).addClass('highlight_input');
					jQuery(save_button).unbind();
					jQuery(save_button).bind('click',function(){
						jQuery(ele).focus();
					});
				}else{
					jQuery(ele).removeClass('highlight_input');
					jQuery(save_button).unbind();
					jQuery(save_button).bind('click',function(){
						saveForm(jQuery(save_button));
					});
				}
			}
		});
	}else{
		jQuery(ele).next('span.message').html('');
	}

}


//displays slides
function displaySlide(ele){
	
	if ( cur_slide != null && ele != cur_slide ){
		jQuery("#"+ele).slideDown('fast');
		jQuery("#"+cur_slide).slideUp('fast');
	}else{
		jQuery("#"+ele).slideToggle('fast');
	}
	
	//assign new slide
	cur_slide = ele;
}


//save form data
function saveForm(ele){
	
	//get the form we are submitting
	$form = jQuery(ele).prev('form');
	$inputs = $form.find(':input');
	var dataString = "";
	
	//construct the datastring
	$inputs.each(function(){
		if ( this.type == 'checkbox' ){
			if (jQuery(this).is(':checked')) {
				jQuery(this).val(1);
			}else{
				jQuery(this).val(0);
			}
		}
		dataString += this.name + "=" + jQuery(this).val() + "&";
	});
	//assign id
	dataString += "id="+user_id;
	
	//make ajax call
	jQuery.ajax({
		
		type	:	'post',
		url		:	'index.php?task=saveProfile&format=raw&tmpl=component',
		data	:	dataString,
		dataType:	'JSON',
		success	:	function(data){
			if ( data.error == true ){
				
			}else{
				//display success
				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
				//hide slide and reset the current slide
				jQuery("#"+cur_slide).slideUp('fast');
				cur_slide = null;
			}
		}
		
	});
	
}

function addEmailBox(){
	var html = '<li><label>'+Joomla.JText._('COBALT_EMAIL')+'</label><input class="inputbox" type="text" name="email[]" value=""><span class="message"></span></li>';
	jQuery("#email_input_boxes").append(html);
	bindEmails();
}