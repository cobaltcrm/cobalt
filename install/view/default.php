<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
      <link rel="stylesheet" href="<?php echo $this->baseurl ?>libraries/crm/media/css/bootstrap.min.css" type="text/css" />
      <link rel="stylesheet" href="<?php echo $this->baseurl ?>install/assets/css/install.css" type="text/css" />
      <script src="<?php echo $this->baseurl ?>libraries/crm/media/js/jquery.js" type="text/javascript"></script>
      <script src="<?php echo $this->baseurl ?>libraries/crm/media/js/jquery-ui.js" type="text/javascript"></script>
      <script src="<?php echo $this->baseurl ?>libraries/crm/media/js/jquery.tools.min.js" type="text/javascript"></script>
      <script src="<?php echo $this->baseurl ?>libraries/crm/media/js/bootstrap.min.js" type="text/javascript"></script>
      <script src="<?php echo $this->baseurl ?>install/assets/js/install.js" type="text/javascript"></script>
</head>
    <body class="contentpane">
      <div id="wrapper">
        <div id="cobalt-3d-container"><img id="cobalt-3d" src="<?php echo $this->baseurl; ?>/install/assets/images/cobalt-3d.png" /></div>
        <div id="tab-container">
          <?php if ( $this->error != null ){ ?>
            <?php if ( is_array($this->error) ){foreach($this->error as $error){ ?>
              <div class="alert alert-error clearfix">
                  <?php echo $error; ?>
              </div>
          <?php } }else{ ?>
              <div class="alert alert-error clearfix">
                  <?php echo $this->error; ?>
              </div>
          <?php } }?>
          <form id="install-form" enctype="multipart/form-data" method="POST" action="<?php echo $this->baseurl; ?>/install/index.php?c=install&m=install" class="form-line">
            <!-- Tab buttons -->
          <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#site" data-toggle="tab"><i class="icon-home"></i> Site</a></li>
            <li><a href="#database" data-toggle="tab"><i class="icon-hdd"></i> Database</a></li>
            <li><a href="#admin" data-toggle="tab"><i class="icon-user"></i> Administrator</a></li>
          </ul>
          <div class="tab-content">
            <!-- Site Info -->
            <div class="tab-pane active fade in" id="site">
              <div class="control-group">
                <label class="control-label" for="inputEmail">Site Name</label>
                <div class="controls">
                  <input data-placement="right" rel="tooltip" title="Name your site" type="text" id="siteName" name="site_name" placeholder="Site Name">
                </div>
              </div>
              <label class="control-label" for="inputEmail">Site Logo</label>
              <div class="fileupload fileupload-new" data-provides="fileupload">
                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;"><img src="<?php echo $this->baseurl; ?>/install/assets/images/no-image.gif" /></div>
                <div id="site-logo-preview" class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                <div>
                  <label></label>
                  <span class="btn btn-file"><span class="fileupload-new">Select Logo</span><span class="fileupload-exists">Change</span><input name="site_logo" id="logo" type="file" /></span>
                  <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
                </div>
              </div>
              <div class="pull-right clearfix">
                <a href="javascript:void(0);" onclick="showTab('database');" data-toggle="tab" class="btn btn-success">Next <i class="icon-arrow-right icon-white"></i></a>
              </div>
            </div>
            <!-- Database Tab -->
            <div class="tab-pane fade" id="database">

                  <div id="db-ajax" class="ajax-loader"></div>

                  <div class="clearfix overflow" id="database-validation-message">
                  </div>
                  
                  <div class="clearfix padding">
                    <label class="control-label" for="database_name">Host</label>
                    <input data-placement="right" rel="tooltip" title="Enter database host name" type="text" id="dbHost" name="database_host" placeholder="e.g localhost,127.0.0.1">
                  
                    <label class="control-label" for="inputPassword">User</label>
                    <input data-placement="right" rel="tooltip" title="Enter database username" type="text" id="dbUser" name="database_user" placeholder="Username for database">

                    <label class="control-label" for="inputPassword">Password</label>
                    <input data-placement="right" rel="tooltip" title="Enter database user password" type="password" id="dbPass" name="database_password" placeholder="Password for database user">

                    <label class="control-label" for="inputPassword">Name</label>
                    <input data-placement="right" rel="tooltip" title="Enter database name" type="text" id="dbName" name="database_name" placeholder="Name of database">

                    <label class="control-label" for="inputPassword">Prefix</label>
                    <input data-placement="right" rel="tooltip" title="Enter database prefix" type="text" id="dbName" name="database_prefix" placeholder="Prefix for database">
                  </div>

                  <div class="pull-left clearfix">
                    <a href="javascript:void(0);" onclick="showTab('site');" data-toggle="tab" class="btn"><i class="icon-arrow-left"></i> Previous</a>
                  </div>

                  <div class="pull-right clearfix">
                    <a href="javascript:void(0);" onclick="showTab('admin');" data-toggle="tab" class="btn btn-success">Next <i class="icon-arrow-right icon-white"></i></a>
                  </div>

            </div>
            <!-- Admin Tab -->
            <div class="tab-pane fade" id="admin">
              <div class="control-group">
                <label class="control-label" for="inputPassword">First Name</label>
                <div class="controls">
                  <input data-placement="right" rel="tooltip" title="Enter administrator first name"  type="text" id="adminFirstname" name="first_name" placeholder="Enter first name">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="inputPassword">Last Name</label>
                <div class="controls">
                  <input data-placement="right" rel="tooltip" title="Enter administrator last name" type="text" id="adminLastname" name="last_name" placeholder="Enter last name">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="inputPassword">Email</label>
                <div class="controls">
                  <input data-placement="right" rel="tooltip" title="Enter administrator email" type="text" id="adminEmail" name="email" placeholder="Enter email address">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="inputPassword">Username</label>
                <div class="controls">
                  <input data-placement="right" rel="tooltip" title="Enter administrator username" type="text" id="adminUsername" name="username" placeholder="Enter administrator username">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="inputPassword">Password</label>
                <div class="controls">
                  <input data-placement="right" rel="tooltip" title="Enter administrator password" type="password" id="adminPassword" name="password" placeholder="Password for administrator">
                </div>
              </div>

              <div class="pull-left clearfix">
                  <a href="javascript:void(0);" onclick="showTab('database');" data-toggle="tab" class="btn"><i class="icon-arrow-left"></i> Previous</a>
              </div>

              <div class="pull-right clearfix">
                  <a href="javascript:void(0);" data-toggle="tab" class="btn btn-success" onclick="install();" >Install <i class="icon-check icon-white"></i></a>
                </div>

            </div>
          </div>
        </form>
        </div>
      </div>
      </div>
    </body>
</html>