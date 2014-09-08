<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset=utf-8" />
    <link rel="stylesheet" href="<?php echo $this->config->get('uri.base.path'); ?>assets/bootstrap/css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo $this->config->get('uri.base.path'); ?>assets/jasny/css/jasny-bootstrap.min.css"/>
    <link rel="stylesheet" href="<?php echo $this->config->get('uri.base.path'); ?>assets/installer/css/install.css" type="text/css"/>
    <script src="<?php echo $this->config->get('uri.base.path'); ?>assets/jquery/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $this->config->get('uri.base.path'); ?>assets/jquery/js/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $this->config->get('uri.base.path'); ?>assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo $this->config->get('uri.base.path'); ?>assets/jasny/js/jasny-bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo $this->config->get('uri.base.path'); ?>assets/wizard/js/wizard.min.js" type="text/javascript"></script>
    <script src="<?php echo $this->config->get('uri.base.path'); ?>assets/installer/js/application.js" type="text/javascript"></script>
</head>
<body>
<div id="wrapper">
<div id="cobalt-3d-container">
    <img id="cobalt-3d" src="<?php echo $this->config->get('uri.base.path'); ?>assets/installer/images/cobalt-3d.png"/>
</div>
<div id="tab-container">
<?php if (isset($this->error) && $this->error != null) { ?>
    <?php if (is_array($this->error)) {
        foreach ($this->error as $error) {
            ?>
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <?php echo $error; ?>
            </div>
        <?php
        }
    } else {
        ?>
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <?php echo $this->error; ?>
        </div>
    <?php
    }
} ?>
<div id="rootwizard" class="tabbable tabs-left">
    <form id="install-form" enctype="multipart/form-data" method="post" action="<?php echo $this->config->get('uri.base.path'); ?>index.php?c=install&m=install" class="form-horizontal" role="form">
        <ul class="nav nav-tabs nav-justified">
            <li><a href="#installer" data-toggle="tab"><i class="icon-wrench"></i> <?php echo JText::_('INSTL_TAB_SETTINGS'); ?></a></li>
            <li><a href="#site" data-toggle="tab" class="active"><i class="icon-home"></i> <?php echo JText::_('INSTL_TAB_SITE'); ?></a></li>
            <li><a href="#database" data-toggle="tab" ><i class="icon-hdd"></i> <?php echo JText::_('INSTL_TAB_DATABASE'); ?></a></li>
            <li><a href="#admin" data-toggle="tab" ><i class="icon-user"></i> <?php echo JText::_('INSTL_TAB_ADMIN'); ?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" id="installer">
                <br />
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo JText::_('INSTL_SELECT_LANGUAGE_TITLE'); ?></label>
                    <div class="col-sm-8">
                        <select name="lang" id="lang" class="form-control">
                            <?php foreach ($this->availableLanguages as $language): ?>
                                <option <?php if (JFactory::getLanguage()->getDefault() == $language['tag']): ?> selected="selected" <?php endif; ?> value="<?php echo $language['tag']; ?>"><?php echo $language['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <h2><?php echo JText::_('INSTL_PRECHECK_RECOMMENDED_SETTINGS_TITLE'); ?></h2>
                <table class="table table-striped">
                    <?php foreach ($this->getPhpOptions as $phpOption): ?>
                        <tr>
                            <td><?php echo $phpOption->label; ?></td>
                            <td>
                                <?php if ($phpOption->state): ?>
                                    <span class="label label-success"><?php echo JText::_('INSTL_YES'); ?></span>
                                <?php else: ?>
                                    <span class="label label-danger"><?php echo JText::_('INSTL_NO'); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <br />
                <div class="pull-right">
                    <a href="#site" data-toggle="tab" data-showtab="site" class="btn btn-success btn-next"><?php echo JText::_('INSTL_NEXT'); ?> <i class="glyphicon glyphicon-chevron-right"></i></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="tab-pane" id="site">
                <br />
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo JText::_('INSTL_LBL_SITE_NAME'); ?></label>
                    <div class="col-sm-8">
                        <input type="text" name="site_name" class="form-control" id="siteName" placeholder="<?php echo JText::_('INSTL_PLHD_SITE_NAME'); ?>" data-placement="right" data-toggle="tooltip" title="<?php echo JText::_('INSTL_TITLE_SITE_NAME'); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo JText::_('INSTL_LBL_SITE_LOGO'); ?></label>
                    <div class="col-sm-8">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                <img src="<?php echo $this->config->get('uri.base.path'); ?>assets/installer/images/no-image.gif" alt="...">
                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                            <div>
                                <span class="btn btn-default btn-file"><span class="fileinput-new"><?php echo JText::_('INSTL_BTN_SELECT_LOGO'); ?></span><span class="fileinput-exists">Change</span><input type="file" name="logo" title="<?php echo JText::_('INSTL_TITLE_SITE_LOGO'); ?>"></span>
                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><?php echo JText::_('INSTL_BTN_REMOVE'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
                <div class="pull-left">
                    <a href="#installer" data-toggle="tab" data-showtab="installer" class="btn btn-danger"><i class="glyphicon glyphicon-chevron-left"></i> <?php echo JText::_('INSTL_BACK'); ?></a>
                </div>
                <div class="pull-right">
                    <a href="#database" data-toggle="tab" data-showtab="database" class="btn btn-success btn-next"><?php echo JText::_('INSTL_NEXT'); ?> <i class="glyphicon glyphicon-chevron-right"></i></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="tab-pane" id="database">
                <div id="db-ajax" class="ajax-loader"></div>
                <div class="clearfix overflow" id="database-validation-message"></div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="dbDriver"><?php echo JText::_('INSTL_LBL_DATABASE_DRIVER'); ?></label>
                    <div class="col-sm-8">
                        <select data-placement="right" data-toggle="tooltip" title="<?php echo JText::_('INSTL_TITLE_DATABASE_DRIVER'); ?>" id="db_drive" name="db_drive" class="form-control">
                            <?php foreach ($this->dboDrivers as $driver): ?>
                                <option <?php if ($driver == 'mysql'): ?> selected="selected" <?php endif; ?> value="<?php echo $driver; ?>"><?php echo $driver; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="dbHost"><?php echo JText::_('INSTL_LBL_DATABASE_HOST'); ?></label>
                    <div class="col-sm-8">
                        <input data-placement="right" data-toggle="tooltip" title="<?php echo JText::_('INSTL_TITLE_DATABASE_HOST'); ?>" type="text" id="dbHost" name="database_host" placeholder="<?php echo JText::_('INSTL_PLHD_DATABASE_HOST'); ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="dbUser"><?php echo JText::_('INSTL_LBL_DATABASE_USER'); ?></label>
                    <div class="col-sm-8">
                        <input data-placement="right" data-toggle="tooltip" title="<?php echo JText::_('INSTL_TITLE_DATABASE_USER'); ?>" type="text" id="dbUser" name="database_user" placeholder="<?php echo JText::_('INSTL_PLHD_DATABASE_USER'); ?>" class="form-control">
                    </div>
                </div>



                <div class="form-group">
                    <label class="col-sm-3 control-label" for="dbPass"><?php echo JText::_('INSTL_LBL_DATABASE_PASSWORD'); ?></label>
                    <div class="col-sm-8">
                        <input data-placement="right" data-toggle="tooltip" title="<?php echo JText::_('INSTL_TITLE_DATABASE_PASSWORD'); ?>" type="password" id="dbPass" name="database_password" placeholder="<?php echo JText::_('INSTL_PLHD_DATABASE_PASSWORD'); ?>" class="form-control">
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-3  control-label" for="dbName"><?php echo JText::_('INSTL_LBL_DATABASE_NAME'); ?></label>
                    <div class="col-sm-8">
                        <input data-placement="right" data-toggle="tooltip" title="<?php echo JText::_('INSTL_TITLE_DATABASE_NAME'); ?>" type="text" id="dbName" name="database_name" placeholder="<?php echo JText::_('INSTL_PLHD_DATABASE_NAME'); ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3  control-label" for="dbPrefix"><?php echo JText::_('INSTL_LBL_DATABASE_TABLE_PREFIX'); ?></label>
                    <div class="col-sm-8">
                        <input data-placement="right" data-toggle="tooltip" title="<?php echo JText::_('INSTL_TITLE_DATABASE_TABLE_PREFIX'); ?>" type="text" id="dbPrefix" name="database_prefix" placeholder="<?php echo JText::_('INSTL_PLHD_DATABASE_TABLE_PREFIX'); ?>" value="cob_" class="form-control">
                    </div>
                </div>
                <div class="pull-left">
                    <a href="#site" data-toggle="tab" data-showtab="site" class="btn btn-danger"><i class="glyphicon glyphicon-chevron-left"></i> <?php echo JText::_('INSTL_BACK'); ?></a>
                </div>
                <div class="pull-right">
                    <a href="#admin" data-toggle="tab" data-showtab="admin" class="btn btn-success btn-next"><?php echo JText::_('INSTL_NEXT'); ?> <i class="glyphicon glyphicon-chevron-right"></i></a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="tab-pane" id="admin">
                <br />
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="adminFirstname"><?php echo JText::_('INSTL_LBL_ADMIN_FIRST_NAME'); ?></label>

                    <div class="col-sm-8">
                        <input data-placement="right" data-toggle="tooltip" title="<?php echo JText::_('INSTL_TITLE_ADMIN_FIRST_NAME'); ?>" type="text" id="adminFirstname" name="first_name" placeholder="<?php echo JText::_('INSTL_PLHD_ADMIN_FIRST_NAME'); ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="adminLastname"><?php echo JText::_('INSTL_LBL_ADMIN_LAST_NAME'); ?></label>

                    <div class="col-sm-8">
                        <input data-placement="right" data-toggle="tooltip" title="<?php echo JText::_('INSTL_TITLE_ADMIN_LAST_NAME'); ?>" type="text" id="adminLastname" name="last_name" placeholder="<?php echo JText::_('INSTL_PLHD_ADMIN_LAST_NAME'); ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="adminEmail"><?php echo JText::_('INSTL_LBL_ADMIN_EMAIL'); ?></label>

                    <div class="col-sm-8">
                        <input data-placement="right" data-toggle="tooltip" title="<?php echo JText::_('INSTL_TITLE_ADMIN_EMAIL'); ?>" type="text" id="adminEmail" name="email" placeholder="<?php echo JText::_('INSTL_PLHD_ADMIN_EMAIL'); ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="adminUsername"><?php echo JText::_('INSTL_LBL_ADMIN_USERNAME'); ?></label>

                    <div class="col-sm-8">
                        <input data-placement="right" data-toggle="tooltip" title="<?php echo JText::_('INSTL_TITLE_ADMIN_USERNAME'); ?>" type="text" id="adminUsername" name="username" placeholder="<?php echo JText::_('INSTL_PLHD_ADMIN_USERNAME'); ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="adminPassword"><?php echo JText::_('INSTL_LBL_ADMIN_PASSWORD'); ?></label>

                    <div class="col-sm-8">
                        <input data-placement="right" data-toggle="tooltip" title="<?php echo JText::_('INSTL_TITLE_ADMIN_PASSWORD'); ?>" type="password" id="adminPassword" name="password" placeholder="<?php echo JText::_('INSTL_PLHD_ADMIN_PASSWORD'); ?>" class="form-control">
                    </div>
                </div>


                <br />
                <div class="pull-left">
                    <a href="#database" data-toggle="tab" data-showtab="database" class="btn btn-danger"><i class="glyphicon glyphicon-chevron-left"></i> <?php echo JText::_('INSTL_BACK'); ?></a>
                </div>
                <div class="pull-right">
                    <a href="#" onclick="validateAdmin();" class="btn btn-success"><?php echo JText::_('INSTL_BTN_INSTALL'); ?></a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
</body>
</html>
