/**
 * Cobalt objext / namespace
 **/
var Cobalt = {

    dataTables: {},

    /**
     * These methods will be triggered on page load.
     **/
    init: function() {
        this.bindPopovers();
        this.bindTooltips();
        this.bindDropdownItems();
        this.bindDatepickers();
        this.initFormSave();
        this.initDataTables();
        this.initModalCentralize();
    },

    initModalCentralize: function(){
        $('#myModal').on('shown.bs.modal', function() {
            var initModalHeight = $('#modal-dialog').outerHeight(); //give an id to .mobile-dialog
            var userScreenHeight = $(document).outerHeight();
            if (initModalHeight > userScreenHeight) {
                $('#modal-dialog').css('overflow', 'auto'); //set to overflow if no fit
            } else {
                $('#modal-dialog').css('margin-top',
                    (userScreenHeight / 2) - (initModalHeight/2)); //center it if it does fit
            }
        });
    },

    /**
     * Popovers initialization. Special HTML attribute data-content-class
     * is used for popover HTML content. Example of usage at Deal detail.
     **/
    bindPopovers: function() {
        var selector = '[data-toggle="popover"]';
        var popovers = jQuery(selector);
        popovers.click(function(e){
            e.preventDefault();
        });
        jQuery.each(popovers, function(i, popover) {
            popover = jQuery(popover);
            var options = {
                html : true,
                container: "body",
                content: function() {
                    var contentClass = popover.attr('data-content-class');
                    if (contentClass) {
                        return $('.'+contentClass).html();
                    }
                }
            };
            popover.popover(options);
        });
    },

    bindTooltips: function() {
        $('[rel="tooltip"]').tooltip();
    },

    /**
     * Datepickers initialized and forms inside them are ready for sumbit.
     **/
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
                Cobalt.sumbitForm(jQuery(this).closest('form'));
            }
        });
    },

    /**
     * Setting of form submit. Some default request attributes are prefilled,
     * success function triggered.
     **/
    getFormSubmitOptions: function() {
        return {
            beforeSubmit: function(arr, $form, options) {
                // add attributes necessary for AJAX call
                arr.push({'name': 'format', 'value': 'raw'});
                arr.push({'name': 'tmpl', 'value': 'component'});
            },
            success:   function(response, status, xhr, $form) {
                Cobalt.onSaveSuccess(response, status, xhr, $form);
            },
            type:      'post',
            dataType:  'json'
        };
    },

    /**
     * jQuery dataTables initializes together with filters, action toolbar and search.
     **/
    initDataTables: function() {

        var options = {
            'processing': true,
            'serverSide': true,
            'bLengthChange': false,
            'sDom': '<"top"l>rti<"bottom"p><"clear">',
            'ajax': 'index.php?format=raw&task=datatable&loc='+loc,
            'fnDrawCallback': function(oSettings) {
                Cobalt.bindPopovers();
                Cobalt.bindDropdownItems();
            }
        };

        var filters = {};
console.log(dataTableColumns);
        if (typeof dataTableColumns === 'object') {
            options.columns = dataTableColumns;

            // get default ordering
            // if (typeof order_col !== 'undefined') {
            //     jQuery.each(options.columns, function(i, column) {
            //         if (typeof column.ordering !== 'undefined' &&
            //             column.ordering === order_col) {
            //             options.order = [[ i, order_dir ]];
            //         }
            //     });
            // }
        }
console.log(options.columns);
        var table = jQuery('table.data-table');
        var datatable = table.DataTable(options);
        var searchbox = jQuery('.datatable-searchbox');

        // set filter
        jQuery('.filter-sentence .dropdown-menu a').click(function(e) {
            e.preventDefault();
            var link = jQuery(this);
            var filterType = link.closest('ul.dropdown-menu').attr('data-filter');
            var filterValue = link.attr('data-filter-value');
            var dropdownLabel = link.closest('.dropdown').find('.dropdown-label');
            dropdownLabel.text(link.text());
            filters[filterType] = filterValue;
            datatable.search(JSON.stringify(filters)).draw();
        });

        // set filter
        searchbox.keyup(function() {
            filters.search = jQuery(this).val();
            datatable.search(JSON.stringify(filters)).draw();
        });

        // store datatable to hash object so it can be used later.
        Cobalt.dataTables[table.attr('id')] = datatable;

        // Toggle action bar
        table.change(function() {
            if (table.find('input:checkbox:checked').length > 0) {
                jQuery('#list_edit_actions').show('fast');
            } else {
                jQuery('#list_edit_actions').hide('fast');
            }
        });
    },

    /**
     * Initialize jQuery form submit plugin.
     * Each form[data-ajax="1"] will be submitted via AJAX.
     **/
    initFormSave: function(options) {

        if(!options) {
            otpions = this.getFormSubmitOptions();
        }

        // bind form using 'ajaxForm' 
        jQuery('form[data-ajax="1"]').submit(function() {
            jQuery(this).ajaxSubmit(options);
            return false;
        });
    },

    /**
     * This is called each time an AJAX save is successfull.
     * Here should be initialized all HTML updates depending on response.
     **/
    onSaveSuccess: function(response) {
        if (typeof response.alert !== 'undefined') {
            Cobalt.modalMessage(Joomla.JText._('COM_PANTASSO_SUCCESS_HEADER'), response.alert.message, response.alert.type);
        }
        // Update info in various HTML tags
        if (typeof response.item !== 'undefined') {
            $('.modal').modal('hide');
            Cobalt.updateStuff(response.item);
        }

        Cobalt.updateDataTables();
    },

    /**
     * Sumbits any form contained in modal box via AJAX. Submit button must be in the same modal.
     * @param HTML element object
     **/
    sumbitModalForm: function(button) {
        var modal = jQuery(button).closest('.modal');
        this.sumbitForm(modal.find('form'));
        modal.modal('hide');
    },

    /**
     * Sumbits any form via AJAX.
     * @param HTML element object
     **/
    sumbitForm: function(form) {
        jQuery(form).ajaxSubmit(Cobalt.getFormSubmitOptions());
        // prevent from classic form submit
        return false;
    },

    /**
     * Saves any JS object or array. Data must contain info about model and task.
     **/
    save: function(data) {
        jQuery.post('index.php', data, function(response) {
            try {
                response = $.parseJSON(response);
            } catch (e) {
                // not json
            }
            Cobalt.onSaveSuccess(response);
        });
    },

    /**
     * Request any JS object or array. Request must contain data(must contain task), onSuccess
     */
    editModalForm: function (data, modalID) {
        jQuery.post('index.php', data, function(response) {
            try {
                response = $.parseJSON(response);
                //display alert
                if (typeof response.alert !== 'undefined') {
                    Cobalt.modalMessage(Joomla.JText._('COM_PANTASSO_SUCCESS_HEADER'), response.alert.message, response.alert.type);
                }
                //bind item
                if (typeof response.item !== 'undefined') {
                    jQuery.each(response.item, function(name, value) {
                        if (value === null) {
                            value = '';
                        }
                        var field = jQuery('[name="'+name+'"]');
                        if (field.length) {
                            field.val(value);
                        }
                    });
                }
                //display modal
                if (typeof response.modal !== 'undefined') {
                    jQuery(modalID).modal(response.modal.action);
                }
            } catch (e) {
                // not json
            }
        });
    },

    /**
     * Update all HTML elements which has ID = data.param+'_'+itemId.
     **/
    updateStuff: function(data) {
        var itemId = data.id;
        jQuery.each(data, function(name, value) {
            if (value === null) {
                value = '';
            }
            var element = jQuery('#'+name+'_'+itemId);
            var field = jQuery('[name="'+name+'"]');
            if (element.length) {
                element.text(value);
            }
            if (field.length) {
                field.val(value);
            }
        });
    },

    /**
     * All dataTables from Cobalt.dataTables variable will be reloaded.
     **/
    updateDataTables: function() {
        for (var id in Cobalt.dataTables) {
            if (typeof Cobalt.dataTables[id] === 'object') {
                Cobalt.dataTables[id].ajax.reload();
            }
        }
    },

    /**
     * Displays modal message about AJAX action result for 2 sec.
     **/
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

    /**
     * Bind drobdowns and make links save-able. 
     * Example on deal detail.
     **/
    bindDropdownItems: function () {
        jQuery('.dropdown_item').click(function() {
            var link = jQuery(this),
                data = {
                    'model': link.attr('data-item'),
                    'id': link.attr('data-item-id'),
                    'task': 'save',
                    'format': 'raw'
                };
            data[link.attr('data-field')] = link.attr('data-value');

            Cobalt.save(data);
        });
    },

    /**
     * Deletes checked rows from the table.
     **/
    deleteListItems: function() {
        var itemIds = [];
        jQuery("input[name='ids\\[\\]']:checked").each(function() {
            itemIds.push(jQuery(this).val());
        });
        var data = {'item_id': itemIds,'item_type': loc, 'task': 'trash', 'format': 'raw'};
        Cobalt.save(data);
    },

    /**
     * Selects all checkboxes in a table.
     **/
    selectAll: function(source) {

        if ( typeof source === 'object' ) {
            checkboxes = [];
            var rows = jQuery(source).closest('table').find('tr');
            jQuery(rows).each(function(index, tr) {
                var td = jQuery(tr).find('td:first');
                var checkbox = jQuery(td).find('input:checkbox');
                if (checkbox.length) {
                    checkboxes.push(jQuery(checkbox));
                }
            });
        } else {
            checkboxes = jQuery('[name="ids[]"]');
        }

        jQuery(checkboxes).each(function(index, checkbox) {
            jQuery(checkbox).prop('checked', source.checked);
        });

        // trigger change event for action bar toggle
        $('table.dataTable').trigger('change');
    },

    showDealContactsDialogModal: function(deal_id) {
        jQuery.ajax({
            url:'index.php?view=contacts&format=raw&tmpl=component&deal_id='+deal_id,
            type:'GET',
            dataType:'html',
            success:function(data) {
                jQuery("#CobaltAjaxModalBody").html(data);
                jQuery("#CobaltAjaxModalHeader").text(ucwords(Joomla.JText._("COBALT_CONTACTS")));
                jQuery("#CobaltAjaxModal").modal('show');
            }
        });
    }
};

/**
 * Cobalt JS initialization
 **/
jQuery(function() {
    Cobalt.init();
});

/**
 * Global functions
 **/

/**
 * String to uppercase
 **/
function ucwords(str) {
    return (str + '')
        .replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function($1) {
            return $1.toUpperCase();
        });
}

/**
 * Function for translations taken from Joomla
 **/
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
