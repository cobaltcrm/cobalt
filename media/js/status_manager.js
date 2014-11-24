$(document).ready(function(){

	$(".hascolorpicker").colorpicker({});

	if ( typeof(status_color) !== 'undefined' ){
		$("input[name=color]").css({'backgroundColor':"#"+status_color});
	}
	//assign color picker
	$('input[name=color]').ColorPicker({
		color	:	status_color,
		onChange : function(rgb,hex){
			$('input[name=color]').val(hex);
			$("input[name=color]").css({'backgroundColor':"#"+hex});
		},
		onSubmit : function(rgb,hex){
			$('input[name=color]').val(hex);
			$("input[name=color]").css({'backgroundColor':"#"+hex});
		}
	});
	$('#colorwheel').ColorPicker({
		color	:	status_color,
		onChange : function(rgb,hex){
			$('input[name=color]').val(hex);
			$("input[name=color]").css({'backgroundColor':"#"+hex});
		},
		onSubmit : function(rgb,hex){
			$('input[name=color]').val(hex);
			$("input[name=color]").css({'backgroundColor':"#"+hex});
		}
	});
});
