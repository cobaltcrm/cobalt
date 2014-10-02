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

//define company
$company = $this->companies[0];?>

<script type="text/javascript">
    var loc = "company";
    var id = <?php echo $company['id']; ?>;
    var company_id = <?php echo $company['id']; ?>;
    var association_type = 'company';
</script>

<!-- COMPANY EDIT MODAL -->
<div data-remote="index.php?view=companies&layout=edit&format=raw&tmpl=component&id=<?php echo $company['id']; ?>" class="modal hide fade" id="companyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo ucwords(TextHelper::_('COBALT_EDIT_COMPANY')); ?></h3>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo ucwords(TextHelper::_('COBALT_CANCEL')); ?></button>
                <button onclick="saveProfileItem('edit_form')" class="btn btn-primary"><?php echo ucwords(TextHelper::_('COBALT_SAVE')); ?></button>
            </div>
        </div>
    </div>
</div>

<iframe id="hidden" name="hidden" style="display:none;width:0px;height:0px;border:0px;"></iframe>

<div class="row-fluid">

    <!-- LEFT MODULE -->
    <div class="col-md-8">
        <div class="page-header">
            <!-- ACTIONS -->
            <div class="btn-group pull-right">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                    <?php echo TextHelper::_('COBALT_ACTION_BUTTON'); ?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a role="button" href="#companyModal" data-toggle="modal"><?php echo TextHelper::_('COBALT_EDIT_BUTTON'); ?></a></li>
                    <?php if ( UsersHelper::isAdmin() ) { ?>
                        <li><a href="index.php?task=trash&item_id=<?php echo $company['id']; ?>&item_type=companies&page_redirect=companies" onclick="deleteProfileItem(this)"><?php echo TextHelper::_('COBALT_DELETE'); ?></a></li>
                    <?php } ?>
                    <li>
                        <a href="index.php?view=print&item_id=<?php echo $company['id']; ?>&layout=company&model=company" target="_blank"><?php echo TextHelper::_('COBALT_PRINT'); ?></a>
                    </li>
                </ul>
            </div>

            <!-- HEADER -->
            <h1><?php echo $company['name']; ?></h1>
        </div>

        <div class="row-fluid">
            <div class="col-md-4 well well-small">
                    <?php echo ucwords(TextHelper::_('COBALT_COMPANY_TOTAL_PIPELINE')); ?>:
                    <span class="amount"><?php echo ConfigHelper::getCurrency(); ?><?php echo $company['pipeline']; ?></span></td>
            </div>
            <div class="col-md-4 well well-small">
                    <?php echo ucwords(TextHelper::_('COBALT_COMPANY_DEALS')); ?>:
                    <span class="text-success"><?php echo ConfigHelper::getCurrency(); ?><?php echo $company['won_deals']; ?></span>
            </div>

            <div class="col-md-4 well well-small">
                    <?php echo ucwords(TextHelper::_('COBALT_COMPANY_CONTACTED')); ?>:
                    <?php echo DateHelper::formatDate($company['modified']); ?>
            </div>
        </div>

        <!-- NOTES -->
        <?php echo $company['notes']->render(); ?>

        <!-- CUSTOM FIELDS -->
        <h3><?php echo TextHelper::_('COBALT_EDIT_CUSTOM'); ?></h3>
        <div class="columncontainer">
            <?php echo $this->custom_fields_view->render(); ?>
        </div>
        <hr />

        <!-- DEALS -->

        <span class="pull-right"><a class="btn" onclick="Cobalt.resetModalForm(this)" data-target="#ajax_search_deal_dialog" data-toggle="modal" href="javascript:void(0);"><i class="glyphicon glyphicon-plus"></i><?php echo ucwords(TextHelper::_('COBALT_ADD_DEAL')); ?></a></span>
        <h3><?php echo ucwords(TextHelper::_('COBALT_EDIT_DEALS')); ?></h3>
        <div class="large_info">
            <?php echo $this->deal_dock->render(); ?>
        </div>
        <hr />

        <!-- PEOPLE -->

        <span class="pull-right"><a class="btn" href="javascript:void(0);" onclick="Cobalt.resetModalForm(this);" data-target="#ajax_search_person_dialog" data-toggle="modal"><i class="glyphicon glyphicon-plus"></i><?php echo ucwords(TextHelper::_('COBALT_ADD_PERSON')); ?></a></span>
        <h3><?php echo ucwords(TextHelper::_('COBALT_EDIT_PEOPLE')); ?></h3>
        <div class="large_info">
            <?php echo $this->people_dock->render(); ?>
        </div>
        <hr />

        <!-- DOCUMENT UPLOAD BUTTON -->
        <span class="actions pull-right">
            <form id="upload_form" action="index.php?task=upload" method="post" enctype="multipart/form-data">


                <div class="btn-group">
                    <div class="btn btn-default btn-file">
                        <i class="glyphicon glyphicon-plus"></i>  <?php echo TextHelper::_('COBALT_UPLOAD_FILE'); ?> <input type="file" id="upload_input_invisible" name="document" />
                    </div>
                </div>



                <input type="hidden" name="association_id" value="<?php echo $company['id']; ?>" />
                <input type="hidden" name="association_type" value="company">
                <input type="hidden" name="return" value="<?php echo base64_encode(JUri::current()); ?>" />
            </form>
        </span>
        <!-- DOCUMENTS -->
        <h2><?php echo TextHelper::_('COBALT_EDIT_DOCUMENTS'); ?></h2>
        <div class="large_info">
             <table class="table table-striped table-hover" id="documents_table">

                   <?php echo $this->document_list->render(); ?>
            </table>
        </div>

    </div>

    <!-- RIGHT MODULE -->
    <div class="col-md-4">
        <div class="widget" id="details">

            <!-- COMPANY DETAILS -->
            <h3><?php echo ucwords(TextHelper::_('COBALT_COMPANY_DETAILS')); ?></h3>
            <div class="media">
                <span class="pull-left">
                    <?php if ( array_key_exists('avatar',$company) && $company['avatar'] != "" && $company['avatar'] != null ) {
                             echo '<img id="avatar_img_'.$company['id'].'" data-item-type="companies" data-item-id="'.$company['id'].'" class="media-object avatar" src="'.JURI::base().'src/Cobalt/media/avatars/'.$company['avatar'].'"/>';
                        } else {
                            echo '<img id="avatar_img_'.$company['id'].'" data-item-type="companies" data-item-id="'.$company['id'].'" class="media-object avatar" src="'.JURI::base().'src/Cobalt/media/images/company.png'.'"/>';
                        } ?>
                </span>
                <div class="media-body">
                    <?php if ( array_key_exists('address_1',$company) && $company['address_1'] != "" ) { ?>
                        <?php $urlString = "http://maps.googleapis.com/maps/api/staticmap?&zoom=13&zoom=2&size=600x400&sensor=false&center=".str_replace(" ","+",$company['address_1'].' '.$company['address_2'].' '.$company['address_city'].' '.$company['address_state'].' '.$company['address_zip'].' '.$company['address_country']); ?>
                        <a href="javascript:void(0);" class="google-map" id="work_address"></a>
                        <div id="work_address_modal" style="display:none;">
                            <div class="google_map_center"></div>
                            <img class="google-image-modal"  style="background-image:url(<?php echo $urlString; ?>);" />
                        </div>
                        <?php echo $company['address_1']; ?><br />
                        <?php if ( array_key_exists('address_2',$company) && $company['address_2'] != "" ) {
                            echo $company['address_2'].'<br />';
                        } ?>
                        <?php echo $company['address_city'].', '.$company['address_state']." ".$company['address_zip']; ?><br />
                        <?php echo $company['address_country']; ?>
                    <?php } ?>
            <?php if ( array_key_exists('phone',$company) && $company['phone']!="") { ?>
                    <b><?php echo ucwords(TextHelper::_('COBALT_COMPANY_PHONE')); ?></b>
                    <div class="infoDetails">
                        <?php echo $company['phone']; ?>
                    </div>
            <?php } ?>
            <?php if ( array_key_exists('fax',$company) && $company['fax']!="" ) { ?>
                    <b><?php echo ucwords(TextHelper::_('COBALT_COMPANY_FAX')); ?></b>
                    <div class="infoDetails">
                        <?php echo $company['fax']; ?>
                    </div>
            <?php } ?>
            <?php if ( array_key_exists('website',$company) && $company['website']!="") { ?>
                    <b><?php echo ucwords(TextHelper::_('COBALT_COMPANY_WEBSITE')); ?></b>
                    <div class="infoDetails">
                        <a target="_blank" href="<?php echo $company['website']; ?>"><?php echo $company['website']; ?></a>
                    </div>
            <?php } ?>
            <?php if ( array_key_exists('email',$company) && $company['email']!="") { ?>
                    <b><?php echo ucwords(TextHelper::_('COBALT_COMPANY_EMAIL')); ?></b>
                    <div class="infoDetails">
                        <a href="mailto:<?php echo $company['email']; ?>"><?php echo $company['email']; ?></a>
                    </div>
            <?php } ?>
                </div>
            </div>
            <?php if ( array_key_exists('description',$company) && $company['description']!="" ) { ?>
                <hr />
                <div class="infoLabel"></div>
                <div class="infoDetails">
                    <?php echo nl2br($company['description']); ?>
                </div>
            <?php } ?>
            <hr />

            <!-- SOCIAL MEDIA -->
            <div class="text-center">

                <!-- FACEBOOK -->
                <?php if (array_key_exists('facebook_url',$company) && $company['facebook_url'] != "") { ?>
                    <a href="<?php echo $company['facebook_url']; ?>" target="_blank"><div class="facebook_light"></div></a>
                <?php } else { ?>
                    <a data-html="true" data-content='<div class="input-append"><form id="facebook_form_<?php echo $company['id']; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $company['id']; ?>" />
                    <input type="hidden" name="item_type" value="people" />
                    <input type="text" class="form-control input-small" name="facebook_url" value="<?php if ( array_key_exists('facebook',$company) )  echo $company['facebook_url']; ?>" />
                    <a href="javascript:void(0);" class="btn button" onclick="Cobalt.saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                    </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_FACEBOOK_URL'); ?>" href="javascript:void(0);"><div class="facebook_dark"></div></a>
                <?php } ?>

                <!-- TWITTER -->
                <?php if (array_key_exists('twitter_user',$company) && $company['twitter_user'] != "") { ?>
                    <a href="http://www.twitter.com/#!/<?php echo $company['twitter_user']; ?>" target="_blank"><div class="twitter_light"></div></a>
                <?php } else { ?>
                    <a data-html="true" data-content='<div class="input-append"><form id="twitter_form_<?php echo $company['id']; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $company['id']; ?>" />
                    <input type="hidden" name="item_type" value="people" />
                    <input type="text" class="form-control input-small" name="twitter_user" value="<?php if ( array_key_exists('twitter_user',$company) )  echo $company['twitter_user']; ?>" />
                    <a href="javascript:void(0);" class="btn button" onclick="Cobalt.saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                    </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_TWITTER_USER'); ?>" href="javascript:void(0);"><div class="twitter_dark"></div></a>
                <?php } ?>

                <!-- YOUTUBE -->
                <?php if (array_key_exists('youtube_url',$company) && $company['youtube_url'] != "" ) { ?>
                    <a href="<?php echo $company['youtube_url']; ?>" target="_blank"><div class="youtube_light"></div></a>
                <?php } else { ?>
                    <a data-html="true" data-content='<div class="input-append"><form id="youtube_form_<?php echo $company['id']; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $company['id']; ?>" />
                    <input type="hidden" name="item_type" value="people" />
                    <input type="text" class="form-control input-small" name="youtube_url" value="<?php if ( array_key_exists('youtube_url',$company) )  echo $company['youtube_url']; ?>" />
                    <a href="javascript:void(0);" class="btn button" onclick="Cobalt.saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                    </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_YOUTUBE_URL'); ?>" href="javascript:void(0);"><div class="youtube_dark"></div></a>
                <?php } ?>

                <!-- FLICKR -->
                <?php if (array_key_exists('flickr_url',$company) && $company['flickr_url'] != "" ) { ?>
                    <a href="<?php echo $company['flickr_url']; ?>" target="_blank"><div class="flickr_light"></div></a>
                <?php } else { ?>
                    <a data-html="true" data-content='<div class="input-append"><form id="flickr_form_<?php echo $company['id']; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $company['id']; ?>" />
                    <input type="hidden" name="item_type" value="people" />
                    <input type="text" class="form-control input-small" name="flickr_url" value="<?php if ( array_key_exists('flickr_url',$company) )  echo $company['flickr_url']; ?>" />
                    <a href="javascript:void(0);" class="btn button" onclick="Cobalt.saveEditableModal(this);" ><?php echo TextHelper::_('COBALT_SAVE'); ?></a>
                    </form></div>' rel="popover" title="<?php echo TextHelper::_('COBALT_UPDATE_FIELD').' '.TextHelper::_('COBALT_FLICKR_URL'); ?>" href="javascript:void(0);"><div class="flickr_dark"></div></a>
                <?php } ?>

            </div>

        </div>

        <!-- TWEETS -->
        <?php if ($company['twitter_user']) { ?>
            <div class="widget">
                <h2><?php echo TextHelper::_('COBALT_LATEST_TWEETS'); ?></h2>
                <?php if ( array_key_exists('tweets',$company) ){ for($i=0; $i<count($company['tweets']); $i++) {
                    $tweet = $company['tweets'][$i];
                ?>
                <div class="tweet">
                    <span class="tweet_date"><?php echo $tweet['date']; ?></span>
                    <?php echo $tweet['tweet']; ?>
                </div>
                <?php } } ?>
            </div>
        <?php } ?>

        <!-- BANTER DOCK INTEGRATION -->
        <?php if ( isset($this->banter_dock) ) {
            echo $this->banter_dock->render();
        }?>
        <!-- EVENT DOCK -->
        <div class="widget" id='event_dock'>
            <?php echo $this->event_dock->render(); ?>
        </div>

    </div>

</div>

<!-- MESSAGE MODAL -->
<div id="message" style="display:none;"><?php echo TextHelper::_('COBALT_SUCCESS_MESSAGE'); ?></div>

<!-- PERSON ASSOCIATION -->

<div class="modal fade" id="ajax_search_person_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo ucwords(TextHelper::_('COBALT_ASSOCIATE_PERSON')); ?></h3>
            </div>
            <div class="modal-body">
                <form method="post" action="index.php">
                    <div id="search_person_autocomplete">
                        <input class="form-control" name="person_name" id="person_name" type="text" placeholder="<?php echo TextHelper::_('COBALT_BEGIN_TYPING_TO_SEARCH'); ?>">
                    </div>
                    <input type="hidden" name="task" value="addPersonToCompany" />
                    <input type="hidden" name="format" value="raw" />
                    <input type="hidden" name="tmpl" value="component" />
                    <input type="hidden" name="company_id" id="note_company_id" value="" />
                    <input type="hidden" name="person_id" id="person_id" value="">
                </form>
            </div>
            <div class="modal-footer">
                <div class="actions"><input class="btn btn-success" type="button" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" onclick="Cobalt.sumbitModalForm(this);"/> <?php echo TextHelper::_('COBALT_OR'); ?> <a href="javascript:void(0);" data-dismiss="modal" aria-hidden="true"><?php echo TextHelper::_('COBALT_CANCEL'); ?></a></div>
            </div>
        </div>
    </div>
</div>
<!--- DEAL ASSOCIATION -->
<div class='modal fade' role='dialog' tabindex='-1' aria-hidden='true' id='ajax_search_deal_dialog'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel"><?php echo ucwords(TextHelper::_('COBALT_ASSOCIATE_DEAL')); ?></h3>
            </div>
            <div class="modal-body">
                <form method="post" action="index.php">
                    <div id="search_deal_autocomplete">
                        <input class="form-control" name="deal_name" id="deal_name" type="text" placeholder="<?php echo TextHelper::_('COBALT_BEGIN_TYPING_TO_SEARCH'); ?>">
                    </div>
                    <input type="hidden" name="task" value="SaveAjax" />
                    <input type="hidden" name="format" value="raw" />
                    <input type="hidden" name="tmpl" value="component" />
                    <input type="hidden" name="field" value="company_id" />
                    <input type="hidden" name="value" id="deal_company_id" value="" />
                    <input type="hidden" name="item_id" id="deal_id" value="">
                    <input type="hidden" name="item_type" value="deal">
                </form>
            </div>
            <div class="modal-footer">
                <div class="actions"><input class="btn btn-success" type="button" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" onclick="Cobalt.sumbitModalForm(this);"/> <?php echo TextHelper::_('COBALT_OR'); ?> <a href="javascript:void(0);" data-dismiss="modal" aria-hidden="true"><?php echo TextHelper::_('COBALT_CANCEL'); ?></a></div>
            </div>
        </div>
    </div>
</div>
<script>
    Company.addPerson();
    Company.addDeal();
</script>
