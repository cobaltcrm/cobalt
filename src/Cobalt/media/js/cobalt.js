//globals
var deal_id = null;
var person_id  = null;
var note_id = null;
var association_id = null;
var association_type = null;
var idExists = null;
var calEvent = null;
var e = null;
var ev = null;
var items_checked = 0;

jQuery(document).ready(function(){

	/** initiate tooltips **/
	bindTooltips();
	bindPopovers();

	/** bind modal refresh functionalities **/
	bindModalRefresh();

	/* JS Event manager for pages */
	document.body.onclick=checkEvent;
	document.body.onkeyup=checkEvent;

	/** input box placeholder text */
	jQuery("'[placeholder]'").focus(function() {
	  var input = jQuery(this);
	  if (input.val() == input.attr("'placeholder'")) {
	    input.val("''");
	    input.removeClass("'placeholder'");
	  }
	}).blur(function() {
	  var input = jQuery(this);
	  if (input.val() == "''" || input.val() == input.attr("'placeholder'")) {
	    input.addClass("'placeholder'");
	    input.val(input.attr("'placeholder'"));
	  }
	}).blur();

	/* Minify buttons for float containers */
	jQuery('a.minify').each(function(index){
		jQuery(this).live('click',function(){minify(this);});
	});

	//make dashboard items draggable and sortable
	jQuery( ".dash_float_list" ).sortable({
		revert: true,
		update: function(event, ui) {
	
		},
		connectWith:'.dash_float_list',
		placeholder: "dash_float_placeholder",
		forcePlaceholderSize: true
	});

	//make goal areas draggable and sortable
	jQuery( ".goal_float_list" ).sortable({
		revert: true,
		update: function(event, ui) {

		},
		connectWith:'.goal_float_list',
		placeholder: "goal_float_placeholder",
		forcePlaceholderSize: true
	});

	/* Make widgets close when clicking anywhere but on the widget */
	jQuery('.ui-widget-overlay').live("click", function() {
	    //Close the dialog
	    jQuery(".ui-dialog-content").dialog("close");
	});

	/* Google map overlays */
	jQuery('.google-map').live('click',function(){
		loadGoogleMap(jQuery(this));
	});

	//bind note entry
	jQuery("#edit_note_message").live('click',function(){
		showNoteArea();
	});

	/** initiate modals **/
    jQuery('div.modal').modal({
        backdrop:false,
        keyboard:true,
        show:false
    });

	//bind conversation entry
	jQuery("#show_convo_area_button").live('click',function(){
		showConvoArea();
	});

	//bind document upload
	bindDocuments();

	/* Bind avatar upload dialogs */
	bindAvatars();

	/* Bind date pickers on page */
	bindDatepickers();

	/* Edit buttons for list items */
	// jQuery("#edit_button_link").live('click',function(){
	// 	showListEditModal();
	// });
	jQuery("#edit_button_link").mousedown(function(){
		showListEditModal();
	});

	bindListEditButtons();

	jQuery(".editable").live('click',function(){
		showEditableField(jQuery(this));
	});

	/** Note editing functions **/
	bindNoteEditHover();

	/** Convo editing functions **/
	bindConvoEditHover();

	/** Probability sliders **/
	bindProbabilitySliders();

	//make people scrollable
	jQuery("#person_details").scrollable({
		circular:false
	});

	//bind actions to checkboxes on list views
	jQuery("input[name='ids\\[\\]']").live('click',function(){
		showListEditActions(jQuery(this));
	});

	bindDropdownChildrenUpdates();

});

function toggleFullScreen() {
  if ((document.fullScreenElement && document.fullScreenElement !== null) ||    // alternative standard method
      (!document.mozFullScreenElement && !document.webkitFullScreenElement)) {  // current working methods
    if (document.documentElement.requestFullScreen) {
      document.documentElement.requestFullScreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullScreen) {
      document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.cancelFullScreen) {
      document.cancelFullScreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitCancelFullScreen) {
      document.webkitCancelFullScreen();
    }
  }
}

function bindPopovers(){
    var $ = jQuery,
        popOvers = $('[rel=popover]');

    if (popOvers.length) {
        popOvers.popover();

        $.each(popOvers, function(index, ele) {
            var base = $(ele);

            base.bind('click', function () {
                popOvers.not(base).popover('hide');
            });
        });
    }
}

function bindTooltips(){
	jQuery("[rel=tooltip]").tooltip();
}

function bindDropdownChildrenUpdates(){
	jQuery("a.dropdown-toggle").each(function(index,aLink){
		if ( jQuery(aLink).hasClass('update-toggle-text') ){
			jQuery(aLink).next("ul.dropdown-menu").children("li").each(function(index,liLink){
				jQuery(liLink).live('click',function(){
					jQuery(liLink).parent("ul.dropdown-menu").prev("a.dropdown-toggle").children('span.dropdown-label').text(jQuery(liLink).text());
				});
			});
		}
		if ( jQuery(aLink).hasClass('update-toggle-html') ){
			jQuery(aLink).next("ul.dropdown-menu").children("li").each(function(index,liLink){
				jQuery(liLink).live('click',function(){
					jQuery(liLink).parent("ul.dropdown-menu").prev("a.dropdown-toggle").html(jQuery(liLink).html());
				});
			});
		}
	});
}

function selectAll(source) {
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
	showListEditActions();
}


function bindNoteEditHover(){
	jQuery("div.note_container").hover(function(){
		jQuery(this).find('div.note_edit_functions').show();
	},function(){
		jQuery(this).find('div.note_edit_functions').hide();
	});
}

function bindConvoEditHover(){
	jQuery("div.conversation").hover(function(){
		jQuery(this).find('div.convo_edit_functions').show();
	},function(){
		jQuery(this).find('div.convo_edit_functions').hide();
	});
}


function addConvoEntry(form_id){
	
	//generate data string for ajax call
	var data = {};
	if ( form_id ){
		var $form = jQuery("#"+form_id+" :input");
	}else{
		var $form = jQuery('form[name=convo_entry] :input');
	}
	$form.each(function(){
		data[this.name] = jQuery(this).val();
	});
	
	//determine if we are editing or adding a new conversation
	if ( convoid ) {
		data["id"] = convoid;	
	}

	data["deal_id"] = deal_id;

	//make ajax call
	jQuery.ajax({
		
		type	:	"POST",
		url		:	'index.php?task=save&model=conversation&format=raw&tmpl=component',
		data	:	data,
		dataType:	'JSON',
		success	:	function(data){
	
			hideConvoArea();
			getNewConvoEntry(data.id);
			modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
			
		}
		
	});
	
}

function hideConvoArea(){
	//hide convo entry area html
	jQuery.when(jQuery("#convo").hide())
	.then(function(){
		//show green add convo button
		jQuery("#show_conversation_entry_button").removeClass('disabled');
		jQuery("#convo_message").val('');
		jQuery("#show_conversation_entry_button").show();
	})
	.then(function(){
		//unbind add convo button
		jQuery("#add_conversation_entry_button").unbind();
	});
}

//show conversation entry area
function showConvoArea(){

	//hide green add convo button
	if ( !jQuery("#convo").is(":visible") ){
		jQuery.when(jQuery("#show_conversation_entry_button").addClass('disabled'))
		
		//show convo html area
		.then(function(){
			jQuery("#show_convo_area_button").hide();
			jQuery("#convo").show();
		})
		.then(function(){
			jQuery("#convo_entry_area").show();
			jQuery("#convo_message").focus();
		});

		//bind convo entry button
		jQuery("#add_conversation_entry_button").bind('click',function(){
			addConvoEntry();
		});

		jQuery('#convo_entry_area textarea').autogrow();
	}

}


function bindProbabilitySliders(){
	jQuery('#probability_slider').removeAttr('slide').slider({
		step:5,
		min:0,
		max:100,
		value:jQuery("#probability_input_hidden").val(),
		slide:function(event,ui){
			jQuery("#probability_label").html(ui.value+"%");
			jQuery("#probability_input_hidden").val(ui.value);
		}
	});
}

function bindDatepickers(){
	jQuery('.date_input').datepicker({
			format:userDateFormat,
	});
	jQuery(".date_input").on('changeDate',function(event){
		var selectedYear = event.date.getFullYear();
		var selectedMonth = event.date.getMonth()+1;
		var selectedDay = event.date.getDate();
		var date = selectedYear+"-"+selectedMonth+"-"+selectedDay;
		jQuery("#"+jQuery(event.currentTarget).attr('id')+'_hidden').val(date);
		jQuery(this).datepicker('hide');
		if ( jQuery(this).hasClass('editable-modal-datepicker') ){
			saveEditableModal(jQuery(this).attr('id')+"_form");
		}
	});
}

function bindDocuments(){
	jQuery("#upload_input_invisible").live("change",function(){
		uploadDocument(jQuery(this));		
	});
	jQuery("#upload_button").bind('click',function(){
		// jQuery('#upload_input_invisible').trigger('click');
	});
}

function bindAvatars(){
	jQuery("#upload_input_invisible_avatar").live('change',function(){
		uploadAvatar(jQuery(this));
	});
	jQuery("img.avatar").live('click',function(){
		showAvatarUploadDialog(jQuery(this));
	});
}

function bindListEditButtons(){
	jQuery(".list_edit_button").live('mouseover',function(){
		showListEditButton(jQuery(this));
	});
	jQuery(".list_edit_button").live('mouseleave',function(){
		hideListEditButton(jQuery(this));
	});
}

	/**
	 * bind actions button
	 */
function actions(){
	//fade in area
	jQuery("#actions").fadeToggle('fast');
	jQuery("#actions").css('left',jQuery("#actions_button").position().left-40+"px");
	//TODO bind area to fade out when losing focus or other clicks
}

function create(){
	jQuery("#create").fadeToggle('fast');
	jQuery("#create").css('left',jQuery("#create_button").position().left-40+"px");
}

//function to store updated information to user tables		
function save(form){

	var valid = true;

	//Remove Default Placeholder text
	jQuery("[placeholder]").parents("form").submit(function() {
	  jQuery(this).find("[placeholder]").each(function() {
	    var input = jQuery(this);
	    if (jQuery(input).val() == jQuery(input).attr("placeholder")) {
	      jQuery(input).val("");
	    }
	  });
	});
	
	jQuery(form).find(':input').each(function(){
		var input = jQuery(this);
		if ( jQuery(input).hasClass('required') && jQuery(input).val() != "" ){
			if(jQuery(input).hasClass('required_highlight')){
				jQuery(input).removeClass('required_highlight');
			}
		}
		if ( jQuery(input).hasClass('required') && jQuery(input).val() == "" ){
			jQuery(input).addClass('required_highlight');
			jQuery(input).focus();
			valid = false;
		}

		if((jQuery(input).attr('data-minlength') > jQuery(input).val()) && jQuery(input).val()!="") {
			jQuery(input).addclass('required_highlight');
			jQuery(input).focus();
			valid = false;
		}

	});

	if ( valid ){
		return true;
	}else{
		return false;
	}
		 	
}

//function to store ajax data with models
// TODO: Rewrite this using templates, language files, and correct structure
function saveAjax(loc,model,value){
	
	var form = loc+'_form';
	
	var parts = loc.split('_');
	
	var updatedValue = parts[1];

	var field = loc+'_link';

	var validateForm = jQuery("#"+form).hasClass('validate');

	var validated = true;

	if (  validateForm ){
		var validated = save(jQuery("#"+form));
	}

	if ( validated ){

	 	//determine if we are creating a new entry or saving to the tables
	 	if ( !idExists ){
			//determine redirect
			if ( window.loc == 'company' ){
				if ( model == 'deal' ) {
					window.location = 'index.php?view=deals&layout=edit&company_id='+company_id;
				}
				if ( model == 'people' ) {
					window.location = 'index.php?view=people&layout=edit&company_id='+company_id;
				}
			}
		}

		
		//generate data string for ajax call
		var dataString = '';
		var $form = jQuery('#'+form+' :input');
		var id = null;
		var parent_id = null;
		var update_future_events = null;
		$form.each(function(){
			if ( this.type != "button" ){
				var val = ( this.type == 'checkbox' || this.type == 'radio' ) ? ( ( jQuery(this).is(':checked') ) ? 1 : 0 ) : jQuery(this).val();
				dataString += "&"+this.name+"="+val;
				//get entry id
				if ( this.name == 'id' ){
					id = jQuery(this).val();
				}
				if ( this.name == 'parent_id' ){
					parent_id = jQuery(this).val();
				}
				if ( this.name == "update_future_events" ){
					update_future_events = jQuery(this).is(':checked');
				}
			}
		});
		
		//determine if we are editing an existing entry or adding a new one
		if ( window.loc =="company" ){
			dataString += "&loc=company&company_id="+company_id;
			if ( model == 'deal' ) {
				dataString += "&id="+deal_id;
			}
			if ( model == 'people' ) {
				dataString += "&id="+person_id;
			}
		}
		
		//determine if we need to filter our results for pages
		if ( window.loc == 'calendar' ) {
			dataString += '&calendar_filter=1';
		}

		if ( window.loc == "person" ){
			dataString += "&loc=person&person_id="+person_id;
			if ( model == "people" ){
				dataString += '&id='+person_id;
			}
		}

		if ( window.loc == "deal" ){
			dataString += "&loc=deal&deal_id="+deal_id;
		}


		
		if ( idExists ){
			
			if( association_id ){
				dataString += "&association_id="+association_id+"&association_type="+association_type;
			}
			
		}

		if(value) {
			dataString +='&'+updatedValue+'='+value;
		}

		//make ajax call
		jQuery.ajax({
			type	:	"POST",
			url		:	'index.php?task=save&model='+model+'&format=raw&tmpl=component',
			data	:	dataString,
			dataType:	'json',
			success	:	function(data){

					jQuery(".modal").modal('hide');

					if ( model == "event" && ( window.loc == "dashboard" || window.loc == "deal" || window.loc == "person" || window.loc == "company" ) ){
						
						updateEvents();
						
					}
					
					//TODO: Language and HTML
					if ( model=='event' && window.loc =="events" ) {
						
						//TODO update existing information on page for existing id numbers
						updateEvents();
						
					}
					
					if ( model=="deal" && window.loc=="company" ) {
						
							updateDeals();
						}
						
					
					if ( model=="people" && window.loc=="company" ) {
						
							updatePeople();
						
					}
					
					//update the calendar page
					if ( model=="event" && window.loc=="calendar" ) {

						data.update_future_events = ( update_future_events == null ) ? true : update_future_events;
						
						// data.update_future_events = true;

						if ( id || parent_id ) { 
							//reset calendar title if changed	
							calEvent.title = data.title;
							calEvent.start = data.start;
							calEvent.end   = data.end;

							if ( id != data.id || parent_id != data.id ){

								var dataSrc = new Array();
								dataSrc.push(data);
								jQuery("#calendar").fullCalendar('removeEvents',calEvent);
								jQuery("#calendar").fullCalendar('addEventSource', dataSrc );

							}else{

								//display new results on calendar
								jQuery("#calendar").fullCalendar( 'updateEvent', calEvent );

							}
							
						}else{

							//update calendar with new information
							var dataSrc = new Array();
							dataSrc.push(data);

							jQuery("#calendar").fullCalendar('addEventSource', dataSrc );
							
						}

					}

					if ( field ){
						jQuery("#"+field).fadeOut('fast',function(){
							jQuery("#"+field).html(data[updatedValue]);	
							jQuery("#"+field).fadeIn("fast");

							//Hide Div
							jQuery("#"+field.replace('_link','')).hide();

						});
						
					}

					closeTaskEvent('task');
					closeTaskEvent('event');
					modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), capitalize(updatedValue) + ' '+Joomla.JText._('COBALT_UPDATED','updated'));
				
			}
			
		});

	}
	
}

function updateDeals(){

	if ( loc == "company" ){
		var dataString = "company_id="+company_id;
	}
	if ( loc == "person" ){
		var dataString = "person_id="+person_id;
	}

	jQuery.ajax({
		url:'index.php?view=deals&layout=deal_dock_list&tmpl=component&format=raw&tmpl=component',
		type:'POST',
		data:dataString,
		dataType:'HTML',
		success:function(data){
			if ( loc == "deals" ){
				jQuery("#deals").html(data);
			}else{
				jQuery("#deal_dock_list").html(data);
			}
		}
	});

}

function updatePeople(){

	if ( loc == "company" ){
		var dataString = "company_id="+company_id;
	}

	jQuery.ajax({
		url:base_url+'index.php?view=people&layout=people_dock_list&tmpl=component&format=raw&tmpl=component',
		type:'POST',
		data:dataString,
		dataType:'HTML',
		success:function(data){
			jQuery('#people_list').html(data);
		}
	});


}

function updateContacts(){

	if ( loc == "deal" ){
		var dataString = "deal_id="+deal_id;
	}

	dataString += "&loc="+loc;

	jQuery.ajax({
		url:base_url+'index.php?view=contacts&layout=default&tmpl=component&format=raw',
		type:'POST',
		data:dataString,
		dataType:'HTML',
		success:function(data){
			jQuery('#contacts_container').replaceWith(data);
			jQuery("#person_details").scrollable({
				circular:false,
			});
		}
	});
	
}

function addPersonToCompany(){
	jQuery.ajax({
		url:'index.php?task=addPersonToCompany&format=raw&tmpl=component',
		type:'POST',
		data:"company_id="+company_id+"&person_id="+person_id,
		dataType:'JSON',
		success:function(data){
			if ( data.success == true ){
				updatePeople();
			}
		}
	})
}

//function to store ajax data with cf tables
function saveCf(table){
	
	//redirect to new url containing information to prefill the next page
	if ( idExists ) {
		//generate dataString
		if ( table == 'people') { 
			if ( loc == 'person' || loc == 'deal' ) {
				var dataString = "person_id="+person_id+"&table="+table+"&association_id="+deal_id+"&association_type=deal&loc="+loc;
			}
		}
		if ( table == "people_cf" ){
			if ( loc == 'person' || loc == 'deal' ) {
				var dataString = "person_id="+person_id+"&table="+table+"&association_id="+deal_id+"&association_type=deal&loc="+loc;
			}	
		}

		//make ajax call
		jQuery.ajax({
			type	:	"POST",
			url		:	'index.php?task=saveCf&format=raw&tmpl=component',
			data	:	dataString,
			dataType:	'json',
			success	:	function(data){
					
						if ( table=='people' && !data.error ) {
							
							if ( loc == 'deal' ) {

								updatePeople();
								updateContacts();
							}
							
							if ( loc == 'person' ) {

								updateDeals();
							}			
						}
						
					
					modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
			}
		});
	}else{
		//determine redirect
		if ( table == 'people' ) {
			if ( loc == 'deal' ) { 
				window.location = 'index.php?view=people&layout=edit&deal_id='+deal_id;
			}
			if ( loc == 'person' ){
				window.location = 'index.php?view=deals&layout=edit&person_id='+person_id;
			}
		}
	}
	
}

//display modal message
function modalMessage(heading,message,autoclose){

	jQuery("#alertMessageHeader").html(heading);
	// jQuery("#alertMessageBody").html(message);
	jQuery("#alertMessage").animate({top:"60px",opacity:1},300);
	setTimeout(function(){
		jQuery("#alertMessage").animate({top:"0px",opacity:0},300);
	},2000);
	
}

//add persons to categories from all pages
function addPerson(urlString){
	
	//ajax search database for users
	jQuery('#ajax_search_person_dialog').modal('show');
	
	jQuery.ajax({
		type	:	'POST',
		url		:	'index.php?task=getPeople&format=raw&tmpl=component',
		dataType:	'json',
		success	:	function(data){
				
				//generate names object from received data
				var names = new Array();
				var namesInfo = new Array();
				jQuery.each(data,function(index,person){
					//gen name string for search
					var name  = '';
						name += person.first_name + " " + person.last_name;
					//gen associative object for id reference
					var infoObj = new Object();
					infoObj = { name : name, id : person.id };
					//push info to objects
					namesInfo[name] = infoObj;
					names.push( name );
				});
				//assign autocomplete to element
				jQuery('input[name=person_name]').autocomplete({
					source:names,
					select:function(event,ui){
						idExists = true;
						person_id = namesInfo[ui.item.value].id;
					},
					search:function(){
						idExists = false;
					}
				});
						
		}
	});
	//determine whether or not the person exists so we can assign to correct id or redirect to new person entry page
	idExists = false;
	
}

//add deals to categories from all pages
function addDeal(urlString){
	
	jQuery('#ajax_search_deal_dialog').modal('show');
	
	jQuery.ajax({
		type	:	'POST',
		url		:	'index.php?task=getDeals&format=raw&tmpl=component',
		dataType:	'json',
		success	:	function(data){
			
				//generate deals object from received data
				var deals = new Array();
				var dealsInfo = new Array();
				jQuery.each(data,function(index,deal){
					//gen name string for search
					var name  = deal.name;
					//gen associative object for id reference
					var infoObj = new Object();
					infoObj = { name : name, id : deal.id };
					//push info to objects
					dealsInfo[name] = infoObj;
					deals.push( name );
				});

				//assign autocomplete to element
				jQuery('input[name=deal_name]').autocomplete({
					source:deals,
					select:function(event,ui){
						idExists = true;
						deal_id = dealsInfo[ui.item.value].id;
					},
					search:function(){
						idExists = false;
					}
				});
						
		}
	});
	//determine whether or not the person exists so we can assign to correct id or redirect to new person entry page
	idExists = false;
}
	
//show note entry area
function showNoteArea(associationType,associationId){

	if ( !jQuery("#note_entry_area").is(":visible") ){

		var dataString 	= "";
		if ( typeof id !== 'undefined' ){
			dataString += "&association="+loc+"&association_id="+id;
		}
		if ( associationType != null ){
			dataString += "&association="+associationType+"&association_id="+associationid;
		}
		
		//make ajax call to populate dropdowns depending on location of page
		jQuery.ajax({
			
				type	:	'POST',
				url		:	'index.php?task=getNoteDropdowns&format=raw&tmpl=component',
				data 	: 	dataString,
				dataType:	'JSON',
				success	:	function(data){
					
					//gen deal dropdowns
					if ( loc == 'person' ) {
						var select_html = "<input type='hidden' name='deal_id' id='deal_id_hidden' /><input class='inputbox' name='deal_id_input' id='deal_id' placeholder='"+Joomla.JText._('COBALT_START_TYPING_DEAL')+"' type='text' />";
					}
					
					//gen people dropdowns
					if ( loc == 'deal' ) {
						var person_html = "<input type='hidden' name='person_id' id='person_id_hidden' /><input class='inputbox' name='person_id_input' id='person_id' placeholder='"+Joomla.JText._('COBALT_START_TYPING_PERSON')+"' type='text' />";
					}
					

					if ( loc == 'company' ) {					
						var select_html = "<input type='hidden' name='deal_id' id='deal_id_hidden' /><input class='inputbox' name='deal_id_input' id='deal_id' placeholder='"+Joomla.JText._('COBALT_START_TYPING_DEAL')+"' type='text' />";
						var person_html = "<input type='hidden' name='person_id' id='person_id_hidden' /><input class='inputbox' name='person_id_input' id='person_id' placeholder='"+Joomla.JText._('COBALT_START_TYPING_PERSON')+"' type='text' />";
					}
		
					//gen category dropdowns
					var cat_html = '<select class="inputbox" name="category_id">';
					jQuery.each(data.categories,function(index,category){
						cat_html += '<option value="'+category.id+'">'+category.name+'</option>';					
					});
					cat_html += '</select>';
					
					//generate html for note entry area
					var html = '<div class="note_dropdowns">';
					
					if(undefined != person_html) {
						html += '<div class="note_dropdown_person">'+person_html+'</div>';
					}

					if ( undefined != select_html ){
						html += '<div class="note_dropdown_deal">'+select_html+'</div>';
					}
					if ( undefined != cat_html ){
						html += '<div class="note_dropdown_cat">'+cat_html+'</div>';
					}
					html += '</div>';
					
					//hide add note button
					// jQuery.when(jQuery("#edit_note_message").hide())
					jQuery.when(jQuery("#edit_note_message").addClass('disabled'))
					
					//show convo html area
					.then(function(){
						jQuery("#deal_note").removeClass('hidden');
						jQuery("#deal_note").show();
						jQuery("#note_entry_area").show();
						jQuery("#note_details_area").append(html);
						jQuery("#note_details_area").show();
						jQuery("#note_actions_area").show();
					})
					.then(function(){
						jQuery('select').each(function() {
						});
						jQuery('#deal_note').focus();
					});
					

					//bind convo entry button
					jQuery("#add_note_entry_button").unbind();
					jQuery("#add_note_entry_button").bind('click',function(){
						addNoteEntry();
					});

					var deals = Array();
					jQuery.each(data.deals,function(index,deal){
						deals.push({ label:deal.name,value:deal.id });
					});

					var people = Array();
					jQuery.each(data.people,function(index,person){
						people.push({ label:person.first_name + " " + person.last_name,value:person.id });
					});

					//Autocompletion
					jQuery("#deal_id").autocomplete({
						source: function(request, response) {
				        var results = jQuery.ui.autocomplete.filter(deals, request.term);
				        	response(results.slice(0, 10));
				    	},
						select:function(event,ui){
							jQuery("#deal_id").val(ui.item.label);
							jQuery("#deal_id_hidden").val(ui.item.value);
							return false;
						},
						search:function(){

						},
						open: function(){
			        		jQuery(this).autocomplete('widget').css('z-index', 3000);
			        		return false;
					    }
					});

					jQuery("#person_id").autocomplete({
						source: function(request,response){
						var results = jQuery.ui.autocomplete.filter(people, request.term);
				        	response(results.slice(0, 10));
				    	},
						select:function(event,ui){
							jQuery("#person_id").val(ui.item.label);
							jQuery("#person_id_hidden").val(ui.item.value);
							return false;
						},
						search:function(){

						},
						open: function(){
			        		jQuery(this).autocomplete('widget').css('z-index', 3000);
			        		return false;
					    }
					});

				}	

		});

			jQuery('#deal_note').css('overflow', 'hidden').autogrow();
	}

}

//add note Entries
function addNoteEntry(ele){
	
	//generate data string for ajax call
	var data = {};
	if (!ele){
		var $form = jQuery('#note :input');	
	}else{
		var $form = jQuery('#'+ele+' :input');
	}
	
	$form.each(function(){
		data[this.name] = this.type=="text" ? jQuery(this).text() : jQuery(this).val();
	});
	
	//determine if we are editing an existing entry or adding a new one
	if ( note_id != null ) { 
		data["id"] =note_id;
	} 
	//determine which page we are adding a note from
	if ( loc == "company" ){
		data["company_id"] = company_id;
	}
	if ( loc == "deal" ){
		data["deal_id"] = deal_id;
	}
	if ( loc == "person" ){
		data["person_id"] = person_id;
	}

	data["model"] = "note";
	
	//make ajax call
	jQuery.ajax({
		type	:	"POST",
		url		:	'index.php?task=save&model=note&format=raw&tmpl=component',
		data	:	data,
		dataType:	'json',
		success	:	function(data){


				//determine if we are editing or adding a new conversation
				if ( !note_id ) {
					
					//add note html to notes list
					getNewNoteEntry(data.id);
									
					//reset note id
					note_id = null;
				
				}else{

					//update existing note
					getNewNoteEntry(note_id);

				}

				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
				jQuery("#add_note_entry_button").unbind();
				jQuery("#deal_note").autogrow();
				jQuery("#CobaltAjaxModal").modal('hide');
				
		}
	});

	hideNoteArea();
	
}



function getNewNoteEntry(note_id){
	jQuery.ajax({
			url:'index.php?task=getNoteEntry&format=raw&tmpl=component',
			data:'note_id='+note_id,
			dataType:'HTML',
			success:function(data){
				if ( jQuery("#note_entry_"+note_id).length ){
					jQuery('.ui-dialog-content').dialog('close');
					jQuery("#note_entry_"+note_id).replaceWith(data);
				}else{
					jQuery("#note_entries").prepend(data);
				}
				bindNoteEditHover();
			}
		});
}

function getNewConvoEntry(convo_id){
	jQuery.ajax({
			url:'index.php?task=getConvoEntry&format=raw&tmpl=component',
			data:'convo_id='+convo_id,
			dataType:'HTML',
			success:function(data){
				if ( jQuery("#convo_entry_"+convo_id).length ){
					jQuery('.ui-dialog-content').dialog('close');
					jQuery("#convo_entry_"+convo_id).replaceWith(data);
				}else{
					jQuery("#conversation_entries").prepend(data);
				}
				bindConvoEditHover();
			}
		});
}

function trashNoteEntry(note_id){
	jQuery.ajax({
		url:'index.php?task=trash&tmpl=component&format=raw',
		type:'POST',
		data:'item_type=notes&item_id='+note_id,
		dataType:'json',
		success:function(data){
			if ( data.success ){
				jQuery("#note_entry_"+note_id).remove();
				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'));
			}
		}
	});
}

function hideNoteArea(){
	//hide convo entry area html
	jQuery("#deal_note").addClass('hidden');
	jQuery("#note_entry_area").hide();
	jQuery("#edit_note_message").removeClass("disabled");
	jQuery("#note_actions_area").hide();
	jQuery.when(jQuery("#note_details_area").hide())
	.then(function(){
		jQuery("#note_details_area").empty();
		jQuery("#deal_note").hide();
		jQuery("#deal_note").val('');
	})
	.then(function(){
		//show green add convo button
		jQuery("#edit_note_message").show();
	})
	.then(function(){
		//unbind add convo button
		jQuery("#show_note_entry_button").unbind();
	});
}

//edit events
function editEvent(id,type,event){

	var parentString = "";
	if ( event != null ){
		parentString = '&parent_id='+event.parent_id;
	}else{
		event = null;
	}

	var dataString = "";

	if ( typeof loc != "undefined" && loc != 'calendar' && loc != "dashboard" ){
		dataString += "&association_type="+loc;
		switch ( loc ){
			case "company":
				dataString += "&association_id="+company_id;
			break;
			case "deal":
				dataString += "&association_id="+deal_id;			
			break;
			case "person":
				dataString += "&association_id="+person_id;
			break;
		}
	}

	jQuery.ajax({
		type	:	'POST',
		url		:	base_url+'index.php?view=events&layout=edit_'+type+'&id='+id+'&tmpl=component&format=raw'+parentString,
		data 	: 	dataString,
		success	:	function(data){

			jQuery("#CobaltAjaxModalBody").html(data);
			jQuery("#CobaltAjaxModalHeader").text(ucwords(Joomla.JText._('COBALT_EDITING_'+ucwords(type))));

			jQuery("#CobaltAjaxModalSaveButton").attr("onclick","saveAjax('edit_"+type+"','event')");
			jQuery("#CobaltAjaxModalCloseButton").attr("onclick","closeTaskEvent('"+type+"');");

			var due_input = jQuery("input[name=due_date_input]").val();
			var start_input = jQuery("input[name=start_time_input]").val();
			var end_input = jQuery("input[name=end_time_input]").val();
			var end_date = jQuery("input[name=end_date_input]").val();

			jQuery("input[name=update_future_events]").bind('click',function(){
				if ( !jQuery(this).is(":checked") ){
					var mysql_format = "yyyy-MM-dd hh:mm:ss";
					switch ( event.type ){
						case "task":
							var input = jQuery.datepicker.formatDate(userDateFormat, event._start);
							jQuery("input[name=due_date_input]").val(input);
						break;
						case "event":
							var input = jQuery.datepicker.formatDate(userDateFormat, event._start);
							jQuery("input[name=start_time_input]").val(input);
							jQuery("input[name=end_time_input]").val(input);
						break;
					}
				}else{
					switch ( event.type ){
						case "task":
							jQuery("input[name=due_date_input]").val(due_input);
						break;
						case "event":
							jQuery("input[name=start_time_input]").val(start_input);
							jQuery("input[name=end_time_input]").val(end_input);
						break;
					}
				}
			});

			jQuery("input[name=end_date_input]").val(end_date);
			
			if ( type == 'task' ) { 

				 jQuery('span.due_date').bind('click',function(){
				 	
				 	//hide span message
				 	jQuery.when(jQuery("span.due_date").hide())
				 	//show input fields
				 	.then(function(){jQuery('#due_date').show();});
				 	
				 });
				 
			}

			jQuery('span.end_date').bind('click',function(){
			 	//hide span message
			 	jQuery.when(jQuery("span.end_date").hide())
			 	//show input fields
			 	.then(function(){jQuery('#end_date').show();});
			 });

			bindDatepickers();
			
			//prefill our dates

			var start_time = null;
			var end_time = null;
			var due_date = null;

			if ( loc == "calendar" ){

				if ( typeof event.due_date === "object" ){
					var due_date = jQuery.datepicker.formatDate(userDateFormat, event.due_date);
				}else{
					var due_date = event.due_date;
				}



				if ( typeof event.start_time === "object" ){
					var year = event.start.getFullYear();	
					var month = event.start.getMonth()+1;
					var day = event.start.getDate();
					var hour = event.start.getHours();
					var minute = event.start.getMinutes();
					var seconds = "00";

					month = ( month > 9 ) ? month : "0"+month;
					day = ( day > 9 ) ? day : "0"+day;
					hour = ( hour > 9 ) ? hour : "0"+hour;
					minute = ( minute > 9 ) ? minute : "0"+minute;

					var start_time = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
				}else{
					var start_time = event.start_time;
				}

				if ( typeof event.end_time === "object" ){
					if ( event.end == null ) event.end = event.start;
					var year = event.end.getFullYear();	
					var month = event.end.getMonth()+1;
					var day = event.end.getDate();
					var hour = event.end.getHours();
					var minute = event.end.getMinutes();
					var seconds = "00";

					month = ( month > 9 ) ? month : "0"+month;
					day = ( day > 9 ) ? day : "0"+day;
					hour = ( hour > 9 ) ? hour : "0"+hour;
					minute = ( minute > 9 ) ? minute : "0"+minute;

					var end_time = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
				}else{
					var end_time = event.end_time;
				}


					jQuery("input[name=start_time]").val(start_time);
					jQuery("input[name=end_time]").val(end_time);
					jQuery("input[name=due_date]").val(due_date);

				}
				
				//open dialog
				jQuery('#edit_'+type).dialog('open');
				
				//assign autocomplete and ajax search functionalities to input fields
		 			//bind association input area
				jQuery("span.associate_to").bind('click',function(){
					
					//get innerheight for smooth page transitions
				 	// var height = jQuery("#associate_to_container").innerHeight();
				 	//set height
				 	// jQuery("#associate_to_container").height(height-1);
				 	
					jQuery.when(jQuery("span.associate_to").hide())
				 	//show input fields
				 	.then(function(){
			 			jQuery('#associate_to').show();
			 			jQuery('#associate_to').focus();
			 		});
			 		
				});

			
			//assign autocomplete and ajax search functionalities to input fields
		 			jQuery.ajax({
						type	:	'POST',
						url		:	'index.php?task=getTaskAssociations&format=raw&tmpl=component',
						dataType:	'json',
						success	:	function(data){
								
								//generate names object from received data
								var names = new Array();
								var namesInfo = new Array();
								jQuery.each(data,function(index,entry){
									//gen name string for search
									if ( entry.type == "person" ) {
										var name  = '';
											name += entry.first_name + " " + entry.last_name;
									} else {
										name = entry.name;
									}
									//gen associative object for id reference
									var infoObj = new Object();
									infoObj = { name : name, id : entry.id, type : entry.type};
									//push info to objects
									namesInfo[name] = infoObj;
									names.push( name );
								});
								//assign autocomplete to element
								jQuery('input[name=associate_name]').autocomplete({
									source:names,
									select:function(event,ui){
										idExists = true;
										association_id = namesInfo[ui.item.value].id;
										association_type = namesInfo[ui.item.value].type;
									},
									search:function(){
										idExists = false;
									}
								});
								
						}
					});
		}
	});

	jQuery("#CobaltAjaxModal").modal('show');

}

//close dialogs
function closeDialog(type){
	
	jQuery.fx.speeds._default = 1000;
	jQuery("#ajax_search_"+type+"_dialog").modal('hide');
	
}

//listen to JS events
function checkEvent(ev){
	ev = ev || window.event;
	var obj = ev.target || ev.srcElement;
	e = obj;

		//here we hide filter dropdowns if user clicks out of the area
		if ( !jQuery(e).parent('span').hasClass('parent') && !jQuery(e).parentsUntil('span').parent('span').hasClass('parent') && jQuery(e).parent('a').attr('id') != current_area && jQuery(e).attr('id') != current_area && current_area != null && jQuery("#"+current_area).is(":visible") && jQuery(e).attr('id')!='column_filter'){
				if (jQuery("#"+current_area).hasClass('editable') ){
					jQuery("#"+current_area).children('div.editable_info').fadeOut('fast');	
					current_area = null;
				}else{
					hideDropdown(current_area);
				}
		}

		

		if ( jQuery(e).attr('id') != "upload_input_invisible_avatar" && !jQuery(e).hasClass('avatar') && jQuery(e).parent("#avatar_upload_dialog").length != 1 && jQuery(e).parentsUntil("#avatar_upload_dialog").parent("#avatar_uload_dialog").length != 1 && jQuery("#avatar_upload_dialog").is(":visible") ){
			jQuery("#avatar_upload_dialog").hide();
			jQuery("#avatar_upload_dialog").appendTo("#templates");
		}
		
		//edit menu
		if ( jQuery("#edit_menu").is(":visible") && jQuery(e).attr('id') != null ){ jQuery("#edit_menu").fadeOut("fast"); }
		
		//create buttons
		if ( jQuery("#create").is(":visible") && jQuery(e).attr('id') != 'create_button' ){ create(); }
		if ( jQuery(e).attr('id') == 'create_button' ){ create(); }
		
		//here we add in functionality for 'actions' on pages
		if ( jQuery("#actions").is(":visible") && jQuery(e).attr('id') != 'actions_button' ){ actions(); }
		if ( jQuery(e).attr('id') == 'actions_button' ){ actions(); }
		
		//document edit buttons
		if ( jQuery(e).hasClass('document_edit')){
				if(jQuery('div.document_edit_menu').is(':visible')) 
				jQuery("div.document_edit_menu").fadeOut("fast"); 
				
				documentEdit(); 
			}
		if ( jQuery('div.document_edit_menu').is(':visible') && jQuery(e).attr('class') != 'document_edit' ){ jQuery('div.document_edit_menu').fadeOut('fast'); }
		
		if( jQuery(e).attr('class') == 'document_download') { documentDownload(); }
		if( jQuery(e).attr('class') == 'document_delete') { documentDelete(); }
		if( jQuery(e).attr('class') == "document_preview" ){ documentPreview(); }

		if ( jQuery(e).parentsUntil('form#note').parent("form#note").length != 1 && jQuery(e).parent("form#note").length != 1 && jQuery(e).attr('id') != "edit_note_message" ){
			hideNoteArea();
		}

		if ( jQuery(e).parentsUntil('form[name=convo_entry]').parent("form[name=convo_entry]").length != 1 && jQuery(e).parent("form[name=convo_entry]").length != 1 && jQuery(e).attr('id') != "show_conversation_entry_button" ){
			hideConvoArea();
		}

}


//minify areas
function minify(obj){
	
	//grab container
	var container = jQuery(obj).parent('div.dash_float_header').next('div');

	//hide container
	jQuery.when(container.slideToggle('fast'))
	//change minify icon
	.then(function(){
		if ( container.css('display') == 'none' ) {
			jQuery(obj).css('background',"url('"+base_url+"/media/images/right_arrow.png') no-repeat center center");
		} else {
			jQuery(obj).css('background',"url('"+base_url+"/media/images/down_arrow.png') no-repeat center center");
		}
	});
	
	
	
}

//TODO: Write all modals to use the same div
//open a task event modal dialog
function openNoteModal(id,type){
	
	jQuery.ajax({
		type	:	'POST',
		url		:	'index.php?view=note&type='+type+'&id='+id+'&format=raw&tmpl=component',
		success	:	function(data){
			
			//clear past html
			jQuery("#edit_task").empty();
			jQuery("#edit_event").empty();
			
			jQuery("#CobaltAjaxModalBody").html(data);
			jQuery("#CobaltAjaxModalHeader").text(Joomla.JText._("COBALT_EDIT_NOTES"));
			//var heading = Joomla.JText._('COBALT_ADD_NEW_NOTE','Add New Note');

			//bind note entry
			jQuery("#show_note_area_button").bind('click',function(){
				showNoteArea(type,id);
			});
			
			//display areas that could possible faded out from other event entries
			jQuery("span.associate_to").css("display",'block');
			jQuery('#associate_to').css('display','none');
			
			//bind association input area
			jQuery("span.associate_to").bind('click',function(){
			 	
				jQuery.when(jQuery("span.associate_to").fadeOut('fast'))
			 	//show input fields
			 	.then(function(){
		 			jQuery('#associate_to').fadeIn('fast');
		 			jQuery('#associate_to').focus();
		 		})
		 		.then(function(){
		 			
		 			//assign autocomplete and ajax search functionalities to input fields
		 			jQuery.ajax({
						type	:	'POST',
						url		:	'index.php?task=getTaskAssociations&format=raw&tmpl=component',
						dataType:	'json',
						success	:	function(data){
								
								//generate names object from received data
								var names = new Array();
								var namesInfo = new Array();
								jQuery.each(data,function(index,entry){
									//gen name string for search
									if ( entry.type == "person" ) {
										var name  = '';
											name += entry.first_name + " " + entry.last_name;
									} else {
										name = entry.name;
									}
									//gen associative object for id reference
									var infoObj = new Object();
									infoObj = { name : name, id : entry.id, type : entry.type};
									//push info to objects
									namesInfo[name] = infoObj;
									names.push( name );
								});
								//assign autocomplete to element
								jQuery('input[name=associate_name]').autocomplete({
									source:names,
									select:function(event,ui){
										idExists = true;
										association_id = namesInfo[ui.item.value].id;
										association_type = namesInfo[ui.item.value].type;
									},
									search:function(){
										idExists = false;
									}
								});
								
						}
					});
		 		});
		 		
			});
			
          	jQuery("#CobaltAjaxModal").modal('show');
			
			if ( type == 'task' ) { 
				 
				 //bind due date fields
				 jQuery('span.due_date').bind('click',function(){
				 	
				 	//hide span message
				 	jQuery.when(jQuery("span.due_date").fadeOut('fast'))
				 	//show input fields
				 	.then(function(){jQuery('#due_date').fadeIn('fast')});
				 	
				 	//assign date picker to field
				 	jQuery('input[name=due_date]').datepicker({
						dateFormat:'yy-mm-dd',
						onClose:function(data){
							//if the user doesnt set the date then hide the picker
							if ( jQuery("input[name=due_date]").val() == '' ){
								jQuery.when(jQuery("#due_date").fadeOut('fast'))
								.then(function(){jQuery("span.due_date").fadeIn('fast');});
							}
						}
					});
				 });
				 
			}
			
		}
	});
}

/*
//close a task event modal dialog
function closeNoteModal(type){
	jQuery.fx.speeds._default = 1000;
	jQuery("#note_modal").dialog('close');
}
*/

/** get Events **/
function updateEvents(){

	var dataString = "";

	if ( typeof layout !== 'undefined' && ( layout == 'default' || layout == 'list' ) ){
		var list_layout = "list";
		var container = "events";
		dataString = "";
	}else{
		var list_layout = "event_listings";
		var container = "task_list";
		if ( typeof loc != "undefined" && loc != 'calendar' && loc != "dashboard" ){
		dataString += "&association_type="+loc;
		switch ( loc ){
			case "company":
				dataString += "&association_id="+company_id;
			break;
			case "deal":
				dataString += "&association_id="+deal_id;			
			break;
			case "person":
				dataString += "&association_id="+person_id;
			break;
		}
	}
	}

	//make ajax call
	jQuery.ajax({
		type	:	"POST",
		url 	: 	'index.php?view=events&layout='+list_layout+'&format=raw&tmpl=component',
		data 	: 	dataString,
		dataType:	'html',
		success	:	function(data){
					modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'),Joomla.JText._('COBALT_EVENTS_SUCCESSFULLY_UPDATED'));
					jQuery("#"+container).html(data);
		}});
}

/** Mark events complete **/
function markEventComplete(element){

	var dataString = "";
	var form = jQuery("#"+jQuery(element).parentsUntil('div').parent('div').attr('id').replace('_menu','_form')+" :input");
	jQuery(form).each(function(){
		if ( this.type != "button" ){
			var val = ( this.type == 'checkbox' || this.type == 'radio' ) ? ( ( jQuery(this).is(':checked') ) ? 1 : 0 ) : jQuery(this).val();
			dataString += "&"+this.name+"="+val;
		}
	});

	jQuery.ajax({
			url:'index.php?task=markEventComplete&format=raw&tmpl=component',
			type:'post',
			data:dataString,
			dataType:'JSON',
			success:function(data){
				updateEvents();
				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'),Joomla.JText._('COBALT_EVENT_MARKED_COMPLETE'));
			}
	});
}

/** Mark events incomplete **/
function markEventIncomplete(element){

	var dataString = "";
	var form = jQuery("#"+jQuery(element).parentsUntil('div').parent('div').attr('id').replace('_menu','_form')+" :input");
	jQuery(form).each(function(){
		if ( this.type != "button" ){
			var val = ( this.type == 'checkbox' || this.type == 'radio' ) ? ( ( jQuery(this).is(':checked') ) ? 1 : 0 ) : jQuery(this).val();
			dataString += "&"+this.name+"="+val;
		}
	});

	jQuery.ajax({
			url:'index.php?task=markEventIncomplete&format=raw&tmpl=component',
			type:'post',
			data:dataString,
			dataType:'json',
			success:function(data){
				updateEvents();
				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'),Joomla.JText._('COBALT_EVENT_MARKED_INCOMPLETE'));
			}
	});
}



/** Postpone events **/
function postponeEvent(element,days){

	var dataString = "";
	var form = jQuery("#"+jQuery(element).parentsUntil('div').parent('div').attr('id').replace('_menu','_form')+" :input");
	jQuery(form).each(function(){
		if ( this.type != "button" ){
			var val = ( this.type == 'checkbox' || this.type == 'radio' ) ? ( ( jQuery(this).is(':checked') ) ? 1 : 0 ) : jQuery(this).val();
			dataString += "&"+this.name+"="+val;
		}
	});

	dataString += "&days="+days;

	jQuery.ajax({
			url:'index.php?task=postponeEvent&format=raw&tmpl=component',
			type:'post',
			data:dataString,
			dataType:'json',
			success:function(data){
				updateEvents();
				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'),Joomla.JText._('COBALT_EVENT_HAS_BEEN_POSTPONED') + days + " " + Joomla.JText._('COBALT_DAYS'));

			}
	});

}

//delete events
function deleteEvent(element){

	var event_id = "";
	var dataString = "";
	var form = jQuery("#"+jQuery(element).parentsUntil('div').parent('div').attr('id').replace('_menu','_form')+" :input");
	jQuery(form).each(function(){
		if ( this.type != "button" ){
			var val = ( this.type == 'checkbox' || this.type == 'radio' ) ? ( ( jQuery(this).is(':checked') ) ? 1 : 0 ) : jQuery(this).val();
			dataString += "&"+this.name+"="+val;
		}
		
		if(this.name=="event_id") {
			event_id = this.value;
		}
	});

	jQuery.ajax({
		url: base_url+"index.php?task=removeEvent&tmpl=component&format=raw",
		type:"post",
		data:dataString,
		dataType:'json',
		success:function(data){
			jQuery('#com_cobalt_listing_'+event_id).fadeOut('fast',function(){
				jQuery('#com_cobalt_listing_'+event_id).remove();
			});
			// var menu = jQuery(element).parentsUntil('div').parent('div');
			// jQuery(menu).fadeOut('fast',function(){
			// 	jQuery(menu).remove();
			// });
			modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'),Joomla.JText._('COBALT_SUCCESSFULLY_REMOVED_EVENT'));
		}
	});

}

//open a task event modal dialog
function addTaskEvent(type){
	
	//reset globals
	association_id = null;
	association_type = null;
	idExists = false;

	var dataString = "";

	if ( typeof loc !== "undefined" && loc != 'calendar' && loc != "dashboard" ){
		dataString += "&association_type="+loc;
		switch ( loc ){
			case "company":
				dataString += "&association_id="+company_id;
			break;
			case "deal":
				dataString += "&association_id="+deal_id;			
			break;
			case "person":
				dataString += "&association_id="+person_id;
			break;
		}
	}
	
	jQuery.ajax({
		type	:	'POST',
		url		:	base_url+'index.php?view=events&layout=edit_'+type+'&tmpl=component&format=raw',
		data 	: 	dataString,
		success	:	function(data){
			
			//clear past html
			jQuery("#edit_task").empty();
			jQuery("#edit_event").empty();
			
			//assign new html
			jQuery("#CobaltAjaxModalBody").html(data);
			jQuery("#CobaltAjaxModalHeader").text(ucwords(Joomla.JText._("COBALT_ADDING_"+ucwords(type))));
			jQuery("#CobaltAjaxModalSaveButton").attr("onclick","saveAjax('edit_"+type+"','event')");
			jQuery("#CobaltAjaxModalCloseButton").attr("onclick","closeTaskEvent('"+type+"');");
			
			//display areas that could possible faded out from other event entries
			jQuery("span.associate_to").css("display",'block');
			jQuery('#associate_to').css('display','none');
			
			//bind association input area
			jQuery("span.associate_to").bind('click',function(){
			 	
				jQuery.when(jQuery("span.associate_to").hide())
			 	//show input fields
			 	.then(function(){
		 			jQuery('#associate_to').show();
		 			jQuery('#associate_to input').focus();
		 		})
		 		.then(function(){
		 			
		 			//assign autocomplete and ajax search functionalities to input fields
		 			jQuery.ajax({
						type	:	'POST',
						url		:	'index.php?task=getTaskAssociations&format=raw&tmpl=component',
						dataType:	'json',
						success	:	function(data){
								
								//generate names object from received data
								var names = new Array();
								var namesInfo = new Array();
								jQuery.each(data,function(index,entry){
									//gen name string for search
									if ( entry.type == "person" ) {
										var name  = '';
											name += entry.first_name + " " + entry.last_name;
									} else {
										name = entry.name;
									}
									//gen associative object for id reference
									var infoObj = new Object();
									infoObj = { name : name, id : entry.id, type : entry.type};
									//push info to objects
									namesInfo[name] = infoObj;
									names.push( name );
								});
								//assign autocomplete to element
								jQuery('input[name=associate_name]').autocomplete({
									source:names,
									select:function(event,ui){
										idExists = true;
										association_id = namesInfo[ui.item.value].id;
										association_type = namesInfo[ui.item.value].type;
									},
									search:function(){
										idExists = false;
									}
								});
								
						}
					});
		 		});
		 		
			});
			
			jQuery("#CobaltAjaxModal").modal('show');

			bindDatepickers();
			
			if ( type == 'event' ) { 

				
				//prefill date input boxes
				if ( typeof new_event_date !== 'undefined' ){
					jQuery("input[name=start_time_input]").val(jQuery.datepicker.formatDate(userDateFormat, new_event_date));
					jQuery("input[name=end_time_input]").val(jQuery.datepicker.formatDate(userDateFormat, new_event_date));
					var d = new Date();
				    var curr_date = new_event_date.getDate();
				    var curr_month = new_event_date.getMonth() + 1; //Months are zero based
				    var curr_year = new_event_date.getFullYear();
				    var date = curr_year + "-" + curr_month + "-" + curr_date;
				    jQuery("#start_time_hidden").val(date);
				    jQuery("#end_time_hidden").val(date);
				}
				
			}

			//bind end date fields
			 jQuery('span.end_date').bind('click',function(){
			 	//hide span message
			 	jQuery.when(jQuery("span.end_date").hide())
			 	//show input fields
			 	.then(function(){jQuery('#end_date').show()});
			 });
			
			if ( type == 'task' ) { 

				jQuery('input[name=due_date_input]').datepicker({
					onClose:function(data){
						//if the user doesnt set the date then hide the picker
						if ( jQuery("input[name=due_date_input]").val() == '' ){
							jQuery.when(jQuery("#due_date").hide())
							.then(function(){jQuery("span.due_date").show();});
						}
					}
				});
				 
				 //bind due date fields
				 jQuery('span.due_date').bind('click',function(){
				 	
				 	//hide span message
				 	jQuery.when(jQuery("span.due_date").hide())
				 	//show input fields
				 	.then(function(){jQuery('#due_date').show()});
				 	
				 	//assign date picker to field
				 	jQuery('input[name=due_date_input]').datepicker({
						onClose:function(data){
							//if the user doesnt set the date then hide the picker
							if ( jQuery("input[name=due_date_input]").val() == '' ){
								jQuery.when(jQuery("#due_date").hide())
								.then(function(){jQuery("span.due_date").show();});
							}
						}
					});
				 });
				 
				 //prefill due date box if set
					if ( typeof new_event_date !== 'undefined' ) {
						jQuery.when(jQuery("span.due_date").hide())
				 		//show input fields
				 		.then(function(){jQuery('#due_date').show()})
				 		.then(function(){
							jQuery("input[name=due_date_input]").val(jQuery.datepicker.formatDate(userDateFormat, new_event_date));
						    var curr_date = new_event_date.getDate();
						    var curr_month = new_event_date.getMonth() + 1; //Months are zero based
						    var curr_year = new_event_date.getFullYear();
						    var date = curr_year + "-" + curr_month + "-" + curr_date;
						    jQuery("#due_date_input_hidden").val(date);
						});
					}
				 
			}
			
		}
	});
}

//close a task event modal dialog
function closeTaskEvent(type){
	
	jQuery.fx.speeds._default = 1000;
	jQuery("#edit_"+type).dialog('close');
	
}

//upload documents
function uploadDocument(element){

	modalMessage(Joomla.JText._('COBALT_UPLOADING'),Joomla.JText._('COBALT_YOUR_DOCUMENT_IS_BEING_UPLOADED'),false);

	// var html  = '<div id="appends"><input type="hidden" name="association_id" value="'+id+'" />';
	// 	html += '<input type="hidden" name="association_type" value="'+association_type+'" /></div>';

	// jQuery("#upload_form").append(html);
	jQuery.when(jQuery("#upload_form").submit())
	.then(function(){jQuery("#appends").remove();});
	
}	

//upload success
function uploadSuccess(document_id){
	jQuery('#message').dialog('close');
	jQuery.ajax({
		url:'index.php?view=documents&layout=document_row&tmpl=component&format=raw',
		data:'document_id='+document_id,
		type:'post',
		dataType:'html',
		success:function(data){
			// jQuery("#documents_table").prepend(edit);
			jQuery(".fileupload").addClass('fileupload-new').removeClass("fileupload-exists");
			jQuery("#documents").prepend(data);
			jQuery("#documents tr:first td").effect("highlight",2000);
			modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
		}
	})
}

//edit documents
function documentEdit(){
	var id = jQuery(e).attr('id');
	jQuery("#document_"+id).fadeIn("fast");
	jQuery("#document_"+id).css('left',jQuery(e).position().left-40+"px");
	jQuery("#document_"+id).css('top',jQuery(e).position().top-40+"px");
}

//download document
function documentDownload(){
	var search = '_';
	var ind = jQuery(e).attr('id').indexOf(search);
	var id = jQuery(e).attr('id').substr(ind+1);
	window.open(base_url+'index.php?view=documents&layout=download&document='+jQuery("#document_"+id+"_hash").val());
}

function documentPreview(){
	var search = '_';
	var ind = jQuery(e).attr('id').indexOf(search);
	var id = jQuery(e).attr('id').substr(ind+1);
	jQuery("#CobaltAjaxModalPreviewHeader").html(jQuery(e).parentsUntil('div.dropdown').parent("div.dropdown").children('a').text());
	jQuery("#CobaltAjaxModalPreviewBody").html("<img style='max-width:400px;' src ='index.php?task=previewDocument&format=raw&tmpl=component&document="+jQuery("#document_"+id+"_hash").val()+"' />");
	jQuery("#CobaltAjaxModalPreview").modal('show');
}

//delete document
function documentDelete(){
	var search = '_';
	var ind = jQuery(e).attr('id').indexOf(search);
	var id = jQuery(e).attr('id').substr(ind+1);
	//make ajax call
	jQuery.ajax({
		
		type	:	'post',
		url		:	'index.php?task=trash&format=raw&tmpl=component',
		data	:	'item_type=documents&item_id='+id,
		dataType:	'json',
		success	:	function(data){
			if ( !data.error ){
				jQuery.when(jQuery("#document_row_"+id).fadeOut("slow"))
				.then(function(){
					jQuery("#document_row_"+id).remove();
				});
				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
			}else{
				//TODO error handling
			}
		}
		
	});
}

/**
 * Run items through the template system
 * @param  {[type]} template_id [description]
 * @return {[type]}             [description]
 */
function createTemplate(template_id){

	var association_type = loc;
	var association_id = 0;

	switch ( association_type ){
		case "company":
			association_id = company_id;
		break;
		case "deal":
			association_id = deal_id;
		break;
		case "person":
			association_id = person_id;
		break;
	}

	if ( association_id > 0 ){
		
			var dataString = "template_id="+template_id+"&association_id="+association_id+"&association_type="+association_type;

			jQuery.ajax({
				url:'index.php?task=createTemplate&tmpl=component&format=raw',
				type:'post',
				data:dataString,
				dataType:'JSON',
				success:function(data){
					if ( data.success ){
						updateEvents();
					}else{
						modalMessage(Joomla.JText._('COBALT_ERROR','Error'),Joomla.JText._('COBALT_ERROR_PROCESSING_TEMPLATE','There was an error processing this template, please try again'));
					}
				}
			});

	}

}


/**
 * Export CSV files
 * @return {[type]} [description]
 */
function exportCsv(){

	var old_action = jQuery("#list_form").attr('action');
	var old_layout = jQuery("#list_form_layout").val();

	jQuery("#list_form").attr('action','index.php?task=downloadCsv&tmpl=component&format=raw');
	jQuery("#list_form_layout").val('custom_report');
	jQuery("#list_form").append('<input type="hidden" id="export_flag" name="export" value="1" />');
	jQuery("#list_form").submit();
	jQuery("#export_flag").remove();
	jQuery("#list_form").attr('action',old_action);
	jQuery("#list_form_layout").val(old_layout);

}

/**
 * Export Vcard Files
 * @return {[type]} [description]
 */
function exportVcard(){

	var old_action = jQuery("#list_form").attr('action');

	jQuery("#vcard_form").attr('action','index.php?task=downloadVcard&tmpl=component&format=raw');
	jQuery("#vcard_form").submit();
	jQuery("#vcard_form").attr('action',old_action);

}

function downloadImportTemplate(){

	var old_action = jQuery("#download_import_template").attr('action');

	jQuery("#download_import_template").attr('action','index.php?task=downloadImportTemplate&tmpl=component&format=raw');
	jQuery("#download_import_template").submit();
	jQuery("#download_import_template").attr('action',old_action);

}

function showListEditButton(ele){
	jQuery.when(jQuery("#edit_button").appendTo(jQuery(ele).children('div.title_holder')))
	.then(function(){
		jQuery("#edit_button_link").attr('data-list-id',jQuery(ele).attr('id'));
	}).then(function(){
		jQuery("#edit_button").show();
	});
}

function hideListEditButton(ele){
	jQuery("#edit_button").hide();
	jQuery("#edit_button").appendTo(jQuery("#templates"));
}

function showListEditModal(){
	var id = jQuery("#edit_button_link").attr('data-list-id').replace('list_','');
	jQuery.ajax({
		url:'index.php?view='+loc+'&layout=edit&tmpl=component&format=raw',
		type:'POST',
		data:'id='+id,
		dataType:'html',
		success:function(data){
			jQuery("#edit_list_modal").html(data);
			jQuery("#edit_list_modal").dialog({
				dialogClass:'com_cobalt',
				width:600,
				title:loc.toUpperCase()+' '+Joomla.JText._('COBALT_EDIT'),
				modal:true,
				resizable: false
			});
			bindDatepickers();
			bindProbabilitySliders();
		}
	});
}


function saveListItem(list_id){

	var model = "";

	switch ( loc ){
		case "deals":
			model = "deal";
		break;
		case "people":
			model = "people";
		break;
		case "companies":
			model =	"company";
		break;
	}


	var form = "edit_form";

	
	//generate data string for ajax call
	var dataString = '';
	var $form = jQuery('#'+form+' :input');
	$form.each(function(){
		if ( this.type != "button" ){
			var val = ( this.type == 'checkbox' || this.type == 'radio' ) ? ( ( jQuery(this).is(':checked') ) ? 1 : 0 ) : jQuery(this).val();
			dataString += "&"+this.name+"="+val;
		}
	});

	//make ajax call
	jQuery.ajax({
		type	:	"POST",
		url		:	'index.php?task=save&model='+model+'&format=raw&tmpl=component',
		data	:	dataString,
		dataType:	'json',
		success	:	function(data){

			jQuery.ajax({
				url:'index.php?view='+loc+'&layout=list&tmpl=component&format=raw',
				type:'POST',
				data:'id='+list_id,
				dataType:'html',
				success:function(data){
					jQuery("#edit_button").appendTo(jQuery('#templates'));
					jQuery("#list_row_"+list_id).replaceWith(data);
					jQuery('.ui-dialog-content').dialog('close');
					bindListEditButtons();
					modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'),Joomla.JText._('COBALT_ITEM_SUCCESSFULLY_UPDATED'));
				}
			});

		}});

}

function showDealContactsDialogModal(deal_id){
	jQuery.ajax({
		url:'index.php?view=contacts&format=raw&tmpl=component&deal_id='+deal_id,
		type:'GET',
		dataType:'html',
		success:function(data){
			jQuery("#CobaltAjaxModalBody").html(data);
			jQuery("#CobaltAjaxModalHeader").text(ucwords(Joomla.JText._("COBALT_CONTACTS")));
			jQuery("#CobaltAjaxModal").modal('show');
		}
	});
}

function showCompanyContactsDialogModal(company_id){
	jQuery.ajax({
		url:'index.php?view=contacts&format=raw&tmpl=component&company_id='+company_id,
		type:'GET',
		dataType:'html',
		success:function(data){
			jQuery("#CobaltAjaxModalBody").html(data);
			jQuery("#CobaltAjaxModalHeader").text(ucwords(Joomla.JText._("COBALT_CONTACTS")));
			jQuery("#CobaltAjaxModal").modal('show');
		}
	});
}

function showEventContactsDialogModal(event_id){
	jQuery.ajax({
		url:'index.php?view=contacts&format=raw&tmpl=component&event_id='+event_id,
		type:'GET',
		dataType:'html',
		success:function(data){
			jQuery("#CobaltAjaxModalBody").html(data);
			jQuery("#CobaltAjaxModal").modal('show');
		}
	});
}


function showAvatarUploadDialog(ele){
	jQuery("#avatar_upload_dialog").insertAfter(jQuery(ele)).show();
	jQuery("#avatar_upload_form").attr('data-item-id',jQuery(ele).attr('data-item-id'));
	jQuery("#avatar_upload_form").attr('data-item-type',jQuery(ele).attr('data-item-type'));
}

function hideAvatarUploadDialog(){
	jQuery("#avatar_upload_dialog").fadeOut('fast');
}


function uploadAvatar(ele){

		var item_id = jQuery("#avatar_upload_form").attr('data-item-id');
		var item_type = jQuery("#avatar_upload_form").attr('data-item-type');

		var avatar_url = 'index.php?task=uploadAvatar&format=raw&tmpl=component';

		var options = "&item_id="+item_id+'&item_type='+item_type;

      	jQuery("#avatar_upload_form").attr('action',avatar_url + options).submit();

      	hideAvatarUploadDialog();

}

function updateAvatar(item_id,new_avatar){

	jQuery("#avatar_img_"+item_id).attr('src',new_avatar);

}


function showEditableField(ele){

	if(current_area!=jQuery(ele).attr('id'))
	{
		hideDropdown(current_area);
	}
	current_area = jQuery(ele).attr('id');
	jQuery(ele).children('div.editable_info').show();
	jQuery(ele).children('div.editable_info').find('.inputbox').focus();

}

function saveEditableModal(form_id){

	var formExists = jQuery("#"+form_id).length > 0;
	var form = formExists ? jQuery("#"+form_id+" :input") : jQuery(form_id).parent('form').find(":input");

	var dataString = "";

	var item_id = id;
	var model = "";

	jQuery(form).each(function(){
		if ( this.type != "button" ){
			var val = ( this.type == 'checkbox' || this.type == 'radio' ) ? ( ( jQuery(this).is(':checked') ) ? 1 : 0 ) : jQuery(this).val();
			dataString += "&"+this.name+"="+val;
			if ( this.name == "item_id" ){
				item_id = jQuery(this).val();
			}
			if ( this.name == "item_type" ){
				model = jQuery(this).val();
			}
		}
	});

	if ( model == "" ){
		switch ( loc ){
			case "person":
				model = "people";
			break;
			case "deal":
				model = "deal";
			break;
			case "company":
				model = "company";
			break;
		}
		dataString += "&item_id="+item_id
		dataString += "&item_type="+model;
	}

	jQuery.ajax({
		url:'index.php?task=saveAjax&format=raw&tmpl=component',
		type:'POST',
		data:dataString,
		dataType:'JSON',
		success:function(data){
			jQuery(form).each(function(){
				if ( this.type != "button" ){
					var val = ( this.type == 'checkbox' || this.type == 'radio' ) ? ( ( jQuery(this).is(':checked') ) ? 1 : 0 ) : jQuery(this).val();
						val = ( val.replace(/ /g,"").length > 0 ) ? val : Joomla.JText._('COBALT_CLICK_TO_EDIT');
						val = nl2br(val);	
					jQuery("#editable_"+this.name).children('a').text(val);
					jQuery("#editable_"+this.name).show();
					jQuery("#editable_"+this.name+"_area").hide();
					if ( this.name == "twitter_user" || this.name == "facebook_url" || this.name == "linkedin_url" || this.name == "aim" || this.name == "flickr_url" || this.name == "youtube_url" ){

						var url = "";
						switch ( this.name ){
							case "twitter_user":
								url = "http://www.twitter.com/#!/"+jQuery(this).val();
							break;
							case "facebook_url":
								url = jQuery(this).val();
							break;
							case "linkedin_url":
								url = jQuery(this).val();
							break;
							case "aim":
								if ( jQuery("#aim_button_"+item_id).hasClass('aim_dark') ){
									jQuery("#aim_button_"+item_id).removeClass('aim_dark');
									jQuery("#aim_button_"+item_id).addClass('aim_light');
								}
							break;
						}

						if ( url != "" ){
							var name = this.name.replace('_url','').replace('_user','');
							jQuery("#editable_"+name+"_container_"+item_id).html("<a href='"+url+"'><div class='"+name+"_light'></div></a>");
						}
					}
				}
			});
			modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
			closeEditableModal();
		}
	});

}

function closeEditableModal(){

	jQuery("#"+current_area).find('a').popover('hide');
}


function fullscreenToggle()
{
	jQuery.ajax({
		url:'index.php?task=saveProfile&format=raw&tmpl=component',
		type:'POST',
		data:'fullscreen=1&url='+window.location,
		dataType:'JSON',
		success:function(data){
			window.location = data.url;
		}
	});
}

function printItems(print_button){

	if ( jQuery("#"+print_button).length == 0 ){
		var form = jQuery(print_button).parentsUntil('form.print_form').parent('form.print_form'); 
	}else{
		var form = jQuery("#"+print_button);
	}
	
	jQuery(form).submit();

}

function loadGoogleMap(ele){
	jQuery("#"+jQuery(ele).attr('id')+"_modal").dialog({
		autoOpen:false,
		height:400,
		width:600,
		dialogClass:'com_cobalt google_map',
		position:['center','center'],
		modal:true,
		resizable:false
	});
	jQuery("#"+jQuery(ele).attr('id')+"_modal").dialog('open');
}

function closeGoogleMap(){
	jQuery('.ui-dialog-content').dialog('close');
}

function capitalize(string)
{
    return typeof string != "undefined" ? string.charAt(0).toUpperCase() + string.slice(1) : "";
}

function showCalendarTasks(){
	jQuery("div.calendar_event").css('display','none');
	jQuery("div.calendar_task").css('display','block');
}

function showCalendarEvents(){
	jQuery("div.calendar_event").css('display','block');
	jQuery("div.calendar_task").css('display','none');
}

function showAllCalendarEvents(){
	jQuery("div.calendar_event").css('display','block');
	jQuery("div.calendar_task").css('display','block');
}

function toggleTeamMemberEvents(user_id){
	jQuery("div.assignee_id_"+user_id).each(function(){
		if ( jQuery(this).is(':visible') ){
			jQuery(this).addClass('hidden');
			jQuery(this).hide();
		}else{
			jQuery(this).removeClass('hidden');
			jQuery(this).show();
		}
	});
}

function editNoteEntry(note_id){
	jQuery.ajax({
		url:'index.php?view=note&layout=edit&tmpl=component&format=raw',
		type:'POST',
		data:'id='+note_id,
		dataType:'HTML',
		success:function(data){
			jQuery("#CobaltAjaxModalBody").html(data);
			jQuery("#CobaltAjaxModalHeader").text(Joomla.JText._('COBALT_EDIT_NOTE'));
			jQuery("#CobaltAjaxModal").modal('show');
			jQuery("#CobaltAjaxModalSaveButton").attr('onclick',"addNoteEntry('note_edit');");
		}
	});
}

function editConvoEntry(convo_id){
	jQuery.ajax({
		url:'index.php?view=deals&layout=edit_conversation&tmpl=component&format=raw',
		type:'POST',
		data:'id='+convo_id,
		dataType:'HTML',
		success:function(data){
			jQuery("#edit_convo_entry").html(data);
			jQuery("#edit_convo_entry").dialog({
				autoOpen:false,
				modal:true,
				width:500,
				resizable:false,
				dialogClass:'com_cobalt',
				title:Joomla.JText._('COBALT_EDIT_CONVO')
			});
			jQuery("#edit_convo_entry").dialog('open');
		}
	});
}

function trashConvoEntry(convo_id){
	jQuery.ajax({
		url:'index.php?task=trash&tmpl=component&format=raw',
		type:'POST',
		data:'item_type=conversations&item_id='+convo_id,
		dataType:'json',
		success:function(data){
			if ( data.success ){
				jQuery("#convo_entry_"+convo_id).remove();
				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'));
			}
		}
	});
}

function deleteItem(ele){
	if ( confirm(Joomla.JText._('COBALT_DELETE_CONFIRMATION')) ){
		jQuery("#delete_form").submit();
	}
}

function removePersonFromDeal(person_id){

	if ( confirm(Joomla.JText._('COBALT_DELETE_PERSON_FROM_DEAL_CONFIRM')) ){
		jQuery.ajax({
			url:'index.php?task=removePersonFromDeal&format=raw&tmpl=component',
			type:'POST',
			data:'person_id='+person_id+"&deal_id="+deal_id,
			dataType:'JSON',
			success:function(data){
				if ( data.success == true ){
					jQuery("#person_container_"+person_id).remove();
				}
			}
		})
	}

}

function eventFilter(filter,value){

	showAjaxLoader();

	dataString = filter+"="+value;
	jQuery.ajax({
		url: base_url+'index.php?view=events&layout=list&tmpl=component&format=raw',
		type:'POST',
		data:dataString,
		dataType:'HTML',
		success:function(data){
			hideAjaxLoader();
			jQuery("#events").html(data);
		}
	});

}

function eventUserFilter(type,id){

	showAjaxLoader();

	dataString = "assignee_id="+id+"&assignee_filter_type="+type;
	jQuery.ajax({
		url: base_url+'index.php?view=events&layout=list&tmpl=component&format=raw',
		type:'POST',
		data:dataString,
		dataType:'HTML',
		success:function(data){
			hideAjaxLoader();
			jQuery("#events").html(data);
		}
	});

}

function checkCompanyName(ele)
{	
	if ( ele != null ){
		clearTimeout(jQuery(ele).attr('timeout'));
		jQuery(ele).attr('timeout',setTimeout(function(){
				performCheckCompanyName();
			},1500));
	}else{
		performCheckCompanyName();
	}
}

function performCheckCompanyName(){
	jQuery('#company_id').val('');
	var company_name = jQuery('#company_name').val();
	if(company_name.length < 3) {
		return;
	}
	dataString = '&company_name='+company_name;
	jQuery.ajax({
		url: base_url+'index.php?task=checkCompanyName&tmpl=component&format=raw',
		type:'POST',
		data:dataString,
		dataType:'JSON',
		success:function(data){
			if(data.success) {
				if(data.company_id) {
					jQuery('#company_id').val(data.company_id);
				}
				jQuery('#company_message').html(data.message);				
				jQuery('#company_message').fadeIn();
			}
		}
	});
}

function checkPersonName(email_id)
{	
	var ele = typeof email_id === 'object' ? email_id : null;
	if ( ele != null ){
		clearTimeout(jQuery(ele).attr('timeout'));
		jQuery(ele).attr('timeout',setTimeout(function(){performPersonNameCheck(email_id);},1500));
	}else{
		performPersonNameCheck(email_id);
	}

}

function performPersonNameCheck(email_id){

	if ( typeof email_id === 'object' ){
		var dataString = "&person_name="+jQuery(email_id).val();
	}else if ( email_id != null ){
		jQuery("#email_form_"+email_id+' span.person_message').html('');
		jQuery("#email_form_"+email_id+" input[name=person_id]").val('');
		var person_name = jQuery("#email_form_"+email_id+" input[name=person_name]").val();
		if(person_name.length < 3) {
			return;
		}
		dataString = '&person_name='+person_name;
		var ele = jQuery("#email_form_"+email_id+" input[name=person_name]");
	}else{
		dataString = '&person_name='+jQuery("#person_name").val();
		var ele = jQuery("#person_name");
	}

	jQuery.ajax({
		url: base_url+'index.php?task=checkPersonName&tmpl=component&format=raw',
		type:'POST',
		data:dataString,
		dataType:'JSON',
		success:function(data){
			if(data.success) {
				if ( email_id != null && typeof email_id !== 'object' ){
					if(data.person_id) {
						jQuery("#email_form_"+email_id+" input[name=person_id]").val(data.person_id);
					}
					jQuery("#email_form_"+email_id+' span.person_message').html(data.message);				
				}else{
					if(data.company_id) {
					jQuery('#person_id').val(data.company_id);
					}
					jQuery('#person_message').html(data.message);	
				}
			}
		}
	});
}

function checkDealName(email_id)
{
	jQuery("#email_form_"+email_id+" span.deal_message").html('');				
	jQuery("#email_form_"+email_id+" input[name=deal_id]").val('');
	var deal_name = jQuery("#email_form_"+email_id+" input[name=deal_name]").val();
	if(deal_name.length < 3) {
		return;
	}
	clearTimeout(jQuery("#email_form_"+email_id+" input[name=deal_name]").attr('timeout'));
	jQuery("#email_form_"+email_id+" input[name=deal_name]").attr('timeout',setTimeout(function(){

		dataString = '&deal_name='+deal_name;
		jQuery.ajax({
			url: base_url+'index.php?task=checkDealName&tmpl=component&format=raw',
			type:'POST',
			data:dataString,
			dataType:'JSON',
			success:function(data){
				if(data.success) {
					if(data.deal_id) {
						jQuery("#email_form_"+email_id+" input[name=deal_id]").val(data.deal_id);
					}
					jQuery("#email_form_"+email_id+" span.deal_message").html(data.message);				
				}
			}
		});

	},1500));
}

function readEmail(email_id){
	jQuery("#email_modal_"+email_id).dialog({
			dialogClass:'com_cobalt',
			autoOpen:false,
			resizable: false,
			height:450,
			width:400,
			modal:true,
			title:Joomla.JText._('COBALT_MESSAGE_DETAILS')
	});
	jQuery("#email_modal_"+email_id).dialog('open');

	/** bind people name dropdowns **/
	if ( typeof people_names != 'undefined' ){
		jQuery("#person_name_"+email_id).autocomplete({
			source: function(request, response) {
	        var results = jQuery.ui.autocomplete.filter(people_names, request.term);
	        	response(results.slice(0, 10));
	    	},
			select:function(event,ui){
				jQuery("#person_name_"+email_id).val(ui.item.label);
				jQuery("#person_id_"+email_id).val(ui.item.value);
				jQuery("#person_message_"+email_id).html('');
				return false;
			},
			search:function(){

			},
			open: function(){
        		jQuery(this).autocomplete('widget').css('z-index', 3000);
        		return false;
		    }
		});
	}

	/** bind deal name dropdowns **/
	if ( typeof deal_names != 'undefined' ){
		jQuery("#deal_name_"+email_id).autocomplete({
			source: function(request, response) {
	        var results = jQuery.ui.autocomplete.filter(deal_names, request.term);
	        	response(results.slice(0, 10));
	    	},
			select:function(event,ui){
				jQuery("#deal_name_"+email_id).val(ui.item.label);
				jQuery("#deal_id_"+email_id).val(ui.item.value);
				jQuery("#deal_name_message"+email_id).html('');
				return false;
			},
			search:function(){

			},
			open: function(){
        		jQuery(this).autocomplete('widget').css('z-index', 3000);
        		return false;
		    }
		});
	}
}

function deleteEmail(email_id){
	jQuery.ajax({
		type:'POST',
		url:'index.php?task=removeEmail&format=raw&tmpl=component',
		data:"id="+email_id,
		dataType:'JSON',
		success:function(data){
			jQuery("#email_modal_"+email_id).remove();
			jQuery("#email_row_"+email_id).remove();
			modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'),Joomla.JText._('COBALT_EMAIL_DELETED'));
		}
	});
}

function saveEmail(email_id){
	var dataString = '';

	var $form = jQuery('#email_form_'+email_id+' :input');
	$form.each(function(){
		dataString += "&"+this.name+"="+jQuery(this).val();
	});

	dataString += "&id="+email_id;
	
	jQuery.ajax({
		type:'POST',
		url:'index.php?task=saveEmail&format=raw&tmpl=component',
		data:dataString,
		dataType:'JSON',
		success:function(data){
			jQuery("#email_modal_"+email_id).remove();
			jQuery("#email_row_"+email_id).remove();
			modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'),Joomla.JText._('COBALT_EMAIL_SAVED'));
		}
	});
}

function showAjaxLoader(){
	jQuery("div.ajax_loader").css('display','inline-block');
}

function hideAjaxLoader(){
	jQuery("div.ajax_loader").css('display','none');
}

function showSiteSearch(){

	jQuery("#site_search_input").val('');
	jQuery("#site_search").fadeToggle('fast');
	//assign autocomplete to element
	jQuery.ajax({
		type	:	'POST',
		url		:	'index.php?task=getTaskAssociations&format=raw&tmpl=component',
		dataType:	'json',
		success	:	function(data){
				
				//generate names object from received data
				var names = new Array();
				var namesInfo = new Array();
				jQuery.each(data,function(index,entry){
					//gen name string for search
					if ( entry.type == "person" ) {
						var name  = '';
							name += entry.first_name + " " + entry.last_name;
					} else {
						name = entry.name;
					}
					//gen associative object for id reference
					var infoObj = new Object();
					infoObj = { name : name, id : entry.id, type : entry.type, association_link : entry.association_link };
					//push info to objects
					namesInfo[name] = infoObj;
					names.push( name );
				});
				//assign autocomplete to element
				jQuery('#site_search_input').autocomplete({
					source:names,
					select:function(event,ui){
						association_link = namesInfo[ui.item.value].association_link;
						window.location = association_link;
					}, 
					search:function(){
						idExists = false;
					}
				}).data( "autocomplete" )._renderItem = function( ul, item ) {
			  		return jQuery( "<li></li>" )
			           .data( "item.autocomplete", item )
			           .append( "<a>"+ item.label +"<span style='font-style:italic;float:right;color:#ABABAB;'>"+namesInfo[item.value].type.substring(0,1).toUpperCase()+namesInfo[item.value].type.substring(1)+"</span></a>" )
			           .appendTo( ul );

				};
				
		}
	});
}

function updateItemsChecked(){
	items_checked = 0;
	jQuery("input[name='ids\\[\\]']").each(function(){
		items_checked += jQuery(this).is(":checked") ? 1 : 0;
	});
}

function showListEditActions(ele){
	updateItemsChecked();
	jQuery("#items_checked").html(items_checked);
	if ( items_checked > 0 ){
		jQuery("#list_edit_actions").slideDown('fast');
	}else{
		jQuery("#list_edit_actions").slideUp('fast');
	}
}

function deleteListItems(){
	itemIds = new Array();
	jQuery("input[name='ids\\[\\]']").each(function(){
		if ( jQuery(this).is(':checked') ){
			itemIds.push(jQuery(this).val());
		}
	});
	showAjaxLoader();
	jQuery.ajax({
		type:'POST',
		url:'index.php?task=trash&tmpl=component&format=raw',
		data: { item_id : itemIds, item_type : loc },
		dataType:'JSON',
		success:function(data){
			if ( data.success ){
				jQuery.each(itemIds,function(key,value){
					jQuery("#list_row_"+value).remove();
				});
				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'));
			}
			hideAjaxLoader();
		}
	});
}

function assignDealPrimaryContact(person_id,deal_id){
	if ( typeof deal_id === 'undefined'){
		deal_id = id;
	}
	jQuery.ajax({
		type:'POST',
		url:'index.php?task=save&format=raw&tmpl=component',
		data:'model=deal&id='+deal_id+'&primary_contact_id='+person_id,
		dataType:'JSON',
		success:function(data){

			var primary_contact_id = jQuery("#primary_contact").attr('data-id');
			var new_primary_ele = jQuery('#star_'+person_id);

			if ( typeof primary_contact_id !== 'undefined' ){

				jQuery("#contacts_container .star").each(function(key,ele){

						jQuery(ele).addClass('white_star');
						jQuery(ele).removeClass('star');
						jQuery(ele).attr('onclick','assignDealPrimaryContact('+primary_contact_id+");")
						jQuery(ele).attr('id','star_'+primary_contact_id);
				});

			}

			jQuery(new_primary_ele).addClass('star');
			jQuery(new_primary_ele).removeClass('white_star');
			jQuery(new_primary_ele).attr('onclick','unassignDealPrimaryContact('+person_id+")");
			jQuery(new_primary_ele).attr('id','primary_contact');

			modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'),Joomla.JText._('COBALT_UPDATED_PRIMARY_CONTACT'));

		}
	});
}

function unassignDealPrimaryContact(person_id,deal_id){
	if ( typeof deal_id === 'undefined'){
		deal_id = id;
	}
	jQuery.ajax({
		type:'POST',
		url:'index.php?task=save&format=raw&tmpl=component',
		data:'model=deal&id='+deal_id+'&primary_contact_id=0',
		dataType:'JSON',
		success:function(data){

			jQuery('#primary_contact').addClass('white_star');
			jQuery("#primary_contact").removeClass('star');
			jQuery('#primary_contact').attr('onclick','assignDealPrimaryContact('+person_id+")")
			jQuery('#primary_contact').attr('id','star_'+person_id);

			modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'),Joomla.JText._('COBALT_UPDATED_PRIMARY_CONTACT'));

		}
	});
}

function manageMailingLists(){

	jQuery.ajax({
		type:'POST',
		url:'index.php?view=acymailing&layout=manage&format=raw&tmpl=component',
		data:'&id='+person_id,
		dataType:'HTML',
		success:function(html){
			jQuery('#mailing_list_modal').dialog({
		
				dialogClass:'com_cobalt',
				autoOpen:false,
				resizable: false,
				minHeight: 60,
				title: Joomla.JText._('COBALT_MANAGE_MAILING_LISTS'),
				position:['center','center'],
				modal:true,
				width:400

			});
			jQuery('#mailing_list_modal').html(html);
			jQuery('#mailing_list_modal').dialog('open');
		}
	});

}

function toggleMailingList(listId,subscribeValue){

	var dataString = "subscribe="+subscribeValue+"&listid="+listId+"&person_id="+person_id;

	jQuery.ajax({
		type:'POST',
		url:'index.php?task=toggleMailingList&format=raw&tmpl=component',
		data:dataString,
		dataType:'JSON',
		success:function(data){
			if ( data.success ){
				var newValue = subscribeValue == 1 ? 0 : 1;
				var lang = newValue ? Joomla.JText._('COBALT_REMOVE') : Joomla.JText._('COBALT_ADD');
				var html = "<a href='javascript:void(0);' onclick=\"toggleMailingList('"+listId+"','"+newValue+"')\">"+lang+"</a>";
				jQuery("#mailing_list_"+listId).html(html);
				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'));
			}else{
				modalMessage(Joomla.JText._('COBALT_ERROR'));
			}
		}
	});

}

function showNewsletterLinks(mailid){

	jQuery.ajax({
		type:'POST',
		url:'index.php?view=acymailing&layout=links&format=raw&tmpl=component',
		data:'&id='+person_id+"&mailid="+mailid,
		dataType:'HTML',
		success:function(html){
			jQuery('#mailing_list_modal').dialog({
		
				dialogClass:'com_cobalt',
				autoOpen:false,
				resizable: false,
				minHeight: 60,
				title: Joomla.JText._('COBALT_MAILING_LIST_LINKS'),
				position:['center','center'],
				modal:true,
				width:400

			});
			jQuery('#mailing_list_modal').html(html);
			jQuery('#mailing_list_modal').dialog('open');
		}
	});

}

function getEmails(){
	jQuery("#email_loader").addClass('ajax_loader');
	jQuery.ajax({
		url:'index.php?view=mail&format=raw&tmpl=component',
		type:'GET',
		dataType:'HTML',
		success:function(html){
			jQuery("#email_loader").removeClass('ajax_loader');
			jQuery("#mail_table_entries").html(html);
		}
	});
}

function ucwords (str) {
    // Uppercase the first character of every word in a string  
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}

function nl2br(str){
	return str.replace(/\n/g,'<br />');
}

function updateTranscripts(roomId){

	jQuery.ajax({
		type:'POST',
		url:'index.php?view=banter&layout=transcripts&format=raw&tmpl=component',
		data:{room_id:roomId},
		dataType:'HTML',
		success:function(html){
			jQuery("#transcript_entries").html(html);
		}
	});

}

function shareItemDialog(){
	jQuery("#shared_user_name").autocomplete({
		source: function(request, response) {
        var results = jQuery.ui.autocomplete.filter(users, request.term);
        	response(results.slice(0, 10));
    	},
		select:function(event,ui){
			jQuery("#shared_user_name").val(ui.item.label);
			jQuery("#shared_user_id").val(ui.item.value);
			return false;
		},
		search:function(){

		},
		open: function(){
    		jQuery(this).autocomplete('widget').css('z-index', 3000);
    		return false;
	    }
	});
	jQuery("#share_item_dialog").modal('show');
}

function shareItem(){

	jQuery.ajax({
		type:'POST',
		url:'index.php?task=shareItem&format=raw&tmpl=component',
		data:'item_id='+id+"&item_type="+loc+"&user_id="+jQuery("#shared_user_id").val(),
		dataType:'JSON',
		success:function(data){
			if ( data.success ){

				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'),Joomla.JText._('COBALT_SUCCESSFULLY_SHARED_ITEM'));

				var html = '<div id="shared_user_'+jQuery("#shared_user_id").val()+'">'+jQuery("#shared_user_name").val()+" - <a href='javascript:void(0);' onclick='unshareItem("+jQuery("#shared_user_id").val()+");'>"+Joomla.JText._("COBALT_REMOVE")+"</a></div>";
				jQuery("#shared_user_list").append(html);

				jQuery("#shared_user_name").val('');
				jQuery("#shared_user_id").val('');
			}else{

				modalMessage(Joomla.JText._('COBALT_ERROR'));

			}
		}
	});

}

function unshareItem(user_id){

	jQuery.ajax({
		type:'POST',
		url:'index.php?task=unshareItem&format=raw&tmpl=component',
		data:'item_id='+id+"&item_type="+loc+"&user_id="+user_id,
		dataType:'JSON',
		success:function(data){
			if ( data.success ){
				modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE'),Joomla.JText._('COBALT_SUCCESSFULLY_UNSHARED_ITEM'));
				jQuery("#shared_user_name").val('');
				jQuery("#shared_user_id").val('');
				jQuery("#shared_user_"+user_id).remove();
			}else{
				modalMessage(Joomla.JText._('COBALT_ERROR'));
			}
		}
	});

}

function performLogout(){
	jQuery("#logout-form").submit();
}

function bindModalRefresh(){
	jQuery(".modal").bind('show',function(){
		if ( !jQuery(this).is(":visible")){
			if ( jQuery(this).data('remote') != null ){
				var remote  = jQuery(this).data('remote');
				jQuery(this).find('.modal-body').load(remote);
				return true;
			}
		}
		bindDatepickers();
	});
}

/** save modal forms for items **/
function saveItem(formId){

	var dataObj = new Object();
	var type = null;
	var ajax = true;

	var form = jQuery('#'+formId+' :input');
	jQuery(form).each(function(){
		if ( this.name == "jsrefresh" && this.value=="1" ){
			jQuery("#"+formId).submit();
			jQuery("#"+formId).parentsUntil('div.modal').parent('div.modal').modal('hide');
			ajax = false;
			return true;
		}
		if ( this.type != "button" ){
			var val = ( this.type == 'checkbox' || this.type == 'radio' ) ? ( ( jQuery(this).is(':checked') ) ? 1 : 0 ) : jQuery(this).val();
			dataObj[this.name] = val;
		}
		if ( this.name == "model" ){
			type = val;
		}
	});

	if ( ajax ){

		jQuery.ajax({
			type:'post',
			url:base_url+'index.php?task=save&format=raw',
			data:dataObj,
			dataType:'json',
			success:function(data){
				if ( data.id > 0 ){

					if ( type != "tasklist" ){
						newListItem(data,type);
					}

					if ( type == "tasklist" ){
						updateTaskLists();
					}

					jQuery('div.modal').modal('hide');

				} else {

					modalMessage(Joomla.JText._('COM_PANTASSO_ERROR_HEADER'))

				}
			}
		});
	
	}

}

/** save modal forms for items **/
function saveProfileItem(formId){

	var dataObj = new Object();
	var type = null;
	var ajax = true;

	var form = jQuery('#'+formId+' :input');
	jQuery(form).each(function(){
		if ( this.name == "jsrefresh" && this.value=="1" ){
			jQuery("#"+formId).submit();
			jQuery("#"+formId).parentsUntil('div.modal').parent('div.modal').modal('hide');
			ajax = false;
			return true;
		}
		if ( this.type != "button" ){
			var val = ( this.type == 'checkbox' || this.type == 'radio' ) ? ( ( jQuery(this).is(':checked') ) ? 1 : 0 ) : jQuery(this).val();
			dataObj[this.name] = val;
		}
		if ( this.name == "model" ){
			type = val;
		}
	});

	if ( ajax ){

		jQuery.ajax({
			type:'post',
			url:base_url+'index.php?task=save&format=raw',
			data:dataObj,
			dataType:'json',
			success:function(data){
				if ( data.id > 0 ){

					updateProfileItem(data);

				} else {

					modalMessage(Joomla.JText._('COM_PANTASSO_ERROR_HEADER'))

				}
				jQuery(".modal").modal('hide');
			}
		});
	
	}

}

function updateProfileItem(data){

	jQuery.each(data,function(name,value){
		if ( jQuery("#"+name+"_"+data.id).text() != value ){
			switch ( name ){
				case "status_name":
					jQuery("#"+name+"_"+data.id).attr('class','deal-status-'+value);
				break;
				case "stage_name":
					jQuery("#"+name+"_"+data.id).attr('title',Joomla.JText._('COBALT_STAGE')+": "+ucwords(value));
				break;
				case "percent":
					var color = getColorForPercentage(value/100);
					var colorDark = getColorForPercentage((value-20)/100);
	          		var style = "background-image: -moz-linear-gradient(top,"+color+","+colorDark+");background-image: -webkit-gradient(linear,0 0,0 100%,from("+color+"),to("+colorDark+"));background-image: -webkit-linear-gradient(top,"+color+","+colorDark+");background-image: -o-linear-gradient(top,"+color+","+colorDark+");background-image: linear-gradient(to bottom,"+color+","+colorDark+");background-color:"+color+" !important; ";
					jQuery("#"+name+"_"+data.id).attr('style',style);
					jQuery("#"+name+"_"+data.id).css('width',value+"%");
				break;
				default:
					jQuery("#"+name+"_"+data.id).html(value);
				break;
			}
			jQuery("#"+name+"_"+data.id).effect("highlight",2000);
		}
	});

}

/** create new list items from data **/
function newListItem(data,type){

	id = "id="+data.id;

	switch ( type ){
		case "deal":
			var loc = "deals";
		break;
		case "company":
			var loc = "companies";
		break;
		case "people":
			var loc ="people";
		break;
		default:
			var loc = type;
		break;
	}

	jQuery.ajax({
		type:'post',
		url:'index.php?view='+loc+'&format=raw&layout=entry&'+id,
		dataType:'html',
		success:function(html){
			if ( type == "tasklist" ){
				jQuery("a.task_list_"+data.id).html(data.name);
				jQuery("a.task_list_"+data.id).effect("highlight",2000);
			}else{
				if ( jQuery("#list_row_"+data.id).length != 0 ){
					jQuery("#list_row_"+data.id).replaceWith(html);
					jQuery("#list_row_"+data.id+" td").effect('highlight',2000);
				}else{	
					jQuery("#list").prepend(html);
					jQuery("#list_row_"+data.id+" td").effect('highlight',2000);
				}
			}
			bindDatepickers();
			bindTooltips();
			bindPopovers();
		}
	});

}

function deleteProfileItem(link){
	if ( confirm(Joomla.JText._('COBALT_DELETE_CONFIRMATION')) ){
		return true;
	}else{
		event.preventDefault();
	}
	return false;
}

var percentColors = [
    { pct: 0.0, color: { r: 0xff, g: 0x00, b: 0 } },
    { pct: 0.5, color: { r: 0xff, g: 0xff, b: 0 } },
    { pct: 1.0, color: { r: 0x00, g: 0xff, b: 0 } } ];
 
var getColorForPercentage = function(pct) {
    for (var i = 0; i < percentColors.length; i++) {
        if (pct <= percentColors[i].pct) {
            var lower = percentColors[i - 1] || { pct: 0.1, color: { r: 0x0, g: 0x00, b: 0 } };
            var upper = percentColors[i];
            var range = upper.pct - lower.pct;
            var rangePct = (pct - lower.pct) / range;
            var pctLower = 1 - rangePct;
            var pctUpper = rangePct;
            var color = {
                r: Math.floor(lower.color.r * pctLower + upper.color.r * pctUpper),
                g: Math.floor(lower.color.g * pctLower + upper.color.g * pctUpper),
                b: Math.floor(lower.color.b * pctLower + upper.color.b * pctUpper)
            };
            var rgb = 'rgb(' + [color.r, color.g, color.b].join(',') + ')';
            return rgb;
        }
    }
}

if("undefined"===typeof Joomla)var Joomla={};Joomla.editors={};Joomla.editors.instances={};Joomla.submitform=function(a,b){if("undefined"===typeof b&&(b=document.getElementById("adminForm"),!b))b=document.adminForm;if("undefined"!==typeof a&&''!==a)b.controller.value=a;if("function"==typeof b.onsubmit)b.onsubmit();"function"==typeof b.fireEvent&&b.fireEvent("submit");b.submit()};Joomla.submitbutton=function(a){Joomla.submitform(a)};
Joomla.JText={strings:{},_:function(a,b){return"undefined"!==typeof this.strings[a.toUpperCase()]?this.strings[a.toUpperCase()]:b},load:function(a){for(var b in a)this.strings[b.toUpperCase()]=a[b];return this}};Joomla.replaceTokens=function(a){for(var b=document.getElementsByTagName("input"),c=0;c<b.length;c++)if("hidden"==b[c].type&&32==b[c].name.length&&"1"==b[c].value)b[c].name=a};Joomla.isEmail=function(a){return/^[\w-_.]*[\w-_.]@[\w].+[\w]+[\w]$/.test(a)};
function submitform(a){if(a)document.adminForm.controller.value=a;if("function"==typeof document.adminForm.onsubmit)document.adminForm.onsubmit();"function"==typeof document.adminForm.fireEvent&&document.adminForm.fireEvent("submit");document.adminForm.submit()}function popupWindow(a,b,c,d,f){winprops="height="+d+",width="+c+",top="+(screen.height-d)/2+",left="+(screen.width-c)/2+",scrollbars="+f+",resizable";win=window.open(a,b,winprops);4<=parseInt(navigator.appVersion)&&win.window.focus()}
Joomla.isChecked=function(a,b){if("undefined"===typeof b&&(b=document.getElementById("adminForm"),!b))b=document.adminForm;!0==a?b.boxchecked.value++:b.boxchecked.value--};Joomla.popupWindow=function(a,b,c,d,f){window.open(a,b,"height="+d+",width="+c+",top="+(screen.height-d)/2+",left="+(screen.width-c)/2+",scrollbars="+f+",resizable").window.focus()};