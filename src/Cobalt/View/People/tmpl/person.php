<?php
/**
 * CRMery
 *
 * @author CRMery
 * @copyright Copyright (C) 2012 crmery.com All Rights Reserved.
 * @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * Website: http://www.crmery.com
 */
// no direct access
defined('_CEXEC') or die('Restricted access');

//define person
$person = $this->people[0];

?>
    <script type="text/javascript">
        var id = <?php echo $person['id']; ?>;
        var loc = 'person';
        var model = 'people';
        var person_id = <?php echo $person['id']; ?>;
        var association_type = 'person';
    </script>

    <div data-remote="index.php?view=people&layout=edit&id=<?php echo $person['id']; ?>&format=raw&tmpl=component" class="modal hide fade" id="personModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"><?php echo ucwords(TextHelper::_('COM_COBALT_ADD_PERSON')); ?></h3>
        </div>
        <div class="modal-body">
            <p></p>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo ucwords(TextHelper::_('COM_COBALT_CANCEL')); ?></button>
            <button onclick="saveProfileItem('edit_form');" class="btn btn-primary"><?php echo ucwords(TextHelper::_('COM_COBALT_SAVE')); ?></button>
        </div>
    </div>

    <iframe id="hidden" name="hidden" style="display:none;width:0px;height:0px;border:0px;"></iframe>


    <div class="row-fluid">
    <div class="span3">
        <?php $image = !empty($person['avatar']) ? '/avatars/' . $person['avatar'] : '/images/person.png'; ?>
        <div class="row-fluid">
            <img id="avatar_img_<?php echo $person['id']; ?>" data-item-type="people" data-item-id="<?php echo $person['id']; ?>" class="avatar" src="<?php echo JUri::base() . 'src/Cobalt/media' . $image; ?>"/>
        </div>

        <div class="well" id="details">
            <div class="row-fluid">
                <strong><?php echo TextHelper::_('COM_COBALT_MOBILE_PHONE'); ?></strong>
                <span class="pull-right"><?php echo $person['mobile_phone']; ?></span>
            </div>

            <div class="row-fluid">
                <strong><?php echo TextHelper::_('COM_COBALT_PERSON_HOME_PHONE'); ?></strong>
                <span class="pull-right"><?php echo $person['home_phone']; ?></span>
            </div>

            <div class="row-fluid">
                <strong><?php echo TextHelper::_('COM_COBALT_EMAIL_SHORT'); ?></strong>
                <?php if (array_key_exists('email', $person))
                { ?>
                    <span class="pull-right"><a target='_blank' href="mailto:<?php echo $person['email']; ?>?bcc=<?php echo ConfigHelper::getConfigValue('imap_user'); ?>"><?php echo $person['email']; ?></a></span>
                <?php } ?>
            </div>


            <?php if (array_key_exists('work_address_1', $person) && $person['work_address_1'] != "")
            { ?>

                <div class='label label-info'><?php echo TextHelper::_('COM_COBALT_WORK_ADDRESS'); ?></div>
                <div class="row-fluid">
                    <?php $urlString = "http://maps.googleapis.com/maps/api/staticmap?&zoom=13&zoom=2&size=600x400&sensor=false&center=" . str_replace(" ", "+", $person['work_address_1'] . ' ' . $person['work_address_2'] . ' ' . $person['work_city'] . ' ' . $person['work_state'] . ' ' . $person['work_zip'] . ' ' . $person['work_country']); ?>
                    <a href="#work_address_modal" data-toggle="modal" class="btn btn-mini pull-right"><i class="icon icon-map-marker"></i></a>
                    <div id="work_address_modal" class="modal hide fade">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h3><?php echo TextHelper::_('COM_COBALT_WORK_ADDRESS'); ?></h3>
                        </div>
                        <div class="modal-body">
                            <img class="google-image-modal" style="background-image:url(<?php echo $urlString; ?>);"/>
                        </div>
                    </div>
                    <?php echo $person['work_address_1']; ?><br/>
                    <?php if (array_key_exists('work_address_2', $person) && $person['work_address_2'] != "")
                    {
                        echo $person['work_address_2'] . '<br />';
                    } ?>
                    <?php echo $person['work_city'] . ', ' . $person['work_state'] . " " . $person['work_zip']; ?><br/>
                    <?php echo $person['work_country']; ?>
                </div>
            <?php } ?>

            <?php if (array_key_exists('home_address_1', $person) && $person['home_address_1'] != "")
            { ?>
                <div class='label label-info'><?php echo TextHelper::_('COM_COBALT_HOME_ADDRESS'); ?></div>
                <div class="row-fluid">
                    <?php $urlString = "http://maps.googleapis.com/maps/api/staticmap?&zoom=13&zoom=2&size=600x400&sensor=false&center=" . str_replace(" ", "+", $person['home_address_1'] . ' ' . $person['home_address_2'] . ' ' . $person['home_city'] . ' ' . $person['home_state'] . ' ' . $person['home_zip'] . ' ' . $person['home_country']); ?>
                    <a href="#home_address_modal" data-toggle="modal" class="btn btn-mini pull-right"><i class="icon icon-map-marker"></i></a>
                    <div id="home_address_modal" class="modal hide fade">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h3><?php echo TextHelper::_('COM_COBALT_HOME_ADDRESS'); ?></h3>
                        </div>
                        <div class="modal-body">
                            <img class="google-image-modal" style="background-image:url(<?php echo $urlString; ?>);"/>
                        </div>
                    </div>
                    <?php echo $person['home_address_1']; ?><br/>
                    <?php if (array_key_exists('home_address_2', $person) && $person['home_address_2'] != "")
                    {
                        echo $person['home_address_2'] . '<br />';
                    }    ?>
                    <?php echo $person['home_city'] . ', ' . $person['home_state'] . " " . $person['home_zip']; ?><br/>
                    <?php echo $person['home_country']; ?>
                </div>
            <?php } ?>

            <div class="infoBlock">
                <div class="infoLabel">&nbsp;</div>
                <div class="infoDetails">
                    <?php if (array_key_exists('facebook_url', $person) && $person['facebook_url'] != "")
                    { ?>
                        <a href="<?php echo $person['facebook_url']; ?>" target="_blank">
                            <div class="facebook_light"></div>
                        </a>
                    <?php
                    }
                    else
                    {
                        ?>
                        <a href="javascript:void(0);" rel="popover" data-title="<?php echo TextHelper::_('COM_COBALT_PERSON_FACEBOOK_URL'); ?>" data-html='true' data-content='<form class="input-append inline-form" id="facebook_form_<?php echo $person['id']; ?>">
							<input type="text" class="input-small" name="facebook_url" value="<?php if (array_key_exists('facebook_url', $person)){ echo $person['facebook_url'];} ?>" />
							<input type="hidden" name="item_id" value="<?php echo $person['id']; ?>"/>
							<input type="hidden" name="item_type" value="people"/>
							<a href="#" class="btn" onclick="saveEditableModal(this);"><?php echo TextHelper::_('COM_COBALT_SAVE'); ?></a>
						</form></div>'><span id="editable_facebook_container_<?php echo $person['id']; ?>"><div class="facebook_dark"></div></span></a>
                    <?php } ?>

                    <?php if (array_key_exists('twitter_user', $person) && $person['twitter_user'] != "")
                    { ?>
                        <a href="http://www.twitter.com/#!/<?php echo $person['twitter_user']; ?>" target="_blank">
                            <div class="twitter_light"></div>
                        </a>
                    <?php
                    }
                    else
                    {
                        ?>
                        <a href="javascript:void(0);" rel="popover" data-title="<?php echo TextHelper::_('COM_COBALT_PERSON_TWITTER_USER'); ?>" data-html='true' data-content='<form class="input-append inline-form" id="twitter_form_<?php echo $person['id']; ?>">
								<input type="text" class="input-small" name="twitter_user" value="<?php if (array_key_exists('twitter_user', $person)){ echo $person['twitter_user'];} ?>" />
								<input type="hidden" name="item_id" value="<?php echo $person['id']; ?>"/>
								<input type="hidden" name="item_type" value="people"/>
								<a href="#" class="btn" onclick="saveEditableModal(this);"><?php echo TextHelper::_('COM_COBALT_SAVE'); ?></a>
							</form></div>'><span id="editable_twitter_container_<?php echo $person['id']; ?>"><div class="twitter_dark"></div></span></a>

                    <?php } ?>

                    <?php if (array_key_exists('linkedin_url', $person) && $person['linkedin_url'] != "")
                    { ?>
                        <a href="<?php echo $person['linkedin_url']; ?>" target="_blank">
                            <div class="linkedin_light"></div>
                        </a>
                    <?php
                    }
                    else
                    {
                        ?>
                        <a href="javascript:void(0);" rel="popover" data-title="<?php echo TextHelper::_('COM_COBALT_PERSON_LINKEDIN_URL'); ?>" data-html='true' data-content='<form class="input-append inline-form" id="linkedin_form_<?php echo $person['id']; ?>">
						<input type="text" class="input-small" name="linkedin_url" value="<?php if (array_key_exists('linkedin_url', $person)){ echo $person['linkedin_url'];} ?>" />
						<input type="hidden" name="item_id" value="<?php echo $person['id']; ?>"/>
						<input type="hidden" name="item_type" value="people"/>
						<a href="#" class="btn" onclick="saveEditableModal(this);"><?php echo TextHelper::_('COM_COBALT_SAVE'); ?></a>
					</form></div>'><span id="editable_linkedin_container_<?php echo $person['id']; ?>"><div class="linkedin_dark"></div></span></a>

                    <?php } ?>

                    <?php $style = (array_key_exists('aim', $person) && $person['aim'] != '') ? 'light' : 'dark'; ?>
                    <a href="javascript:void(0);" rel="popover" data-title="<?php echo TextHelper::_('COM_COBALT_PERSON_AIM_USER'); ?>" data-html='true' data-content='<form class="input-append inline-form" id="aim_form_<?php echo $person['id']; ?>">
					<input type="text" class="input-small" name="aim" value="<?php if (array_key_exists('aim', $person)){ echo $person['aim'];} ?>" />
					<input type="hidden" name="item_id" value="<?php echo $person['id']; ?>"/>
					<input type="hidden" name="item_type" value="people"/>
					<a href="#" class="btn" onclick="saveEditableModal(this);"><?php echo TextHelper::_('COM_COBALT_SAVE'); ?></a>
				</form></div>'><span id="editable_aim_container_<?php echo $person['id']; ?>"><div id="aim_button_<?php echo $person['id']; ?>" class="aim_<?php echo $style; ?>"></div></span></a>

                </div>
            </div>
        </div>
        
        <?php if ($person['twitter_user'])
        { ?>
            <div class="widget">
                <h3><?php echo TextHelper::_('COM_COBALT_LATEST_TWEETS'); ?></h3>
                <?php if (array_key_exists('tweets', $person))
                {
                    for ($i = 0; $i < count($person['tweets']); $i++)
                    {
                        $tweet = $person['tweets'][$i];
                        ?>
                        <div class="tweet">
                            <span class="tweet_date"><?php echo $tweet['date']; ?></span>
                            <?php echo $tweet['tweet']; ?>
                        </div>
                    <?php }
                } ?>
            </div>
        <?php } ?>

        <div class="widget" id='event_dock'>
            <?php $this->event_dock->render(); ?>
        </div>
    </div>

    <div class="span9">
    <div class="btn-group actions_container">
        <a class="btn" role="button" href="#personModal" data-toggle="modal"><?php echo ucwords(TextHelper::_("COM_COBALT_EDIT_BUTTON")); ?></a>
        <button class="btn dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right">
            <li>
                <a onclick="addDeal('person_id=<?php echo $person['id']; ?>')"><?php echo TextHelper::_('COM_COBALT_ASSOCIATE_TO_DEAL'); ?></a>
            </li>
            <?php if ($person['owner_id'] == UsersHelper::getUserId()) : ?>
                <li><a onclick="shareItemDialog();"><?php echo TextHelper::_('COM_COBALT_SHARE'); ?></a></li>
            <?php endif; ?>
            <?php if (UsersHelper::canDelete() || $person['owner_id'] == UsersHelper::getUserId()) : ?>
                <li>
                    <a onclick="deleteItem(this)"><?php echo TextHelper::_('COM_COBALT_DELETE_CONTACT'); ?></a>
                    <form id="delete_form" method="POST" action="<?php echo JRoute::_('index.php?task=main.trash'); ?>">
                        <input type="hidden" name="item_id" value="<?php echo $person['id']; ?>"/>
                        <input type="hidden" name="item_type" value="people"/>
                        <input type="hidden" name="page_redirect" value="people"/>
                    </form>
                </li>
            <?php endif; ?>
            <li>
                <a onclick="printItems(this,'people')"><?php echo TextHelper::_('COM_COBALT_PRINT'); ?></a>
                <form class="print_form" method="POST" target="_blank" action="<?php echo JRoute::_('index.php?view=print'); ?>">
                    <input type="hidden" name="item_id" value="<?php echo $person['id']; ?>"/>
                    <input type="hidden" name="layout" value="person"/>
                    <input type="hidden" name="model" value="people"/>
                </form>
            </li>
            <li>
                <form id="vcard_form" action="" method="POST">
                    <input type="hidden" name="person_id" value="<?php echo $person['id']; ?>"/>
                    <a onclick="exportVcard()">
                        <?php echo TextHelper::_('COM_COBALT_VCARD'); ?>
                    </a>
                </form>
            </li>
        </ul>
    </div>

    <div class="page-header">
        <h1><?php echo $person['first_name'] . ' ' . $person['last_name']; ?></h1>
    </div>

    <div class="row-fluid">
        <div class="span4">
            <div class="well well-small text-center">
                <?php echo TextHelper::_('COM_COBALT_PERSON_TOTAL'); ?>
                <h2 class="amount"><?php echo ConfigHelper::getCurrency(); ?><?php echo (float) $person['total_pipeline']; ?></h2>
            </div>
            <div class="crmeryRow top">
                <div class="crmeryField"><?php echo ucwords(TextHelper::_('COM_COBALT_COMPANY')); ?>:</div>
                <div class="crmeryValue">
                    <?php if (array_key_exists('company_id', $person) && $person['company_id'] > 0)
                    { ?>
                        <a href="<?php echo JRoute::_("index.php?view=companies&layout=company&company_id=" . $person['company_id']); ?>"><?php echo $person['company_name']; ?></a>
                    <?php
                    }
                    else
                    {
                        echo ucwords(TextHelper::_('COM_COBALT_NO_COMPANY'));
                    } ?>
                </div>
                <div class="clear"></div>
            </div>
            <div class="crmeryRow">
                <div class="crmeryField"><?php echo ucwords(TextHelper::_('COM_COBALT_EDIT_OWNER')); ?>:</div>
                <div class="crmeryValue">
                    <div class='dropdown' id="person_owner" data-item="deal" data-field="owner_id" data-item-id="<?php echo $person['id']; ?>">
                        <a href="#" class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id='person_owner_link'><span id="owner_first_name_<?php echo $person['id']; ?>"><?php echo $person['owner_first_name']." ".$person['owner_last_name']; ?></span></a>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <?php
                            $me = array(array('label'=>TextHelper::_('COM_COBALT_ME'),'value'=>UsersHelper::getLoggedInUser()->id));
                            $users = UsersHelper::getUsers(null,TRUE);
                            $users = array_merge($me,$users);
                            if ( count($users) ){ foreach ( $users as $key => $user ){ ?>
                                <li>
                                    <a href="javascript:void(0);" class="dropdown_item" data-field="owner_id" data-item="person" data-item-id="<?php echo $person['id']; ?>" data-value="<?php echo $user['value']; ?>">
                                        <?php echo $user['label']; ?>
                                    </a>
                                </li>
                            <?php }} ?>
                        </ul>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>

        <div class="span4">
            <div class="well well-small text-center">
                <?php echo ucwords(TextHelper::_('COM_COBALT_PERSON_DEALS')); ?>
                <h2 class="amount"><?php echo ConfigHelper::getCurrency(); ?><?php echo (float) $person['won_deal_amount']; ?></h2>
            </div>
            <div class="crmeryRow top">
                <div class="crmeryField"><?php echo TextHelper::_('COM_COBALT_TITLE'); ?>:</div>
                <div class="crmeryValue">
					<span class="editable parent" id="editable_position_container">
						<div class="inline" id="editable_position">
                            <?php $data_title = (array_key_exists('position', $person) && $person['position'] != "") ? $person['position'] : ucwords(TextHelper::_('COM_COBALT_CLICK_TO_EDIT')); ?>
                            <a href="javascript:void(0);" rel="popover" data-title="<?php echo TextHelper::_('COM_COBALT_POSITION'); ?>" data-html='true' data-content='<form class="input-append inline-form" id="position_form">
								<input type="text" class="input-small" name="position" value="<?php if(array_key_exists('position', $person)): echo $person['position']; endif; ?>" />
								<a href="#" class="btn" onclick="saveEditableModal(this);"><?php echo TextHelper::_('COM_COBALT_SAVE'); ?></a>
							</form></div>' ><span id="position_<?php echo $person['id']; ?>"><?php echo $data_title; ?></span></a>

                        </div>
					</span>
                </div>
                <div class="clear"></div>
            </div>
            <div class="crmeryRow">
                <div class="crmeryField"><?php echo TextHelper::_('COM_COBALT_TYPE'); ?>:</div>
                <div class="crmeryValue">
                    <?php $person_type = (array_key_exists('type', $person) && $person['type'] != "") ? $person['type'] : TextHelper::_('COM_COBALT_NOT_SET'); ?>
                    <div class="dropdown" data-item="people" data-field="type" data-item-id="<?php echo $person['id']; ?>" id="person_type">
                        <a href="#" class='dropdown-toggle' data-toggle="dropdown" id="person_type_link"><?php echo ucwords($person_type); ?></a>
                        <ul class="dropdown-menu pull-right">
                            <?php
                            $types = PeopleHelper::getPeopleTypes(false);
                            if (count($types))
                            {
                                foreach ($types as $key => $type)
                                {
                                    echo '<li><a href="javascript:void(0);" class="dropdown_item" data-value="' . $key . '">' . ucwords($type) . '</a></li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>

        <div class="span4">
            <div class="well well-small text-center">
                <?php echo TextHelper::_('COM_COBALT_PERSON_CONTACTED'); ?>
                <?php
                echo "<h2>" . DateHelper::formatDate($person['modified']) . "</h2>";
                ?>
            </div>
            <div class="crmeryRow top">
                <div class="crmeryField"><?php echo TextHelper::_('COM_COBALT_STATUS'); ?>:</div>
                <div class="crmeryValue">
                    <?php $person['status_name'] = ($person['status_name'] == '') ? TextHelper::_('COM_COBALT_NO_STATUS') : $person['status_name']; ?>
                    <div class="dropdown" data-item="people" data-field="status_id" data-item-id="<?php echo $person['id']; ?>" id="person_status_<?php echo $person['id']; ?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="person_status_<?php echo $person['id']; ?>_link"><span class="status-dot person-status-color" style="background-color:#<?php echo $person['status_color']; ?>"></span><?php echo $person['status_name']; ?></a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="javascript:void(0)" class="status_select dropdown_item" data-value="0">
                                    <span class="status-dot person-status-none"></span>None
                                </a>
                            </li>
                            <?php $statuses = PeopleHelper::getStatusList();
                            if (count($statuses))
                            {
                                foreach ($statuses as $key => $status)
                                {
                                    echo '<li><a href="javascript:void(0)" class="status_select dropdown_item" data-value="' . $status['id'] . '"><span class="status-dot person-status-color" style="background-color:#' . $status['color'] . '"></span>' . $status['name'] . '</a></li>';
                                }
                            } ?>
                        </ul>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="crmeryRow">
                <div class="crmeryField"><?php echo TextHelper::_('COM_COBALT_SOURCE'); ?>:</div>
                <div class="crmeryValue">
                    <?php $person['source_name'] = ($person['source_name'] == '') ? TextHelper::_('COM_COBALT_NO_SOURCE') : $person['source_name']; ?>
                    <div class="dropdown" data-item="people" data-field="source_id" data-item-id="<?php echo $person['id']; ?>" id="person_source_<?php echo $person['id']; ?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="person_source_<?php echo $person['id']; ?>_link"><?php echo $person['source_name']; ?></a>
                        <ul class="dropdown-menu pull-right">
                            <?php $sources = DealHelper::getSources();
                            if (count($sources))
                            {
                                foreach ($sources as $id => $name)
                                {
                                    echo '<li><a href="javascript:void(0)" class="source_select dropdown_item" data-value="' . $id . '">' . $name . '</a></li>';
                                }
                            } ?>
                        </ul>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>

    <?php $this->custom_fields_view->render(); ?>


    <h2 class="dotted"><?php echo TextHelper::_('COM_COBALT_EDIT_NOTES'); ?></h2>
    <?php echo $person['notes']->render(); ?>

    <h2 class="dotted"><?php echo ucwords(TextHelper::_('COM_COBALT_EDIT_DEALS')); ?></h2>
    <div class="large_info">
        <?php $this->deal_dock->render(); ?>
    </div>

	<span class="actions pull-right">
		<form id="upload_form" target="hidden" action="<?php echo JRoute::_('index.php?task=documents.uploadDocument&format=raw&tmpl=component'); ?>" method="POST" enctype="multipart/form-data">
            <div class="input_upload_button fltrt">
                <a href="javascript:void(0);" class="button" id="upload_button"><?php echo TextHelper::_("COM_COBALT_UPLOAD_FILE"); ?></a>
                <input type="file" id="upload_input_invisible" name="document"/>
            </div>
        </form>
	</span>
    <h2 class="dotted"><?php echo TextHelper::_('COM_COBALT_EDIT_DOCUMENTS'); ?></h2>
    <div class="large_info">
        <table id="documents_table" class="com_crmery_table">
            <thead>
            <tr>
                <th><?php echo TextHelper::_('COM_COBALT_TYPE'); ?></th>
                <th><?php echo TextHelper::_('COM_COBALT_FILE_NAME'); ?></th>
                <th><?php echo TextHelper::_('COM_COBALT_OWNER'); ?></th>
                <th><?php echo TextHelper::_('COM_COBALT_SIZE'); ?></th>
                <th><?php echo TextHelper::_('COM_COBALT_UPLOADED'); ?></th>
            </tr>
            </thead>
            <tbody id="documents">
            <?php echo $this->document_list->render(); ?>
            </tbody>
        </table>
    </div>

    <!--- DEAL ASSOCIATION -->
    <div class='modal hide fade' role='dialog' tabindex='-1' aria-hidden='true' id='ajax_search_deal_dialog'>
        <div class="modal-header small"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3><?php echo TextHelper::_("COM_COBALT_ASSOCIATE_TO_DEAL"); ?></h3></div>
        <div class="modal-body text-center">
            <form id="deal">
                <div class="input-append">
                    <input name="deal_name" type="text" placeholder="<?php echo TextHelper::_('COM_COBALT_BEGIN_TYPING_TO_SEARCH'); ?>" />
                    <input type="hidden" name="company_id" value="<?php echo $company['id'];  ?>" />
                    <a class="btn btn-success" href="" onclick="saveCf('people');"><i class="icon-white icon-plus"></i><?php echo TextHelper::_('COM_COBALT_SAVE'); ?></a>
                </div>
            </form>
        </div>
    </div>
    </div>
    </div>
<?php echo CobaltHelper::showShareDialog(); ?>