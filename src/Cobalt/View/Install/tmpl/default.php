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
defined('_CEXEC') or die('Restricted access');

use Joomla\Language\Text; ?>

<div id="wrapper">
<div id="cobalt-3d-container">
	<img id="cobalt-3d" src="<?php echo $this->basepath; ?>/themes/bootstrap/img/cobalt-3d.png"/>
</div>
<div id="tab-container">
<?php if (isset($this->error) && $this->error != null) : ?>
	<?php if (is_array($this->error)) :
	foreach ($this->error as $error) : ?>
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
			</button>
			<?php echo $error; ?>
		</div>
	<?php endforeach;
	else : ?>
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<?php echo $this->error; ?>
	</div>
	<?php endif; ?>
<?php endif; ?>
<div id="rootwizard" class="tabbable tabs-left">
	<form id="install-form" enctype="multipart/form-data" method="post" action="<?php echo $this->basepath; ?>/index.php?task=install" class="form-horizontal" role="form">
		<ul class="nav nav-tabs">
			<li><a href="#installer" data-toggle="tab"><i class="icon-wrench"></i> <?php echo Text::_('INSTL_TAB_SETTINGS'); ?></a></li>
			<li><a href="#site" data-toggle="tab" class="active"><i class="icon-home"></i> <?php echo Text::_('INSTL_TAB_SITE'); ?></a></li>
			<li><a href="#database" data-toggle="tab"><i class="icon-hdd"></i> <?php echo Text::_('INSTL_TAB_DATABASE'); ?></a></li>
			<li><a href="#admin" data-toggle="tab"><i class="icon-user"></i> <?php echo Text::_('INSTL_TAB_ADMIN'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane" id="installer">
				<br/>

				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo Text::_('INSTL_SELECT_LANGUAGE_TITLE'); ?></label>

					<div class="col-sm-8">
						<select name="lang" id="lang" class="form-control">
							<?php foreach ($this->knownLangs as $language): ?>
								<option <?php if ($this->defaultLang == $language['tag']
								): ?> selected="selected" <?php endif; ?> value="<?php echo $language['tag']; ?>"><?php echo $language['name']; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<h2><?php echo Text::_('INSTL_PRECHECK_RECOMMENDED_SETTINGS_TITLE'); ?></h2>
				<table class="table table-striped">
					<?php $canInstall = true;
					foreach ($this->phpOptions as $phpOption): ?>
						<tr>
							<td><?php echo $phpOption->label; ?></td>
							<td>
								<?php if ($phpOption->state): ?>
									<span <?php if (!empty($phpOption->notice)): ?> data-placement="right" data-toggle="tooltip" title="<?php echo $phpOption->notice; ?>" <?php endif; ?> class="label label-success"><?php echo Text::_('INSTL_YES'); ?></span>
								<?php else: $canInstall = false; ?>
									<span <?php if (!empty($phpOption->notice)): ?> data-placement="right" data-toggle="tooltip" title="<?php echo $phpOption->notice; ?>" <?php endif; ?> class="label label-danger"><?php echo Text::_('INSTL_NO'); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<br/>

				<div class="pull-right">
					<?php if ($canInstall): ?>
						<a href="#site" data-toggle="tab" data-showtab="site" class="btn btn-success btn-next"><?php echo Text::_('INSTL_NEXT'); ?>
							<i class="glyphicon glyphicon-chevron-right"></i></a>
					<?php else: ?>
						<a href="" class="btn btn-primary"><?php echo Text::_('INSTL_BTN_CHECK_SETTINGS'); ?>
							<i class="glyphicon glyphicon-refresh"></i></a>
					<?php endif; ?>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="tab-pane" id="site">
				<br/>

				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo Text::_('INSTL_LBL_SITE_NAME'); ?></label>

					<div class="col-sm-8">
						<input type="text" name="site_name" class="form-control" id="siteName" placeholder="<?php echo Text::_('INSTL_PLHD_SITE_NAME'); ?>" data-placement="right" data-toggle="tooltip" title="<?php echo Text::_('INSTL_TITLE_SITE_NAME'); ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo Text::_('INSTL_LBL_SITE_LOGO'); ?></label>

					<div class="col-sm-8">
						<div class="fileinput fileinput-new" data-provides="fileinput">
							<div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
								<img src="<?php echo $this->basepath; ?>/themes/bootstrap/img/no-image.gif" alt="...">
							</div>
							<div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
							<div>
								<span class="btn btn-default btn-file"><span class="fileinput-new"><?php echo Text::_('INSTL_BTN_SELECT_LOGO'); ?></span><span class="fileinput-exists">Change</span><input type="file" name="logo" title="<?php echo Text::_('INSTL_TITLE_SITE_LOGO'); ?>"></span>
								<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><?php echo Text::_('INSTL_BTN_REMOVE'); ?></a>
							</div>
						</div>
					</div>
				</div>
				<br/>

				<div class="pull-left">
					<a href="#installer" data-toggle="tab" data-showtab="installer" class="btn btn-danger"><i class="glyphicon glyphicon-chevron-left"></i> <?php echo Text::_('INSTL_BACK'); ?>
					</a>
				</div>
				<div class="pull-right">
					<a href="#database" data-toggle="tab" data-showtab="database" class="btn btn-success btn-next"><?php echo Text::_('INSTL_NEXT'); ?>
						<i class="glyphicon glyphicon-chevron-right"></i></a>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="tab-pane" id="database">
				<div id="db-ajax" class="ajax-loader"></div>
				<div class="clearfix overflow" id="database-validation-message"></div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="dbDriver"><?php echo Text::_('INSTL_LBL_DATABASE_DRIVER'); ?></label>

					<div class="col-sm-8">
						<select data-placement="right" data-toggle="tooltip" title="<?php echo Text::_('INSTL_TITLE_DATABASE_DRIVER'); ?>" id="db_drive" name="db_drive" class="form-control">
							<?php foreach ($this->dboDrivers as $driver): ?>
								<option <?php if ($driver == 'mysql'): ?> selected="selected" <?php endif; ?> value="<?php echo $driver; ?>"><?php echo Text::_(strtoupper($driver)); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="dbHost"><?php echo Text::_('INSTL_LBL_DATABASE_HOST'); ?></label>

					<div class="col-sm-8">
						<input data-placement="right" data-toggle="tooltip" title="<?php echo Text::_('INSTL_TITLE_DATABASE_HOST'); ?>" type="text" id="dbHost" name="database_host" placeholder="<?php echo Text::_('INSTL_PLHD_DATABASE_HOST'); ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="dbUser"><?php echo Text::_('INSTL_LBL_DATABASE_USER'); ?></label>

					<div class="col-sm-8">
						<input data-placement="right" data-toggle="tooltip" title="<?php echo Text::_('INSTL_TITLE_DATABASE_USER'); ?>" type="text" id="dbUser" name="database_user" placeholder="<?php echo Text::_('INSTL_PLHD_DATABASE_USER'); ?>" class="form-control">
					</div>
				</div>


				<div class="form-group">
					<label class="col-sm-3 control-label" for="dbPass"><?php echo Text::_('INSTL_LBL_DATABASE_PASSWORD'); ?></label>

					<div class="col-sm-8">
						<input data-placement="right" data-toggle="tooltip" title="<?php echo Text::_('INSTL_TITLE_DATABASE_PASSWORD'); ?>" type="password" id="dbPass" name="database_password" placeholder="<?php echo Text::_('INSTL_PLHD_DATABASE_PASSWORD'); ?>" class="form-control">
					</div>
				</div>


				<div class="form-group">
					<label class="col-sm-3  control-label" for="dbName"><?php echo Text::_('INSTL_LBL_DATABASE_NAME'); ?></label>

					<div class="col-sm-8">
						<input data-placement="right" data-toggle="tooltip" title="<?php echo Text::_('INSTL_TITLE_DATABASE_NAME'); ?>" type="text" id="dbName" name="database_name" placeholder="<?php echo Text::_('INSTL_PLHD_DATABASE_NAME'); ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3  control-label" for="dbPrefix"><?php echo Text::_('INSTL_LBL_DATABASE_TABLE_PREFIX'); ?></label>

					<div class="col-sm-8">
						<input data-placement="right" data-toggle="tooltip" title="<?php echo Text::_('INSTL_TITLE_DATABASE_TABLE_PREFIX'); ?>" type="text" id="dbPrefix" name="database_prefix" placeholder="<?php echo Text::_('INSTL_PLHD_DATABASE_TABLE_PREFIX'); ?>" value="cob_" class="form-control">
					</div>
				</div>
				<div class="pull-left">
					<a href="#site" data-toggle="tab" data-showtab="site" class="btn btn-danger"><i class="glyphicon glyphicon-chevron-left"></i> <?php echo Text::_('INSTL_BACK'); ?>
					</a>
				</div>
				<div class="pull-right">
					<a href="#admin" data-toggle="tab" data-showtab="admin" class="btn btn-success btn-next"><?php echo Text::_('INSTL_NEXT'); ?>
						<i class="glyphicon glyphicon-chevron-right"></i></a>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="tab-pane" id="admin">
				<br/>

				<div class="form-group">
					<label class="col-sm-3 control-label" for="adminFirstname"><?php echo Text::_('INSTL_LBL_ADMIN_FIRST_NAME'); ?></label>

					<div class="col-sm-8">
						<input data-placement="right" data-toggle="tooltip" title="<?php echo Text::_('INSTL_TITLE_ADMIN_FIRST_NAME'); ?>" type="text" id="adminFirstname" name="first_name" placeholder="<?php echo Text::_('INSTL_PLHD_ADMIN_FIRST_NAME'); ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="adminLastname"><?php echo Text::_('INSTL_LBL_ADMIN_LAST_NAME'); ?></label>

					<div class="col-sm-8">
						<input data-placement="right" data-toggle="tooltip" title="<?php echo Text::_('INSTL_TITLE_ADMIN_LAST_NAME'); ?>" type="text" id="adminLastname" name="last_name" placeholder="<?php echo Text::_('INSTL_PLHD_ADMIN_LAST_NAME'); ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="adminEmail"><?php echo Text::_('INSTL_LBL_ADMIN_EMAIL'); ?></label>

					<div class="col-sm-8">
						<input data-placement="right" data-toggle="tooltip" title="<?php echo Text::_('INSTL_TITLE_ADMIN_EMAIL'); ?>" type="text" id="adminEmail" name="email" placeholder="<?php echo Text::_('INSTL_PLHD_ADMIN_EMAIL'); ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="adminUsername"><?php echo Text::_('INSTL_LBL_ADMIN_USERNAME'); ?></label>

					<div class="col-sm-8">
						<input data-placement="right" data-toggle="tooltip" title="<?php echo Text::_('INSTL_TITLE_ADMIN_USERNAME'); ?>" type="text" id="adminUsername" name="username" placeholder="<?php echo Text::_('INSTL_PLHD_ADMIN_USERNAME'); ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="adminPassword"><?php echo Text::_('INSTL_LBL_ADMIN_PASSWORD'); ?></label>

					<div class="col-sm-8">
						<input data-placement="right" data-toggle="tooltip" title="<?php echo Text::_('INSTL_TITLE_ADMIN_PASSWORD'); ?>" type="password" id="adminPassword" name="password" placeholder="<?php echo Text::_('INSTL_PLHD_ADMIN_PASSWORD'); ?>" class="form-control">
					</div>
				</div>


				<br/>

				<div class="pull-left">
					<a href="#database" data-toggle="tab" data-showtab="database" class="btn btn-danger"><i class="glyphicon glyphicon-chevron-left"></i> <?php echo Text::_('INSTL_BACK'); ?>
					</a>
				</div>
				<div class="pull-right">
					<a href="#" onclick="validateAdmin();" class="btn btn-success"><?php echo Text::_('INSTL_BTN_INSTALL'); ?></a>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</form>
</div>
</div>
</div>
