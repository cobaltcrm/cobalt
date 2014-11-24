var tabsHoverCss = "";
var tabsHoverTextCss = "";
var old_link_style = "";

$(document).ready(function(){
	
	//define our areas
	var customization_area = $("#themes").children('ul').children('li').children('input:text');
	var customization_area_colorwheel = $("#themes").children('ul').children('li').children('div.colorwheel');
	
	//assign input binds
	$.each(customization_area,function(index,area){
		bindColorInputs(area);
	});
	
	//assign color wheel binds
	$.each(customization_area_colorwheel,function(index,area){
		bindColorWheels(area);
	});
	
	//assign new custom area depending on radio select
	$('input[name=id]').each(function(index){
		$(this).bind('click',function(){changeTheme($(this).val())});
	});
	
	//assign the default theme
	changeTheme(assigned_theme);

	$("#site-logo").bind("change",function(){
		setTimeout(function(){
			var src = $("#site-logo-preview").find("img").attr("src");
			$("#site-logo-img").attr("src",src);
		},300);
	});

	$(".branding-input").on("changeColor",function(ev){
		var cssClass = $(this).data("css-class");
		var cssStyle = $(this).data("css-style");
		var color = ev.color.toHex();
		if ( cssClass == ".navbar-inner" ){
			
			var c25 = increase_brightness(color,25);
			var c50 = increase_brightness(color,50);
			var cn25 = increase_brightness(color,-25);

			$(".block-btn").css("border-left","1px solid "+c25);
			$(".feature-btn").css("border-left","1px solid"+c50);
			$(".feature-btn").css("border-right","1px solid"+c50);
			$(".feature-btn").css("background",cn25);

			$("#block-btn-border").val(c25);
			$("#feature-btn-border").val(c50);
			$("#feature-btn-bg").val(cn25);

		}
		switch(cssStyle){
			case "hover-color":
				var oldColor = $(cssClass).css("color");
				$(cssClass).hover(function(eleEv){
					var element = eleEv.currentTarget;
					$(element).css("color",color);
				},function(eleEv){
					var element = eleEv.currentTarget;
					$(element).css("color",oldColor);
				});
			break;
			case "hover-background":
				var oldColor = $(cssClass).css("background");
				$(cssClass).hover(function(eleEv){
					var element = eleEv.currentTarget;
					$(element).css("background",color);
				},function(eleEv){
					var element = eleEv.currentTarget;
					$(element).css("background",oldColor);
				});
			break;
			default:
				$(cssClass).css(cssStyle,color);
			break;
		}
	});

	//update site name
	$("#site-name").bind("keyup",function(){
		$("#site-name-link").text($(this).val());
	})
	
});

function increase_brightness(hex, percent){
    // strip the leading # if it's there
    hex = hex.replace(/^\s*#|\s*$/g, '');

    // convert 3 char codes --> 6, e.g. `E0F` --> `EE00FF`
    if(hex.length == 3){
        hex = hex.replace(/(.)/g, '$1$1');
    }

    var r = parseInt(hex.substr(0, 2), 16),
        g = parseInt(hex.substr(2, 2), 16),
        b = parseInt(hex.substr(4, 2), 16);

    return '#' +
       ((0|(1<<8) + r + (256 - r) * percent / 100).toString(16)).substr(1) +
       ((0|(1<<8) + g + (256 - g) * percent / 100).toString(16)).substr(1) +
       ((0|(1<<8) + b + (256 - b) * percent / 100).toString(16)).substr(1);
}


/**
 * function to change theme
 */
function changeTheme(id){
	//update page html
	$.when($("#customization_content").html($("#"+id).html()))
	
	//define new areas
	.then(function(){
		var customization_area = $("#customization_content").children('ul').children('li').children('input:text');
		var customization_area_colorwheel = $("#customization_content").children('ul').children('li').children('div.colorwheel');
		//assign input binds
		$.each(customization_area,function(index,area){
			bindColorInputs(area);
		});
		//assign color wheel binds
		$.each(customization_area_colorwheel,function(index,area){
			bindColorWheels(area);
		});
		
	})
	.then(function(){
		//update page css
		$("#com_cobalt_toolbar").css({'backgroundColor':"#"+$("#customization_content input[name=header]").val()});
		$("#com_cobalt_toolbar ul.cobalt_menu a").hover(
		  function () {
		    $(this).css({'backgroundColor':"#"+$("#customization_content input[name=tabs_hover]").val()});
		  },
		  function () {
		    var cssObj = {
		      'background-color' : 'transparent'
		    };
		    $(this).css(cssObj);
		  }); 
		var old_tabs_hover_text = $("#com_cobalt_toolbar ul.cobalt_menu a").css('color');
		$("#com_cobalt_toolbar ul.cobalt_menu a").hover(
		  function () {
		    $(this).css({'color':"#"+$("#customization_content input[name=tabs_hover_text]").val()});
		  },
		  function () {
    	var cssObj = {
	      'color' : old_tabs_hover_text
				    };
		    $(this).css(cssObj);
		  }); 
		$(".com_cobalt_table th").css({'backgroundColor':"#"+$("#customization_content input[name=table_header_row]").val()});
		$(".com_cobalt_table th").css({'color':"#"+$("#customization_contenet input[name=table_header_text]").val()});
	});
	
	
}

// bind color input events
function bindColorInputs(ele){
	var name = $(ele).attr('name');
	$(ele).css({'backgroundColor':"#"+$(ele).val()});
	// $(ele).colorpicker({
	// 		color	: "#"+$(ele).val(),
	// 		onChange : function(rgb,hex){
	// 			updateCss(name,hex);
	// 			$(ele).val(hex);
	// 			$(ele).css({'backgroundColor':"#"+hex});
	// 		},
	// 		onSubmit : function(rgb,hex){
	// 			updateCss(name,hex);
	// 			$(ele).val(hex);
	// 			$(ele).css({'backgroundColor':"#"+hex});
	// 		}
	// 	});
}

//bind color wheel events
function bindColorWheels(ele){
	var parent_input = $(ele).prev('input:text');
	var name = $(ele).prev('input:text').attr('name');
	// $(parent_input).css({'backgroundColor':"#"+$(ele).val()});
	// 	$(ele).colorpicker({
	// 		color	: "#"+$(parent_input).val(),
	// 		onChange : function(rgb,hex){
	// 			updateCss(name,hex);
	// 			$(parent_input).val(hex);
	// 			$(parent_input).css({'backgroundColor':"#"+hex});
	// 		},
	// 		onSubmit : function(rgb,hex){
	// 			updateCss(name,hex);
	// 			$(parent_input).val(hex);
	// 			$(parent_input).css({'backgroundColor':"#"+hex});
	// 		}
	// 	});
}

/**
 * dynamically updates the css on the page
 */
function updateCss(name,hex){
	
	//change the radio button
	$('input:radio[name=id]')[1].checked = true;
	
	//update page css
	if ( name == 'header' ){
		$("#com_cobalt_toolbar").css({'backgroundColor':"#"+hex});
	}
	if ( name == 'tabs_hover' ){
		tabsHoverCss = "background:#"+hex+"!important";
		$("#com_cobalt_toolbar ul.cobalt_menu a").hover(
		  function () {
		    $(this).attr('style','background:#'+hex+" !important;"+tabsHoverTextCss);
		  },
		  function () {
		    var cssObj = {
		      'background-color' : 'transparent'
		    };
		    $(this).css(cssObj);
		  }); 
	}
	if ( name == 'tabs_hover_text' ){
		var old_tabs_hover_text = $("#com_cobalt_toolbar ul.cobalt_menu a").css('color');
		tabsHoverTextCss = "color:#"+hex+" !important";
		$("#com_cobalt_toolbar ul.cobalt_menu li a").hover(
		  function () {
		    $(this).attr('style','color:#'+hex+" !important;"+tabsHoverCss+";");
		  },
		  function () {
    	var cssObj = {
	      'color' : old_tabs_hover_text
				    };
		    $(this).css(cssObj);
		  }); 
	}
	if ( name == 'table_header_row' ){
		$(".com_cobalt_table th").css({'backgroundColor':"#"+hex});
	}
	if ( name == 'table_header_text'){
		$(".com_cobalt_table th").css({'color':"#"+hex});
	}
	if ( name == "link" ){
		$("#com_cobalt a").attr('style','color:#'+hex+' !important;');
	}
	if ( name == "link_hover" ){
		$("#user_functions a").hover(function(){
			$(this).css({'color':'#'+hex});
		},function(){
			$(this).css({'color':old_link_style});
		});
	}
}
