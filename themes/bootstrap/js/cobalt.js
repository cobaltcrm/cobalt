var Cobalt = {

    init: function() {
        this.bindPopovers();
        this.bindTooltips();
        this.bindDropdownItems();
    },

    bindPopovers: function() {
        $('[rel="tooltip"]').tooltip({
            container: "body"
        });
    },

    bindTooltips: function() {
        $('[rel="popover"]').popover();
    },

    bindDatepickers: function() {
        jQuery('.date_input').datepicker({
            format:userDateFormat,
        });
        jQuery(".date_input").on('changeDate',function(event){
            var selectedYear = event.date.getFullYear(),
                selectedMonth = event.date.getMonth()+ 1,
                selectedDay = event.date.getDate(),
                date = selectedYear+"-"+selectedMonth+"-"+selectedDay;

            jQuery("#"+jQuery(event.currentTarget).attr('id')+'_hidden').val(date);
            jQuery(this).datepicker('hide');
            if ( jQuery(this).hasClass('editable-modal-datepicker') ){
                Cobalt.saveEditableModal(jQuery(this).attr('id')+"_form");
            }
        });
    },

    saveItem: function (formId) {
        var dataObj = {},
            type = null,
            ajax = true,
            form = jQuery('#'+formId),
            formInputs = form.find(' :input');

        formInputs.each(function() {
            var thisInput = jQuery(this);

            if (this.name == "jsrefresh" && this.value=="1") {
                form.submit();
                form.parentsUntil('div.modal').parent('div.modal').modal('hide');
                ajax = false;
                return true;
            }

            if (this.type != "button") {
                dataObj[this.name] = (this.type == 'checkbox' || this.type == 'radio') ? ((thisInput.is(':checked')) ? 1 : 0) : thisInput.val();
            }

            if (this.name == "model") {
                type = dataObj[this.name];
            }

            return true;
        });

        if (ajax) {
            jQuery.ajax({
                type:'post',
                url:base_url+'index.php?task=save&format=raw',
                data:dataObj,
                dataType:'json',
                success:function(data){
                    if ( data.id > 0 ){

                        if (type != "tasklist") {
                            Cobalt.newListItem(data, type);
                        }

                        if ( type == "tasklist" ){
                            Cobalt.updateTaskLists();
                        }

                        jQuery('div.modal').modal('hide');

                    } else {

                        Cobalt.modalMessage(Joomla.JText._('COM_PANTASSO_ERROR_HEADER'))

                    }
                }
            });

        }
    },

    modalMessage: function (heading, message, autoclose) {

        jQuery("#alertMessageHeader").html(heading);
        // jQuery("#alertMessageBody").html(message);
        jQuery("#alertMessage").animate({top:"60px",opacity:1},300);
        setTimeout(function(){
            jQuery("#alertMessage").animate({top:"0px",opacity:0},300);
        },2000);
    },

    newListItem: function (data, type) {

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
                var taskItem = jQuery('a.task_list_'+data.id),
                    listItem = jQuery('#list_row_'+data.id);

                if (type == "tasklist") {
                    taskItem.html(data.name);
                    taskItem.effect("highlight",2000);
                }else{
                    if (listItem.length != 0) {
                        listItem.replaceWith(html);
                    }else{
                        jQuery("#list").prepend(html);
                    }

                    listItem.find('td').effect('highlight', 2000);
                }

                Cobalt.bindDatepickers();
                Cobalt.bindTooltips();
                Cobalt.bindPopovers();
            }
        });
    },

    updateTaskLists: function () {

    },

    saveEditableModal: function (clickedButton) {

        var button = jQuery(clickedButton),
            form = jQuery(button.closest('form')),
            fields = form.find('input'),
            dataString = "",
            item_id = id,
            model = "";

        jQuery(fields).each(function() {
            if ( this.type != "button" ) {
                if (this.type == 'checkbox' || this.type == 'radio') {
                    var val = jQuery(this).is(':checked');
                } else {
                    var val = jQuery(this).val();
                }
                dataString += "&"+this.name+"="+val;
                if ( this.name == "item_id" ){
                    item_id = jQuery(this).val();
                }
                if ( this.name == "item_type" ){
                    model = jQuery(this).val();
                }
            }
        });

        if ( model == "" ) {
            switch ( loc ) {
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
            success:function(data) {
                jQuery(fields).each(function() {
                    if ( this.type != "button" ) {
                        if (this.type == 'checkbox' || this.type == 'radio') {
                            var val = jQuery(this).is(':checked');
                        } else {
                            var val = jQuery(this).val();
                        }
                        val = ( val.replace(/ /g,"").length > 0 ) ? val : Joomla.JText._('COBALT_CLICK_TO_EDIT');
                        // val = nl2br(val); // @TODO: define nl2br function
                        jQuery("#editable_"+this.name).children('a').text(val);
                        jQuery("#editable_"+this.name).show();
                        jQuery("#editable_"+this.name+"_area").hide();
                        if ( this.name == "twitter_user" || 
                            this.name == "facebook_url" || 
                            this.name == "linkedin_url" || 
                            this.name == "aim" || 
                            this.name == "flickr_url" || 
                            this.name == "youtube_url" ) {

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

                            if ( url != "" ) {
                                var name = this.name.replace('_url','').replace('_user','');
                                jQuery("#editable_"+name+"_container_"+item_id).html("<a href='"+url+"'><div class='"+name+"_light'></div></a>");
                            }
                        }
                    }
                });
                Cobalt.modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
                Cobalt.closeEditableModal();
            }
        });
    },

    closeEditableModal: function () {
        jQuery("body").find('a').popover('hide');
    },

    bindDropdownItems: function () {

        jQuery('.dropdown_item').live('click', function() {
            var base = jQuery(this)
                id = base.parentsUntil('div.filters').parent('div.filters').attr('id')+"_link";

            jQuery("#"+id).html(base.html());

            if ( typeof base.attr('data-value') !== 'undefined' ){
                Cobalt.ajaxSaveModal(base);
            }
        });
    },

    ajaxSaveModal: function (ele){
        var item_id = jQuery(ele).attr('data-item-id');
        var item_type = jQuery(ele).attr('data-item');
        var value_type = jQuery(ele).attr('data-field');
        var new_value = jQuery(ele).attr('data-value');

        dataString = "item_id="+item_id+"&item_type="+item_type+"&field="+value_type+"&value="+new_value;

        jQuery.ajax({
            url:'index.php?task=saveajax&format=raw&tmpl=component',
            type:'POST',
            data:dataString,
            dataType:'JSON',
            success:function(data){
                modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
                if ( item_type == "deal" && value_type == "stage_id" ){
                    if ( data.closed == true ){
                        actual_close = data.actual_close;
                        actual_close_formatted = data.actual_close_formatted;
                        jQuery("#actual_close").val(actual_close_formatted);
                        jQuery("#actual_close_hidden").val(actual_close);
                        jQuery("#expected_close_container").hide();
                        jQuery("#actual_close_container").show();
                    } else {
                        expeced_close = data.expeced_close;
                        expeced_close_formatted = data.expected_close_formatted;
                        jQuery("#expeced_close").val(expeced_close_formatted);
                        jQuery("#expected_close_hidden").val(expeced_close);
                        jQuery("#actual_close_container").hide();
                        jQuery("#expected_close_container").show();
                    }
                }
                if ( loc == "deals" ){
                    expected_close = data.expected_close_formatted;
                    actual_close = data.actual_close_formatted;
                    if ( data.closed == true ){
                        jQuery("#expected_close_"+data.id).html(expected_close);
                        jQuery("#actual_close"+data.id).html(actual_close);
                    }else{
                        jQuery("#actual_close"+data.id).html(Joomla.JText._('COBALT_ACTIVE_DEAL'));
                    }
                }
            }
        });
    },

    saveProfileItem: function(button) {

        var formData = '';
        button = jQuery(button);
        var form = button.closest('.modal-content').find('form');

        jQuery.ajax({
            type: 'post',
            url: base_url+'index.php?task=save&format=raw',
            data: form.serialize(),
            dataType: 'json',
            success:function(data) {
                console.log(data);
                if ( data.id > 0 ) {
                    Cobalt.updateProfileItem(data);
                } else {
                    Cobalt.modalMessage(Joomla.JText._('COM_PANTASSO_ERROR_HEADER'))
                }
                jQuery(".modal").modal('hide');
            }
        });
    },

    updateProfileItem: function(data) {
        // @TODO: repair commented lines
        jQuery.each(data, function(name, value) {
            if ( jQuery("#"+name+"_"+data.id).text() != value ) {
                switch ( name ){
                    case "status_name":
                        jQuery("#"+name+"_"+data.id).attr('class','deal-status-'+value);
                    break;
                    case "stage_name":
                        jQuery("#"+name+"_"+data.id).attr('title',Joomla.JText._('COBALT_STAGE')+": "+ucwords(value));
                    break;
                    case "percent":
                        // @TODO: It's better to use user-defined color then to calculate it.
                        // var color = getColorForPercentage(value/100);
                        // var colorDark = getColorForPercentage((value-20)/100);
                        // var style = "background-image: -moz-linear-gradient(top,"+color+","+colorDark+");background-image: -webkit-gradient(linear,0 0,0 100%,from("+color+"),to("+colorDark+"));background-image: -webkit-linear-gradient(top,"+color+","+colorDark+");background-image: -o-linear-gradient(top,"+color+","+colorDark+");background-image: linear-gradient(to bottom,"+color+","+colorDark+");background-color:"+color+" !important; ";
                        // jQuery("#"+name+"_"+data.id).attr('style',style);
                        // jQuery("#"+name+"_"+data.id).css('width',value+"%");
                    break;
                    default:
                        jQuery("#"+name+"_"+data.id).html(value);
                    break;
                }
                // jQuery("#"+name+"_"+data.id).effect("highlight",2000);
            }
        });
    },

    addTaskEvent: function(type) {
        
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
            type    :   'POST',
            url     :   base_url+'index.php?view=events&layout=edit_'+type+'&tmpl=component&format=raw',
            data    :   dataString,
            success :   function(data){
                
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
                            type    :   'POST',
                            url     :   'index.php?task=getTaskAssociations&format=raw&tmpl=component',
                            dataType:   'json',
                            success :   function(data){
                                    
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

                Cobalt.bindDatepickers();
                
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
};

window.onload = function () {
    Cobalt.init();
};

/**
 * Global functions
 **/

function ucwords(str) {
  return (str + '')
    .replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
      return $1.toUpperCase();
    });
}

var Joomla = {
    JText: {
            strings: {},
            '_': function(key, def) {
                return typeof this.strings[key.toUpperCase()] !== 'undefined' ? this.strings[key.toUpperCase()] : def;
        },
        load: function(object) {
            for (var key in object) {
                this.strings[key.toUpperCase()] = object[key];
            }
            return this;
        }
    }
};
