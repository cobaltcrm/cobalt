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
        this.initDocumentUploader();
    },

    initDocumentUploader: function(){
        $('#upload_button').click(function(){
            $('#upload_form').submit();
        });
        $('#upload_input_invisible').change(function() {
            $('#upload_form').submit();
        });
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
            format: userDateFormat,
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

        if (typeof loc == 'undefined') {
            console.log('Cant Initialize Datatables, loc is undefined');
            return false;
        }

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

        if (typeof dataTableColumns === 'object') {
            options.columns = dataTableColumns;

            options.language = {
            		"sEmptyTable": Joomla.JText._("COBALT_DATATABLE_NO_DATA_AVAILABLE_IN_TABLE"),
            		"sInfo": Joomla.JText._("COBALT_DATATABLE_SHOWING_START_TO_END_ENTRIES"),
            		"sInfoEmpty": Joomla.JText._("COBALT_DATATABLE_SHOWING_ZERO_TO_ZERO_OF_ZERO_ENTRIES"),
            		"sInfoFiltered": Joomla.JText._("COBALT_DATATABLE_FILTERED_TOTAL_ENTRIES"),
            		"sInfoThousands": Joomla.JText._("COBALT_DATATABLE_INFO_THOUSANDS"),
            		"sLengthMenu": Joomla.JText._("COBALT_DATATABLE_SHOW_MENU_ENTRIES"),
            		"sLoadingRecords": Joomla.JText._("COBALT_DATATABLE_LOADING"),
            		"sProcessing": Joomla.JText._("COBALT_DATATABLE_PROCESSING"),
            		"sSearch": Joomla.JText._("COBALT_DATATABLE_SEARCH"),
            		"sZeroRecords": Joomla.JText._("COBALT_DATATABLE_NO_MATCHING_RECORDS_FOUND"),
            		"oPaginate": {
            			"sFirst": Joomla.JText._("COBALT_DATATABLE_FIRST_PAGE"),
            			"sLast": Joomla.JText._("COBALT_DATATABLE_LAST_PAGE"),
            			"sNext": Joomla.JText._("COBALT_DATATABLE_NEXT_PAGE"),
            			"sPrevious": Joomla.JText._("COBALT_DATATABLE_PREVIOUS_PAGE")
            		},
            		"oAria": {
            			"sSortAscending": Joomla.JText._("COBALT_DATATABLE_ACTIVATE_TO_SORT_COLUMN_ASCENDING"),
            			"sSortDescending": Joomla.JText._("COBALT_DATATABLE_ACTIVATE_TO_SORT_COLUMN_DESCENDING")
            		}
            };
            
            // get default ordering
            if (typeof order_col !== 'undefined') {
                jQuery.each(options.columns, function(i, column) {
                    if (typeof column.ordering !== 'undefined' &&
                        column.ordering === order_col) {
                        options.order = [[ i, order_dir ]];
                    }
                });
            }
        }

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
            options = Cobalt.getFormSubmitOptions();
        }

        // bind form using 'ajaxForm' 
        var form = jQuery('form[data-ajax="1"]');
        form.submit(function() {
            Cobalt.makeFormBusy(form);
            jQuery(this).ajaxSubmit(options);
            return false;
        });
    },

    /**
     * This is called each time an AJAX save is successfull.
     * Here should be initialized all HTML updates depending on response.
     **/
    onSaveSuccess: function(response, status, xhr, form) {
        Cobalt.stopFormBeingBusy(form);

        Cobalt.updateDataTables();

        //display alert
        CobaltResponse.alertMessage(response);
        CobaltResponse.modalAction('.modal', response);
        CobaltResponse.reloadPage(response);
        // Update info in various HTML tags
        if (typeof response.item !== 'undefined') {
            Cobalt.updateStuff(response.item);
        }

        // Dispatch Events
        Cobalt.trigger('onSaveSuccess', response);
    },

    /**
     * Add Event
     */
    on: function (event, closure) {
        jQuery(Cobalt).on( event, closure);
    },

    /**
     * Trigger a event
     */
    trigger: function (event, params) {
        jQuery(Cobalt).trigger( event, params );
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
            Cobalt.trigger('onSaveSuccess', response);
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
                CobaltResponse.alertMessage(response);
                CobaltResponse.modalAction(modalID,response);
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
        jQuery('.dropdown_item').click(function(e) {
            e.preventDefault();
            var link = jQuery(this),
                data = {
                    'model': link.attr('data-item'),
                    'id': link.attr('data-item-id'),
                    'task': 'save',
                    'format': 'raw'
                };
            data[link.attr('data-field')] = link.attr('data-value');
            Cobalt.link = link;
            Cobalt.save(data);
        });
        Cobalt.on('onSaveSuccess', function(event, response){
            if (typeof Cobalt.link != 'undefined') {
                var id = jQuery(Cobalt.link[0].parentElement.parentElement).attr('aria-labelledby') + '_link';
                jQuery('#'+id+' span').text(jQuery(Cobalt.link).find('span').text());
                Cobalt.link = undefined;
            }
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

        jQuery('#list_edit_actions').hide('fast');
    },

    /**
     * Disables submit button at provided form.
     **/
    makeFormBusy: function(form) {
        jQuery(form)
            .find('input[type="submit"]')
            .attr('disabled', true);
    },

    /**
     * Enables submit button at provided form.
     **/
    stopFormBeingBusy: function(form) {
        jQuery(form)
            .find('input[type="submit"]')
            .attr('disabled', false);
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
            success:function(content) {
                jQuery("#CobaltAjaxModalBody").html(content);
                jQuery("#CobaltAjaxModalHeader").text(ucwords(Joomla.JText._("COBALT_CONTACTS")));

                var response = {};
                response.modal.action = 'show';
                CobaltResponse.alertMessage(response);
                CobaltResponse.modalAction('#CobaltAjaxModal',response);
            }
        });
    },

    /**
     * Clones last list item and puts it below.
     * @param string selector of parent element (ul).
     **/
    cloneItem: function(selector) {
        var parent = jQuery(selector);
        var lastItem = parent.children().last();
        var newItem = lastItem.clone().hide();
        parent.append(newItem);
        newItem.show('fast');
    },

    exportCSV: function(){
        var old_action = jQuery("#list_form").attr('action');
        var old_layout = jQuery("#list_form_layout").val();

        jQuery("#list_form").attr('action','index.php?task=downloadCsv&tmpl=component&format=raw');
        jQuery("#list_form_layout").val('custom_report');
        jQuery("#list_form").append('<input type="hidden" id="export_flag" name="export" value="1" />');
        jQuery("#list_form").submit();
        jQuery("#export_flag").remove();
        jQuery("#list_form").attr('action',old_action);
        jQuery("#list_form_layout").val(old_layout);
    },

    /**
     * Reset a Modal according with modalID and association_type
     */
    resetModalForm: function (button) {
        var modalID = jQuery(button).attr('data-target');

        switch (modalID) {
            case '#ajax_search_person_dialog':
                    $('#person_name').val('');
                    switch (association_type) {
                        case 'company':
                            $('#person_id').val('');
                            break;
                        case 'deal':
                            $('input[name=person_name]').val('');
                            $('#person_id').val('');
                            break;
                        default:
                            console.log('Missing reset '+modalID+' for '+association_type);
                            break;
                    }
                break;
            case '#addNote':
                    $('#note #deal_note').val('');
                    $('#note_category_id').val('');
                    //reset note extra fields according with association type
                    switch (association_type) {
                        case 'company':
                            $('input[name=note_autocomplete_person]').val('');
                            $('#note_person_id').val('');
                            $('input[name=note_autocomplete_deal]').val('');
                            $('#note_deal_id').val('');
                            break;
                        case 'deal':
                            $('input[name=note_autocomplete_person]').val('');
                            $('#note_person_id').val('');
                            break;
                        case 'person':
                            $('input[name=note_autocomplete_deal]').val('');
                            $('#note_deal_id').val('');
                            break;
                        case 'event':
                            break;
                        default:
                            alert('Missing reset '+modalID+' for '+association_type);
                            break;
                    }
                break;
            case '#ajax_search_deal_dialog':
                $('#deal_name').val('');
                switch (association_type) {
                    case 'company':
                        $('#deal_id').val('');
                        break;
                    default:
                        console.log('Missing reset '+modalID+' for '+association_type);
                        break;
                }
                break;
            default:
                console.log('Missing reset configuration for '+modalID);
                break;
        }
    },

    closeTaskEvent: function (type) {
        jQuery("#edit_"+type).dialog('close');
    },

    seekImport: function(seek) {
        var current_import = Cobalt.current_import || 0;
        var next_import = current_import + seek;
        var import_length = jQuery('.imported_row').length;

        jQuery("#editForm").innerHeight(jQuery("#editForm").css('height').replace('px', ''));

        if (next_import >= 0 && next_import <= (import_length - 1)) {
            jQuery("#import_entry_" + current_import).fadeOut('fast', function() {
                jQuery("#viewing_entry").fadeOut('fast',function() {
                    jQuery("#viewing_entry").html(next_import + 1)
                    jQuery("#viewing_entry").fadeIn('fast');
                })
                jQuery("#import_entry_" + next_import).fadeIn('fast');
            });
            Cobalt.current_import = next_import;
        }
    },

    showSiteSearch: function() {

        jQuery("#site_search").slideToggle('fast');
        var searchInput = jQuery("#site_search_input").focus().val('');
        var searchForm = jQuery("#site_search_form");

        var CompaniesAutocomplete = CobaltAutocomplete.create({
            id: 'siteSearch.company',
            object: 'company',
            fields: 'id,name',
            display_key: 'name',
            prefetch: {
                filter: function(list) {
                    return $.map(list, function (item) {
                        item.association_type = 'company'; return item;
                    });
                },
                ajax: {
                    type: 'post',
                    data: {
                        published: 1
                    }
                }
            }
        });

        CompaniesAutocomplete.templates = {
            header: '<h3 class="autocomplete-title">'+Joomla.JText._("COBALT_COMPANY_HEADER")+'</h3>'
        };

        var DealAutocomplete = CobaltAutocomplete.create({
            id: 'siteSearch.deal',
            object: 'deal',
            fields: 'id,name',
            display_key: 'name',
            prefetch: {
                filter: function(list) {
                    return $.map(list, function (item) {
                        item.association_type = 'deal'; return item;
                    });
                },
                ajax: {
                    type: 'post',
                    data: {
                        published: 1
                    }
                }
            }
        });

        DealAutocomplete.templates = {
            header: '<h3 class="autocomplete-title">'+Joomla.JText._("COBALT_DEALS_HEADER")+'</h3>'
        };

        var PersonAutocomplete = CobaltAutocomplete.create({
            id: 'siteSearch.person',
            object: 'people',
            fields: 'id,first_name,last_name',
            display_key: 'name',
            prefetch: {
                filter: function(list) {
                    return $.map(list, function (item) {
                        item.association_type = 'person'; item.name = item.first_name+' '+item.last_name; return item;
                    });
                },
                ajax: {
                    type: 'post',
                    data: {
                        published: 1
                    }
                }
            }
        });

        PersonAutocomplete.templates = {
            header: '<h3 class="autocomplete-title">'+Joomla.JText._("COBALT_PEOPLE_HEADER")+'</h3>'
        };

        searchInput
        .typeahead({highlight: true}, DealAutocomplete, CompaniesAutocomplete, PersonAutocomplete)
        .on('typeahead:selected', function(event, item, name) {
            var view = '';
            if (item.association_type === 'deal') {
                view = 'deals';
            } else if (item.association_type === 'company') {
                view = 'companies';
            } else if (item.association_type === 'person') {
                view = 'people';
            }
            searchForm.find('input[name=view]').val(view);
            searchForm.find('input[name=layout]').val(item.association_type);
            searchForm.find('input[name=id]').val(item.id);
            searchForm.submit();
        });
    }
};

var CobaltResponse = {

    //display alert
    alertMessage: function (response, custom_message) {
        if (typeof response.alert !== 'undefined') {
            if (typeof response.alert.message != 'undefined' && typeof custom_message != 'undefined') {
                response.alert.message = custom_message;
            }
            Cobalt.modalMessage(Joomla.JText._('COM_PANTASSO_SUCCESS_HEADER'), response.alert.message, response.alert.type);
        }
    },

    //display/hide modal
    modalAction: function (modalID, response) {
        if (typeof response.modal !== 'undefined') {
            jQuery(modalID).modal(response.modal.action);
        }
    },

    //reload page
    reloadPage: function (response)
    {
        if (typeof response.reload !== 'undefined') {
            setTimeout('location.reload()',response.reload);
        }
    }
};

var Deals = {
    initRemoveContact: function(){
        Cobalt.on('onRemoveContact', function(event, response, person_id){
            if ( response.success == true ){
                jQuery("#person_container_"+person_id).remove();
            }
        });
    },

    initAssignContact: function(){
        Cobalt.on('onAssignDealContact', function(event, response, person_id) {
            var primary_contact_id = jQuery("#primary_contact").attr('data-id');
            var new_primary_ele = jQuery('#star_'+person_id);
            var icon = '#primary_contact > i';

            if (typeof primary_contact_id != 'undefined' && primary_contact_id != person_id) {
                jQuery('#primary_contact i').removeClass('glyphicon-star');
                jQuery('#primary_contact i').addClass('glyphicon-star-empty');
                jQuery('#primary_contact').attr('id','star_'+primary_contact_id);
            }

            jQuery(new_primary_ele).attr('id','primary_contact');

            if (jQuery(icon).hasClass('glyphicon-star')) {
                jQuery(icon).removeClass('glyphicon-star').addClass('glyphicon-star-empty');
            } else {
                jQuery(icon).removeClass('glyphicon-star-empty').addClass('glyphicon-star');
            }
        });
    },

    assignPrimaryContact: function (person_id) {
        jQuery.ajax({
                type:'POST',
                url:'index.php?task=save&format=raw&tmpl=component',
                data:'model=deal&id='+deal_id+'&primary_contact_id='+person_id,
                dataType:'JSON',
                success: function(response){
                    CobaltResponse.alertMessage(response,Joomla.JText._('COBALT_UPDATED_PRIMARY_CONTACT'));
                    Cobalt.trigger('onAssignDealContact', [response, person_id]);
                }
        });
    },

    removeContact: function (person_id) {
        if ( confirm(Joomla.JText._('COBALT_DELETE_PERSON_FROM_DEAL_CONFIRM')) ) {
            jQuery.post('index.php', {
                person_id: person_id,
                deal_id: deal_id,
                task: 'removePersonFromDeal',
                format: 'raw',
                tmpl: 'component'
            }, function(response) {
                try {
                    response = $.parseJSON(response);
                } catch (e) {
                    // not json
                }
                Cobalt.trigger('onRemoveContact', [response, person_id]);
            });
        }
    }
};

var CobaltAutocomplete = {
    //bloodhound objects
    bloodhound: {},
    config: {},

    create: function (config) {
        if (typeof config.object == 'undefined') {
            alert('Please send object attribute');
            return false;
        }
        // assign object to id if not exists
        if (typeof config.id == 'undefined') {
            config.id = config.object;
        }
        if (typeof config.fields == 'undefined') {
            config.fields = '';
        }
        var id = config.id;
        var object_type = config.object;

        if (typeof this.bloodhound[id] == 'undefined') {
            if (typeof config.prefetch == 'undefined') {
                config.prefetch = {};
            }
            config.prefetch.url = base_url+'index.php?task=collection&object='+object_type+'&fields='+config.fields+'&format=raw&tmpl=component';

            this.bloodhound[id] = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace(config.display_key),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                prefetch: config.prefetch
            });
        }

        this.bloodhound[id].clearPrefetchCache();
        this.bloodhound[id].initialize();

        //initialize empty config
        if (typeof this.config[id] == 'undefined') {
            this.config[id] = {};
        }

        this.config[id] = {
            displayKey: config.display_key,
            source: this.bloodhound[id].ttAdapter()
        };

        return this.config[id];
    },

    getBloodhound: function (id) {
        return this.bloodhound[id];
    },

    getConfig: function (id) {
        return this.config[id];
    }
};

var Task = {
    add: function(type) {
        if (typeof id == 'undefined') {
            id = 0;
        }
        if (typeof association_type == 'undefined') {
            association_type = 'task';
        }
        jQuery.ajax({
            type	:	'POST',
            url     :   base_url+'index.php?view=events&layout=edit_'+type+'&tmpl=component&format=raw',
            data 	: 	{
                association_type: association_type,
                association_id: id
            },
            dataType:   'text',
            success	:	function(response) {
                jQuery("#edit_task").empty();
                jQuery("#CobaltAjaxModalBody").html(response);
                jQuery("#CobaltAjaxModalHeader").text(ucwords(Joomla.JText._("COBALT_ADDING_"+ucwords(type))));
                jQuery("#CobaltAjaxModalSaveButton").attr("onclick","Cobalt.sumbitModalForm(this)");

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


                            CobaltAutocomplete.create({
                                id: 'addTask.company',
                                object: 'company',
                                fields: 'id,name',
                                display_key: 'name',
                                prefetch: {
                                    filter: function(list) {
                                        return $.map(list, function (item){ item.association_type = 'company'; return item; });
                                    },
                                    ajax: {
                                        type: 'post',
                                        data: {
                                            published: 1
                                        }
                                    }
                                }
                            });
                            var CompaniesAutocomplete = CobaltAutocomplete.getConfig('addTask.company');
                            CompaniesAutocomplete.templates = {
                                header: '<h3 class="autocomplete-title">'+Joomla.JText._("COBALT_COMPANY")+'</h3>'
                            };

                            CobaltAutocomplete.create({
                                id: 'addTask.deal',
                                object: 'deal',
                                fields: 'id,name',
                                display_key: 'name',
                                prefetch: {
                                    filter: function(list) {
                                        return $.map(list, function (item){ item.association_type = 'deal'; return item; });
                                    },
                                    ajax: {
                                        type: 'post',
                                        data: {
                                            published: 1
                                        }
                                    }
                                }
                            });
                            var DealAutocomplete = CobaltAutocomplete.getConfig('addTask.deal');
                            DealAutocomplete.templates = {
                                header: '<h3 class="autocomplete-title">'+Joomla.JText._("COBALT_DEALS_HEADER")+'</h3>'
                            };

                            CobaltAutocomplete.create({
                                id: 'addTask.person',
                                object: 'people',
                                fields: 'id,first_name,last_name',
                                display_key: 'name',
                                prefetch: {
                                    filter: function(list) {
                                        return $.map(list, function (item){ item.association_type = 'person'; item.name = item.first_name+' '+item.last_name; return item; });
                                    },
                                    ajax: {
                                        type: 'post',
                                        data: {
                                            published: 1
                                        }
                                    }
                                }
                            });
                            var PersonAutocomplete = CobaltAutocomplete.getConfig('addTask.person');
                            PersonAutocomplete.templates = {
                                header: '<h3 class="autocomplete-title">'+Joomla.JText._("COBALT_PEOPLE")+'</h3>'
                            };

                            $('input[name=associate_name]').typeahead({highlight: true},DealAutocomplete,CompaniesAutocomplete,PersonAutocomplete).on('typeahead:selected', function(event, item, name){
                                jQuery('input[name=association_type]').val(item.association_type);
                                jQuery('input[name=association_id]').val(item.id);
                            });

                        });
                });

                if ( type == 'event' ) {
                    Cobalt.bindDatepickers();

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

                jQuery('input[name=association_type]').val(association_type);
                jQuery('input[name=association_id]').val(id);
                CobaltResponse.modalAction("#CobaltAjaxModal",{modal: {action: 'show'}});
            }
        });
    },

    updateEventList: function(user,team) {
        //make ajax call for new event listings
        var search_event_id = ( user ) ? user : team;
        var dataString = "";
        if ( user ){
            dataString += 'assignee_id='+search_event_id+"&assignee_filter_type=individual";
        }else{
            dataString += 'assignee_id='+search_event_id+"&assignee_filter_type=team";
        }
        if ( typeof loc !== 'undefined' && typeof id !== 'undefined' ){
            dataString += "&association_type="+loc+"&association_id="+id;
        }
        jQuery.ajax({
            type:'post',
            url: base_url+'index.php?view=events&layout=event_listings&tmpl=comp&format=raw&tmpl=component',
            data: dataString,
            dataType:'html',
            success:function(data){
                //assign new html
                jQuery.when(jQuery("#task_list").empty())
                    .then(function(){
                        jQuery("#task_list").html(data);
                    });

                //update link message
                if ( user ){
                    jQuery("#"+Task.current_area).html(jQuery("#event_user a.filter_user_"+search_event_id).text());
                }else{
                    jQuery("#"+Task.current_area).html(jQuery("#event_user a.filter_team_"+search_event_id).text());
                }
            }
        });
    }
};

var Notes = {
    config: {},

    init: function(){
        this.initEvents();
        this.initModalFormData();
        this.initAutocomplete();
    },

    initModalFormData: function()
    {
        if (typeof deal_id == 'number') {
            $('input[name=deal_id]').val(deal_id);
        }
        if (typeof person_id == 'number') {
            $('input[name=person_id]').val(person_id);
        }
        if (typeof category_id == 'number') {
            $('input[name=category_id]').val(category_id);
        }
        if (typeof company_id == 'number') {
            $('input[name=company_id]').val(company_id);
        }
        if (typeof event_id == 'number') {
            $('input[name=event_id]').val(event_id);
        }
        if (typeof note_id == 'number') {
            $('input[name=note_id]').val(note_id);
        }
    },

    initEvents: function() {
        Cobalt.on('onSaveSuccess', function (event, response){
            // reload notes
            if (typeof response.alert !== 'undefined') {
                if (response.alert.type == 'success') {
                    Notes.reloadNotes();
                }
            }
        });
    },

    initAutocomplete: function() {
        if (typeof association_type != 'string') {
            return false;
        }

        switch (association_type) {
            case 'deal':
                this.addAutocompletePerson();
                break;
            case 'person':
                this.addAutocompleteDeal();
                break;
            case 'company':
                this.addAutocompletePerson();
                this.addAutocompleteDeal();
                break;
        }
    },

    addAutocompletePerson: function() {
        CobaltAutocomplete.create({
            id: 'note.addperson',
            object: 'people',
            fields: 'id,first_name,last_name',
            display_key: 'name',
            prefetch: {
                filter: function(list) {
                    return $.map(list, function (item){ item.name = item.first_name+' '+item.last_name; return item; });
                },
                ajax: {
                    type: 'post',
                    data: {
                        published: 1
                    }
                }
            }
        });
        $('input[name=note_autocomplete_person]').typeahead({
            highlight: true
        },CobaltAutocomplete.getConfig('note.addperson')).on('typeahead:selected', function(event, item, name){
            jQuery('#note_person_id').val(item.id);
        });
        $('#note_autocomplete_person_container').removeClass('hidden');
    },

    addAutocompleteDeal: function() {
        CobaltAutocomplete.create({
            id: 'note.adddeal',
            object: 'deal',
            fields: 'id,name',
            display_key: 'name',
            prefetch: {
                ajax: {
                    type: 'post',
                    data: {
                        published: 1
                    }
                }
            }
        });
        $('input[name=note_autocomplete_deal]').typeahead({
            highlight: true
        },CobaltAutocomplete.getConfig('note.adddeal')).on('typeahead:selected', function(event, item, name){
            jQuery('#note_deal_id').val(item.id);
        });
        $('#note_autocomplete_deal_container').removeClass('hidden');
    },

    reloadNotes: function() {
        if (typeof this.config == 'undefined') {
            alert('Cant Reload Notes');
            return false;
        }
        this.loadMore(this.config.item_type,this.config.object_id, this.config.target_id, this.config.start_id, this.config.limit_id)
    },

    loadMore: function(item_type, object_id, target_id, start_id, limit_id, limit_qtdy) {
        //note state values
        this.config = {
            item_type: item_type,
            object_id: object_id,
            target_id: target_id,
            start_id: start_id,
            limit_id: limit_id,
            limit: limit_qtdy
        };
        jQuery.ajax({
            url: 'index.php?task=GetNotes&item_type='+item_type+'&object_id='+object_id+'&start='+jQuery(start_id).val()+'&limit='+jQuery(limit_id).val()+'&format=raw&tmpl=component',
            type: 'GET',
            dataType: 'JSON',
            success: function(response)
            {
                CobaltResponse.alertMessage(response);
                if (typeof response.html !== 'undefined') {
                    jQuery(target_id).html(response.html);
                }
                if (typeof response.loadmore !== 'undefined') {
                    jQuery(limit_id).val(response.loadmore.limit);
                    jQuery(target_id).append('<button onclick="Notes.loadMore(\''+item_type+'\',\''+object_id+'\',\''+target_id+'\', \''+start_id+'\', \''+limit_id+'\');" class="btn btn-block">Load More</button>');
                }
            }
        });
    },

    removeNote: function(note_id) {
        Cobalt.save({task: 'RemoveAjax', model: 'note', id: note_id, format: 'raw', tmpl: 'component'});
    },

    editNote: function(data, modalID){
        if (typeof this.config !== 'undefined') {
            var limit = $(this.config.limit_id).val();
            limit = limit - this.config.limit;
            if (limit < 0 || limit < this.config.limit) {
                limit = this.config.limit;
            }
            $(this.config.limit_id).val(limit);
        }
        Cobalt.editModalForm(data, modalID);
    }
};

var Calendar = {
    showCalendarTasks: function(){
        jQuery("div.calendar_event").css('display','none');
        jQuery("div.calendar_task").css('display','block');
    },

    showCalendarEvents: function (){
        jQuery("div.calendar_event").css('display','block');
        jQuery("div.calendar_task").css('display','none');
    },

    showAllCalendarEvents: function (){
        jQuery("div.calendar_event").css('display','block');
        jQuery("div.calendar_task").css('display','block');
    },

    initialise: function(){
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

                if ( typeof cloning == 'undefined' || cloning || event.server || event.clone ){

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
                            Cobalt.modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Successfully Updated!'),Joomla.JText._('COBALT_CALENDAR_SUCCESS','Calendar updated successfully'));

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
                                Cobalt.modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
                            }
                        });
                    }
                }

            },

            eventClick: function(calEvent,jsEvent,view){

                //set event object
                window.calEvent = calEvent;
                Calendar.showMenu(calEvent,jsEvent);

            },

            dayClick: function( date, allDay, jsEvent, view ) {

                new_event_date = date;
                //showEventDialog();

            },

            buttonIcons: {
                prev: 'glyphicon glyphicon-chevron-left',
                next: 'glyphicon glyphicon-chevron-right'
            }

        });

        jQuery(".fc-border-separate tbody td").each(function() {
            jQuery(this).attr('rel','#addTaskEvent');
            jQuery(this).cluetip({activation: 'click', sticky: false, closePosition: 'title', arrows: true, local: true,positionBy: 'bottomTop', topOffset: 20, width: '180px', showTitle: false});
            current_area = "cluetip";
        });
    },

    showMenu: function (calEvent,jsEvent){

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
                        Calendar.removeCalendarEvent(calEvent,'single',date);
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
                            Calendar.removeCalendarEvent(calEvent,'series',null);
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
                        jQuery(clone).find('a.remove_event_series_button').hide().parent('li').hide();
                        jQuery("div.edit_menu").fadeOut('fast')
                    }

                    //assign edit button
                    jQuery(clone).find('a.edit_event_button').bind('click',function(){
                        menu = false;
                        Calendar.editEvent(calEvent.id,calEvent.type,calEvent);
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
                        Calendar.markAsComplete(dateString, function(data){
                            if ( calEvent.parent_id != 0 ){
                                calEvent.id = data.id;
                                calEvent.parent_id = data.parent_id;
                            }
                            if (data.success) {
                                jQuery(jsEvent.currentTarget).css('text-decoration','line-through');
                            } else {
                                CobaltResponse.alertMessage({alert: {type: 'danger',message: Joomla.JText._('COBALT_ERROR_MARK_ITEM_COMPLETE')}});
                            }
                            jQuery("div.edit_menu").fadeOut('fast')
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

    },

    markAsComplete: function (postData, onSuccess)
    {
        //default they
        if (typeof onSuccess == 'undefined') {
            onSuccess = function(response){
                CobaltResponse.alertMessage(response);
                CobaltResponse.reloadPage(response);
            };
        }
        jQuery.ajax({
            url: 'index.php?task=markEventComplete&format=raw&tmpl=component',
            type: 'post',
            data: postData,
            dataType: 'json',
            success:function(data){
                onSuccess(data);
            }
        });
    },

    markAsIncomplete: function (postData, onSuccess)
    {
        //default they
        if (typeof onSuccess == 'undefined') {
            onSuccess = function(response){
                CobaltResponse.alertMessage(response);
                CobaltResponse.reloadPage(response);
            };
        }
        jQuery.ajax({
            url: 'index.php?task=markEventComplete&format=raw&tmpl=component',
            type: 'post',
            data: postData,
            dataType: 'json',
            success:function(data){
                onSuccess(data);
            }
        });
    },

    removeCalendarEvent: function (calEvent,type,date){

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
    },

    editEvent: function(id,type,event){

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

                jQuery("#CobaltAjaxModalSaveButton").attr("onclick","Cobalt.saveAjax('edit_"+type+"','event')");
                jQuery("#CobaltAjaxModalCloseButton").attr("onclick","Cobalt.closeTaskEvent('"+type+"');");

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

                Cobalt.bindDatepickers();

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

    },

    showEventContactsDialogModal: function(event_id){
        jQuery.ajax({
            url:'index.php?view=contacts&format=raw&tmpl=component&event_id='+event_id,
            type:'GET',
            dataType:'html',
            success:function(data){
                jQuery("#CobaltAjaxModalBody").html(data);
                jQuery("#CobaltAjaxModal").modal('show');
            }
        });
    },

    openNoteModal: function(id,type){

        jQuery.ajax({
            type	:	'POST',
            url		:	'index.php?view=note&type='+type+'&id='+id+'&format=raw&tmpl=component',
            success	:	function(data){

                //clear past html
                jQuery("#edit_task").empty();
                jQuery("#edit_event").empty();

                jQuery("#noteModalBody").html(data);
                jQuery("#noteModalHeaderTitle").text(Joomla.JText._("COBALT_EDIT_NOTES"));
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

                jQuery("#note_modal").modal('show');

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
};

var Company = {
    checkName: function (company_name, onSuccess) {
        if(company_name.length < 3) {
            return;
        }
        jQuery.ajax({
            url: base_url+'index.php?task=checkCompanyName&tmpl=component&format=raw',
            type:'POST',
            data: {company_name: company_name},
            dataType: 'JSON',
            success: function(response){
                onSuccess(response);
            }
        });
    },

    addPerson: function () {
        CobaltAutocomplete.create({
            id: 'company.addperson',
            object: 'people',
            fields: 'id,first_name,last_name',
            display_key: 'name',
            prefetch: {
                filter: function(list) {
                    return $.map(list, function (item){ item.name = item.first_name+' '+item.last_name; return item; });
                },
                ajax: {
                    type: 'post',
                    data: {
                        published: 1
                    }
                }
            }
        });
        $('#person_name').typeahead({
            highlight: true
        },CobaltAutocomplete.getConfig('company.addperson')).on('typeahead:selected', function(event, item, name){
            jQuery('#person_id').val(item.id);
        });
        $('#note_company_id').val(company_id);
    },

    addDeal: function () {
        CobaltAutocomplete.create({
            id: 'company.adddeal',
            object: 'deal',
            fields: 'id,name',
            display_key: 'name',
            prefetch: {
                ajax: {
                    type: 'post',
                    data: {
                        published: 1
                    }
                }
            }
        });
        $('#deal_name').typeahead({
            highlight: true
        },CobaltAutocomplete.getConfig('company.adddeal')).on('typeahead:selected', function(event, item, name){
            jQuery('#deal_id').val(item.id);
        });
        $('#deal_company_id').val(company_id);
    }
};

var Goal = {
    delete: function(area){
        if (confirm(Joomla.JText._('COBALT_DELETE_GOALS'))) {
            //get goal id to delete
            var goal =jQuery(area).parentsUntil('tr').parent('tr');
            var goal_id = jQuery(goal).attr('id');
            //make ajax call
            jQuery.ajax({
                url		: 'index.php?task=deleteGoalEntry&format=raw&tmpl=component',
                type	: 'post',
                data	: 'goal_id='+goal_id,
                dataType: 'json',
                success : function(data){
                    if ( data.error == 0 ){
                        //display success
                        Cobalt.modalMessage(Joomla.JText._('COBALT_SUCCESS_MESSAGE','Success'), Joomla.JText._('COBALT_GENERIC_UPDATED','Successfully updated'));
                        //get goal area to fade out
                        jQuery(goal).fadeOut('fast');
                        jQuery("#goal_"+goal_id).fadeOut("fast");
                    } else {
                        //display success
                        Cobalt.modalMessage(Joomla.JText._('COBALT_ERROR_MESSAGE','COBALT_ERROR_ON_REMOVE'), Joomla.JText._('COBALT_ERROR_MESSAGE','COBALT_ERROR_ON_REMOVE'),'danger');
                    }
                }
            });
        }
    }
};

var Person = {
    checkName: function(person_name, onSuccess) {
        if(person_name.length < 3) {
            return;
        }

        jQuery.ajax({
            url: base_url+'index.php?task=checkPersonName&tmpl=component&format=raw',
            type:'POST',
            data: {person_name: person_name},
            dataType:'JSON',
            success:function(response){
                onSuccess(response);
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
