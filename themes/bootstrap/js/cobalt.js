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

        //display alert
        CobaltResponse.alertMessage(response);
        CobaltResponse.modalAction('.modal',response);
        CobaltResponse.reloadPage(response);
        // Update info in various HTML tags
        if (typeof response.item !== 'undefined') {
            Cobalt.updateStuff(response.item);
        }

        Cobalt.updateDataTables();

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
                        default:
                            console.log('Missing reset '+modalID+' for '+association_type);
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
                                header: '<h3 class="autocomplete-title">Companies</h3>'
                            };

                            CobaltAutocomplete.create({
                                id: 'addTask.deal',
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
                            var DealAutocomplete = CobaltAutocomplete.getConfig('addTask.deal');
                            DealAutocomplete.templates = {
                                header: '<h3 class="autocomplete-title">Deals</h3>'
                            };

                            CobaltAutocomplete.create({
                                id: 'addTask.person',
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
                            var PersonAutocomplete = CobaltAutocomplete.getConfig('addTask.person');
                            PersonAutocomplete.templates = {
                                header: '<h3 class="autocomplete-title">People</h3>'
                            };

                            $('input[name=associate_name]').typeahead({highlight: true},DealAutocomplete,CompaniesAutocomplete,PersonAutocomplete);

                        });
                });

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

                CobaltResponse.modalAction("#CobaltAjaxModal",{modal: {action: 'show'}});
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
            console.log(response);
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

var Company = {
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
