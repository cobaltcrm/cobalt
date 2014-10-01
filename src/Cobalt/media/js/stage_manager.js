$(document).ready(function(){
	
	$("#percent").slider({
		
		max		:	100,
		min		:	0,
		value 	:	$("input[name=percent]").val(),
		slide	:	function(event,ui){
			$("input[name=percent]").val(ui.value);
			$("#percent_value").html(ui.value+"%");
		},
		stop	:	function(event,ui){
			$("input[name=percent]").val(ui.value);
			$("#percent_value").html(ui.value+"%");
		},
		change	:	function(event,ui){
			$("input[name=percent]").val(ui.value);
			$("#percent_value").html(ui.value+"%");
		}
	});

	//define our areas
	var customization_area = $("#adminForm input.color");
	var customization_area_colorwheel = $("#adminForm div.colorwheel");

	//assign input binds
	$.each(customization_area,function(index,area){
		bindColorInputs(area);
	});
	
	//assign color wheel binds
	$.each(customization_area_colorwheel,function(index,area){
		bindColorWheels(area);
	});
	
});

// bind color input events
function bindColorInputs(ele){
	var name = $(ele).attr('name');
	$(ele).css({'backgroundColor':"#"+$(ele).val()});
	$(ele).ColorPicker({
			color	: "#"+$(ele).val(),
			onChange : function(rgb,hex){
				$(ele).val(hex);
				$(ele).css({'backgroundColor':"#"+hex});
			},
			onSubmit : function(rgb,hex){
				updateCss(name,hex);
				$(ele).val(hex);
				$(ele).css({'backgroundColor':"#"+hex});
			}
		});
}

//bind color wheel events
function bindColorWheels(ele){
	/*
	var parent_input = $(ele).prev('input:text');
	var name = $(ele).prev('input:text').attr('name');
	$(parent_input).css({'backgroundColor':"#"+$(ele).val()});
		$(ele).colorpicker({
			color	: "#"+$(parent_input).val(),
			onChange : function(rgb,hex){
				$(parent_input).val(hex);
				$(parent_input).css({'backgroundColor':"#"+hex});
			},
			onSubmit : function(rgb,hex){
				updateCss(name,hex);
				$(parent_input).val(hex);
				$(parent_input).css({'backgroundColor':"#"+hex});
			}
		});
	*/
}