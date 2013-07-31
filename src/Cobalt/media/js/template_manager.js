$(document).ready(function(){
	
	//bind button to add more choices for picklists
	bindAdd();
	//bind remove value button
	bindRemove();
	
});

//bind add to picklist
function bindAdd(){
	$("#add_item").unbind();
	$("#add_item").bind('click',function(){
		addValue();
	});
}

//bind picklist areas
function bindRemove(){
	var ele = $("#items").children('.item:last');
	$('.item').each(function(index){
		$(this).find('.remove_item').unbind();
		$(this).find('.remove_item').bind('click',function(){
			removeValue($(this).parentsUntil('div').parent('div'));
		})
	});
}

//add choices to the picklist
function addValue(){
	//append template
	$("#items").append($("#item_template").html());
	//get the new entry
	bindRemove();
}

//remove entry choices
function removeValue(element){
	//remove the element
	element.remove();
}
