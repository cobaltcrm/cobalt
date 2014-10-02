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
<div class="modal fade" id="ajax_search_person_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo ucwords(TextHelper::_('COBALT_ADD_PERSON')); ?></h3>
            </div>
            <div class="modal-body">
                <form method="post" action="index.php">
                    <div id="person_complete">
                        <input class="form-control" name="person_name"  type="text" placeholder="<?php echo TextHelper::_('COBALT_BEGIN_TYPING_TO_SEARCH'); ?>">
                    </div>
                    <input type="hidden" name="task" value="saveCf" />
                    <input type="hidden" name="format" value="raw" />
                    <input type="hidden" name="tmpl" value="component" />
                    <input type="hidden" name="table" value="people" />
                    <input type="hidden" name="association_id" id="association_id" value="" />
                    <input type="hidden" name="person_id" id="person_id" value="">
                    <input type="hidden" name="association_type" id="association_type" value="deal" />
                    <input type="hidden" name="loc" id="loc" value="" />
                </form>
            </div>
            <div class="modal-footer">
                <div class="actions"><input class="btn btn-success" type="button" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" onclick="Cobalt.sumbitModalForm(this);"/> <?php echo TextHelper::_('COBALT_OR'); ?> <a href="javascript:void(0);" data-dismiss="modal" aria-hidden="true"><?php echo TextHelper::_('COBALT_CANCEL'); ?></a></div>
            </div>
        </div>
    </div>
</div>
<div class="clearfix" id="contacts">
    <?php if ( is_array($contacts) && count($contacts) > 0 ){ foreach ($contacts as $person) { ?>
            <?php if (isset($this->primary_contact_id) && $this->primary_contact_id == $person['id']) { $class = "active"; } else { $class = ""; } ?>
            <div class="media <?php echo $class; ?>" id="person_container_<?php echo $person['id']; ?>">
              <span class="pull-left widget">
                    <img id="avatar_img_<?php echo $person['id']; ?>" data-item-type="people" data-item-id="<?php echo $person['id']; ?>" class="avatar" src="<?php echo $person['avatar']; ?>"/>
              </span>
              <div class="media-body">
                  <div class="pull-left">
                            <div class="text-center socialIcons infoDetails">
                            <?php if (array_key_exists('twitter_user',$person) && $person['twitter_user'] != "") { ?>
                                <a href="http://www.twitter.com/#!/<?php echo $person['twitter_user']; ?>" target="_blank"><div class="twitter_light"></div></a>
                            <?php } else { ?>
                            <span class="editable parent" id="editable_twitter_container_<?php echo $person['id']; ?>">
                            <div class="list-inline">
                                <a data-html="true" data-content='<div class="input-append"><form id="twitter_form_<?php echo $person['id']; ?>">
                                    <input type="hidden" name="item_id" value="<?php echo $person['id']; ?>" />
                                    <input type="hidden" name="item_type" value="people" />
                                    <input type="text" class="form-control input-small" name="twitter_user" value="<?php if ( array_key_exists('twitter_user',$person) ) echo $person['twitter_user']; ?>" />
                                    <a href="javascript:void(0);" class="btn" onclick="Cobalt.saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                                </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_TWITTER'); ?>" href="javascript:void(0);"><div class="twitter_dark"></div></a>
                            </div>
                            </span>
                            <?php } ?>

                            <?php if (array_key_exists('facebook_url',$person) && $person['facebook_url'] != "") { ?>
                                <a href="<?php echo $person['facebook_url']; ?>" target="_blank"><div class="facebook_light"></div></a>
                            <?php } else { ?>
                            <span class="editable parent" id="editable_facebook_container_<?php echo $person['id']; ?>">
                            <div class="list-inline">
                                <a data-html="true" data-content='<div class="input-append"><form id="facebook_form_<?php echo $person['id']; ?>">
                                    <input type="hidden" name="item_id" value="<?php echo $person['id']; ?>" />
                                    <input type="hidden" name="item_type" value="people" />
                                    <input type="text" class="form-control input-small" name="facebook_url" value="<?php if ( array_key_exists('facebook_url',$person) ) echo $person['facebook_url']; ?>" />
                                    <a href="javascript:void(0);" class="btn button" onclick="Cobalt.saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                                </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_FACEBOOK'); ?>" href="javascript:void(0);"><div class="facebook_dark"></div></a>
                            </div>
                            </span>
                            <?php } ?>

                            <?php if (array_key_exists('linkedin_url',$person) && $person['linkedin_url'] != "" ) { ?>
                                <a rel="popover" href="<?php echo $person['linkedin_url']; ?>" target="_blank"><div class="linkedin_light"></div></a>
                            <?php } else { ?>
                            <span class="editable parent" id="editable_linkedin_container_<?php echo $person['id']; ?>">
                            <div class="list-inline">
                                <a data-html="true" data-content='<div class="input-append"><form id="linkedin_form_<?php echo $person['id']; ?>">
                                    <input type="hidden" name="item_id" value="<?php echo $person['id']; ?>" />
                                    <input type="hidden" name="item_type" value="people" />
                                    <input type="text" class="form-control input-small" name="linkedin_url" value="<?php if ( array_key_exists('linkedin_url',$person) ) echo $person['linkedin_url']; ?>" />
                                    <a href="javascript:void(0);" class="btn button" onclick="Cobalt.saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                                </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_LINKEDIN'); ?>" href="javascript:void(0);"><div class="linkedin_dark"></div></a>
                            </div>
                            </span>
                            <?php } ?>

                            <span class="editable parent" id="editable_aim_container_<?php echo $person['id']; ?>">
                                    <div class="list-inline">
                                        <?php if (array_key_exists('aim',$person) && $person['aim'] != "" ) { ?>
                                            <a data-html="true" data-content='<div class="input-append"><form id="aim_form_<?php echo $person['id']; ?>">
                                            <input type="hidden" name="item_id" value="<?php echo $person['id']; ?>" />
                                            <input type="hidden" name="item_type" value="people" />
                                            <input type="text" class="form-control input-small" name="aim" value="<?php if ( array_key_exists('aim',$person) )  echo $person['aim']; ?>" />
                                            <a href="javascript:void(0);" class="btn button" onclick="Cobalt.saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                                        </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_AIM'); ?>" href="javascript:void(0);"><div class="aim_light"></div></a>
                                        <?php } else { ?>
                                            <a data-html="true" data-content='<div class="input-append"><form id="aim_form_<?php echo $person['id']; ?>">
                                            <input type="hidden" name="item_id" value="<?php echo $person['id']; ?>" />
                                            <input type="hidden" name="item_type" value="people" />
                                            <input type="text" class="form-control input-small" name="aim" value="<?php if ( array_key_exists('aim',$person) )  echo $person['aim']; ?>" />
                                            <a href="javascript:void(0);" class="btn button" onclick="Cobalt.saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                                        </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_AIM'); ?>" href="javascript:void(0);"><div id="aim_button_<?php echo $person['id']; ?>" class="aim_dark"></div></a>
                                        <?php } ?>
                                    </div>
                                    </span>
                                </div>
                            </span>

                    <div class="btn-group">
                        <a href="javascript:void(0);" id="contact-id-<?php echo $person['id']; ?>" class="dropdown-toggle" data-toggle="dropdown"><?php echo $person['first_name'] . ' ' . $person['last_name']; ?> <b class="caret"></b></a>
                        <?php if ( $app->input->get('view') == "contacts" || $app->input->get('view') == "deals" || $app->input->get('loc') == "deal" ) { ?>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="<?php echo RouteHelper::_('index.php?view=people&layout=person&id='.$person['id']);?>">View Contact</a></li>
                                    <?php if ($this->primary_contact_id == $person['id']) { ?>
                                        <li><a class="star" id="primary_contact" onclick="Deals.assignPrimaryContact(0)" data-id="<?php echo $person['id']; ?>" href="javascript:void(0);" > <i class="glyphicon glyphicon-star"></i></a></li>
                                    <?php } else { ?>
                                        <li><a class="star" id="star_<?php echo $person['id']; ?>" data-id="<?php echo $person['id']; ?>" onclick="Deals.assignPrimaryContact(<?php echo $person['id']; ?>);" href="javascript:void(0);" > <i class="glyphicon glyphicon-star-empty"></i> </a></li>
                                    <?php } ?>
                                    <li><a class="remove" href="javascript:void(0);" onclick="Deals.removeContact(<?php echo $person['id']; ?>);"><i class="glyphicon glyphicon-trash"></i></a></li>
                                </ul>
                        <?php } ?>
                    </div>
                    <?php if (array_key_exists('position',$person)) { ?>
                      <br><?php echo $person['position']; ?> <?php echo TextHelper::_('COBALT_AT'); ?>
                    <?php } ?>
                    <?php if (array_key_exists('company_id',$person)) { ?>
                        <a href="<?php echo RouteHelper::_("index.php?view=companies&layout=company&company_id=".$person['company_id']); ?>"><?php echo $person['company_name']; ?></a><br>
                    <?php } ?>
                    <?php if (array_key_exists('email',$person) && $person['email']!="") { ?>
                            <a href="mailto:<?php echo $person['email']; ?>"><?php echo $person['email']; ?></a>
                    <?php } ?>
                  </div>

            </div>
        </div>
    <?php } } ?>
</div>
<script>
Deals.initRemoveContact();
Deals.initAssignContact();
if (typeof association_type != 'undefined') {
    $('#association_type').val(association_type);
}
if (typeof deal_id != 'undefined') {
    $('#association_id').val(deal_id);
}
$('#loc').val(loc);

CobaltAutocomplete.create({
    id: 'addperson',
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
$('input[name=person_name]').typeahead({
    highlight: true
},CobaltAutocomplete.getConfig('addperson')).on('typeahead:selected', function(event, item, name){
    jQuery('#person_id').val(item.id);
});
</script>