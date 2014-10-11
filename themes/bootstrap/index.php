<?php
/**
 * Cobalt CRM
 *
 * @copyright  Copyright (C) 2012 - 2014 cobaltcrm.org All Rights Reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

defined('_CEXEC') or die;

use Cobalt\Factory;
use Cobalt\Helper\RouteHelper;
use Cobalt\Helper\TemplateHelper;
use Cobalt\Helper\TextHelper;
use Cobalt\Helper\UsersHelper;

$app = Factory::getApplication();
$isAuthenticated = $app->getUser()->isAuthenticated();

$view['assets']->addStylesheet('themes/bootstrap/css/bootstrap.css');
$view['assets']->addStylesheet('themes/bootstrap/css/cobalt.css');
$view['assets']->addStylesheet('themes/bootstrap/css/fullcalendar.css');

// Add core stylesheets
$view['assets']->addStylesheet('media/css/datepicker.css');
$view['assets']->addStylesheet('media/css/dataTables.foundation.css');

// Add theme Scripts
$view['assets']->addScript('themes/bootstrap/js/jquery.js');
$view['assets']->addScript('themes/bootstrap/js/jquery-ui.min.js');
$view['assets']->addScript('themes/bootstrap/js/bootstrap.min.js');
$view['assets']->addScript('themes/bootstrap/js/bootstrap-typeahead.min.js');
$view['assets']->addScript('themes/bootstrap/js/bloodhound.min.js');
$view['assets']->addScript('themes/bootstrap/js/jquery.cluetip.min.js');
$view['assets']->addScript('themes/bootstrap/js/fullcalendar.js');
$view['assets']->addScript('themes/bootstrap/js/ChartNew.js');
$view['assets']->addScript('themes/bootstrap/js/cobalt.js');

// Add core scripts
$view['assets']->addScript('media/js/bootstrap-datepicker.js');
$view['assets']->addScript('media/js/jquery.form.js');
$view['assets']->addScript('media/js/jquery.dataTables.min.js');
$view['assets']->addScript('media/js/dataTables.foundation.js');

if ($isAuthenticated)
{
	$view['assets']->addScriptDeclaration('var base_url = "' . $app->get('uri.base.full') . '";');
	$view['assets']->addScriptDeclaration('var userDateFormat = "' . UsersHelper::getDateFormat(false) . '";');
	$view['assets']->addScriptDeclaration('var user_id = "' . $app->getUser()->id . '";');
	$view['assets']->addScriptDeclaration(TemplateHelper::showMessages());
}

/*if (strpos(\Cobalt\Factory::getApplication()->get('uri.route'), 'install') !== false) :
	$this->addStylesheet($this->baseurl . 'themes/bootstrap/jasny/css/jasny-bootstrap.min.css')
		->addStylesheet($this->baseurl . 'themes/bootstrap/css/install.css')
		->addScript($this->baseurl . 'themes/bootstrap/wizard/js/wizard.min.js')
		->addScript($this->baseurl . 'themes/bootstrap/js/install.js');
endif;*/
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Cobalt</title>
		<?php $view['assets']->outputHeadDeclarations(); ?>
	</head>
	<body>
		<?php if ($isAuthenticated && !in_array($app->input->get('view'), array('print'))) : ?>
		<?php $view->render('toolbar.php'); ?>
		<?php endif; ?>
		<?php if ($isAuthenticated) : ?>
		<div class="container">
			<div id="com_cobalt">
				<div id="message" style="display:none;"></div>
				<div id="CobaltModalMessage" class="modal hide fade top-right" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-header small">
						<h3 id="CobaltModalMessageHeader"></h3>
					</div>
					<div id="CobaltModalMessageBody" class="modal-body">
						<p></p>
					</div>
				</div>
				<div id="google-map" style="display:none;"></div>
				<div id="edit_note_entry" style="display:none;"></div>
				<div id="edit_convo_entry" style="display:none;"></div>
				<div id="document_preview_modal" style="display:none;"></div>
				<div id="new_event_dialog" style="display:none;">
					<div class="new_events">
						<a href="javasript:void(0);" class="task" onclick="Cobalt.addTaskEvent('task');"><?php echo TextHelper::_('COBALT_ADD_TASK'); ?></a>
						<a href="javasript:void(0);" class="event" onclick="Cobalt.addTaskEvent('event');"><?php echo TextHelper::_('COBALT_ADD_EVENT'); ?></a>
						<a href="javasript:void(0);" class="complete" onclick="jQuery('#new_event_dialog').dialog('close');"><?php echo TextHelper::_('COBALT_DONE'); ?></a>
					</div>
				</div>
				<iframe id="avatar_frame" name="avatar_frame" style="display:none;border:0px;width:0px;height:0px;opacity:0;"></iframe>
				<div class="filters" id="avatar_upload_dialog" style="display:none;">
					<div id="avatar_message"><?php echo TextHelper::_('COBALT_SELECT_A_FILE_TO_UPLOAD'); ?></div>
					<div class="input_upload_button">
						<form id="avatar_upload_form" method="POST" enctype="multipart/form-data" target="avatar_frame">
							<input type="hidden" name="option" value="com_cobalt" />
							<input type="button" class="button" id="upload_avatar_button" value="<?php echo TextHelper::_('COBALT_UPLOAD_FILE'); ?>" />
							<input type="file" id="upload_input_invisible_avatar" name="avatar" />
						</form>
					</div>
				</div>
				<?php if (TemplateHelper::isMobile()) : ?>
				<div class="page" data-role="page" data-theme="b" id="">
				<?php endif; ?>
					<div id="logoutModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="logoutModal" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h3><?php echo TextHelper::_('COBALT_LOGOUT_HEADER'); ?></h3>
								</div>
								<div class="modal-body">
									<p><?php echo TextHelper::_('COBALT_LOGOUT_MESSAGE'); ?></p>
									<form id="logout-form" class="inline-form block-btn" action="<?php echo RouteHelper::_('index.php?view=logout'); ?>" method="post">
										<input type="hidden" name="return" value="<?php echo base64_encode('/'); ?>"/>
										<?php // echo JHtml::_('form.token'); ?>
									</form>
								</div>
								<div class="modal-footer">
									<button type="button" data-dismiss="modal" class="btn btn-default"><?php echo TextHelper::_('COBALT_CANCEL'); ?></button>
									<button type="button" onclick="document.getElementById('logout-form').submit();" class="btn btn-primary"><?php echo TextHelper::_('COBALT_LOGOUT'); ?></button>
								</div>
							</div>
						</div>
					</div>
					<?php $view['slots']->output('_content'); ?>
					<?php if (TemplateHelper::isMobile() && $app->input->get('view') != 'dashboard') : ?>
						<div data-role="footer" data-position="fixed" data-id="cobaltFooter">
							<div data-role="navbar" data-iconpos="top">
								<ul>
									<li>
										<a data-icon="agenda" data-iconpos="top" id="agendaButton" href="<?php echo RouteHelper::_('index.php?view=events'); ?>"><?php echo ucwords(TextHelper::_('COBALT_AGENDA')); ?></a>
									</li>
									<li>
										<a data-icon="deals" data-iconpos="top" id="dealsButton" href="<?php echo RouteHelper::_('index.php?view=deals'); ?>"><?php echo ucwords(TextHelper::_('COBALT_DEALS_HEADER')); ?></a>
									</li>
									<li>
										<a data-icon="leads" data-iconpos="top" id="leadsButton" href="<?php echo RouteHelper::_('index.php?view=people&type=leads'); ?>"><?php echo ucwords(TextHelper::_('COBALT_LEADS')); ?></a>
									</li>
									<li>
										<a data-icon="contacts" data-iconpos="top" id="contactsButton" href="<?php echo RouteHelper::_('index.php?view=people&type=not_leads'); ?>"><?php echo ucwords(TextHelper::_('COBALT_CONTACTS')); ?></a>
									</li>
									<li>
										<a data-icon="companies" data-iconpos="top" id="CompaniesButton" href="<?php echo RouteHelper::_('index.php?view=companies'); ?>"><?php echo ucwords(TextHelper::_('COBALT_COMPANIES')); ?></a>
									</li>
								</ul>
							</div>
						</div>
					<?php endif; ?>
					<div class="modal fade" role="dialog" id="CobaltAjaxModal">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h3 id="CobaltAjaxModalHeader">&nbsp;</h3>
								</div>
								<div class="modal-body" id="CobaltAjaxModalBody">
								</div>
								<div class="modal-footer" id="CobaltAjaxModalFooter">
									<button id="CobaltAjaxModalCloseButton" class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo ucwords(TextHelper::_('COBALT_CANCEL')); ?></button>
									<button id="CobaltAjaxModalSaveButton" onclick="Cobalt.sumbitModalForm(this)" class="btn btn-primary"><?php echo ucwords(TextHelper::_('COBALT_SAVE')); ?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="modal hide fade" role="dialog" id="CobaltAjaxModalPreview">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h3 id="CobaltAjaxModalPreviewHeader">&nbsp;</h3>
								</div>
								<div class="text-center dmodal-body" id="CobaltAjaxModalPreviewBody">
								</div>
							</div>
						</div>
					</div>
				<?php if (TemplateHelper::isMobile()) : ?>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php else : ?>
		<?php $view['slots']->output('_content'); ?>
		<?php endif; ?>
	</body>
</html>
