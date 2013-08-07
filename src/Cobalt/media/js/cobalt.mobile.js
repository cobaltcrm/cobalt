var geocoder = new google.maps.Geocoder();
var latlng = null;
var map = null;

$(document).ready(function(){
  /** clean any forms on the page **/
  //cleanPageForms();
 
  //reset type=date inputs to text
  $( document ).bind( "mobileinit", function(){
    $.mobile.page.prototype.options.degradeInputs.date = true;
  });

  $.datepicker.setDefaults({
    onSelect: function (event,ui) {
      $(ui.settings.altField+'_hidden').val(ui.selectedYear+"-"+("0"+(ui.selectedMonth+1)).slice(-2)+"-"+("0"+(ui.selectedDay)).slice(-2));
    }
  });


 $('#map_canvas').gmap().bind('init',function(event, map) {

     navigator.geolocation.getCurrentPosition(function(position){
        var latlng = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
          geocoder.geocode({'location':latlng}, function(results,status) {
            if(status=='OK') {
              $('#from').val(results[0].formatted_address);
              $('#map_canvas').gmap('displayDirections', 
                {'origin': $('#from').val(), 
                'destination': $('#to').val(), 
                'travelMode':
                google.maps.DirectionsTravelMode.DRIVING }, {},
                function(result, status){
                    if (status === 'OK'){
                        center = result.routes[0].bounds.getCenter();
                        $('#map_canvas').gmap('option', 'center', center);
                    } else {
                    } 
                });
            } else {
              codeAddress();
            }
        });
      });
  });

    $('#submit').click(function() {
      $('#map_canvas').gmap('displayDirections', { 
        'origin': $('#from').val(), 
        'destination': $('#to').val(), 
        'travelMode': google.maps.DirectionsTravelMode.DRIVING 
      }, { 'panel': document.getElementById('directions')}, function(response, status) {
        ( status === 'OK' ) ? $('#results').show() : $('#results').hide();
      });
      return false;
    });

});

function codeAddress() {
    geocoder.geocode( { 'address': $('#to').val()}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        var myOptions = {
        zoom: 8,
        center: results[0].geometry.location,
        mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });
      }
    });
  }

function cleanPageForms(){
  /*
  $("form :input").each(function(){
    if ( $(this)attr('type') != "hidden" ){
      $(this).val('');
    }
  });
   */
}

//add note Entries
function addTaskEntry(){
  
  if(loc=="events") {
    $('#task_edit').submit();
    return;
  }

  //generate data string for ajax call
  var dataString = '';
 
  var $form = $('#task_edit :input');  
  $form.each(function(){
    dataString += "&"+this.name+"="+$(this).val();
  });
  
  //determine which page we are adding a note from
  if ( loc == "company" ){
    dataString += "&association_id="+company_id;
    dataString += "&association_type=company";
  }
  if ( loc == "deal" ){
    dataString += "&association_id="+deal_id;
    dataString += "&association_type=deal";

  }
  if ( loc == "person" ){
    dataString += "&association_id="+person_id;
    dataString += "&association_type=person";

  }

  dataString += "&model=event";

  var due = $('#due_date_hidden').val();
  var t = due.split(/[-]/);

  // Apply each element to the Date function
  var due_date = new Date(t[0], t[1]-1, t[2]);

  //make ajax call
  $.ajax({
    type  : "POST",
    url   : 'index.php?task=save&model=event&format=raw&tmpl=component',
    data  : dataString,
    dataType: 'json',
    success : function(data){


      var date = $.datepicker.formatDate(userDateFormat, due_date);

      var owner = owner_first_name + " " + owner_last_name;
      var task = $("input[name=name]").val();

      var html = '<li>';
          html += '<a href="'+base_url+'index.php?view=events&layout=event&id='+data.id+'">'
          html += '<div class="ui-li-count"><b>'+date+'</b></div>';
          html += '<h3 class="ui-li-heading"><b>'+task+'</b></h3>';
          html += '<p class="ui-li-desc">'+owner+'</p>';         
         html += '</a></li>';

         $("#events").prepend(html);
         $("#events").listview('refresh');
         $form.each(function(){
            $(this).val('');
         });

        alert(Joomla.JText._('COBALT_ADDED_MESSAGE','Added'));
        
    }
  });
  
}

//add note Entries
function addNoteEntry(ele){
  
  //generate data string for ajax call
  var dataString = '';
  if (!ele){
    var $form = $('#note :input');  
  }else{
    var $form = $('#'+ele+' :input');
  }
  
  $form.each(function(){
    dataString += "&"+this.name+"="+$(this).val();
  });
  
  //determine which page we are adding a note from
  if ( loc == "company" ){
    dataString += "&company_id="+company_id;
  }
  if ( loc == "deal" ){
    dataString += "&deal_id="+deal_id;
  }
  if ( loc == "person" ){
    dataString += "&person_id="+person_id;
  }

  dataString += "&model=note";

  //make ajax call
  $.ajax({
    type  : "POST",
    url   : 'index.php?task=save&model=note&format=raw&tmpl=component',
    data  : dataString,
    dataType: 'json',
    success : function(data){

      var today = new Date();
      var date = $.datepicker.formatDate(userDateFormat, today);

      var owner = owner_first_name + " " + owner_last_name;
      var note = $("textarea[name=note]").val();

      var html = '<li>';
          html += '<div class="ui-li-aside"><b>'+date+'</b></div>';
          html += '<h3 class="ui-li-heading"><b>'+owner+'</b></h3>';
          html += '<p class="ui-li-desc">'+note+'</p>';         
         html += '</li>';

         $("#notes").prepend(html);
         $("#notes").listview('refresh');
         $form.each(function(){
            $(this).val('');
         });

        alert(Joomla.JText._('COBALT_ADDED_MESSAGE','Added'));
        
    }
  });
  
}

/** Mark events complete **/
function markEventComplete(event_id){

  var completed_value = $("#completed").is(":checked") ? 1 : 0;
  var dataString = "event_id="+event_id+"&completed="+completed_value;

  $.ajax({
      url:'index.php?task=markEventComplete&format=raw&tmpl=component',
      type:'post',
      data:dataString,
      dataType:'json',
      success:function(data){
        var event_status = ( completed_value == 1 ) ? "INCOMPLETE" : "COMPLETE";
        $("#completed_label .ui-btn-text").text(Joomla.JText._('COBALT_MARK_'+event_status));
        event_bool = completed_value == 1 ? true : false;
        $("input[type='checkbox']").prop("checked",event_bool).checkboxradio("refresh");
        alert(Joomla.JText._('COBALT_ADDED_MESSAGE','Added'));

      }
  });

}

//function to store updated information to user tables    
function save(form){

  var valid = true;

  //Remove Default Placeholder text
  $("[placeholder]").parents("form").submit(function() {
    $(this).find("[placeholder]").each(function() {
      var input = $(this);
      if ($(input).val() == $(input).attr("placeholder")) {
        $(input).val("");
      }
    });
  });
  
  $(form).find(':input').each(function(){
    var input = $(this);
    if ( $(input).hasClass('required') && $(input).val() != "" ){
      if($(input).hasClass('required_highlight')){
        $(input).removeClass('required_highlight');
      }
    }
    if ( $(input).hasClass('required') && $(input).val() == "" ){
      $(input).addClass('required_highlight');
      $(input).focus();
      valid = false;
    }
  });

  if ( valid ){
    return true;
  }else{
    return false;
  }
      
}