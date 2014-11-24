<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

$document = $this->document;
$app = \Cobalt\Factory::getApplication();
$raw = $app->input->get('format');
$return = $app->input->getBase64('return', base64_encode($app->get('uri.request')));
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span><span class="sr-only">
            <?php echo ucwords(TextHelper::_('COBALT_CLOSE')); ?>
        </span>
    </button>
    <h3 class="modal-title" id="dealModal">
        <?php echo ucwords(TextHelper::_('COBALT_UPLOAD_DOCUMENT')); ?>
    </h3>
</div>
<div class="modal-body">
    <form id="upload_form" method="post" name="new_deal" action="<?php echo RouteHelper::_('index.php?task=upload'); ?>" enctype="multipart/form-data"  role="form">
        <?php if (!$raw) { ?>
            <div class="page-header">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-success"onclick="Cobalt.sumbitModalForm(this)"><?php echo TextHelper::_('COBALT_SAVE_BUTTON'); ?></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo TextHelper::_('COBALT_CANCEL_BUTTON'); ?></button>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label" for="name"><?php echo TextHelper::_('COBALT_ASSIGN_TO'); ?><span class="required">*</span></label>
                    <div class="controls"><input type="text" class="form-control" name="associate_name" placeholder="<?php echo TextHelper::_('COBALT_DEAL_NAME_NULL'); ?>" value="" /></div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label" for="amount"><?php echo TextHelper::_('COBALT_UPLOAD_FILE'); ?></label>
                    <div class="controls">
                        <div class="input-group">
                            <div class="btn-group">
                                <div class="btn btn-default btn-file">
                                    <i class="glyphicon glyphicon-plus"></i>  <?php echo TextHelper::_('COBALT_UPLOAD_FILE'); ?> <input type="file" id="upload_input_invisible" name="document" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="association_type" />
        <input type="hidden" name="association_id" />
        <input type="hidden" name="return" value="<?php echo $return; ?>" />
    </form>
</div>
<div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
        <?php echo ucwords(TextHelper::_('COBALT_CANCEL')); ?>
    </button>
</div>
<script>
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
    jQuery('#upload_input_invisible').change(function() {
        jQuery('#upload_form').submit();
    });
</script>
