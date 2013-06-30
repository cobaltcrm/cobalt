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
defined( '_JEXEC' ) or die( 'Restricted access' );

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
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel"><?php echo ucwords(CRMText::_('COBALT_EDIT_COMPANY')); ?></h3>
    </div>
    <div class="modal-body">
        <p></p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo ucwords(CRMText::_('COBALT_CANCEL')); ?></button>
        <button onclick="saveProfileItem('edit_form')" class="btn btn-primary"><?php echo ucwords(CRMText::_('COBALT_SAVE')); ?></button>
    </div>
</div>

<iframe id="hidden" name="hidden" style="display:none;width:0px;height:0px;border:0px;"></iframe>

<div class="row-fluid">

	<!-- LEFT MODULE -->
	<div class="span8">
		<div class="page-header">
			<!-- ACTIONS -->
			<div class="btn-group pull-right">
			    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
			        <?php echo CRMText::_('COBALT_ACTION_BUTTON'); ?>
			        <span class="caret"></span>
			    </a>
			    <ul class="dropdown-menu">
			        <li><a role="button" href="#companyModal" data-toggle="modal"><?php echo CRMText::_('COBALT_EDIT_BUTTON'); ?></a></li>
					<?php if ( CobaltHelperUsers::isAdmin() ) { ?>
						<li><a href="index.php?controller=trash&item_id=<?php echo $company['id']; ?>&item_type=companies&page_redirect=companies" onclick="deleteProfileItem(this)"><?php echo CRMText::_('COBALT_DELETE'); ?></a></li>
					<?php } ?>
					<li>
						<a href="index.php?view=print&item_id=<?php echo $company['id']; ?>&layout=company&model=company" target="_blank"><?php echo CRMText::_('COBALT_PRINT'); ?></a>
					</li>
			    </ul>
			</div>

			<!-- HEADER -->
			<h1><?php echo $company['name']; ?></h1>
		</div>

		<div class="row-fluid">
			<div class="span4 well well-small">
					<?php echo ucwords(CRMText::_('COBALT_COMPANY_TOTAL_PIPELINE')); ?>:
					<span class="amount"><?php echo CobaltHelperConfig::getCurrency(); ?><?php echo $company['pipeline']; ?></span></td>
			</div>
			<div class="span4 well well-small">
					<?php echo ucwords(CRMText::_('COBALT_COMPANY_DEALS')); ?>:
					<span class="text-success"><?php echo CobaltHelperConfig::getCurrency(); ?><?php echo $company['won_deals']; ?></span>
			</div>

			<div class="span4 well well-small">
					<?php echo ucwords(CRMText::_('COBALT_COMPANY_CONTACTED')); ?>:
					<?php echo CobaltHelperDate::formatDate($company['modified']); ?>
			</div>
		</div>

		<!-- NOTES -->
		<?php echo $company['notes']->render(); ?>

		<!-- CUSTOM FIELDS -->
		<h3><?php echo CRMText::_('COBALT_EDIT_CUSTOM'); ?></h3>
		<div class="columncontainer">
			<?php echo $this->custom_fields_view->render(); ?>
		</div>
		<hr />

		<!-- DEALS -->
		<span class="pull-right"><a class="btn" onclick="addDeal('company_id=<?php echo $company['id']; ?>')" href="javascript:void(0);"><i class="icon-plus"></i><?php echo ucwords(CRMText::_('COBALT_ADD_DEAL')); ?></a></span>
		<h3><?php echo ucwords(CRMText::_('COBALT_EDIT_DEALS')); ?></h3>
		<div class="large_info">
			<?php echo $this->deal_dock->render(); ?>
		</div>
		<hr />

		<!-- PEOPLE -->
		<span class="pull-right"><a class="btn" href="javascript:void(0);" onclick="addPerson('company_id=<?php echo $company['id']; ?>');"><i class="icon-plus"></i><?php echo ucwords(CRMText::_('COBALT_ADD_PERSON')); ?></a></span>
		<h3><?php echo ucwords(CRMText::_('COBALT_EDIT_PEOPLE')); ?></h3>
		<div class="large_info">
			<?php echo $this->people_dock->render(); ?>
		</div>
		<hr />

		<!-- DOCUMENT UPLOAD BUTTON -->
		<span class="pull-right">
		    <form id="upload_form" target="hidden" action="index.php?controller=upload" method="POST" enctype="multipart/form-data">
		        <div class="fileupload fileupload-new" data-provides="fileupload">
		         	<span class="btn btn-file"><span class="fileupload-new" id="upload_button"><i class="icon-upload"></i><?php echo CRMText::_('COBALT_UPLOAD_FILE'); ?></span><span class="fileupload-exists"><?php echo CRMText::_('COBALT_UPLOADING_FILE'); ?></span><input type="file" id="upload_input_invisible" name="document" /></span>
		        </div>
		        <input type="hidden" name="association_id" value="<?php echo $company['id']; ?>" />
				<input type="hidden" name="association_type" value='company' />
		    </form>
		</span>
		<!-- DOCUMENTS -->
		<h2><?php echo CRMText::_('COBALT_EDIT_DOCUMENTS'); ?></h2>
		<div class="large_info">
	         <table class="table table-striped table-hover" id="documents_table">
		        <thead>
		            <th><?php echo CRMText::_('COBALT_TYPE'); ?></th>
		            <th><?php echo CRMText::_('COBALT_FILE_NAME'); ?></th>
		            <th><?php echo CRMText::_('COBALT_OWNER'); ?></th>
		            <th><?php echo CRMText::_('COBALT_SIZE'); ?></th>
		            <th><?php echo CRMText::_('COBALT_UPLOADED'); ?></th>
		        </thead>
		        <tbody id="documents">
		           <?php echo $this->document_list->render(); ?>
		        </tbody>
	        </table>
	    </div>

	</div>

	<!-- RIGHT MODULE -->
	<div class="span4">
		<div class="widget" id="details">

			<!-- COMPANY DETAILS -->
			<h3><?php echo ucwords(CRMText::_('COBALT_COMPANY_DETAILS')); ?></h3>
			<div class="media">
				<span class="pull-left">
					<?php if ( array_key_exists('avatar',$company) && $company['avatar'] != "" && $company['avatar'] != null ){
	                         echo '<img id="avatar_img_'.$company['id'].'" data-item-type="companies" data-item-id="'.$company['id'].'" class="media-object avatar" src="'.JURI::base().'libraries/crm/media/avatars/'.$company['avatar'].'"/>';
	                    }else{
	                        echo '<img id="avatar_img_'.$company['id'].'" data-item-type="companies" data-item-id="'.$company['id'].'" class="media-object avatar" src="'.JURI::base().'libraries/crm/media/images/company.png'.'"/>';
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
						<?php if ( array_key_exists('address_2',$company) && $company['address_2'] != "" ){
							echo $company['address_2'].'<br />';
						} ?>
						<?php echo $company['address_city'].', '.$company['address_state']." ".$company['address_zip']; ?><br />
						<?php echo $company['address_country']; ?>
					<?php } ?>
			<?php if ( array_key_exists('phone',$company) && $company['phone']!="") { ?>
					<b><?php echo ucwords(CRMText::_('COBALT_COMPANY_PHONE')); ?></b>
					<div class="infoDetails">
						<?php echo $company['phone']; ?>
					</div>
			<?php } ?>
			<?php if ( array_key_exists('fax',$company) && $company['fax']!="" ) { ?>
					<b><?php echo ucwords(CRMText::_('COBALT_COMPANY_FAX')); ?></b>
					<div class="infoDetails">
						<?php echo $company['fax']; ?>
					</div>
			<?php } ?>
			<?php if ( array_key_exists('website',$company) && $company['website']!="") { ?>
					<b><?php echo ucwords(CRMText::_('COBALT_COMPANY_WEBSITE')); ?></b>
					<div class="infoDetails">
						<a target="_blank" href="<?php echo $company['website']; ?>"><?php echo $company['website']; ?></a>
					</div>
			<?php } ?>
			<?php if ( array_key_exists('email',$company) && $company['email']!="") { ?>
					<b><?php echo ucwords(CRMText::_('COBALT_COMPANY_EMAIL')); ?></b>
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
				<?php if(array_key_exists('facebook_url',$company) && $company['facebook_url'] != ""){ ?>
					<a href="<?php echo $company['facebook_url']; ?>" target="_blank"><div class="facebook_light"></div></a>
				<?php } else { ?>
					<a data-html="true" data-content='<div class="input-append"><form id="facebook_form_<?php echo $company['id']; ?>">
					<input type="hidden" name="item_id" value="<?php echo $company['id']; ?>" />
					<input type="hidden" name="item_type" value="people" />
					<input type="text" class="inputbox input-small" name="facebook_url" value="<?php if ( array_key_exists('facebook',$company) )  echo $company['facebook_url']; ?>" />
					<a href="javascript:void(0);" class="btn button" onclick="saveEditableModal(this);" ><?php echo CRMText::_('COBALT_SAVE'); ?></a>
					</form></div>' rel="popover" title="<?php echo CRMText::_('COBALT_UPDATE_FIELD').' '.CRMText::_('COBALT_FACEBOOK_URL'); ?>" href="javascript:void(0);"><div class="facebook_dark"></div></a>
				<?php } ?>

				<!-- TWITTER -->
				<?php if(array_key_exists('twitter_user',$company) && $company['twitter_user'] != ""){ ?>
					<a href="http://www.twitter.com/#!/<?php echo $company['twitter_user']; ?>" target="_blank"><div class="twitter_light"></div></a>
				<?php } else { ?>
					<a data-html="true" data-content='<div class="input-append"><form id="twitter_form_<?php echo $company['id']; ?>">
					<input type="hidden" name="item_id" value="<?php echo $company['id']; ?>" />
					<input type="hidden" name="item_type" value="people" />
					<input type="text" class="inputbox input-small" name="twitter_user" value="<?php if ( array_key_exists('twitter_user',$company) )  echo $company['twitter_user']; ?>" />
					<a href="javascript:void(0);" class="btn button" onclick="saveEditableModal(this);" ><?php echo CRMText::_('COBALT_SAVE'); ?></a>
					</form></div>' rel="popover" title="<?php echo CRMText::_('COBALT_UPDATE_FIELD').' '.CRMText::_('COBALT_TWITTER_USER'); ?>" href="javascript:void(0);"><div class="twitter_dark"></div></a>
				<?php } ?>

				<!-- YOUTUBE -->
				<?php if(array_key_exists('youtube_url',$company) && $company['youtube_url'] != "" ){ ?>
					<a href="<?php echo $company['youtube_url']; ?>" target="_blank"><div class="youtube_light"></div></a>
				<?php } else { ?>
					<a data-html="true" data-content='<div class="input-append"><form id="youtube_form_<?php echo $company['id']; ?>">
					<input type="hidden" name="item_id" value="<?php echo $company['id']; ?>" />
					<input type="hidden" name="item_type" value="people" />
					<input type="text" class="inputbox input-small" name="youtube_url" value="<?php if ( array_key_exists('youtube_url',$company) )  echo $company['youtube_url']; ?>" />
					<a href="javascript:void(0);" class="btn button" onclick="saveEditableModal(this);" ><?php echo CRMText::_('COBALT_SAVE'); ?></a>
					</form></div>' rel="popover" title="<?php echo CRMText::_('COBALT_UPDATE_FIELD').' '.CRMText::_('COBALT_YOUTUBE_URL'); ?>" href="javascript:void(0);"><div class="youtube_dark"></div></a>
				<?php } ?>

				<!-- FLICKR -->
				<?php if(array_key_exists('flickr_url',$company) && $company['flickr_url'] != "" ){ ?>
					<a href="<?php echo $company['flickr_url']; ?>" target="_blank"><div class="flickr_light"></div></a>
				<?php } else { ?>
					<a data-html="true" data-content='<div class="input-append"><form id="flickr_form_<?php echo $company['id']; ?>">
					<input type="hidden" name="item_id" value="<?php echo $company['id']; ?>" />
					<input type="hidden" name="item_type" value="people" />
					<input type="text" class="inputbox input-small" name="flickr_url" value="<?php if ( array_key_exists('flickr_url',$company) )  echo $company['flickr_url']; ?>" />
					<a href="javascript:void(0);" class="btn button" onclick="saveEditableModal(this);" ><?php echo CRMText::_('COBALT_SAVE'); ?></a>
					</form></div>' rel="popover" title="<?php echo CRMText::_('COBALT_UPDATE_FIELD').' '.CRMText::_('COBALT_FLICKR_URL'); ?>" href="javascript:void(0);"><div class="flickr_dark"></div></a>
				<?php } ?>

			</div>

		</div>

		<!-- TWEETS -->
		<?php if($company['twitter_user']) { ?>
			<div class="widget">
				<h2><?php echo CRMText::_('COBALT_LATEST_TWEETS'); ?></h2>
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
		<?php if ( isset($this->banter_dock) ){
			echo $this->banter_dock->render();
		}?>
		<!-- EVENT DOCK -->
		<div class="widget" id='event_dock'>
			<?php echo $this->event_dock->render(); ?>
		</div>

	</div>

</div>

<!-- MESSAGE MODAL -->
<div id="message" style="display:none;"><?php echo CRMText::_('COBALT_SUCCESS_MESSAGE'); ?></div>

<!-- PERSON ASSOCIATION -->
<div class='modal hide fade' role='dialog' tabindex='-1' aria-hidden='true' id='ajax_search_person_dialog'>
	<div class="modal-header small"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3><?php echo CRMText::_('COBALT_ASSOCIATE_PERSON'); ?></h3></div>
	<div class="modal-body text-center">
		<div class="input-append">
			<input name="person_name" class="inputbox" type="text" placeholder="<?php echo CRMText::_('COBALT_BEGIN_TYPING_USER'); ?>" />
			<input type="hidden" name="shared_user_id" id="shared_user_id" />';
			<a class="btn btn-success" href="javascript:void(0);" onclick="addPersonToCompany();"><i class="icon-white icon-plus"></i><?php echo CRMText::_('COBALT_ADD'); ?></a>
		</div>
	</div>
</div>

<!--- DEAL ASSOCIATION -->
<div class='modal hide fade' role='dialog' tabindex='-1' aria-hidden='true' id='ajax_search_deal_dialog'>
	<div class="modal-header small"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3><?php echo CRMText::_('COBALT_ASSOCIATE_DEAL'); ?></h3></div>
	<div class="modal-body text-center">
		<form id="deal">
			<div class="input-append">
				<input name="deal_name" class="inputbox" type="text" placeholder="<?php echo CRMText::_('COBALT_BEGIN_TYPING_TO_SEARCH'); ?>" />
				<input type="hidden" name="company_id" value="<?php echo $company['id'];  ?>" />
				<a class="btn btn-success" href="javascript:void(0);" onclick="saveAjax('deal','deal');"><i class="icon-white icon-plus"></i><?php echo CRMText::_('COBALT_SAVE'); ?></a>
			</div>
		</form>
	</div>
</div>