/**
 * Globals
 */
var new_event_date = null;
var menu = true;
var curr_cal_event = null;
var cloning = false;

jQuery(document).ready(function(){
	
	//construct calendar object
	jQuery("#calendar").fullCalendar({
		
		theme:true,
		
		events:function(start, end, callback) {
	        jQuery.ajax({
	            url: base_url+'index.php?task=getCalendarEvents&format=raw&tmpl=component',
	            dataType: 'JSON',
	            data: {
	                start: Math.round(start.getTime() / 1000),
	                end: Math.round(end.getTime() / 1000)
	            },
	            success: function(data) {
	                callback(data);
	            }
	        });
    	},

		editable:true,

		//Rendering events
		eventRender: function(event,element){

			//Update css for completed events
			if ( event.completed == 1 ){
				jQuery(element).css('text-decoration','line-through');
			}
			
			jQuery(element).addClass("calendar_"+event.type);
			jQuery(element).addClass("assignee_id_"+event.assignee_id);
			
			if ( typeof event.assignee_color != 'undefined' ){
				jQuery("div.assignee_id_"+event.assignee_id+" .fc-event-skin").css('background','#'+event.assignee_color);
				jQuery("div.assignee_id_"+event.assignee_id+" .fc-event-skin").css('borderColor','#'+event.assignee_color);
				jQuery("div.assignee_id_"+event.assignee_id).css('borderColor','#'+event.assignee_color);
			}else{
				jQuery(element).addClass("calendar_"+event.type+"_bg");
			}
		},

		eventAfterRender: function(event,element,view){

				if ( event.assignee_id != user_id ){
					jQuery(element).addClass("hidden");
				}


				//Update css for completed events
				if ( event.completed == 1 ){
					jQuery(element).css('text-decoration','line-through');
				}

				if ( cloning || event.server || event.clone ){

					return true;

				}else{

					if ( event.repeats != "none" && !event.cloned && event.update_future_events ){

						calEvents = new Array();
						cloning = true;

						jQuery("#calendar").fullCalendar('clientEvents',function(clientEvent){
							if ( clientEvent.parent_id == event.id && clientEvent.id != event.id ){
									// jQuery("#calendar").fullCalendar('removeEvents',clientEvent._id);
									// clientEvent.title = event.title;
									// jQuery("#calendar").fullCalendar('updatEvent',clientEvent);
							}
							if ( clientEvent.parent_id == event.parent_id && clientEvent.id != event.id ){
									// jQuery("#calendar").fullCalendar('removeEvents',clientEvent._id);	
									// clientEvent.title = event.title;
									// jQuery("#calendar").fullCalendar('updatEvent',clientEvent);
							}
						});

						var newEvent = new Object();

						if ( event.type == 'event' ){
							// Split timestamp into [ Y, M, D, h, m, s ]
							var st = event.start_time.split(/[- :]/);	
							var et = event.end_time.split(/[- :]/);
							// Apply each element to the Date function
							newEvent.start_time = new Date(st[0], st[1]-1, st[2]/*, st[3], st[4], st[5]);*/); 
							newEvent.end_time = new Date(et[0], et[1]-1, et[2]/*, et[3], et[4], et[5]);*/);
						}else{
							//Get due date time stamp
							var dt = event.due_date.split(/[- :]/);
							newEvent.due = new Date(dt[0], dt[1]-1, dt[2]/*, dt[3], dt[4], dt[5]);*/);
						}

						switch ( event.repeats ){

							/**
							 * DAILY
							 */
							case 'daily':

								if ( event.type == "event" ){
									var nextMonth = newEvent.start_time.getMonth()+2;
									var currMonth = newEvent.start_time.getMonth()+1;
									newEvent.start_time.setDate(newEvent.start_time.getDate()-1);
									newEvent.end_time.setDate(newEvent.end_time.getDate()-1);
								}else{
									var nextMonth = newEvent.due.getMonth()+2;
									var currMonth = newEvent.due.getMonth()+1;
									newEvent.due.setDate(newEvent.due.getDate()-1);
								}	
								
								var counter = 1;

								var buffer = false;
								while ( ( currMonth < nextMonth ) && !buffer ){

									if ( event.type == "event" ){
									
										newEvent.start_time.setDate(newEvent.start_time.getDate()+1);

										var year = newEvent.start_time.getFullYear();
										var month = newEvent.start_time.getMonth()+1;
										var day = newEvent.start_time.getDate()+1;
										var hour = newEvent.start_time.getHours();
										var minute = newEvent.start_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

										newEvent.end_time.setDate(newEvent.end_time.getDate()+1);

										var year = newEvent.end_time.getFullYear();
										var month = newEvent.end_time.getMonth()+1;
										var day = newEvent.end_time.getDate()+1;
										var hour = newEvent.end_time.getHours();
										var minute = newEvent.end_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

									}else{

										newEvent.due.setDate(newEvent.due.getDate()+1);

										var year = newEvent.due.getFullYear();
										var month = newEvent.due.getMonth()+1;
										var day = newEvent.due.getDate()+1;
										var hour = newEvent.due.getHours();
										var minute = newEvent.due.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.due_date = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
									}

									newEvent.allDay = event.allDay;
									newEvent.assignee_id = event.assignee_id;
									newEvent.association_id = event.association_id;
									newEvent.association_type = event.association_type;
									newEvent.category_id = event.category_id;
									newEvent.completed = event.completed;
									newEvent.description = event.description;
									newEvent.name = event.name;
									newEvent.owner_id = event.owner_id;
									newEvent.parent_id = event.id;
									newEvent.title = event.title;
									newEvent.type = event.type;
									newEvent.clone = true;
									newEvent.repeats = event.repeats;

									calEvents.push(jQuery.extend({},newEvent));

									if ( event.type == "event" ){
										currMonth = newEvent.start_time.getMonth()+1;
									}else{
										currMonth = newEvent.due.getMonth()+1;
									}
									
									counter++;
									if ( counter == 31 ) buffer = true;

								}

							break;

							/**
							 * WEEKDAYS
							 */
							case 'weekdays':

								if ( event.type == "event" ){
									var nextMonth = newEvent.start_time.getMonth()+2;
									var currMonth = newEvent.start_time.getMonth()+1;
									newEvent.start_time.setDate(newEvent.start_time.getDate()-1);
									newEvent.end_time.setDate(newEvent.end_time.getDate()-1);
								}else{
									var nextMonth = newEvent.due.getMonth()+2;
									var currMonth = newEvent.due.getMonth()+1;
									newEvent.due.setDate(newEvent.due.getDate()-1);
								}	
								
								var counter = 1;

								var buffer = false;
								while ( ( currMonth < nextMonth ) && !buffer ){

									if ( event.type == "event" ){
									
										newEvent.start_time.setDate(newEvent.start_time.getDate()+1);

										var year = newEvent.start_time.getFullYear();
										var month = newEvent.start_time.getMonth()+1;
										var day = newEvent.start_time.getDate()+1;
										var hour = newEvent.start_time.getHours();
										var minute = newEvent.start_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

										newEvent.end_time.setDate(newEvent.end_time.getDate()+1);

										var year = newEvent.end_time.getFullYear();
										var month = newEvent.end_time.getMonth()+1;
										var day = newEvent.end_time.getDate()+1;
										var hour = newEvent.end_time.getHours();
										var minute = newEvent.end_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

									}else{

										newEvent.due.setDate(newEvent.due.getDate()+1);

										var year = newEvent.due.getFullYear();
										var month = newEvent.due.getMonth()+1;
										var day = newEvent.due.getDate()+1;
										var hour = newEvent.due.getHours();
										var minute = newEvent.due.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.due_date = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
									}

									newEvent.allDay = event.allDay;
									newEvent.assignee_id = event.assignee_id;
									newEvent.association_id = event.association_id;
									newEvent.association_type = event.association_type;
									newEvent.category_id = event.category_id;
									newEvent.completed = event.completed;
									newEvent.description = event.description;
									newEvent.name = event.name;
									newEvent.owner_id = event.owner_id;
									newEvent.parent_id = event.id;
									newEvent.title = event.title;
									newEvent.type = event.type;
									newEvent.clone = true;
									newEvent.repeats = event.repeats;

									if ( event.type == "event" ){
										var day = newEvent.start_time.getDay();
										currMonth = newEvent.start_time.getMonth()+1;
									}else{
										var day = newEvent.due.getDay();
										currMonth = newEvent.due.getMonth()+1;
									}

									if( day > -1 && day < 5 ){
										calEvents.push(jQuery.extend({},newEvent));
									}
									
									counter++;
									if ( counter == 31 ) buffer = true;

								}

							break;

			                /**
							 * WEEKLY
							 */
							case 'weekly':

								if ( event.type == "event" ){
									var nextMonth = newEvent.start_time.getMonth()+2;
									var currMonth = newEvent.start_time.getMonth()+1;
									newEvent.start_time.setDate(newEvent.start_time.getDate()-1);
									newEvent.end_time.setDate(newEvent.end_time.getDate()-1);
								}else{
									var nextMonth = newEvent.due.getMonth()+2;
									var currMonth = newEvent.due.getMonth()+1;
									newEvent.due.setDate(newEvent.due.getDate()-1);
								}
								
								var counter = 1;

								var buffer = false;
								while ( ( currMonth < nextMonth ) && !buffer ){

									if ( event.type == "event" ){
									
										newEvent.start_time.setDate(newEvent.start_time.getDate()+7);

										var year = newEvent.start_time.getFullYear();
										var month = newEvent.start_time.getMonth()+1;
										var day = newEvent.start_time.getDate()+1;
										var hour = newEvent.start_time.getHours();
										var minute = newEvent.start_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

										newEvent.end_time.setDate(newEvent.end_time.getDate()+7);

										var year = newEvent.end_time.getFullYear();
										var month = newEvent.end_time.getMonth()+1;
										var day = newEvent.end_time.getDate()+1;
										var hour = newEvent.end_time.getHours();
										var minute = newEvent.end_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

									}else{

										newEvent.due.setDate(newEvent.due.getDate()+7);

										var year = newEvent.due.getFullYear();
										var month = newEvent.due.getMonth()+1;
										var day = newEvent.due.getDate()+1;
										var hour = newEvent.due.getHours();
										var minute = newEvent.due.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.due_date = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

									}

									newEvent.allDay = event.allDay;
									newEvent.assignee_id = event.assignee_id;
									newEvent.association_id = event.association_id;
									newEvent.association_type = event.association_type;
									newEvent.category_id = event.category_id;
									newEvent.completed = event.completed;
									newEvent.description = event.description;
									newEvent.name = event.name;
									newEvent.owner_id = event.owner_id;
									newEvent.parent_id = event.id;
									newEvent.title = event.title;
									newEvent.type = event.type;
									newEvent.clone = true;
									newEvent.repeats = event.repeats;

									if ( event.type == "event" ){
										currMonth = newEvent.start_time.getMonth()+1;
									}else{
										currMonth = newEvent.due.getMonth()+1;
									}

									calEvents.push(jQuery.extend({},newEvent));
									
									counter++;
									if ( counter == 31 ) buffer = true;

								}

							break;


			                //Weekly Monday Wednesday and Friday
			                case 'weekly-mwf':

								if ( event.type == "event" ){
									var nextMonth = newEvent.start_time.getMonth()+2;
									var currMonth = newEvent.start_time.getMonth()+1;
									newEvent.start_time.setDate(newEvent.start_time.getDate()-1);
									newEvent.end_time.setDate(newEvent.end_time.getDate()-1);
								}else{
									var nextMonth = newEvent.due.getMonth()+2;
									var currMonth = newEvent.due.getMonth()+1;
									newEvent.due.setDate(newEvent.due.getDate()-1);
								}	
								
								var counter = 1;

								var buffer = false;
								while ( ( currMonth < nextMonth ) && !buffer ){

									if ( event.type == "event" ){
									
										newEvent.start_time.setDate(newEvent.start_time.getDate()+1);

										var year = newEvent.start_time.getFullYear();
										var month = newEvent.start_time.getMonth()+1;
										var day = newEvent.start_time.getDate()+1;
										var hour = newEvent.start_time.getHours();
										var minute = newEvent.start_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

										newEvent.end_time.setDate(newEvent.end_time.getDate()+1);

										var year = newEvent.end_time.getFullYear();
										var month = newEvent.end_time.getMonth()+1;
										var day = newEvent.end_time.getDate()+1;
										var hour = newEvent.end_time.getHours();
										var minute = newEvent.end_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

									}else{

										newEvent.due.setDate(newEvent.due.getDate()+1);

										var year = newEvent.due.getFullYear();
										var month = newEvent.due.getMonth()+1;
										var day = newEvent.due.getDate()+1;
										var hour = newEvent.due.getHours();
										var minute = newEvent.due.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.due_date = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
									}

									newEvent.allDay = event.allDay;
									newEvent.assignee_id = event.assignee_id;
									newEvent.association_id = event.association_id;
									newEvent.association_type = event.association_type;
									newEvent.category_id = event.category_id;
									newEvent.completed = event.completed;
									newEvent.description = event.description;
									newEvent.name = event.name;
									newEvent.owner_id = event.owner_id;
									newEvent.parent_id = event.id;
									newEvent.title = event.title;
									newEvent.type = event.type;
									newEvent.clone = true;
									newEvent.repeats = event.repeats;

									if ( event.type == "event" ){
										var day = newEvent.start_time.getDay();
										currMonth = newEvent.start_time.getMonth()+1;
									}else{
										var day = newEvent.due.getDay();
										currMonth = newEvent.due.getMonth()+1;
									}

									if( day == 0 || day == 2 || day == 4 ){
										calEvents.push(jQuery.extend({},newEvent));
									}
									
									counter++;
									if ( counter == 31 ) buffer = true;

								}

							break;

			                    
			                //Weekly Tuesday Thursday
			                case 'weekly-tr':

								if ( event.type == "event" ){
									var nextMonth = newEvent.start_time.getMonth()+2;
									var currMonth = newEvent.start_time.getMonth()+1;
									newEvent.start_time.setDate(newEvent.start_time.getDate()-1);
									newEvent.end_time.setDate(newEvent.end_time.getDate()-1);
								}else{
									var nextMonth = newEvent.due.getMonth()+2;
									var currMonth = newEvent.due.getMonth()+1;
									newEvent.due.setDate(newEvent.due.getDate()-1);
								}	
								
								var counter = 1;

								var buffer = false;
								while ( ( currMonth < nextMonth ) && !buffer ){

									if ( event.type == "event" ){
									
										newEvent.start_time.setDate(newEvent.start_time.getDate()+1);

										var year = newEvent.start_time.getFullYear();
										var month = newEvent.start_time.getMonth()+1;
										var day = newEvent.start_time.getDate()+1;
										var hour = newEvent.start_time.getHours();
										var minute = newEvent.start_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

										newEvent.end_time.setDate(newEvent.end_time.getDate()+1);

										var year = newEvent.end_time.getFullYear();
										var month = newEvent.end_time.getMonth()+1;
										var day = newEvent.end_time.getDate()+1;
										var hour = newEvent.end_time.getHours();
										var minute = newEvent.end_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

									}else{

										newEvent.due.setDate(newEvent.due.getDate()+1);

										var year = newEvent.due.getFullYear();
										var month = newEvent.due.getMonth()+1;
										var day = newEvent.due.getDate()+1;
										var hour = newEvent.due.getHours();
										var minute = newEvent.due.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.due_date = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
									}

									newEvent.allDay = event.allDay;
									newEvent.assignee_id = event.assignee_id;
									newEvent.association_id = event.association_id;
									newEvent.association_type = event.association_type;
									newEvent.category_id = event.category_id;
									newEvent.completed = event.completed;
									newEvent.description = event.description;
									newEvent.name = event.name;
									newEvent.owner_id = event.owner_id;
									newEvent.parent_id = event.id;
									newEvent.title = event.title;
									newEvent.type = event.type;
									newEvent.clone = true;
									newEvent.repeats = event.repeats;

									if ( event.type == "event" ){
										var day = newEvent.start_time.getDay();
										currMonth = newEvent.start_time.getMonth()+1;
									}else{
										var day = newEvent.due.getDay();
										currMonth = newEvent.due.getMonth()+1;
									}

									if( day == 1 || day == 3 ){
										calEvents.push(jQuery.extend({},newEvent));
									}
									
									counter++;
									if ( counter == 31 ) buffer = true;

								}

							break;

			                    
			                /**
			                 * MONTHLY
			                 */
			                case 'monthly':

								if ( event.type == "event" ){
									var nextYear = newEvent.start_time.getFullYear()+1;
									var currYear = newEvent.start_time.getFullYear();
									newEvent.start_time.setDate(newEvent.start_time.getDate()+1);
									newEvent.end_time.setDate(newEvent.end_time.getDate()+1);
								}else{
									var nextYear = newEvent.due.getFullYear()+1;
									var currYear = newEvent.due.getFullYear();
									newEvent.due.setDate(newEvent.due.getDate()+1);
								}	
								
								var counter = 1;

								var buffer = false;
								while ( ( currYear < nextYear ) && !buffer ){

									if ( event.type == "event" ){
									
										newEvent.start_time.setMonth(newEvent.start_time.getMonth()+1);

										var year = newEvent.start_time.getFullYear();
										var month = newEvent.start_time.getMonth()+1;
										var day = newEvent.start_time.getDate()-1;
										var hour = newEvent.start_time.getHours();
										var minute = newEvent.start_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

										newEvent.end_time.setMonth(newEvent.end_time.getMonth()+1);

										var year = newEvent.end_time.getFullYear();
										var month = newEvent.end_time.getMonth()+1;
										var day = newEvent.end_time.getDate()-1;
										var hour = newEvent.end_time.getHours();
										var minute = newEvent.end_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

									}else{

										newEvent.due.setMonth(newEvent.due.getMonth()+1);

										var year = newEvent.due.getFullYear();
										var month = newEvent.due.getMonth()+1;
										var day = newEvent.due.getDate()-1;
										var hour = newEvent.due.getHours();
										var minute = newEvent.due.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.due_date = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
									}

									newEvent.allDay = event.allDay;
									newEvent.assignee_id = event.assignee_id;
									newEvent.association_id = event.association_id;
									newEvent.association_type = event.association_type;
									newEvent.category_id = event.category_id;
									newEvent.completed = event.completed;
									newEvent.description = event.description;
									newEvent.name = event.name;
									newEvent.owner_id = event.owner_id;
									newEvent.parent_id = event.id;
									newEvent.title = event.title;
									newEvent.type = event.type;
									newEvent.clone = true;
									newEvent.repeats = event.repeats;

									if ( event.type == "event" ){
										currYear = newEvent.start_time.getFullYear();
									}else{
										currYear = newEvent.due_date.getFullYear();
									}

									calEvents.push(jQuery.extend({},newEvent));
									
									counter++;
									if ( counter == 31 ) buffer = true;

								}

							break;	
			                    

			                //Yearly
		                	case 'yearly':

								if ( event.type == "event" ){
									var nextNextYear = newEvent.start_time.getFullYear()+2;
									var nextYear = newEvent.start_time.getFullYear()+1;
									newEvent.start_time.setDate(newEvent.start_time.getDate());
									newEvent.end_time.setDate(newEvent.end_time.getDate());
								}else{
									var nextNextYear = newEvent.due.getFullYear()+2;
									var nextYear = newEvent.due.getFullYear()+1;
									newEvent.due.setDate(newEvent.due.getDate());
								}	
								
								var counter = 1;

								var buffer = false;
								while ( ( nextYear < nextNextYear ) && !buffer ){

									if ( event.type == "event" ){
									
										newEvent.start_time.setFullYear(newEvent.start_time.getFullYear()+1);

										var year = newEvent.start_time.getFullYear();
										var month = newEvent.start_time.getMonth()+1;
										var day = newEvent.start_time.getDate();
										var hour = newEvent.start_time.getHours();
										var minute = newEvent.start_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

										newEvent.end_time.setFullYear(newEvent.end_time.getFullYear()+1);

										var year = newEvent.end_time.getFullYear();
										var month = newEvent.end_time.getMonth()+1;
										var day = newEvent.end_time.getDate();
										var hour = newEvent.end_time.getHours();
										var minute = newEvent.end_time.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

									}else{

										newEvent.due.setFullYear(newEvent.due.getFullYear()+1);

										var year = newEvent.due.getFullYear();
										var month = newEvent.due.getMonth()+1;
										var day = newEvent.due.getDate();
										var hour = newEvent.due.getHours();
										var minute = newEvent.due.getMinutes();
										var seconds = "00";

										month = ( month > 9 ) ? month : "0"+month;
										day = ( day > 9 ) ? day : "0"+day;
										hour = ( hour > 9 ) ? hour : "0"+hour;
										minute = ( minute > 9 ) ? minute : "0"+minute;

										newEvent.start = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.end = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
										newEvent.due_date = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
									}

									newEvent.allDay = event.allDay;
									newEvent.assignee_id = event.assignee_id;
									newEvent.association_id = event.association_id;
									newEvent.association_type = event.association_type;
									newEvent.category_id = event.category_id;
									newEvent.completed = event.completed;
									newEvent.description = event.description;
									newEvent.name = event.name;
									newEvent.owner_id = event.owner_id;
									newEvent.parent_id = event.id;
									newEvent.title = event.title;
									newEvent.type = event.type;
									newEvent.clone = true;
									newEvent.repeats = event.repeats;

									if ( event.type == "event" ){
										nextYear = newEvent.start_time.getFullYear()+1;
									}else{
										nextYear = newEvent.due.getFullYear()+1;
									}

									calEvents.push(jQuery.extend({},newEvent));
									
									counter++;
									if ( counter == 31 ) buffer = true;

								}

							break;

						}

						jQuery("#calendar").fullCalendar('addEventSource',calEvents);
						cloning = false;
						event.cloned = true;

					}
				}

		},
		
		 eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {

	        if (!confirm(Joomla.JText._('COBALT_VERIFY_ALERT'))) {
	        	//revert event
	            revertFunc();
	        }else{
	        	
	        	//form data string
	        	var date 		= new Date();
	        	var modified 	= date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate()+" "+date.getHours()+":"+date.getMinutes()+":00";
	        	var start 		= new Date(event.start);
	        		start		= start.getFullYear()+"-"+(start.getMonth()+1)+"-"+start.getDate()+" 00:00:00";
	        	var end			= new Date(event.end);
	        		end			= end.getFullYear()+"-"+(end.getMonth()+1)+"-"+end.getDate()+" 00:00:00";
	        	var dataString 	= "id="+event.id+"&due_date="+start+"&start_time="+start+"&end_time="+end+"&modified="+modified+"&parent_id="+event.parent_id;
	        	
	        	//make ajax call
				jQuery.ajax({
					
					type	:	"POST",
					url		:	'index.php?task=save&model=event&format=raw&tmpl=component',
					data	:	dataString,
					dataType:	'json',
					success	:	function(data){
						
						//success
			        	modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Successfully Updated!'),Joomla.JText._('COBALT_CALENDAR_SUCCESS','Calendar updated successfully'));
	        	
	        		}
	        		
	        	});
	        }
	
	    },
	    
	    eventResize:function(event,dayDelta,minuteDelta,revertFunc,jsEvent,ui,view) {

	        if (!confirm(Joomla.JText._('COBALT_VERIFY_ALERT'))) {
	        	//revert event
	            revertFunc();
	        }else{
	        	
	        	//form data string
	        	var date 		= new Date();
	        	var modified 	= date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate()+" "+date.getHours()+":"+date.getMinutes()+":00";
	        	var start 		= new Date(event.start);
	        		start		= start.getFullYear()+"-"+(start.getMonth()+1)+"-"+start.getDate()+" 00:00:00";
	        	var end			= new Date(event.end);
	        		end			= end.getFullYear()+"-"+(end.getMonth()+1)+"-"+end.getDate()+" 00:00:00";
	        	var dataString 	= "id="+event.id+"&due_date="+start+"&start_time="+start+"&end_time="+end+"&modified="+modified+"&parent_id="+event.parent_id;
	        	
	        	if ( start != end && event.type == 'task' ) { 
	        			alert("Tasks can only have one due date.");
	        			revertFunc();
	        		} else {
		        	//make ajax call
					jQuery.ajax({
						type	:	"POST",
						url		:	'index.php?task=save&model=event&format=raw&tmpl=component',
						data	:	dataString,
						dataType:	'json',
						success	:	function(data){
							//success
				        	modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
		        		}
		        	});
		        }
	        }
	
	    },
		
		eventClick: function(calEvent,jsEvent,view){
			
			//set event object
			window.calEvent = calEvent;
			showMenu(calEvent,jsEvent);
			
		},
		
		dayClick: function( date, allDay, jsEvent, view ) { 
			
			new_event_date = date;
			//showEventDialog();
			
		}
		
	});

	jQuery(".fc-border-separate tbody td").each(function() {
		jQuery(this).attr('rel','#addTaskEvent');
		jQuery(this).cluetip({activation: 'click', sticky: false, closePosition: 'title', arrows: true, local: true,positionBy: 'bottomTop', topOffset: 20, width: '180px', showTitle: false});
		current_area = "cluetip";
	});


});

/**
 * Display event menu
 */
 function showMenu(calEvent,jsEvent){
	
	//display menu
	if ( menu ){
		//fade out any existing edit menus
		jQuery.when(jQuery("div.edit_menu").fadeOut('fast'))
		.then(function(){
			//reset our zindex layering for past selected event
			if ( curr_cal_event != null ){
				jQuery(curr_cal_event).css('z-index','8');
			}

			//assign the new event
			curr_cal_event = jsEvent.currentTarget;

			//clone menu
			var clone = jQuery("#edit_menu").clone();

			//assign remove button
			jQuery(clone).find('a.remove_event_button').bind('click',function(){
				var date = ( calEvent.due_date != null ) ? calEvent.due_date : calEvent.start_time;
				removeCalendarEvent(calEvent,'single',date);
				menu = false;
				// jQuery(jsEvent.currentTarget).fadeOut('slow',function(){ jQuery(jsEvent.currentTarget).remove(); });
				jQuery("div.edit_menu").fadeOut('fast')
			});


			if ( calEvent.association_type != null ){
				jQuery(clone).find("a.show_event_association").show();
				jQuery(clone).find('a.show_event_association').attr('href',calEvent.association_link);
				jQuery(clone).find('a.show_event_association').html(calEvent.association_link_lang);
			}else{
				jQuery(clone).find("a.show_event_association").hide();
			}
			
			//assign remove series button
			if ( calEvent.repeats != 'none' ){
				jQuery(clone).find('a.remove_event_series_button').show();
				jQuery(clone).find('a.remove_event_series_button').bind('click',function(){
					removeCalendarEvent(calEvent,'series',null);
					menu = false;
					var id = ( calEvent.parent_id == 0 ) ? calEvent.id : calEvent.parent_id;
					jQuery("#calendar").fullCalendar('clientEvents',function(event){
						if ( event.id == id || event.parent_id == id ){
							jQuery("#calendar").fullCalendar('removeEvents',event._id);
						}
					});
					jQuery(jsEvent.currentTarget).fadeOut('slow',function(){ jQuery(jsEvent.currentTarget).remove(); });
					jQuery("div.edit_menu").fadeOut('fast')
				});
			}else{
				jQuery(clone).find('a.remove_event_series_button').hide();
				jQuery("div.edit_menu").fadeOut('fast')	
			}

			//assign edit button
			jQuery(clone).find('a.edit_event_button').bind('click',function(){
				menu = false;
				editEvent(calEvent.id,calEvent.type,calEvent);
				jQuery("div.edit_menu").fadeOut('fast')
			});

			//assign complete button
			jQuery(clone).find('a.complete_event_button').bind('click',function(){
				menu = false;
				var year = calEvent.start.getFullYear();
				var month = calEvent.start.getMonth()+1;
				var day = calEvent.start.getDate();
				var hour = calEvent.start.getHours();
				var minute = calEvent.start.getMinutes();
				var seconds = "00";

				month = ( month > 9 ) ? month : "0"+month;
				day = ( day > 9 ) ? day : "0"+day;
				hour = ( hour > 9 ) ? hour : "0"+hour;
				minute = ( minute > 9 ) ? minute : "0"+minute;

				var date = dateString = end_date = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

				/**
				var year = calEvent.end.getFullYear();
				var month = calEvent.end.getMonth()+1;
				var day = calEvent.end.getDate();
				var hour = calEvent.end.getHours();
				var minute = calEvent.end.getMinutes();
				var seconds = "00";

				month = ( month > 9 ) ? month : "0"+month;
				day = ( day > 9 ) ? day : "0"+day;
				hour = ( hour > 9 ) ? hour : "0"+hour;
				minute = ( minute > 9 ) ? minute : "0"+minute;

				var end_date = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;
				**/

				var dataString = "event_id="+calEvent.id+"&parent_id="+calEvent.parent_id+"&date="+date+"&event_type="+calEvent.type+"&repeats="+calEvent.repeats+"&end_time="+end_date+"&start_time="+date+"&due_date="+date;
				jQuery.ajax({
						url:'index.php?task=markEventComplete&format=raw&tmpl=component',
						type:'post',
						data:dataString,
						dataType:'json',
						success:function(data){
							if ( calEvent.parent_id != 0 ){
								calEvent.id = data.id;
								calEvent.parent_id = data.parent_id;
							}
							jQuery(jsEvent.currentTarget).css('text-decoration','line-through');
							jQuery("div.edit_menu").fadeOut('fast')
						}
				});
			});

			//fade in menu
			jQuery(clone).appendTo(jsEvent.currentTarget).fadeIn('fast');
			jQuery(jsEvent.currentTarget).css('z-index','10');
		});
	}else{
		jQuery(curr_cal_event).css('z-index','8');
		jQuery('#edit_menu').fadeOut("fast");
	}
	//reset menu trigger
	menu = true;
	
}

/**
 * Remove an event from the database
 */
function removeCalendarEvent(calEvent,type,date){

	var year = calEvent.start.getFullYear();
	var month = calEvent.start.getMonth()+1;
	var day = calEvent.start.getDate();
	var hour = calEvent.start.getHours();
	var minute = calEvent.start.getMinutes();
	var seconds = "00";

	month = ( month > 9 ) ? month : "0"+month;
	day = ( day > 9 ) ? day : "0"+day;
	hour = ( hour > 9 ) ? hour : "0"+hour;
	minute = ( minute > 9 ) ? minute : "0"+minute;

	var due_date = dateString = end_date = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

	/**

	var year = calEvent.end.getFullYear();
	var month = calEvent.end.getMonth()+1;
	var day = calEvent.end.getDate();
	var hour = calEvent.end.getHours();
	var minute = calEvent.end.getMinutes();
	var seconds = "00";

	month = ( month > 9 ) ? month : "0"+month;
	day = ( day > 9 ) ? day : "0"+day;
	hour = ( hour > 9 ) ? hour : "0"+hour;
	minute = ( minute > 9 ) ? minute : "0"+minute;

	var end_date = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+seconds;

	**/

	var dataString = "event_id="+calEvent.id+"&parent_id="+calEvent.parent_id+"&type="+type+"&date="+dateString+"&repeats="+calEvent.repeats+"&event_type="+calEvent.type+"&start_time="+dateString+"&end_time="+end_date+"&due_date="+due_date;
	jQuery.ajax({
		url: "index.php?task=removeEvent&tmpl=component&format=raw",
		type:"post",
		data:dataString,
		dataType:'json',
		success:function(data){
			
		}
	});
	jQuery("#calendar").fullCalendar('removeEvents',calEvent._id);
}
