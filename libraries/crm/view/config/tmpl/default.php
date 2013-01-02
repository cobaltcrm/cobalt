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
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="container-fluid">
    <?php echo $this->menu['quick_menu']->render(); ?>
    <div class="row-fluid">
        <div class="span12" id="content">
            <div id="system-message-container"></div>
            <div class="row-fluid">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="span9">
                    <form action="index.php?view=config" method="post" name="adminForm" id="adminForm" class="form-validate"  >
                        <ul class="nav nav-tabs" id="myTab">
                            <li class="active"><a data-toggle="tab" href="#locale"><?php echo JText::_('COBALT_LOCALE'); ?></a></li>
                            <li><a data-toggle="tab" href="#currency"><?php echo JText::_('COBALT_CURRENCY'); ?></a></li>
                            <li><a data-toggle="tab" href="#email"><?php echo JText::_('COBALT_EMAIL'); ?></a></li>
                            <li><a data-toggle="tab" href="#language"><?php echo JText::_('COBALT_LANGUAGE'); ?></a></li>
                            <li><a data-toggle="tab" href="#help"><?php echo JText::_('COBALT_HELP'); ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="locale">
                                 <ul class="unstyled adminlist cobaltadminlist">
                                    <li>
                                        <label><b><?php echo JText::_('COBALT_LANGUAGE'); ?></b></label>
                                        <select class="inputbox" name="site_language" rel="tooltip" data-original-title='<?php echo JText::_('COBALT_SELECT_SITE_LANGUAGE'); ?>' >
                                            <?php echo JHtml::_('select.options', $this->languages, 'value', 'text', $this->language, true);?>
                                        </select>
                                    </li>
                                    <li>
                                        <label><b><?php echo JText::_('COBALT_TIMEZONE'); ?></b></label>
                                        <select class="inputbox" name="timezone" rel="tooltip" data-original-title='<?php echo JText::_('COBALT_SELECT_COBALT_TIMEZONE'); ?>' >
                                            <?php echo JHtml::_('select.options', $this->timezones, 'value', 'text', $this->config->timezone, true);?>
                                        </select>
                                    </li>
                                    <li>
                                        <label><b><?php echo JText::_('COBALT_TIME_FORMAT'); ?></b></label>
                                        <select class="inputbox" name="time_format" rel="tooltip" data-original-title='<?php echo JText::_('COBALT_SELECT_SERVER_TIME_FORMAT'); ?>' >
                                            <?php echo JHtml::_('select.options', $this->time_formats, 'value', 'text', $this->config->time_format, true);?>
                                        </select>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="currency">
                                 <ul class="unstyled adminlist cobaltadminlist">
                                    <li>
                                        <label><b><?php echo JText::_('COBALT_CURRENCY'); ?></b></label>
                                        <input type="text" class="inputbox" name="currency" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_CURRENCY_TOOLTIP'); ?>" value="<?php if ( array_key_exists('currency',$this->config ) ) echo $this->config->currency; ?>" />
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="email">
                                <div class="alert alert-block">
                                    <a class="close" data-dismiss="alert" href="#">×</a>
                                    <h4 class="alert-heading"><?php echo JText::_('COBALT_IMAP_SETTINGS_TITLE'); ?></h4>
                                    <?php echo JText::_('COBALT_IMAP_SETTINGS_DESCRIPTION'); ?>           
                                </div>
                                <?php if ( !$this->imap_found ){ ?>
                                    <div class="alert alert-error">
                                        <?php echo JText::_("COBALT_WARNING_IMAP_NOT_ENABLED"); ?>
                                    </div>
                                 <?php } ?>
                                <ul class="unstyled adminlist cobaltadminlist">
                                    <li>
                                        <label><b><?php echo JText::_('COBALT_IMAP_HOST'); ?></b></label>
                                        <input type="text" class="inputbox" name="imap_host" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_INPUT_IMAP_HOST'); ?>" value="<?php if ( array_key_exists('imap_host',$this->config ) ) echo $this->config->imap_host; ?>" />
                                    </li>
                                    <li>
                                        <label><b><?php echo JText::_('COBALT_IMAP_USER'); ?></b></label>
                                        <input type="text" class="inputbox" name="imap_user" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_INPUT_IMAP_USER'); ?>" value="<?php if ( array_key_exists('imap_user',$this->config ) ) echo $this->config->imap_user; ?>" />
                                    </li>
                                    <li>
                                        <label><b><?php echo JText::_('COBALT_IMAP_PASS'); ?></b></label>
                                        <input type="password" class="inputbox" name="imap_pass" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_INPUT_IMAP_PASS'); ?>" value="<?php if ( array_key_exists('imap_pass',$this->config ) )  echo $this->config->imap_pass; ?>" />
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="language">
                                <ul class="unstyled adminlist cobaltadminlist">
                                    <li>
                                        <label><b><?php echo JText::_('COBALT_WELCOME_MESSAGE'); ?></b></label>
                                        <input type="text" class="inputbox" name="welcome_message" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_WELCOME_MESSAGE_TOOLTIP'); ?>" value="<?php if ( array_key_exists('welcome_message',$this->config ) ) echo $this->config->welcome_message; ?>" />
                                    </li>
                                    <li>
                                        <label><b><?php echo JText::_('COBALT_I_CALL_A_DEAL'); ?></b></label>
                                        <input type="text" class="inputbox" name="lang_deal" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_I_CALL_A_DEAL_TOOLTIP'); ?>" value="<?php if ( array_key_exists('lang_deal',$this->config ) ) echo $this->config->lang_deal; ?>" />
                                    </li>
                                    <li>
                                        <label><b><?php echo JText::_('COBALT_I_CALL_A_PERSON'); ?></b></label>
                                        <input type="text" class="inputbox" name="lang_person" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_I_CALL_A_PERSON_TOOLTIP'); ?>" value="<?php if ( array_key_exists('lang_person',$this->config ) ) echo $this->config->lang_person; ?>" />
                                    </li>
                                    <li>
                                        <label><b><?php echo JText::_('COBALT_I_CALL_A_COMPANY'); ?></b></label>
                                        <input type="text" class="inputbox" name="lang_company" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_I_CALL_A_COMPANY_TOOLTIP'); ?>" value="<?php if ( array_key_exists('lang_company',$this->config ) )  echo $this->config->lang_company; ?>" />
                                    </li>
                                     <li>
                                        <label><b><?php echo JText::_('COBALT_I_CALL_A_CONTACT'); ?></b></label>
                                        <input type="text" class="inputbox" name="lang_contact" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_I_CALL_A_CONTACT_TOOLTIP'); ?>" value="<?php if ( array_key_exists('lang_contact',$this->config ) )  echo $this->config->lang_contact; ?>" />
                                    </li>
                                     <li>
                                        <label><b><?php echo JText::_('COBALT_I_CALL_A_LEAD'); ?></b></label>
                                        <input type="text" class="inputbox" name="lang_lead" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_I_CALL_A_LEAD_TOOLTIP'); ?>" value="<?php if ( array_key_exists('lang_lead',$this->config ) )  echo $this->config->lang_lead; ?>" />
                                    </li>
                                     <li>
                                        <label><b><?php echo JText::_('COBALT_I_CALL_A_TASK'); ?></b></label>
                                        <input type="text" class="inputbox" name="lang_task" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_I_CALL_A_TASK_TOOLTIP'); ?>" value="<?php if ( array_key_exists('lang_task',$this->config ) )  echo $this->config->lang_task; ?>" />
                                    </li>
                                     <li>
                                        <label><b><?php echo JText::_('COBALT_I_CALL_AN_EVENT'); ?></b></label>
                                        <input type="text" class="inputbox" name="lang_event" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_I_CALL_AN_EVENT_TOOLTIP'); ?>" value="<?php if ( array_key_exists('lang_event',$this->config ) )  echo $this->config->lang_event; ?>" />
                                    </li>
                                     <li>
                                        <label><b><?php echo JText::_('COBALT_I_CALL_A_GOAL'); ?></b></label>
                                        <input type="text" class="inputbox" name="lang_goal" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_I_CALL_A_GOAL_TOOLTIP'); ?>" value="<?php if ( array_key_exists('lang_goal',$this->config ) )  echo $this->config->lang_goal; ?>" />
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-pane" id="help">
                                <ul class="unstyled adminlist cobaltadminlist">
                                     <li>
                                        <label class="checkbox">
                                            <input type="checkbox" class="inputbox" name="show_help" rel="tooltip" data-original-title="<?php echo JText::_('COBALT_SHOW_HELP_TOOLTIP'); ?>" value="1" <?php if ( array_key_exists('show_help',$this->config ) && $this->config->show_help == 1 )  echo "checked='checked'"; ?> />
                                            <b><?php echo JText::_('COBALT_SHOW_COBALT_CONFIG_HELP'); ?></b>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div style="display:none;" >
                            <input type="hidden" name="id" value="1" />
                            <input type="hidden" name="task" value="" />
                            <input type="hidden" name="controller" value="" />
                            <input type="hidden" name="model" value="config" />
                            <?php echo JHtml::_('form.token'); ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
	</div>
</div>