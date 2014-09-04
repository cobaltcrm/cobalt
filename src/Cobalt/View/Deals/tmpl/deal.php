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

//define deal
$deal = $this->dealList[0];
?>

<script type="text/javascript">
    var id = <?php echo $deal->id; ?>;
    var deal_id = <?php echo $deal->id; ?>;
    var loc = "deal";
    var AMOUNT = <?php $deal->amount = ( $deal->amount == 0 ) ? 0 : $deal->amount; echo $deal->amount; ?>;
    var archived = <?php echo (int) $deal->archived; ?>;
    var association_type = 'deal';
</script>

<div class="modal fade" id="dealModal" tabindex="-1" role="dialog" aria-labelledby="dealModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>

<iframe id="hidden" name="hidden" style="display:none;width:0px;height:0px;border:0px;"></iframe>

<div class="row-fluid">

    <div class="span8">

        <div class="page-header">
            <div class="btn-group pull-right dropdown">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <?php echo TextHelper::_('COBALT_ACTION_BUTTON'); ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a role="button" href="<?php echo RouteHelper::_('index.php?view=deals&layout=edit&format=raw&tmpl=component&id='.$deal->id); ?>" data-target="#dealModal" data-toggle="modal">
                            <?php echo TextHelper::_('COBALT_EDIT_BUTTON'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" id="archive" >
                            <?php if($deal->archived==0) echo TextHelper::_('COBALT_ARCHIVE'); if($deal->archived==1) echo TextHelper::_('COBALT_UNARCHIVE'); ?>
                        </a>
                    </li>
                    <?php if ( $deal->owner_id == UsersHelper::getUserId() ) { ?>
                        <li><a href="javascript:void(0);" onclick="Cobalt.shareItemDialog();" ><?php echo TextHelper::_('COBALT_SHARE'); ?></a></li>
                    <?php } ?>
                    <li>
                        <?php if ( UsersHelper::canDelete() || $deal->owner_id == UsersHelper::getUserId() ) { ?>
                            <a href="<?php echo RouteHelper::_('index.php?task=trash&item_id='.$deal->id.'&item_type=deals&page_redirect=deals'); ?>" onclick="deleteProfileItem(this)"><?php echo TextHelper::_('COBALT_DELETE'); ?></a>
                        <?php } ?>
                    </li>
                    <li>
                        <a href="<?php echo RouteHelper::_('index.php?view=printFriendly&item_id='.$deal->id.'&layout=deal&model=deal'); ?>" target="_blank"><?php echo TextHelper::_('COBALT_PRINT'); ?></a>
                    </li>
                </ul>
            </div>
            <h1>
                <span id="name_<?php echo $deal->id; ?>"><?php echo $deal->name; ?></span>
            </h1>
            <p class="muted">
                <?php if (isset($deal->company_id) && $deal->company_id) { ?>
                <?php echo TextHelper::_('COBALT_ASSOCIATED_WITH').' <a href="'.RouteHelper::_('index.php?view=companies&layout=company&id='.$deal->company_id).'"><span id="company_name_'.$deal->id.'">'.$deal->company_name.'</span></a>'; ?><br />
                <?php } ?>
                <?php if (isset($deal->status_name) && $deal->status_name) { ?>
                <?php echo TextHelper::_('COBALT_DEALS_STATUS'); ?>: <span id="status_name_<?php echo $deal->id; ?>" class='deal-status-<?php echo strtolower($deal->status_name); ?>'>
                    <?php echo $deal->status_name; ?>
                </span><br />
                <?php } ?>
                <?php if (isset($deal->stage_name) && $deal->stage_name) { ?>
                <?php echo TextHelper::_('COBALT_DEALS_STAGE'); ?>: <span id="stage_name_<?php echo $deal->id; ?>" class='deal-stage-<?php echo strtolower($deal->stage_name); ?>'>
                    <?php echo $deal->stage_name; ?>
                </span>
                <?php } ?>
            </p>
        </div>

        <div rel="tooltip" title="<?php echo ucwords(TextHelper::_('COBALT_STAGE')).": ".$deal->stage_name; ?>" class="progress">
            <?php $light = "#".CobaltHelper::percent2color($deal->percent); ?>
            <?php $dark = "#".CobaltHelper::percent2color($deal->percent-20); ?>
          <div class="bar" id="percent_<?php echo $deal->id; ?>" style="
                  background-image: -moz-linear-gradient(top,<?php echo $light; ?>,<?php echo $dark; ?>);
                background-image: -webkit-gradient(linear,0 0,0 100%,from(<?php echo $light; ?>),to(<?php echo $dark; ?>));
                background-image: -webkit-linear-gradient(top,<?php echo $light; ?>,<?php echo $dark; ?>);
                background-image: -o-linear-gradient(top,<?php echo $light; ?>,<?php echo $dark; ?>);
                background-image: linear-gradient(to bottom,<?php echo $light; ?>,<?php echo $dark; ?>);
                background-color:<?php echo $light; ?> !important; width: <?php echo $deal->percent; ?>%;"></div>
        </div>
        <div class="row-fluid">
            <div class="span4 text-center">
                <div class="text-center well well-small">
                    <?php echo TextHelper::_('COBALT_EDIT_AMOUNT'); ?>
                    <span class="editable parent" id="editable_amount_container">
                        <div class="list-inline" id="editable_amount">
                            <h2>
                                <?php echo ConfigHelper::getCurrency(); ?>
                                <a href="javascript:void(0)" rel="popover" data-title="<?php echo ucwords(TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_AMOUNT')); ?>" data-html='true' data-content='<form id="amount_form">
                                <div class="input-append">
                                    <span class="input-append-addon"><?php echo ConfigHelper::getCurrency(); ?></span>
                                    <input type="text" class="inputbox" name="amount" value="<?php echo $deal->amount; ?>" />
                                    <span class="input-append-btn">
                                        <button type="button" class="btn btn-default" onclick="Cobalt.saveEditableModal(this);"><?php echo TextHelper::_('COBALT_SAVE'); ?></button>
                                    </span>
                                </div>
                                </form>'><span id="amount_<?php echo $deal->id; ?>"><?php echo $deal->amount; ?></span></a>
                            </h2>
                        </div>
                    </span>
                </div>
                <div class="cobaltRow">
                    <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_OWNER'); ?></div>
                    <div class="cobaltValue">
                        <div class='dropdown'>
                            <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id='deal_owner_link'>
                                <span id="owner_first_name_<?php echo $deal->id; ?>"><?php echo $deal->owner_first_name; ?></span>
                                <span id="owner_last_name_<?php echo $deal->id; ?>"><?php echo $deal->owner_last_name; ?></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                            <?php
                            $me = array(array('label'=>TextHelper::_('COBALT_ME'),'value'=>UsersHelper::getLoggedInUser()->id));
                            $users = UsersHelper::getUsers(null,TRUE);
                            $users = array_merge($me,$users);
                            if ( count($users) ){ foreach ($users as $key => $user) { ?>
                                <li>
                                    <a href="javascript:void(0)" class="dropdown_item" data-field="owner_id" data-item="deal" data-item-id="<?php echo $deal->id; ?>" data-value="<?php echo $user['value']; ?>">
                                        <?php echo $user['label']; ?>
                                    </a>
                                </li>
                            <?php }} ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="span4 text-center">
                <div class="text-center well well-small">
                    <?php echo TextHelper::_('COBALT_EDIT_PROBABILITY'); ?>
                    <span class="editable parent" id="editable_probability_container">
                    <div class="list-inline" id="editable_probability">
                        <h2>
                            <a href="javascript:void(0);" rel="popover" data-conent-class="probability-form" data-title="<?php echo ucwords(TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_PROBABILITY')); ?>">
                            <span id="probability_<?php echo $deal->id; ?>"><?php echo $deal->probability; ?></span>%</a>
                        </h2>
                        <div class="probability-form hidden">
                            <form action="<?php echo RouteHelper::_('index.php'); ?>" method="post" onsubmit="return Cobalt.sumbitForm(this)">
                                <div class="input-append">
                                    <input type="number" class="span1" name="probability" value="<?php echo $deal->probability; ?>" />
                                    <span class="add-on">%</span>
                                    <span class="input-append-btn">
                                        <button type="submit" class="btn btn-default"><?php echo TextHelper::_('COBALT_SAVE'); ?></button>
                                    </span>
                                </div>
                                <input type="hidden" name="task" value="save" />
                                <input type="hidden" name="model" value="deal" />
                                <input type="hidden" name="id" value="<?php echo $deal->id; ?>" />
                            </form>
                        </div>
                    </div>
                    </span>
                </div>
                <div class="cobaltRow">
                    <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_AGE'); ?></div>
                    <div class="cobaltValue">
                        <?php
                            echo DateHelper::getElapsedTime($deal->created);
                        ?>
                    </div>
                </div>
            </div>
            <div class="span4 text-center">
                <div class="text-center well well-small">
                    <?php $style = "style='display:none;'"; ?>
                    <?php if (in_array($deal->stage_id,$this->closed_stages) ) {
                        $actual_close = true;
                    } else {
                        $actual_close = false;
                    } ?>
                    <div id="actual_close_container"<?php if (!$actual_close) { echo $style; } ?>>
                        <?php echo TextHelper::_('COBALT_ACTUAL_CLOSE'); ?>
                        <h2>
                            <form class="inline-form" name="actual_close_form">
                                <input type="text" class="input-invisible input-small inputbox date_input" name="actual_close_hidden" id="actual_close" value="<?php echo DateHelper::formatDate($deal->actual_close); ?>" />
                                <input type="hidden" name="actual_close" id="actual_close_hidden" value="<?php echo $deal->actual_close; ?>" />
                            </form>
                        </h2>
                    </div>
                    <div id="expected_close_container"<?php if ($actual_close) { echo $style; } ?>>
                        <?php echo TextHelper::_('COBALT_EXP_CLOSE'); ?>
                        <h2>
                            <form class="inline-form" name="expected_close_form">
                                <input type="text" class="input-invisible input-small inputbox date_input" name="expected_close_hidden" id="expected_close" value="<?php echo DateHelper::formatDate($deal->expected_close); ?>" />
                                <input type="hidden" name="expected_close" id="expected_close_hidden" value="<?php echo $deal->expected_close; ?>" />
                            </form>
                        </h2>
                    </div>
                </div>
                <div class="cobaltRow">
                    <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_SOURCE'); ?></div>
                    <div class="cobaltValue">
                        <div class='dropdown'>
                            <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id='deal_source_<?php echo $deal->id; ?>_link'>
                                <span id="source_name_<?php echo $deal->id; ?>">
                                    <?php $sourceName = $deal->source_id > 0 ? $deal->source_name : TextHelper::_('COBALT_CLICK_TO_EDIT'); ?>
                                    <?php echo $sourceName; ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                            <?php
                            $sources = DealHelper::getSources();
                            if (count($sources)) { foreach ($sources as $id => $name) { ?>
                                <li>
                                    <a href="javascript:void(0)" class="dropdown_item" data-field="source_id" data-item="deal" data-item-id="<?php echo $deal->id; ?>" data-value="<?php echo $id; ?>"><?php echo $name; ?></a>
                                </li>
                            <?php }} ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr />
        <div class="edit-summary-container">
            <h2><?php echo TextHelper::_('COBALT_EDIT_SUMMARY'); ?></h2>
            <div class="well well-small large_info">
                <?php $summary = ( array_key_exists('summary',$deal) && strlen(trim($deal->summary)) > 0 ) ? $deal->summary : TextHelper::_('COBALT_CLICK_TO_EDIT'); ?>
                <div class="list-inline"><span id="summary_<?php echo $deal->id; ?>"><?php echo nl2br($summary); ?></span></div>
                <div id="editable_summary_area" style="display:none;">
                    <form id="summary_form">
                        <textarea class="inputbox" name="summary"><?php echo $summary; ?></textarea>
                    </form>
                    <a class="btn" href="javscript:void(0);" onclick="Cobalt.saveEditableModal('summary_form');" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                </div>
            </div>
        </div>

        <?php echo $deal->notes->render(); ?>

        <h2><?php echo TextHelper::_('COBALT_ADDITIONAL_FIELDS'); ?></h2>
                <div class="columncontainer">
                    <?php echo $this->custom_fields_view->render(); ?>
                </div>

        <span class="pull-right">
            <form id="upload_form" target="hidden" action="index.php?task=upload" method="post" enctype="multipart/form-data">
                <div class="fileupload fileupload-new" data-provides="fileupload">
                     <span class="btn btn-file"><span class="fileupload-new" id="upload_button"><?php echo TextHelper::_('COBALT_UPLOAD_FILE'); ?></span><span class="fileupload-exists"><?php echo TextHelper::_('COBALT_UPLOADING_FILE'); ?></span><input type="file" id="upload_input_invisible" name="document" /></span>
                </div>
                <input type="hidden" name="association_id" value="<?php echo $deal->id; ?>" />
                <input type="hidden" name="association_type" value='deal' />
            </form>
        </span>

        <h2><?php echo TextHelper::_('COBALT_EDIT_DOCUMENTS'); ?></h2>
        <div class="large_info">
            <table class="table table-striped table-hover" id="documents_table">
            <thead>
                <th><?php echo TextHelper::_('COBALT_TYPE'); ?></th>
                <th><?php echo TextHelper::_('COBALT_FILE_NAME'); ?></th>
                <th><?php echo TextHelper::_('COBALT_OWNER'); ?></th>
                <th><?php echo TextHelper::_('COBALT_SIZE'); ?></th>
                <th><?php echo TextHelper::_('COBALT_UPLOADED'); ?></th>
            </thead>
            <tbody id="documents">
               <?php echo $this->document_list->render(); ?>
            </tbody>
            </table>
        </div>
    </div>
    <div class="span4">
        <div class="widget">
            <h3><?php echo ucwords(TextHelper::_('COBALT_DEAL_CONTACTS')); ?></h3>
            <?php echo $this->contact_info->render(); ?>
        </div>
        <div class="widget" id='event_dock'>
            <?php echo $this->event_dock->render(); ?>
        </div>
        <?php if ( isset($this->banter_dock) ) {
            echo $this->banter_dock->render();
        }?>
    </div>
</div>

<?php echo CobaltHelper::showShareDialog();
