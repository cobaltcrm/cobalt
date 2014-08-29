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

$app = JFactory::getApplication();
//define deal
$contacts = $this->contacts;
?>

<div class="modal hide fade" id="ajax_search_person_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"><?php echo ucwords(TextHelper::_('COBALT_ADD_PERSON')); ?></h3>
        </div>
        <div class="modal-body">
            <input class="inputbox" type="text" name="person_name" placeholder="<?php echo TextHelper::_('COBALT_BEGIN_TYPING_TO_SEARCH'); ?>" value="" />
        </div>
         <div class="modal-footer">
            <div class="actions"><input class="btn btn-success" type="button" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" onclick="saveCf('people');closeDialog('person')"/> <?php echo TextHelper::_('COBALT_OR'); ?> <a href="javascript:void(0);" onclick="closeDialog('person')"><?php echo TextHelper::_('COBALT_CANCEL'); ?></a></div>
        </div>
</div>
<div class="clearfix" id="contacts">
    <div class="clearfix">
        <span class="pull-right">
            <a class="btn" href="javascript:void(0);" onclick="addPerson()" ><i class="icon-plus"></i></a>
        </span>
    </div>
    <?php if ( is_array($contacts) && count($contacts) > 0 ){ foreach ($contacts as $person) { ?>
            <?php if ($this->primary_contact_id == $person['id']) { $class = "active"; } else { $class = ""; } ?>
            <div class="media <?php echo $class; ?>" id="person_container_<?php echo $person['id']; ?>">
              <span class="pull-left widget">
                    <?php echo '<img id="avatar_img_'.$person['id'].'" data-item-type="people" data-item-id="'.$person['id'].'" class="avatar" src="'.$person['avatar'].'"/>'; ?>
                    <?php if ( $app->input->get('view') == "deals" || $app->input->get('loc') == "deal" ) { ?>
                        <div class="person_actions">
                            <?php if ($this->primary_contact_id == $person['id']) { ?>
                                <a class="star" id="primary_contact" onclick="unassignDealPrimaryContact(<?php echo $person['id']; ?>)" data-id="<?php echo $person['id']; ?>" href="javascript:void(0);" ></a>
                            <?php } else { ?>
                                <a class="white_star" id="star_<?php echo $person['id']; ?>" data-id="<?php echo $person['id']; ?>" onclick="assignDealPrimaryContact(<?php echo $person['id']; ?>);" href="javascript:void(0);" ></a>
                            <?php } ?>
                            <a class="remove" href="javascript:void(0);" onclick="removePersonFromDeal(<?php echo $person['id']; ?>);"></a>
                        </div>
                    <?php } ?>
              </span>
              <div class="media-body">
                    <span class="pull-right">
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
                    <strong><a href="<?php echo RouteHelper::_('index.php?view=people&layout=person&id='.$person['id']);?>"><?php echo $person['first_name'] . ' ' . $person['last_name']; ?></a></strong><br>
                        <?php if (array_key_exists('company_id',$person)) { ?>
                            <a href="<?php echo RouteHelper::_("index.php?view=companies&layout=company&company_id=".$person['company_id']); ?>"><?php echo $person['company_name']; ?></a><br>
                        <?php } ?>
                        <?php if (array_key_exists('phone',$person)) { ?>
                            <?php echo $person['phone']; ?>
                        <?php } ?>
                        <?php if (array_key_exists('email',$person) && $person['email']!="") { ?>
                                <i class="icon-envelope"></i><a href="mailto:<?php echo $person['email']; ?>"><?php echo $person['email']; ?></a>
                        <?php } ?>
              </div>
            </div>
    <?php } } ?>
</div>
