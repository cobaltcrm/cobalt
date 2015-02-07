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
defined( '_CEXEC' ) or die( 'Restricted access' ); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <?php if (!$this->launch_default) { ?>
                <div class="alert">
                <?php echo TextHelper::_('COBALT_YOUR_SETUP_IS').$this->setup_percent.'% '.JText::_('COBALT_COMPLETED'); ?>
                    <div class="progress progress-striped">
                        <div class="bar" style="width:<?php echo $this->setup_percent; ?>%;"></div>
                    </div>
                </div>
            <?php  } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php echo $this->menu['help_menu']->render(); ?>
        </div>
    </div>
    <?php echo $this->menu['quick_menu']->render(); ?>
    <div class="row">
        <div class="col-md-12" id="content">
            <div id="system-message-container"></div>
            <div class="row">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="col-md-9">
                    <?php if (!$this->php_version_check) { ?>
                        <div class="alert alert-error">
                            <?php echo TextHelper::sprintf("COBALT_WARNING_PHP_VERSION_INVALID",$this->php_version); ?>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-sm-12">
                        <?php if ($this->latestVersion == 'no_curl') { ?>
                            <span class="btn btn-danger btn-mini">
                                <i class="glyphicon glyphicon-remove icon-white tip"></i>
                            </span>
                            <?php echo TextHelper::_('COBALT_CURL_NOT_INSTALLED'); ?>
                            <?php } elseif ( version_compare($this->installedVersion, $this->latestVersion, 'ge') ) { ?>
                                <span class="btn btn-success btn-mini">
                                    <i class="glyphicon glyphicon-ok icon-white"></i>
                                </span>
                                <?php echo TextHelper::sprintf('COBALT_UP_TO_DATE', $this->installedVersion); ?>
                            <?php } else {	?>
                                <span class="btn btn-danger btn-mini">
                                    <i class="glyphicon glyphicon-remove icon-white"></i>
                                </span>
                                <?php echo TextHelper::sprintf('COBALT_UPDATE', $this->installedVersion, $this->latestVersion); ?>
                                <a href="<?php echo $this->updateUrl; ?>" target="_blank"><?php echo TextHelper::_('COBALT_UPDATE_LINK'); ?></a>
                        <?php } ?>
                        </div>
                    </div>
                    <hr />
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?php echo TextHelper::_('COBALT_LATEST_NEWS'); ?></h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="alert alert-warning"><?php echo TextHelper::_('COBALT_NO_MATCHING_RESULTS'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
