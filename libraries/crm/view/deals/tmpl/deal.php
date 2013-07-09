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

//define deal
$deal = $this->dealList[0];
?>

<script type="text/javascript">
	var id = <?php echo $deal['id']; ?>;
	var deal_id = <?php echo $deal['id']; ?>;
	var loc = "deal";
	var AMOUNT = <?php $deal['amount'] = ( $deal['amount'] == 0 ) ? 0 : $deal['amount']; echo $deal['amount']; ?>;
	var archived = <?php echo (int)$deal['archived']; ?>;
	var association_type = 'deal';
</script>

<div data-remote="index.php?view=deals&layout=edit&format=raw&tmpl=component&id=<?php echo $deal['id']; ?>" class="modal hide fade" id="dealModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"><?php echo ucwords(CRMText::_('COBALT_EDIT_DEAL')); ?></h3>
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

	<div class="span8">

		<div class="page-header">
			<div class="btn-group pull-right">
			    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
			        <?php echo CRMText::_('COBALT_ACTION_BUTTON'); ?>
			        <span class="caret"></span>
			    </a>
			    <ul class="dropdown-menu">
			    	<li><a role="button" href="#dealModal" data-toggle="modal"><?php echo CRMText::_('COBALT_EDIT_BUTTON'); ?></a></li>
					<li><a href="javascript:void(0);" id="archive" ><?php if($deal['archived']==0) echo CRMText::_('COBALT_ARCHIVE'); if($deal['archived']==1) echo CRMText::_('COBALT_UNARCHIVE'); ?></a></li>
					<?php if ( $deal['owner_id'] == CobaltHelperUsers::getUserId() ){ ?>
						<li><a href="javascript:void(0);" onclick="shareItemDialog();" ><?php echo CRMText::_('COBALT_SHARE'); ?></a></li>
					<?php } ?>
					<li>
						<?php if ( CobaltHelperUsers::canDelete() || $deal['owner_id'] == CobaltHelperUsers::getUserId() ) { ?>
							<a href="index.php?controller=trash&item_id=<?php echo $deal['id']; ?>&item_type=deals&page_redirect=deals" onclick="deleteProfileItem(this)"><?php echo CRMText::_('COBALT_DELETE'); ?></a>
						<?php } ?>
					</li>
					<li>
						<a href="index.php?view=print&item_id=<?php echo $deal['id']; ?>&layout=deal&model=deal" target="_blank"><?php echo CRMText::_('COBALT_PRINT'); ?></a>
					</li>
				</ul>
			</div>
			<h1><span id="name_<?php echo $deal['id']; ?>"><?php echo $deal['name']; ?></span><div id="status_name_<?php echo $deal['id']; ?>" class='deal-status-<?php echo strtolower($deal['status_name']); ?>'></div></h1>
			<p class="muted">
				<?php echo CRMText::_('COBALT_ASSOCIATED_WITH').' <a href="'.JRoute::_('index.php?view=companies&layout=company&id='.$deal['company_id']).'"><span id="company_name_'.$deal['id'].'">'.$deal['company_name'].'</span></a>'; ?>
			</p>
		</div>

		<div rel="tooltip" id="stage_name_<?php echo $deal['id']; ?>" title="<?php echo ucwords(CRMText::_('COBALT_STAGE')).": ".$deal['stage_name']; ?>" class="progress">
			<?php $light = "#".CobaltHelperCobalt::percent2color($deal['percent']); ?>
			<?php $dark = "#".CobaltHelperCobalt::percent2color($deal['percent']-20); ?>
          <div class="bar" id="percent_<?php echo $deal['id']; ?>" style="
          		background-image: -moz-linear-gradient(top,<?php echo $light; ?>,<?php echo $dark; ?>);
				background-image: -webkit-gradient(linear,0 0,0 100%,from(<?php echo $light; ?>),to(<?php echo $dark; ?>));
				background-image: -webkit-linear-gradient(top,<?php echo $light; ?>,<?php echo $dark; ?>);
				background-image: -o-linear-gradient(top,<?php echo $light; ?>,<?php echo $dark; ?>);
				background-image: linear-gradient(to bottom,<?php echo $light; ?>,<?php echo $dark; ?>);
				background-color:<?php echo $light; ?> !important; width: <?php echo $deal['percent']; ?>%;"></div>
        </div>
		<div class="row-fluid">
			<div class="span4 text-center">
				<div class="text-center well well-small">
					<?php echo CRMText::_('COBALT_EDIT_AMOUNT'); ?>
					<span class="editable parent" id="editable_amount_container">
						<div class="inline" id="editable_amount">
							<h2>
								<?php echo CobaltHelperConfig::getCurrency(); ?>
		                        <a href="javascript:void(0);" rel="popover" data-title="<?php echo ucwords(CRMText::_('COBALT_UPDATE_FIELD').' '.CRMText::_('COBALT_AMOUNT')); ?>" data-html='true' data-content='<div class="input-prepend input-append"><form class="inline-form" id="amount_form">
		                        	<span class="add-on"><?php echo CobaltHelperConfig::getCurrency(); ?></span>
										<input type="text" class="inputbox input-small" name="amount" value="<?php echo $deal['amount']; ?>" />
										<a href="javascript:void(0);" class="btn" onclick="saveEditableModal(this);"><?php echo CRMText::_('COBALT_SAVE'); ?></a>
									</form></div>' ><span id="amount_<?php echo $deal['id']; ?>"><?php echo $deal['amount']; ?></span></a>
							</h2>
	                    </div>
                	</span>
				</div>
				<div class="cobaltRow">
					<div class="cobaltField"><?php echo CRMText::_('COBALT_EDIT_OWNER'); ?></div>
					<div class="cobaltValue">
		                <div class='dropdown'>
	                        <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id='deal_owner_link'><span id="owner_first_name_<?php echo $deal['id']; ?>"><?php echo $deal['owner_first_name']." ".$deal['owner_last_name']; ?></span></a>
	                        <ul class="dropdown-menu" role="menu">
                        	<?php
	                        $me = array(array('label'=>CRMText::_('COBALT_ME'),'value'=>CobaltHelperUsers::getLoggedInUser()->id));
                        	$users = CobaltHelperUsers::getUsers(null,TRUE);
                        	$users = array_merge($me,$users);
	                        if ( count($users) ){ foreach ( $users as $key => $user ){ ?>
	                            <li>
	                                <a href="javascript:void(0)" class="dropdown_item" data-field="owner_id" data-item="deal" data-item-id="<?php echo $deal['id']; ?>" data-value="<?php echo $user['value']; ?>">
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
					<?php echo CRMText::_('COBALT_EDIT_PROBABILITY'); ?>
					<span class="editable parent" id="editable_probability_container">
					<div class="inline" id="editable_probability">
						<h2>
	                        <a href="javascript:void(0);" rel="popover" data-title="<?php echo ucwords(CRMText::_('COBALT_UPDATE_FIELD').' '.CRMText::_('COBALT_PROBABILITY')); ?>" data-html='true' data-content='<div class="input-append"><form class="inline-form" id="probability_form">
									<input type="text" class="inputbox input-small" name="probability" value="<?php echo $deal['probability']; ?>" />
									<span class="add-on">%</span>
									<a href="javascript:void(0);" class="btn" onclick="saveEditableModal(this);"><?php echo CRMText::_('COBALT_SAVE'); ?></a>
								</form></div>' ><span id="probability_<?php echo $deal['id']; ?>"><?php echo $deal['probability']; ?></span>%</a>
						</h2>
                    </div>
            		</span>
				</div>
				<div class="cobaltRow">
					<div class="cobaltField"><?php echo CRMText::_('COBALT_EDIT_AGE'); ?></div>
					<div class="cobaltValue">
						<?php
							echo CobaltHelperDate::getElapsedTime($deal['created']);
						?>
					</div>
				</div>
			</div>
			<div class="span4 text-center">
				<div class="text-center well well-small">
					<?php $style = "style='display:none;'"; ?>
					<?php if (in_array($deal['stage_id'],$this->closed_stages) ){
						$actual_close = true;
					} else {
						$actual_close = false;
					} ?>
					<div id="actual_close_container"<?php if ( !$actual_close ){ echo $style; } ?>>
						<?php echo CRMText::_('COBALT_ACTUAL_CLOSE'); ?>
						<h2>
							<form class="inline-form" name="actual_close_form">
								<input type="text" class="input-invisible input-small inputbox-hidden date_input" name="actual_close_hidden" id="actual_close" value="<?php echo CobaltHelperDate::formatDate($deal['actual_close']); ?>" />
								<input type="hidden" name="actual_close" id="actual_close_hidden" value="<?php echo $deal['actual_close']; ?>" />
							</form>
						</h2>
					</div>
					<div id="expected_close_container"<?php if ( $actual_close ){ echo $style; } ?>>
						<?php echo CRMText::_('COBALT_EXP_CLOSE'); ?>
						<h2>
							<form class="inline-form" name="expected_close_form">
								<input type="text" class="input-invisible input-small inputbox-hidden date_input" name="expected_close_hidden" id="expected_close" value="<?php echo CobaltHelperDate::formatDate($deal['expected_close']); ?>" />
								<input type="hidden" name="expected_close" id="expected_close_hidden" value="<?php echo $deal['expected_close']; ?>" />
							</form>
						</h2>
					</div>
				</div>
				<div class="cobaltRow">
					<div class="cobaltField"><?php echo CRMText::_('COBALT_EDIT_SOURCE'); ?></div>
					<div class="cobaltValue">
		                <div class='dropdown'>
	                        <a href='javascript:void(0);' class='dropdown-toggle update-toggle-html' role='button' data-toggle='dropdown' id='deal_source_<?php echo $deal['id']; ?>_link'>
	                        	<span id="source_name_<?php echo $deal['id']; ?>">
		                        	<?php $sourceName = $deal['source_id'] > 0 ? $deal['source_name'] : CRMText::_('COBALT_CLICK_TO_EDIT'); ?>
		                        	<?php echo $sourceName; ?>
	                        	</span>
	                        </a>
	                        <ul class="dropdown-menu" role="menu">
                        	<?php
	                        $sources = CobaltHelperDeal::getSources();
	                        if (count($sources)) { foreach($sources as $id => $name ){ ?>
	                            <li>
									<a href="javascript:void(0)" class="dropdown_item" data-field="source_id" data-item="deal" data-item-id="<?php echo $deal['id']; ?>" data-value="<?php echo $id; ?>"><?php echo $name; ?></a>
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
			<h2><?php echo CRMText::_('COBALT_EDIT_SUMMARY'); ?></h2>
			<div class="well well-small large_info">
				<?php $summary = ( array_key_exists('summary',$deal) && strlen(trim($deal['summary'])) > 0 ) ? $deal['summary'] : CRMText::_('COBALT_CLICK_TO_EDIT'); ?>
				<div class="inline"><span id="editable_summary"><?php echo nl2br($summary); ?></span></div>
				<div id="editable_summary_area" style="display:none;">
					<form id="summary_form">
						<textarea class="inputbox" name="summary"><?php echo $summary; ?></textarea>
					</form>
					<a class="btn" href="javscript:void(0);" onclick="saveEditableModal('summary_form');" ><?php echo CRMText::_('COBALT_SAVE'); ?></a>
				</div>
			</div>
		</div>

		<?php echo $deal['notes']->render(); ?>

		<h2><?php echo CRMText::_('COBALT_ADDITIONAL_FIELDS'); ?></h2>
				<div class="columncontainer">
					<?php echo $this->custom_fields_view->render(); ?>
				</div>



		<span class="pull-right">
	        <form id="upload_form" target="hidden" action="index.php?controller=upload" method="post" enctype="multipart/form-data">
		        <div class="fileupload fileupload-new" data-provides="fileupload">
	             	<span class="btn btn-file"><span class="fileupload-new" id="upload_button"><?php echo CRMText::_('COBALT_UPLOAD_FILE'); ?></span><span class="fileupload-exists"><?php echo CRMText::_('COBALT_UPLOADING_FILE'); ?></span><input type="file" id="upload_input_invisible" name="document" /></span>
		        </div>
		        <input type="hidden" name="association_id" value="<?php echo $deal['id']; ?>" />
				<input type="hidden" name="association_type" value='deal' />
	        </form>
	    </span>

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
	<div class="span4">
		<div class="widget">
			<h3><?php echo ucwords(CRMText::_('COBALT_DEAL_CONTACTS')); ?></h3>
			<?php echo $this->contact_info->render(); ?>
		</div>
		<div class="widget" id='event_dock'>
			<?php echo $this->event_dock->render(); ?>
		</div>
		<?php if ( isset($this->banter_dock) ){
			echo $this->banter_dock->render();
		}?>
	</div>
</div>

<?php echo CobaltHelperCobalt::showShareDialog(); ?>