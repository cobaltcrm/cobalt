var current_import = 0;

jQuery(document).ready(function(){

	jQuery("#upload_input_invisible").bind("change",function(){
     		jQuery(this).parentsUntil('form').parent('form').submit();
   	});

   	//make people scrollable
	jQuery("#import_entries").scrollable({
		
	});

});

function seekImport(seek){

	jQuery("#editForm").innerHeight(jQuery("#editForm").css('height').replace('px',''));

	var beginning = ( current_import == 0 && seek == -1 ) ? true : false;
	var end = ( current_import >= import_length-1 && seek == 1 ) ? true : false;

	if ( !beginning && !end ){

		jQuery("#import_entry_"+current_import).fadeOut('fast',function(){
			current_import += seek;
			jQuery("#viewing_entry").fadeOut('fast',function(){
				jQuery("#viewing_entry").html(current_import+1)
				jQuery("#viewing_entry").fadeIn('fast');
			})
			jQuery("#import_entry_"+current_import).fadeIn('fast');
		});

	}

}