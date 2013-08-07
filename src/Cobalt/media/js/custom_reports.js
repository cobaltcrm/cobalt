jQuery(document).ready(function(){
	
	//make items draggable
	draggable();
	
	
	jQuery(".holder").droppable({
		drop:function(event,ui){
			if ( jQuery(ui.draggable).hasClass('data') ){
				jQuery.when(jQuery(this).html(jQuery(ui.draggable).html()))
				.then(function(){ jQuery(this).attr('id',jQuery(ui.draggable).attr('id')); })
				.then(function(){ jQuery(this).attr('name',jQuery(ui.draggable).attr('name')); })
				.then(function(){jQuery(ui.draggable).remove();})
				.then(function(){jQuery(this).append('<div class="remove"><a onclick="remove(this)" class="remove"></a></div>');})
				.then(function(){jQuery(this).css('top','0px');})
				.then(function(){jQuery(this).css('left','0px');})
				.then(function(){jQuery(this).fadeIn('fast');})
				.then(function(){jQuery(this).effect("highlight",{},3000);})
				.then(function(){jQuery(this).addClass('added_data');})
				.then(function(){jQuery(this).removeClass('data');})
				.then(function(){jQuery(this).removeClass('highlight_holder');})
				.then(function(){jQuery('.holders').sortable('refresh');})
				.then(function(){jQuery('.holders').sortable('destroy');})
				.then(function(){sortable();});
			}
		},
		over:function(event,ui){
			if ( jQuery(ui.draggable).hasClass('data') ){
				jQuery(this).addClass('highlight_holder');
			}
		},
		out:function(event,ui){
			if ( jQuery(ui.draggable).hasClass('data') ){
				jQuery(this).removeClass("highlight_holder");
			}
		}
	});
	
	//bind delete button to delete custom reports
	jQuery('.delete_custom_report').bind('click',function(){deleteReport(this);});
	
	//make items sortable
	sortable();
	
});

//remove elements from the custom fields blocks
function remove(ele){
	
	//block to remove and re-append to the custom fields columns
	var block = jQuery(ele).parentsUntil('li').parent('li');
    jQuery(block).appendTo('#custom_field_columns ul.columns');
    jQuery(block).css('display','inline-block');
    jQuery(block).addClass('data');
    jQuery(block).removeClass('added_data');
    
    //make items draggable
    draggable();
   
   //add a placeholder
   jQuery("#custom_field_holders ul.holders").append("<li class=\"holder\">"+Joomla.JText._('COBALT_DRAG_FIELD_HERE')+"</li>");
   
}

//make items draggable
function draggable(){
	
	jQuery(".data").draggable({
		revert:true,
		start:function(event,ui){
			sorting = true;
		},
		stop:function(event,ui){
			sorting = false;
		},
		opacity:.5,
	});
	
}

//make items sortable
function sortable(){
	jQuery('.holders').sortable({});
}


//validate custom fields
function validateCustomForm(form){

	var validated = save(jQuery(form));

	if ( validated ){
		var field_count = 0;
		var fields = jQuery('.holders').sortable('toArray');
		var field_data = new Object();
		jQuery(fields).each(function(index,value){
			if ( value.length > 0 ){
				field_data[value] = jQuery("#"+value).attr('name');
			}
		});
		if ( Object.keys(field_data).length > 0 ){
			
			jQuery(field_data).each(function(index,fields){
				for ( var i in fields){
					var value = fields[i];
					jQuery("#post").append("<input type='hidden' name=fields["+i+"] value='"+value+"' />");
				}
			});
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}

	
}

//delete document
function deleteReport(e){
	
	if ( confirm(Joomla.JText._('COBALT_ARE_YOU_SURE_DELETE_REPORT')) ){
	
		var search = 'custom_report_';
		var id =jQuery(e).parentsUntil('tr').parent('tr').attr('id').replace(search,'');
		//make ajax call
		jQuery.ajax({
			
			type	:	'post',
			url		:	'index.php?task=deleteReport&format=raw&tmpl=component',
			data	:	'id='+id,
			dataType:	'json',
			success	:	function(data){
				if ( !data.error ){
					jQuery.when(jQuery("#custom_report_"+id).fadeOut("slow"))
					.then(function(){
						jQuery("#custom_report_"+id).remove();
					});
					modalMessage(Joomla.JText._(COBALT_SUCCESS_MESSAGE,'Success'), Joomla.JText._(COBALT_GENERIC_UPDATED,'Successfully updated'));
				}else{
					//TODO error handling
				}
			}
			
		});
	
	}
}
