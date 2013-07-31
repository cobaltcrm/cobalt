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

//define person
$person = $this->people[0];

?>

<script type="text/javascript">
    var id  = <?php echo $person['id']; ?>;
    var loc = 'person';
    var model = 'people';
    var person_id  = <?php echo $person['id']; ?>;
    var association_type = 'person';
</script>

<!-- PERSON EDIT MODAL -->
<div data-remote="index.php?view=people&layout=edit&format=raw&tmpl=component&id=<?php echo $person['id']; ?>" class="modal hide fade" id="personModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel"><?php echo ucwords(TextHelper::_('COBALT_EDIT_PERSON')); ?></h3>
    </div>
    <div class="modal-body">
        <p></p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo ucwords(TextHelper::_('COBALT_CANCEL')); ?></button>
        <button onclick="saveProfileItem('edit_form')" class="btn btn-primary"><?php echo ucwords(TextHelper::_('COBALT_SAVE')); ?></button>
    </div>
</div>
<iframe id="hidden" name="hidden" style="display:none;width:0px;height:0px;border:0px;"></iframe>
<div class="row-fluid">

    <!-- LEFT MODULES AND DOCKS -->
    <div class="span8">

        <div class="page-header">
            <!-- ACTIONS -->
            <div class="btn-group pull-right">
                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                    <?php echo TextHelper::_('COBALT_ACTION_BUTTON'); ?>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a role="button" href="#personModal" data-toggle="modal"><?php echo TextHelper::_('COBALT_EDIT_BUTTON'); ?></a></li>
                    <li><a href="javascript:void(0);" onclick="addDeal('person_id=<?php echo $person['id']; ?>')"><?php echo TextHelper::_('COBALT_ASSOCIATE_TO_DEAL'); ?></a></li>
                    <?php if ( $person['owner_id'] == UsersHelper::getUserId() ) { ?>
                        <li><a href="javascript:void(0);" onclick="shareItemDialog();" ><?php echo TextHelper::_('COBALT_SHARE'); ?></a></li>
                    <?php } ?>
                    <li>
                        <?php if ( UsersHelper::canDelete() || $person['owner_id'] == UsersHelper::getUserId() ) { ?>
                            <a href="index.php?controller=trash&item_id=<?php echo $person['id']; ?>&item_type=people&page_redirect=people" onclick="deleteProfileItem(this)"><?php echo TextHelper::_('COBALT_DELETE_CONTACT'); ?></a>
                        <?php } ?>
                    </li>
                    <li>
                        <a href="index.php?view=print&item_id=<?php echo $person['id']; ?>&layout=person&model=people" target="_blank"><?php echo TextHelper::_('COBALT_PRINT'); ?></a>
                    </li>
                     <li>
                         <a href="javascript:void(0);" onclick="exportVcard()">
                            <?php echo TextHelper::_('COBALT_VCARD'); ?>
                        </a>
                        <div style="display:none;">
                            <form id="vcard_form" action="" method="post">
                                <input type="hidden" name="person_id" value="<?php echo $person['id']; ?>" />
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- HEADER -->
            <h1><?php echo $person['first_name'].' '.$person['last_name']; ?></h1>
        </div>

    <!-- EDITABLE FIELDS AND INFO -->
    <div class="row-fluid">
        <div class="columncontainer">
            <div class="threecolumn">
                <div class="small_info first">
                    <?php echo TextHelper::_('COBALT_PERSON_TOTAL'); ?>:
                    <span class="amount"><?php echo CobaltHelperConfig::getCurrency(); ?>0</span>
                </div>
                <div class="cobaltRow top">
                    <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_COMPANY')); ?>:</div>
                    <div class="cobaltValue">
                        <?php if ( array_key_exists('company_id',$person) ) { ?>
                            <a href="<?php echo JRoute::_("index.php?view=companies&layout=company&company_id=".$person['company_id']); ?>"><?php echo $person['company_name']; ?></a>
                        <?php } else {
                            echo TextHelper::_('COBALT_NO_COMPANY');
                        } ?>
                    </div>
                </div>
                <div class="cobaltRow">
                    <div class="cobaltField"><?php echo TextHelper::_('COBALT_EDIT_OWNER'); ?></div>
                    <div class="cobaltValue">
                        <div class='dropdown'>
                            <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id='person_owner_<?php echo $person['id']; ?>_link'>
                                <span id="owner_name_<?php echo $person['id']; ?>">
                                    <?php $ownerName = $person['owner_first_name'].' '.$person['owner_last_name'];  ?>
                                    <?php echo $ownerName; ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                            <?php 	$me = array(array('label'=>TextHelper::_('COBALT_ME'),'value'=>UsersHelper::getLoggedInUser()->id));
                                    $users = UsersHelper::getUsers(null,TRUE);
                                    $users = array_merge($me,$users);
                                    if ( count($users) ){ foreach ($users as $key => $user) { ?>
                                        <li>
                                            <a href="javascript:void(0)" class="dropdown_item" data-field="owner_id" data-item="people" data-item-id="<?php echo $person['id']; ?>" data-value="<?php echo $user['value']; ?>">
                                                <?php echo $user['label']; ?>
                                            </a>
                                        </li>
                            <?php }} ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="threecolumn">
                <div class="small_info middle">
                    <?php echo ucwords(TextHelper::_('COBALT_PERSON_DEALS')); ?>:
                    <span class="amount"><?php echo CobaltHelperConfig::getCurrency(); ?>0</span>
                </div>
                <div class="cobaltRow top">
                    <div class="cobaltField"><?php echo TextHelper::_('COBALT_TITLE'); ?>:</div>
                    <div class="cobaltValue">
                        <?php $personTitle = $person['position'] != "" && !is_null($person['position']) ? $person['position'] : TextHelper::_("COBALT_CLICK_TO_EDIT"); ?>
                        <a href="javascript:void(0);" rel="popover" data-title="<?php echo ucwords(TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_TITLE')); ?>" data-html='true' data-content='<div class="input-prepend input-append"><form class="inline-form" id="position_form">
                            <input type="text" class="inputbox input-small" name="position" value="<?php echo $person['position']; ?>" />
                            <a href="javascript:void(0);" class="btn" onclick="saveEditableModal(this);"><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                        </form></div>' ><span id="position_<?php echo $person['id']; ?>"><?php echo $personTitle; ?></span></a>
                    </div>
                </div>
                <div class="cobaltRow">
                    <div class="cobaltField"><?php echo TextHelper::_('COBALT_TYPE'); ?>:</div>
                    <div class="cobaltValue">
                        <div class='dropdown'>
                            <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id='person_type_<?php echo $person['id']; ?>_link'>
                                <span id="type_<?php echo $person['id']; ?>">
                                    <?php $person_type = ( array_key_exists('type',$person) && $person['type'] != "" ) ? $person['type'] : TextHelper::_('COBALT_NOT_SET'); ?>
                                    <?php echo $person_type; ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                            <?php
                                $types = PeopleHelper::getPeopleTypes(FALSE);
                                if ( count($types) ){ foreach ($types as $key => $type) { ?>
                                    <li>
                                        <a href="javascript:void(0)" class="dropdown_item" data-field="type" data-item="people" data-item-id="<?php echo $person['id']; ?>" data-value="<?php echo $key; ?>"><?php echo ucwords($type); ?></a>
                                    </li>
                                <?php }} ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="threecolumn">
                <div class="small_info last">
                    <?php echo TextHelper::_('COBALT_PERSON_CONTACTED'); ?>:
                    <?php echo CobaltHelperDate::formatDate($person['modified']); ?>
                </div>
                <div class="cobaltRow top">
                    <div class="cobaltField"><?php echo TextHelper::_('COBALT_STATUS'); ?>:</div>
                    <div class="cobaltValue">
                        <div class='dropdown'>
                            <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id='person_status_<?php echo $person['id']; ?>_link'>
                                <span id="status_name_<?php echo $person['id']; ?>">
                                    <?php 	$statusName = ( $person['status_name'] == '' ) ? TextHelper::_('COBALT_NO_STATUS') : $person['status_name'];  ?>
                                    <div class='person-status-color' style='background-color:#<?php echo $person['status_color']; ?>'></div><div class='person-status'><?php echo $statusName; ?></div>
                                </span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                            <?php $statuses = PeopleHelper::getStatusList();
                                if (count($statuses)) { foreach ($statuses as $key => $status) { ?>
                                <li>
                                    <a href="javascript:void(0)" class="dropdown_item" data-field="status_id" data-item="people" data-item-id="<?php echo $person['id']; ?>" data-value="<?php echo $status['id']; ?>">
                                        <div class="person-status-color" style="background-color:#<?php echo $status['color']; ?>"></div><div class="person-status"><?php echo $status['name']; ?></div>
                                    </a>
                                </li>
                            <?php }} ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="cobaltRow">
                    <div class="cobaltField"><?php echo TextHelper::_('COBALT_SOURCE'); ?>:</div>
                    <div class="cobaltValue">
                         <div class='dropdown'>
                            <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id='person_source_<?php echo $person['id']; ?>_link'>
                                <span id="source_name_<?php echo $person['id']; ?>">
                                    <?php $sourceName = $person['source_id'] > 0 ? $person['source_name'] : TextHelper::_('COBALT_CLICK_TO_EDIT'); ?>
                                    <?php echo $sourceName; ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                            <?php
                            $sources = CobaltHelperDeal::getSources();
                            if (count($sources)) { foreach ($sources as $id => $name) { ?>
                                <li>
                                    <a href="javascript:void(0)" class="dropdown_item" data-field="source_id" data-item="people" data-item-id="<?php echo $person['id']; ?>" data-value="<?php echo $id; ?>"><?php echo $name; ?></a>
                                </li>
                            <?php }} ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DEALS -->
        <h2><?php echo ucwords(TextHelper::_('COBALT_EDIT_DEALS')); ?></h2>
        <div class="large_info">
            <?php echo $this->deal_dock->render(); ?>
        </div>

        <!-- NOTES -->
        <?php echo $person['notes']->render(); ?>

        <!-- CUSTOM FIELDS -->
        <h2><?php echo TextHelper::_('COBALT_EDIT_CUSTOM'); ?></h2>
        <div class="columncontainer">
            <?php echo $this->custom_fields_view->render(); ?>
        </div>

        <!-- DOCUMENT UPLOAD BUTTON -->
        <span class="pull-right">
            <form id="upload_form" target="hidden" action="index.php?controller=upload" method="post" enctype="multipart/form-data">
                <div class="fileupload fileupload-new" data-provides="fileupload">
                     <span class="btn btn-file"><span class="fileupload-new" id="upload_button"><i class="icon-upload"></i><?php echo TextHelper::_('COBALT_UPLOAD_FILE'); ?></span><span class="fileupload-exists"><?php echo TextHelper::_('COBALT_UPLOADING_FILE'); ?></span><input type="file" id="upload_input_invisible" name="document" /></span>
                </div>
                <input type="hidden" name="association_id" value="<?php echo $deal['id']; ?>" />
                <input type="hidden" name="association_type" value='deal' />
            </form>
        </span>

        <!-- DOCUMENTS -->
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
</div>

    <!-- RIGHT MODULES AND DOCKS -->
    <div class="span4">

            <div class="widget" id="details">
                <h3><?php echo ucwords(TextHelper::_('COBALT_CONTACT_INFO')); ?></h3>
                <div class="infoBlock">
                    <div class="infoLabel">
                        <?php
                        if ( array_key_exists('avatar',$person) && $person['avatar'] != "" && $person['avatar'] != null ) {
                                 echo '<td class="avatar" ><img id="avatar_img_'.$person['id'].'" data-item-type="people" data-item-id="'.$person['id'].'" class="avatar" src="'.JURI::base().'libraries/crm/media/avatars/'.$person['avatar'].'"/></td>';
                            } else {
                                echo '<td class="avatar" ><img id="avatar_img_'.$person['id'].'" data-item-type="people" data-item-id="'.$person['id'].'" class="avatar" src="'.JURI::base().'libraries/crm/media/images/person.png'.'"/></td>';
                            } ?>
                    </div>
                    <div class="infoDetails">
                        <span class="largeDetails"><?php echo $person['first_name'] . ' ' . $person['last_name']; ?></span><br />
                        <span class="smallDetails"><?php echo $person['owner_first_name'] . ' ' . $person['owner_last_name']; ?></span><br />
                        <?php if (array_key_exists('company_id',$person)) { ?>
                            <a href="<?php echo JRoute::_("index.php?view=companies&layout=company&company_id=".$person['company_id']); ?>"><?php echo $person['company_name']; ?></a>
                        <?php } ?>
                    </div>
                </div>
                <div class="infoBlock">
                    <div class="infoLabel"><?php echo TextHelper::_('COBALT_WORK_PHONE_SHORT'); ?></div>
                    <div class="infoDetails"><?php echo $person['phone']; ?></div>
                </div>

                <div class="infoBlock">
                    <div class="infoLabel"><?php echo TextHelper::_('COBALT_EMAIL_SHORT'); ?></div>
                    <div class="infoDetails">
                        <?php if (array_key_exists('email',$person)) { ?>
                            <a href="mailto:<?php echo $person['email']; ?>?bcc=<?php echo CobaltHelperConfig::getConfigValue('imap_user'); ?>"><?php echo $person['email']; ?></a>
                        <?php } ?>
                    </div>
                </div>

                <?php if ( array_key_exists('work_address_1',$person) && $person['work_address_1'] != "" ) { ?>
                    <address>
                        <div class="infoBlock">
                            <div class="infoLabel"><?php echo TextHelper::_('COBALT_WORK_ADDRESS'); ?></div>
                            <div class="infoDetails">
                                <?php $urlString = "http://maps.googleapis.com/maps/api/staticmap?&zoom=13&zoom=2&size=600x400&sensor=false&center=".str_replace(" ","+",$person['work_address_1'].' '.$person['work_address_2'].' '.$person['work_city'].' '.$person['work_state'].' '.$person['work_zip'].' '.$person['work_country']); ?>
                                <a href="javascript:void(0);" class="google-map" id="work_address"></a>
                                <div id="work_address_modal" style="display:none;">
                                    <div class="google_map_center"></div>
                                    <img class="google-image-modal"  style="background-image:url(<?php echo $urlString; ?>);" />
                                </div>
                                <?php echo $person['work_address_1']; ?><br />
                                <?php if ( array_key_exists('work_address_2',$person) && $person['work_address_2'] != "" ) {
                                    echo $person['work_address_2'].'<br />';
                                } ?>
                                <?php echo $person['work_city'].', '.$person['work_state']." ".$person['work_zip']; ?><br />
                                <?php echo $person['work_country']; ?>
                            </div>
                        </div>
                    </address>
                <?php } ?>

                <?php if ( array_key_exists('home_address_1',$person) && $person['home_address_1'] != "" ) { ?>
                    <address>
                        <div class="infoBlock">
                            <div class="infoLabel"><?php echo TextHelper::_('COBALT_HOME_ADDRESS'); ?></div>
                            <div class="infoDetails">
                                <?php $urlString = "http://maps.googleapis.com/maps/api/staticmap?&zoom=13&zoom=2&size=600x400&sensor=false&center=".str_replace(" ","+",$person['home_address_1'].' '.$person['home_address_2'].' '.$person['home_city'].' '.$person['home_state'].' '.$person['home_zip'].' '.$person['home_country']); ?>
                                <a href="javascript:void(0);" class="google-map" id="home_address"></a>
                                <div id="home_address_modal" style="display:none;">
                                    <div class="google_map_center"></div>
                                    <img class="google-image-modal" style="background-image:url(<?php echo $urlString; ?>);" />
                                </div>
                                <?php echo $person['home_address_1']; ?><br />
                                <?php if ( array_key_exists('home_address_2',$person) && $person['home_address_2'] != "" ) {
                                    echo $person['home_address_2'].'<br />';
                                    }	?>
                                <?php echo $person['home_city'].', '.$person['home_state']." ".$person['home_zip']; ?><br />
                                <?php echo $person['home_country']; ?>
                            </div>
                        </div>
                    </address>
                <?php } ?>

                    <div class="media">
                        <span class="text-center">
                                <div class="text-center socialIcons infoDetails">
                                <?php if (array_key_exists('twitter_user',$person) && $person['twitter_user'] != "") { ?>
                                    <a href="http://www.twitter.com/#!/<?php echo $person['twitter_user']; ?>" target="_blank"><div class="twitter_light"></div></a>
                                <?php } else { ?>
                                <span class="editable parent" id="editable_twitter_container_<?php echo $person['id']; ?>">
                                <div class="inline">
                                    <a data-html="true" data-content='<div class="input-append"><form id="twitter_form_<?php echo $person['id']; ?>">
                                        <input type="hidden" name="item_id" value="<?php echo $person['id']; ?>" />
                                        <input type="hidden" name="item_type" value="people" />
                                        <input type="text" class="inputbox input-small" name="twitter_user" value="<?php if ( array_key_exists('twitter_user',$person) ) echo $person['twitter_user']; ?>" />
                                        <a href="javascript:void(0);" class="btn" onclick="saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                                    </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_TWITTER'); ?>" href="javascript:void(0);"><div class="twitter_dark"></div></a>
                                </div>
                                </span>
                                <?php } ?>

                                <?php if (array_key_exists('facebook_url',$person) && $person['facebook_url'] != "") { ?>
                                    <a href="<?php echo $person['facebook_url']; ?>" target="_blank"><div class="facebook_light"></div></a>
                                <?php } else { ?>
                                <span class="editable parent" id="editable_facebook_container_<?php echo $person['id']; ?>">
                                <div class="inline">
                                    <a data-html="true" data-content='<div class="input-append"><form id="facebook_form_<?php echo $person['id']; ?>">
                                        <input type="hidden" name="item_id" value="<?php echo $person['id']; ?>" />
                                        <input type="hidden" name="item_type" value="people" />
                                        <input type="text" class="inputbox input-small" name="facebook_url" value="<?php if ( array_key_exists('facebook_url',$person) ) echo $person['facebook_url']; ?>" />
                                        <a href="javascript:void(0);" class="btn button" onclick="saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                                    </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_FACEBOOK'); ?>" href="javascript:void(0);"><div class="facebook_dark"></div></a>
                                </div>
                                </span>
                                <?php } ?>

                                <?php if (array_key_exists('linkedin_url',$person) && $person['linkedin_url'] != "" ) { ?>
                                    <a rel="popover" href="<?php echo $person['linkedin_url']; ?>" target="_blank"><div class="linkedin_light"></div></a>
                                <?php } else { ?>
                                <span class="editable parent" id="editable_linkedin_container_<?php echo $person['id']; ?>">
                                <div class="inline">
                                    <a data-html="true" data-content='<div class="input-append"><form id="linkedin_form_<?php echo $person['id']; ?>">
                                        <input type="hidden" name="item_id" value="<?php echo $person['id']; ?>" />
                                        <input type="hidden" name="item_type" value="people" />
                                        <input type="text" class="inputbox input-small" name="linkedin_url" value="<?php if ( array_key_exists('linkedin_url',$person) ) echo $person['linkedin_url']; ?>" />
                                        <a href="javascript:void(0);" class="btn button" onclick="saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                                    </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_LINKEDIN'); ?>" href="javascript:void(0);"><div class="linkedin_dark"></div></a>
                                </div>
                                </span>
                                <?php } ?>

                                <span class="editable parent" id="editable_aim_container_<?php echo $person['id']; ?>">
                                <div class="inline">
                                    <?php if (array_key_exists('aim',$person) && $person['aim'] != "" ) { ?>
                                        <a data-html="true" data-content='<div class="input-append"><form id="aim_form_<?php echo $person['id']; ?>">
                                        <input type="hidden" name="item_id" value="<?php echo $person['id']; ?>" />
                                        <input type="hidden" name="item_type" value="people" />
                                        <input type="text" class="inputbox input-small" name="aim" value="<?php if ( array_key_exists('aim',$person) )  echo $person['aim']; ?>" />
                                        <a href="javascript:void(0);" class="btn button" onclick="saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                                    </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_AIM'); ?>" href="javascript:void(0);"><div class="aim_light"></div></a>
                                    <?php } else { ?>
                                        <a data-html="true" data-content='<div class="input-append"><form id="aim_form_<?php echo $person['id']; ?>">
                                        <input type="hidden" name="item_id" value="<?php echo $person['id']; ?>" />
                                        <input type="hidden" name="item_type" value="people" />
                                        <input type="text" class="inputbox input-small" name="aim" value="<?php if ( array_key_exists('aim',$person) )  echo $person['aim']; ?>" />
                                        <a href="javascript:void(0);" class="btn button" onclick="saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                                    </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_AIM'); ?>" href="javascript:void(0);"><div id="aim_button_<?php echo $person['id']; ?>" class="aim_dark"></div></a>
                                    <?php } ?>
                                </div>
                                </span>
                            </div>
                        </span>
                    </div>
            </div>

        <?php if ($this->acymailing) { ?>
            <div class="widget" id='acymailing'>
                <h3><?php echo ucwords(TextHelper::_('COBALT_ACYMAILING_HEADER')); ?></h3>
                <?php echo $this->acymailing_dock->render(); ?>
            </div>
        <?php } ?>

        <?php if ( isset($this->banter_dock) ) {
            echo $this->banter_dock->render();
        }?>

        <?php if ($person['twitter_user']) { ?>
            <div class="widget">
                <h3><?php echo TextHelper::_('COBALT_LATEST_TWEETS'); ?></h3>
                <?php if ( array_key_exists('tweets',$person) ){ for($i=0; $i<count($person['tweets']); $i++) {
                    $tweet = $person['tweets'][$i];
                ?>
                <div class="tweet">
                    <span class="tweet_date"><?php echo $tweet['date']; ?></span>
                    <?php echo $tweet['tweet']; ?>
                </div>
                <?php } } ?>
            </div>
        <?php } ?>

        <div class="widget" id='event_dock'>
            <?php echo $this->event_dock->render(); ?>
        </div>

    </div>
</div>

<!--- DEAL ASSOCIATION -->
<div class='modal hide fade' role='dialog' tabindex='-1' aria-hidden='true' id='ajax_search_deal_dialog'>
    <div class="modal-header small"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3><?php echo TextHelper::_('COBALT_ASSOCIATE_DEAL'); ?></h3></div>
    <div class="modal-body text-center">
        <form id="deal">
            <div class="input-append">
                <input name="deal_name" class="inputbox" type="text" placeholder="<?php echo TextHelper::_('COBALT_BEGIN_TYPING_TO_SEARCH'); ?>" />
                <input type="hidden" name="company_id" value="<?php echo $company['id'];  ?>" />
                <a class="btn btn-success" href="javascript:void(0);" onclick="saveAjax('deal','deal');"><i class="icon-white icon-plus"></i><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
            </div>
        </form>
    </div>
</div>
