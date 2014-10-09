var current_import = 0;

$(document).ready(function(){
	$(":checkbox").tooltip({
		placement:'right',
		trigger:'hover'
	});
	$(":input[rel=tooltip]").tooltip({
		placement:'right',
		trigger:'focus'
	});
	$("select[rel=tooltip]").tooltip({
		placement:'right',
		trigger:'focus'
	});
	$("a[rel=tooltip]").tooltip({
		placement:'right',
	});

	//make people scrollable
	jQuery("#import_entries").scrollable({
		
	});

	bindColorpickers();

	if ( typeof window.show_tab !== 'undefined' ){
		showTab(show_tab);
	}
});

function bindColorpickers(){
	$(".hascolorpicker,.colorpicker").each(function(index,element){
		var val = $(element).val();
		val = ( val.indexOf("#") !== -1 ) ? val : "#"+val;
		$(element).css("background-color",val);
	});
	$(".hascolorpicker,.colorpicker").colorpicker().on('changeColor', function(ev){
  		$(ev.currentTarget).css('background-color',ev.color.toHex());
  		$(ev.currentTarget).val(ev.color.toHex());
	});
}

function checkAll(source) {
  if ( typeof source === 'object' ){
  	checkboxes = new Array();
  	var rows = jQuery(source).parentsUntil('table').parent('table').find('tr');
  	jQuery(rows).each(function(index,element){
  		var td = jQuery(element).find('td:first');
  		var checkbox = jQuery(td).find('input:checkbox');
  		checkboxes.push(checkbox);
  	});
  }else{
  	checkboxes = document.getElementsByName('ids[]');
  }
	jQuery(checkboxes).each(function(){
		jQuery(this).attr('checked',source.checked);
	});
}

function selectTextarea(ele){
	if ( typeof ele == 'object' ){
		$(ele).select();
	}else{
		if ( $("#"+ele).is(":visible") ){
			$("#"+ele).select();
		}else{
			setTimeout(function(){
				$("#"+ele).select();	
			},1);
		}
	}
}

function showTooltip(idOrName){
	if ( $("#"+idOrName).length > 0 ){
		$("#"+idOrName).tooltip({
			placement:'right',
			trigger:'manual'
		});
		$("#"+idOrName).tooltip('show');
	}else{
		$("[name="+idOrName+"]").tooltip({
			placement:'right',
			trigger:'focus'
		});
		$("[name="+idOrName+"]").tooltip('show');
	}
	$("#"+idOrName).parent('a').tooltip('show');
}

function showTab(name){
		$("a[href=#"+name+"]").tab('show');
}

function updateConfig(config_type,config_value){
	if ( config_value == 1 ){
		$("#"+config_type.replace('.','\\.')).addClass('completed').removeClass('uncompleted');
		$("#"+config_type.replace('.','\\.')).find('span.uncompleted').addClass('completed').removeClass('uncompleted');
		$("#"+config_type.replace('.','\\.')).find('span.uncompleted-inner').addClass('completed-inner').removeClass('uncompleted-inner');
		$.ajax({
			type:'POST',
			url:'index.php?task=config&task=updateConfig&format=raw&tmpl=component',
			data:'show_help=1&'+config_type+"="+config_value,
			dataType:'JSON',
			success:function(data){

			}
		})
	}
	$("#help_actions").fadeOut('fast');
}

function disableHelp(){
	$.ajax({
			type:'POST',
			url:'index.php?task=config&task=updateConfig&format=raw&tmpl=component',
			data:'show_help=0',
			dataType:'JSON',
			success:function(data){
				// $("#disable_help_hidden").modal();
				$("#help_description_action").slideUp('fast');
			}
		})
}

function downloadImportTemplate(ele){

	var form = $(ele).parent('form');
	var old_action = jQuery(form).attr('action');

	jQuery(form).attr('action','index.php?task=downloadImportTemplate&tmpl=component&format=raw');
	jQuery(form).submit();
	jQuery(form).attr('action',old_action);

}

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