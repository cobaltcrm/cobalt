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

<script type="text/javascript">
    var user_id = <?php echo $this->user_id; ?>;
    var loc = 'profile';
</script>

<h1><?php echo TextHelper::_('COBALT_PROFILE'); ?></h1>

<div class="panel-group" id="accordion">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#language">
                    <?php echo TextHelper::_('COBALT_EDIT_LANGUAGE'); ?>
                </a>
            </h4>
        </div>
        <div id="language" class="panel-collapse collapse in">
            <div class="panel-body">
                <form method="post" action="<?php echo RouteHelper::_('index.php?task=save&format=raw'); ?>" data-ajax="1" role="form" class="form-inline">
                    <select class="form-control" name="language">
                        <?php
                            $lngs = ConfigHelper::getLanguages();
                            echo JHtml::_('select.options', $lngs, 'value', 'text', $this->user->language, true);
                        ?>
                    </select>
                    <input type="hidden" name="model" value="user" />
                    <input type="hidden" name="id" value="<?php echo $this->user_id ?>" />
                    <input type="submit" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" class="btn btn-success button save" >
                    <span> - </span>
                    <input type="button" value="<?php echo TextHelper::_('COBALT_CANCEL'); ?>" class="button btn cancel">
                </form>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#personal">
                    <?php echo TextHelper::_('COBALT_EDIT_PERSONAL_INFO'); ?>
                </a>
            </h4>
        </div>
        <div id="personal" class="panel-collapse collapse">
          <div class="panel-body">
            <form>
                <fieldset>
                    <ul class="list-unstyled">
                        <li>
                            <label><?php echo TextHelper::_('COBALT_PERSON_FIRST'); ?></label>
                            <input class="form-control" type="text" name="first_name" value="<?php echo $this->user->first_name; ?>">
                        </li>
                        <li>
                            <label><?php echo TextHelper::_('COBALT_PERSON_LAST'); ?></label>
                            <input class="form-control" type="text" name="last_name" value="<?php echo $this->user->last_name; ?>">
                        </li>
                    </ul>
                </fieldset>
            </form>
            <input type="button" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" class="btn btn-success button save" > - <input type="button" value="<?php echo TextHelper::_('COBALT_CANCEL'); ?>" class="button btn cancel">
          </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#date">
                    <?php echo TextHelper::_('COBALT_DATE_TIME_SETTINGS'); ?>
                </a>
            </h4>
        </div>
        <div id="date" class="panel-body collapse">
            <div class="panel-body">
                <form>
                    <fieldset>
                        <ul class="list-unstyled">
                            <li>
                                <label><?php echo TextHelper::_('COBALT_TIMEZONE'); ?></label>
                                <select class="form-control" name="time_zone">
                                    <?php
                                        $timezone_abbreviations = DateHelper::getTimezones();
                                        echo JHtml::_('select.options', $timezone_abbreviations, 'value', 'text', $this->user->time_zone, true);
                                    ?>
                                </select>
                            </li>
                            <li>
                            </li>
                            <li>
                                <label><?php echo TextHelper::_('COBALT_DATE_FORMAT'); ?></label>
                                <select class="form-control" name="date_format">
                                    <?php
                                        $date_formats = DateHelper::getDateFormats();
                                        echo JHtml::_('select.options', $date_formats, 'value', 'text', $this->user->date_format, true);
                                    ?>
                                </select>
                            </li>
                            <li>
                                <label><?php echo TextHelper::_('COBALT_TIME_FORMAT'); ?></label>
                                <select class="form-control" name="time_format">
                                    <?php
                                        $time_formats = DateHelper::getTimeFormats();
                                        echo JHtml::_('select.options', $time_formats, 'value', 'text', $this->user->time_format, true);
                                    ?>
                                </select>
                            </li>
                        </ul>
                    </fieldset>
                </form>
                <input type="button" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" class="btn btn-success button save" > - <input type="button" value="<?php echo TextHelper::_('COBALT_CANCEL'); ?>" class="btn button cancel">
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#email1">
                    <?php echo TextHelper::_('COBALT_USER_INBOX_MULTIPLE_EMAILS'); ?>
                </a>
            </h4>
        </div>
        <div id="email1" class="panel-body collapse">
            <div class="panel-body">
                <form>
                    <fieldset>
                        <ul class="list-unstyled" id="email_input_boxes">
                            <li><?php echo TextHelper::_('COBALT_MULTIPLE_INBOX_DESC_1'); ?></li>
                            <li><?php echo TextHelper::_('COBALT_MULTIPLE_INBOX_DESC_2'); ?></li>
                            <li>
                                <label><?php echo TextHelper::_('COBALT_PRIMARY_EMAIL'); ?></label>
                                <input class="form-control" disabled="disabled" value="<?php echo $this->user->email; ?>" />
                            </li>
                            <?php if (isset($this->user->emails) && count($this->user->emails)) { ?>
                                <?php foreach ($this->user->emails as $key => $email) { ?>
                                    <?php if (isset($email['email']) && $email['email'] != $this->user->email) { ?>
                            <li>
                                <label><?php echo TextHelper::_('COBALT_EMAIL'); ?></label>
                                <input class="form-control" type="text" name="email[]" value="<?php echo $email['email']; ?>">
                                <span class="message"></span>
                            </li>
                                    <?php } ?>
                                <?php } ?>
                                <?php $remaining = 2 - count($this->user->emails); ?>
                                <?php for ($i = 0; $i < $remaining; $i++) { ?>
                            <li>
                                <label><?php echo TextHelper::_('COBALT_EMAIL'); ?></label>
                                <input class="form-control" type="text" name="email[]" value="">
                                <span class="message"></span>
                            </li>
                                <?php } ?>
                            <?php } else { ?>
                            <li>
                                <label><?php echo TextHelper::_('COBALT_EMAIL'); ?></label>
                                <input class="form-control" type="text" name="email[]" value="">
                                <span class="message"></span>
                            </li>
                            <li>
                                <label><?php echo TextHelper::_('COBALT_EMAIL'); ?></label>
                                <input class="form-control" type="text" name="email[]" value="">
                                <span class="message"></span>
                            </li>
                            <?php } ?>
                        </ul>
                        <a class="add_email_link" onclick="addEmailBox();"><?php echo TextHelper::_('COBALT_ADD_ANOTHER_EMAIL'); ?></a>
                    </fieldset>
                </form>
                <input type="button" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" class="btn btn-success button save" > - <input type="button" value="<?php echo TextHelper::_('COBALT_CANCEL'); ?>" class="btn button cancel">
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#email2">
                    <?php echo TextHelper::_('COBALT_EMAIL_PREF_REMINDERS'); ?>
                </a>
            </h4>
        </div>
        <div id="email2" class="panel-body collapse">
            <div class="panel-body">
                <form>
                    <fieldset>
                        <ul class="list-unstyled" >
                            <?php /**
                            <li>
                                <label class="small"><input type="checkbox" name="daily_agenda" <?php if ( $this->user['daily_agenda'] ) echo 'checked'; ?> ></label>
                                <span class="faux_input"><b><?php echo TextHelper::_('COBALT_DAILY_AGENDA'); ?></b></span>
                                <span class="faux_input_details"><?php echo TextHelper::_('COBALT_DAILY_AGENDA_DESC'); ?></span>
                            </li>
                             **/ ?>
                            <li>
                                <label class="small"><input type="checkbox" name="morning_coffee" <?php if ( $this->user->morning_coffee ) echo 'checked'; ?>></label>
                                <span class="faux_input"><b><?php echo TextHelper::_('COBALT_MORNING_COFFEE'); ?></b></span>
                                <span class="faux_input_details"><?php echo TextHelper::_('COBALT_MORNING_COFFEE_DESC'); ?></span>
                            </li>
                            <?php /**
                            <?php if (UsersHelper::getRole()!='basic') { ?>
                            <li>
                                <label class="small"><input type="checkbox" name="weekly_team_report" <?php if ( $this->user['weekly_team_report'] ) echo 'checked'; ?>></label>
                                <span class="faux_input"><b><?php echo TextHelper::_('COBALT_TEAM_USE_REPORT'); ?></b></span>
                                <span class="faux_input_details"><?php echo TextHelper::_('COBALT_TEAM_USE_REPORT_DESC'); ?></span>
                            </li>
                            <?php } ?>
                            <li>
                                <label class="small"><input type="checkbox" name="weekly_personal_report" <?php if ( $this->user['weekly_personal_report'] ) echo 'checked'; ?>></label>
                                <span class="faux_input"><b><?php echo TextHelper::_('COBALT_TEAM_USE_REPORT'); ?></b></span>
                                <span class="faux_input_details"><?php echo TextHelper::_('COBALT_PERSONAL_USE_REPORT_DESC'); ?></span>
                            </li>
                            <?php /* TODO: ADD CRON for 15 Min Email Reminder
                            <li>
                                <label class="small"><input type="checkbox" name="reminder_notifications" <?php if ( $this->user['reminder_notifications'] ) echo 'checked'; ?>></label>
                                <span class="faux_input"><b>Email me 15 minutes before a reminder is due</b></span>
                            </li>
                             */ ?>
                        </ul>
                    </fieldset>
                </form>
                <input type="button" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" class="button btn btn-success save" > - <input type="button" value="<?php echo TextHelper::_('COBALT_CANCEL'); ?>" class="btn button cancel">
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#home">
                    <?php echo TextHelper::_('COBALT_SET_HOME_CHART'); ?>
                </a>
            </h4>
        </div>
        <div id="home" class="panel-body collapse">
            <div class="panel-body">
                <form>
                    <fieldset>
                        <ul class="list-unstyled">
                            <li><?php echo TextHelper::_('COBALT_SET_HOME_CHART_DESC'); ?></li>
                            <li>
                                <label>
                                    <select class="form-control" name="home_page_chart">
                                        <?php
                                            $charts = ChartsHelper::getDashboardCharts();
                                            echo JHtml::_('select.options', $charts, 'value', 'text', $this->user->home_page_chart, true);
                                        ?>
                                    </select>
                                </label>
                            </li>
                        </ul>
                    </fieldset>
                </form>
                <input type="button" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" class="btn btn-success button save" > - <input type="button" value="<?php echo TextHelper::_('COBALT_CANCEL'); ?>" class="button btn cancel">
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#commission">
                    <?php echo TextHelper::_('COBALT_SET_COMMISSION_RATE'); ?>
                </a>
            </h4>
        </div>
        <div id="commission" class="panel-body collapse">
            <div class="panel-body">
                <form>
                    <fieldset>
                        <ul class="list-unstyled" >
                            <li><?php echo TextHelper::_('COBALT_SET_COMMISSION_RATE_DESC'); ?></li>
                            <li>
                                <label>
                                    <?php echo TextHelper::_('COBALT_COMMISSION_RATE'); ?>
                                </label>
                                <div class="input-append">
                                    <input class="form-control" type="text" name="commission_rate" value="<?php echo isset($this->user->commission_rate) ? $this->user->commission_rate : ''; ?>">
                                    <span class="input-append-addon">%</span>
                                </div>
                            </li>
                        </ul>
                    </fieldset>
                </form>
                <input type="button" value="<?php echo TextHelper::_('COBALT_SAVE'); ?>" class="btn btn-success button save" > - <input type="button" value="<?php echo TextHelper::_('COBALT_CANCEL'); ?>" class="btn button cancel">
            </div>
        </div>
    </div>
</div>
