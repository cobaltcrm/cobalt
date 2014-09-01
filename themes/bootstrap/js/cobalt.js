var Cobalt = {

    init: function() {
        this.bindPopovers();
        this.bindTooltips();
        this.bindDropdownItems();
        this.initFormSave();
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

    getFormSubmitOptions: function() {
        return { 
            beforeSubmit: function(arr, $form, options) {      
                // add attributes necessary for AJAX call
                arr.push({'name': 'format', 'value': 'raw'});
                arr.push({'name': 'tmpl', 'value': 'component'});         
            },
            success:   function(response, status, xhr, $form) {
                Cobalt.onFormSaveSuccess(response, status, xhr, $form);
            },
            type:      'post',
            dataType:  'json'
        }; 
    },

    initFormSave: function(options) {
        // initialize jQuery form submit plugin
     
        if(!options) {
            otpions = this.getFormSubmitOptions();
        }

        // bind form using 'ajaxForm' 
        $('form').submit(function() { 
            // inside event callbacks 'this' is the DOM element so we first 
            // wrap it in a jQuery object and then invoke ajaxSubmit 
            $(this).ajaxSubmit(options); 
     
            // !!! Important !!! 
            // always return false to prevent standard browser submit and page navigation 
            return false; 
        }); 
    },

    onFormSaveSuccess: function(response) {
                    console.log(response.alert);
        if (typeof response.alert !== 'undefined') {
            Cobalt.modalMessage(Joomla.JText._('COM_PANTASSO_SUCCESS_HEADER'), response.alert.message, response.alert.type);
        }
        if (typeof response.item !== 'undefined') {
            $('.modal').modal('hide');
            Cobalt.updateStuff(response.item);
        }
    },

    sumbitModalForm: function(button) {
        jQuery(button).closest('.modal').find('form').ajaxSubmit(Cobalt.getFormSubmitOptions());
    },

    updateStuff: function(data) {
        var itemId = data.id;
        jQuery.each(data, function(name, value) {
            if (value === null) {
                value = '';
            }
            var element = jQuery('#'+name+'_'+itemId);
            if (element.length) {
                if (element.prop("tagName") === 'SPAN' || element.prop("tagName") === 'DIV') {
                    element.text(value);
                } else if (element.prop("tagName") === 'INPUT') {
                    element.val(value);
                }
            }
        });
    },

    modalMessage: function (heading, message, type, autoclose) {
        var html = '<div class="alert alert-flying alert-'+type+' alert-dismissible" role="alert">';
        html += '<button type="button" class="close" data-dismiss="alert">';
        html += '<span aria-hidden="true">&times;</span>';
        html += '</button>';
        if (typeof heading !== 'undefined') {
            html += '<strong>'+heading+'</strong>';
        }
        if (typeof message !== 'undefined') {
            html += message;
        }
        html += '</div>';
        var alert = jQuery(html);
        jQuery('body').append(alert);
        alert.animate({top: "60px", opacity: 1}, 300);
        if (autoclose !== false) {
            setTimeout(function() {
                alert.animate({top: "0px" ,opacity: 0}, 300);
            }, 2000);
        }
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
