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
    <?php echo $this->menu['quick_menu']->render(); ?>
    <div class="row-fluid">
        <div class="span12" id="content">
            <div id="system-message-container"></div>
            <div class="row-fluid">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="span9">
                    <form enctype="multipart/form-data" action="index.php?view=branding" method="post" name="adminForm" id="adminForm">
                        <fieldset class="adminform">
                            <legend><h3><?php echo TextHelper::_("COBALT_PREVIEW"); ?></h3></legend>
                            <table cellspacing="0" cellpadding="0" class="table table-striped table-hover">
                                <thead>
                                    <th><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_HEADER"); ?></th>
                                    <th><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_HEADER"); ?></th>
                                    <th><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_HEADER"); ?></th>
                                    <th><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_HEADER"); ?></th>
                                </thead>
                                <tbody>
                                        <tr class='cobalt_row_0'>
                                            <td><a href="javascript:void(0);"><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_TEXT"); ?></a></td>
                                            <td><a href="javascript:void(0);"><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_TEXT"); ?></a></td>
                                            <td><a href="javascript:void(0);"><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_TEXT"); ?></a></td>
                                            <td><a href="javascript:void(0);"><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_TEXT"); ?></a></td>
                                        </tr>
                                        <tr class='cobalt_row_1'>
                                            <td><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_TEXT"); ?></td>
                                            <td><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_TEXT"); ?></td>
                                            <td><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_TEXT"); ?></td>
                                            <td><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_TEXT"); ?></td>
                                        </tr>
                                </tbody>
                            </table>
                        </fieldset>
                        <fieldset class="adminform">
                            <legend><h3><?php echo TextHelper::_("COBALT_ADMIN_UPLOAD_LOGO"); ?></h3></legend>
                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                  <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                    <?php if ( isset($this->site_logo) ) { ?>
                                        <img src="<?php echo $this->site_logo; ?>" />
                                    <?php } else { ?>
                                        <img src="<?php echo \Cobalt\Factory::getApplication()->get('uri.media.full'); ?>images/no-image.gif" />
                                    <?php } ?>
                                </div>
                                  <div id="site-logo-preview" class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                  <div>
                                    <span class="btn btn-file btn-primary"><span class="fileupload-new"><?php echo TextHelper::_('COBALT_SELECT_IMAGE'); ?></span><span class="fileupload-exists"><?php echo TextHelper::_('COBALT_CHANGE'); ?></span><input id="site-logo" name="site_logo" type="file" /></span>
                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload"><?php echo TextHelper::_('COBALT_REMOVE'); ?></a>
                                  </div>
                                </div>
                        </fieldset>
                        <fieldset class="adminform">
                            <legend><h3><?php echo TextHelper::_("COBALT_ADMIN_SITE_NAME"); ?></h3></legend>
                            <input type="text" class="form-control" id="site-name" name="site_name" value="<?php echo $this->site_name; ?>" />
                        </fieldset>
                        <fieldset class="adminform">
                            <legend><h3><?php echo TextHelper::_("COBALT_ADMIN_CHOOSE_THEME"); ?></h3></legend>
                                <label class="checkbox">
                                    <input type="radio" name="id" value="1" <?php if ($this->themes[0]['assigned']) { echo "checked"; } ?> />
                                    <?php echo TextHelper::_("COBALT_ADMIN_STANDARD"); ?>
                                </label>
                                <label class="checkbox">
                                    <input type="radio" name="id" value="2" <?php if ($this->themes[1]['assigned']) { echo "checked"; } ?> >
                                    <?php echo TextHelper::_("COBALT_ADMIN_USER_DEFINED"); ?>
                                </label>
                        </fieldset>
                        <fieldset id="customization_area" class="adminform">
                            <legend><h3><?php echo TextHelper::_("COBALT_ADMIN_THEME_CUSTOMIZATION"); ?></h3></legend>
                            <div id="customization_content"></div>
                        </fieldset>
                        <div>
                            <input type="hidden" name="controller" value="" />
                            <input type="hidden" name="model" value="branding" />
                            <input type="hidden" name="view" value="branding" />
                            <input type="hidden" name="block_btn_border" id="block-btn-border" value="" />
                            <input type="hidden" name="feature_btn_border" id="feature-btn-border" value="" />
                            <input type="hidden" name="feature_btn_bg" id="feature-btn-bg" value="" />

                        </div>
                    </form>
                    <div id="themes" style="display:none;">
                        <div id="1">
                            <ul class="list-unstyled adminlist cobaltadminlist">
                                <li>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_HEADER"); ?></b></label>
                                    <input class="hascolorpicker inputbox branding-input" type="text" name="header" data-css-class=".navbar-inner" data-css-style="background" value="<?php echo $this->themes[0]['header']; ?>"><div class="colorwheel"></div>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_TABS_HOVER"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="tabs_hover" data-css-class=".navbar .nav > li > a" data-css-style="hover-background" value="<?php echo $this->themes[0]['tabs_hover']; ?>" ><div class="colorwheel"></div>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_TABS_HOVER_TEXT"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="tabs_hover_text" data-css-class=".navbar .nav > li > a" data-css-style="hover-color" value="<?php echo $this->themes[0]['tabs_hover_text']; ?>" ><div class="colorwheel"></div>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_TABLE_HEADER_ROW"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="table_header_row" data-css-class=".table th" data-css-style="background" value="<?php echo $this->themes[0]['table_header_row']; ?>" ><div class="colorwheel"></div>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_TABLE_HEADER_TEXT"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="table_header_text" data-css-class=".table th" data-css-style="color" value="<?php echo $this->themes[0]['table_header_text']; ?>" ><div class="colorwheel"></div>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_LINK_COLOR"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="link" data-css-class=".table tr td a" data-css-style="color" value="<?php echo $this->themes[0]['link']; ?>" ><div class="colorwheel"></div>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_LINK_HOVER"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="link_hover" data-css-class=".table tr td a" data-css-style="hover-color" value="<?php echo $this->themes[0]['link_hover']; ?>" ><div class="colorwheel"></div>
                                </li>
                            </ul>
                        </div>
                        <div id="2">
                            <ul class="list-unstyled adminlist cobaltadminlist">
                               <li>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_GENERIC_HEADER"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="header" data-css-class=".navbar-inner" data-css-style="background" value="<?php echo $this->themes[1]['header']; ?>"><div class="colorwheel"></div>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_TABS_HOVER"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="tabs_hover" data-css-class=".navbar .nav > li > a" data-css-style="hover-background" value="<?php echo $this->themes[1]['tabs_hover']; ?>" ><div class="colorwheel"></div>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_TABS_HOVER_TEXT"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="tabs_hover_text" data-css-class=".navbar .nav > li > a" data-css-style="hover-color" value="<?php echo $this->themes[1]['tabs_hover_text']; ?>" ><div class="colorwheel"></div>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_TABLE_HEADER_ROW"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="table_header_row" data-css-class=".table th" data-css-style="background" value="<?php echo $this->themes[1]['table_header_row']; ?>" ><div class="colorwheel"></div>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_TABLE_HEADER_TEXT"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="table_header_text" data-css-class=".table th" data-css-style="color" value="<?php echo $this->themes[1]['table_header_text']; ?>" ><div class="colorwheel"></div>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_LINK_COLOR"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="link" data-css-class=".table tr td a" data-css-style="color"  value="<?php echo $this->themes[1]['link']; ?>" ><div class="colorwheel"></div>
                                    <label><b><?php echo TextHelper::_("COBALT_ADMIN_LINK_HOVER"); ?></b></label>
                                    <input class="hascolorpicker branding-input inputbox" type="text" name="link_hover" data-css-class=".table tr td a" data-css-style="hover-color"  value="<?php echo $this->themes[1]['link_hover']; ?>" ><div class="colorwheel"></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
