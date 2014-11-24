jQuery(document).ready(function(){
	
	//assign input box search for documents
	bindSearch();
	
});

function bindSearch(){
	
	jQuery("input[name=document_name_search]").autocomplete({
			source:document_names,
			dataType:'JSON',
			search:function(event,ui){
				if(jQuery(event.target).val()==''){
					showAllDocuments();
				}else{
					showDocuments(event);
				}
			},
			close:function(event,ui){
				if(jQuery(event.target).val()==''){
					showAllDocuments();
				}
			},
			select:function(event,ui){
				jQuery(event.target).val(ui.item.value);
				showDocuments(event);
			},
			change:function(event,ui){
				if(jQuery(event.target).val()==''){
					showAllDocuments();
				}
			}
     });
     
}


function showDocuments(e,ui){
	
	var search_val = new RegExp(jQuery(e.target).val(),'i');
	var i = 0;
	jQuery(documents).each(function(index,document){
		if(document.name.search(search_val) != -1){
			i++
			jQuery(".document_"+index).show('fast');
		}else{
			jQuery(".document_"+index).hide('fast');
		}
	});
	jQuery("#documents_matched").html(i);
	
}

function showAllDocuments(){
	
	var i =0;
	jQuery(documents).each(function(index,document){
		i++;
		jQuery(".document_"+index).show('fast');
	});
	jQuery("#documents_matched").html(i);
}
