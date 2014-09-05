<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <link rel="stylesheet" href="<?php echo $this->config->get('uri.base.path'); ?>assets/bootstrap/css/bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="<?php echo $this->config->get('uri.base.path'); ?>assets/installer/css/install.css" type="text/css"/>
    <script src="<?php echo $this->config->get('uri.base.path'); ?>assets/jquery/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $this->config->get('uri.base.path'); ?>assets/jquery/js/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $this->config->get('uri.base.path'); ?>assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
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
                        <?php echo $error; ?>
                    </div>
                <?php
                }
            } else {
                ?>
                <div class="alert alert-danger">
                    <?php echo $this->error; ?>
                </div>
            <?php
            }
        } ?>
        <form id="install-form" enctype="multipart/form-data" method="post" action="<?php echo $this->config->get('uri.base.path'); ?>index.php?c=install&m=install" class="form-horizontal" role="form">
            <!-- Tab buttons -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#site" data-toggle="tab"><i class="icon-home"></i> Site</a></li>
                <li><a href="#database" data-toggle="tab"><i class="icon-hdd"></i> Database</a></li>
                <li><a href="#admin" data-toggle="tab"><i class="icon-user"></i> Administrator</a></li>
            </ul>
            <div class="tab-content">
                <!-- Site Info -->
                <div class="tab-pane active fade in" id="site">
                    <br />
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Site Name</label>
                        <div class="col-sm-8">
                            <input type="text" name="site_name" class="form-control" id="siteName" placeholder="Name your site" data-placement="right" data-toggle="tooltip" title="Site Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Site Logo</label>
                        <div class="col-sm-8">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;"><img src="<?php echo $this->config->get('uri.base.path'); ?>assets/installer/images/no-image.gif"/></div>
                                <div id="site-logo-preview" class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;">

                                </div>
                                <div>
                                    <label></label>
                                    <span class="btn btn-file"><span class="fileupload-new">Select Logo</span><span class="fileupload-exists">Change</span><input name="site_logo" id="logo" type="file"/></span>
                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pull-right">
                        <a href="#database" data-toggle="tab" data-showtab="database" class="btn btn-success">Next <i class="icon-arrow-right icon-white"></i></a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <!-- Database Tab -->
                <div class="tab-pane fade" id="database">
                    <div id="db-ajax" class="ajax-loader"></div>
                    <div class="clearfix overflow" id="database-validation-message"></div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="dbHost">Host</label>
                            <div class="col-sm-8">
                                <input data-placement="right" rel="tooltip" title="Enter database host name" type="text" id="dbHost" name="database_host" placeholder="e.g localhost,127.0.0.1" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="dbUser">User</label>
                            <div class="col-sm-8">
                                <input data-placement="right" rel="tooltip" title="Enter database username" type="text" id="dbUser" name="database_user" placeholder="Username for database" class="form-control">
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="dbPass">Password</label>
                            <div class="col-sm-8">
                                <input data-placement="right" rel="tooltip" title="Enter database user password" type="password" id="dbPass" name="database_password" placeholder="Password for database user" class="form-control">
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-3  control-label" for="dbName">Name</label>
                            <div class="col-sm-8">
                                <input data-placement="right" rel="tooltip" title="Enter database name" type="text" id="dbName" name="database_name" placeholder="Name of database" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3  control-label" for="dbPrefix">Prefix</label>
                            <div class="col-sm-8">
                                <input data-placement="right" rel="tooltip" title="Enter database prefix" type="text" id="dbPrefix" name="database_prefix" placeholder="Prefix for database" value="cob_" class="form-control">
                            </div>
                        </div>

                    <div class="pull-left clearfix">
                        <a href="#site" data-toggle="tab" data-showtab="site" class="btn btn-primary"><i class="icon-arrow-left"></i> Previous</a>
                    </div>

                    <div class="pull-right">
                        <a href="#admin" data-toggle="tab" data-showtab="admin" class="btn btn-success">Next <i class="icon-arrow-right icon-white"></i></a>
                    </div>

                    <div class="clearfix"></div>
                </div>
                <!-- Admin Tab -->
                <div class="tab-pane fade" id="admin">
                    <br />

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="adminFirstname">First Name</label>

                        <div class="col-sm-8">
                            <input data-placement="right" rel="tooltip" title="Enter administrator first name" type="text" id="adminFirstname" name="first_name" placeholder="Enter first name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="adminLastname">Last Name</label>

                        <div class="col-sm-8">
                            <input data-placement="right" rel="tooltip" title="Enter administrator last name" type="text" id="adminLastname" name="last_name" placeholder="Enter last name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="adminEmail">Email</label>

                        <div class="col-sm-8">
                            <input data-placement="right" rel="tooltip" title="Enter administrator email" type="text" id="adminEmail" name="email" placeholder="Enter email address" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="adminUsername">Username</label>

                        <div class="col-sm-8">
                            <input data-placement="right" rel="tooltip" title="Enter administrator username" type="text" id="adminUsername" name="username" placeholder="Enter administrator username" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="adminPassword">Password</label>

                        <div class="col-sm-8">
                            <input data-placement="right" rel="tooltip" title="Enter administrator password" type="password" id="adminPassword" name="password" placeholder="Password for administrator" class="form-control">
                        </div>
                    </div>

                    <div class="pull-left">
                        <a href="#database" data-toggle="tab" data-showtab="database" class="btn btn-primary"><i class="icon-arrow-left"></i> Previous</a>
                    </div>

                    <div class="pull-right">
                        <a href="#" class="btn btn-success" id="install-cobalt">Install <i
                                class="icon-check icon-white"></i></a>
                    </div>
                    <div class="clearfix"></div>

                </div>
            </div>
        </form>
    </div>
</div>
</div>
</body>
</html>
