CREATE TABLE IF NOT EXISTS `#__shared` (
  `item_id` int(11) NOT NULL,
  `item_type` enum('deal','person','company') DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ;

CREATE TABLE IF NOT EXISTS `#__banter_rooms` (
  `room_id` int(11) DEFAULT NULL,
  `association_id` int(11) DEFAULT NULL,
  `association_type` enum('deal','person','company') DEFAULT NULL
) ;

CREATE TABLE IF NOT EXISTS `#__formwizard` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `type` enum('contact','lead','deal','company') DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `fields` text,
  `html` text,
  `return_url` text,
  `owner_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `#__menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_items` text,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;

INSERT IGNORE INTO `#__menu` (`id`, `menu_items`, `modified`)
VALUES
  (1,'a:8:{i:0;s:9:\"dashboard\";i:1;s:5:\"deals\";i:2;s:6:\"people\";i:3;s:9:\"companies\";i:4;s:8:\"calendar\";i:5;s:9:\"documents\";i:6;s:5:\"goals\";i:7;s:7:\"reports\";}','2012-07-19 09:19:46');

CREATE TABLE IF NOT EXISTS `#__branding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `header` varchar(255) NOT NULL,
  `tabs_hover` varchar(255) NOT NULL,
  `tabs_hover_text` varchar(255) NOT NULL,
  `table_header_row` varchar(255) NOT NULL,
  `table_header_text` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `link_hover` varchar(255) DEFAULT NULL,
  `assigned` tinyint(2) NOT NULL,
  `modified` datetime NOT NULL,
  `site_logo` varchar(255) NOT NULL DEFAULT 'cobalt-3d.png',
  `feature_btn_bg` varchar(255) DEFAULT NULL,
  `feature_btn_border` varchar(255) DEFAULT NULL,
  `block_btn_border` varchar(255) DEFAULT NULL,
  `site_name` varchar(255) NOT NULL DEFAULT 'COBALT',
  PRIMARY KEY (`id`)
) ;


INSERT IGNORE INTO `#__branding` (`id`, `header`, `tabs_hover`, `tabs_hover_text`, `table_header_row`, `table_header_text`, `link`, `link_hover`, `assigned`, `modified`)
VALUES
  (1,'eff6f7','d6edf2','000000','d6edf2','6793a7','1E759E','1E759E',0,'2012-02-21 16:11:03'),
  (2,'eff6f7','d6edf2','000000','d6edf2','6793a7','1E759E','1E759E',1,'2012-07-18 17:30:10');

CREATE TABLE IF NOT EXISTS `#__companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `address_1` varchar(255) NOT NULL DEFAULT '',
  `address_2` varchar(255) NOT NULL DEFAULT '',
  `address_city` varchar(255) DEFAULT NULL,
  `address_state` varchar(255) DEFAULT NULL,
  `address_zip` varchar(255) DEFAULT NULL,
  `address_country` varchar(255) DEFAULT NULL,
  `website` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `notes` text NOT NULL,
  `phone` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `published` tinyint(2) DEFAULT '1',
  `fax` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `twitter_user` text,
  `facebook_url` text,
  `flickr_url` text,
  `youtube_url` text,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `#__config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timezone` varchar(255) DEFAULT 'America/New_York',
  `imap_host` varchar(255) DEFAULT NULL,
  `imap_user` varchar(255) DEFAULT NULL,
  `imap_pass` varchar(255) DEFAULT NULL,
  `users_add` tinyint(2) DEFAULT '0',
  `config_default` tinyint(2) DEFAULT '0',
  `templates_edit` tinyint(2) DEFAULT '0',
  `menu_default` tinyint(2) DEFAULT '0',
  `import_default` tinyint(2) DEFAULT '0',
  `launch_default` tinyint(2) DEFAULT '0',
  `show_help` tinyint(2) DEFAULT '1',
  `import_sample` text,
  `currency` varchar(255) DEFAULT '$',
  `lang_deal` varchar(255) DEFAULT 'deal',
  `lang_person` varchar(255) DEFAULT 'person',
  `lang_company` varchar(255) DEFAULT 'company',
  `lang_contact` varchar(255) DEFAULT 'contact',
  `lang_lead` varchar(255) DEFAULT 'lead',
  `lang_task` varchar(255) DEFAULT 'task',
  `lang_event` varchar(255) DEFAULT 'event',
  `lang_goal` varchar(255) DEFAULT 'goal',
  `welcome_message` varchar(255) DEFAULT 'Hello',
  `time_format` varchar(255) DEFAULT 'H:i',
  PRIMARY KEY (`id`)
) ;

INSERT IGNORE INTO `#__config` (`id`, `timezone`, `imap_host`, `imap_user`, `imap_pass`,`welcome_message`)
VALUES
  (1,'America/New_York','','','','Hello');


CREATE TABLE IF NOT EXISTS `#__conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `conversation` longtext NOT NULL,
  `modified` datetime NOT NULL,
  `published` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `#__deal_custom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `values` varchar(255) NOT NULL,
  `type` enum('number','text','currency','picklist','forecast','date') NOT NULL,
  `required` tinyint(2) NOT NULL,
  `reported` tinyint(2) NOT NULL,
  `multiple_selections` tinyint(2) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `#__deal_custom_cf` (
  `deal_id` int(11) NOT NULL,
  `custom_field_id` int(11) NOT NULL,
  `value` text NOT NULL,
  `modified` datetime NOT NULL
) ;

DROP TABLE IF EXISTS `#__deal_status`;

CREATE TABLE `#__deal_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `ordering` int(11) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;

INSERT INTO `#__deal_status` (`id`, `name`, `ordering`, `class`)
VALUES
  (1,'bad',NULL,'bad'),
  (2,'good',NULL,'good'),
  (3,'question',NULL,'question');

CREATE TABLE IF NOT EXISTS `#__deals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `company_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `stage_id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `probability` varchar(255) NOT NULL,
  `status_id` int(11) NOT NULL,
  `expected_close` date NOT NULL,
  `created` datetime NOT NULL,
  `notes` text NOT NULL,
  `category` varchar(255) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `archived` tinyint(4) NOT NULL,
  `actual_close` datetime NOT NULL,
  `primary_contact_id` int(11) NOT NULL,
  `published` tinyint(2) DEFAULT '1',
  `last_viewed` datetime NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `#__documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `association_id` int(11) NOT NULL,
  `association_type` enum('company','person','deal') DEFAULT 'deal',
  `created` datetime NOT NULL,
  `filetype` varchar(255) NOT NULL DEFAULT '',
  `size` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `shared` tinyint(2) NOT NULL,
  `email` tinyint(2) DEFAULT '0',
  `is_image` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `type` enum('task','event') NOT NULL DEFAULT 'task',
  `assignee_id` int(11) NOT NULL,
  `due_date` datetime NOT NULL,
  `end_date` date DEFAULT NULL,
  `repeats` varchar(255) NOT NULL,
  `repeat_end` datetime NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `all_day` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `completed` tinyint(2) NOT NULL,
  `actual_close` datetime NOT NULL,
  `excludes` mediumtext NOT NULL,
  `parent_id` int(11) NOT NULL,
  `published` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ;


CREATE TABLE IF NOT EXISTS `#__events_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ;


INSERT IGNORE INTO `#__events_categories` (`id`, `name`, `created`, `modified`)
VALUES
  (1,'Call','0000-00-00 00:00:00','0000-00-00 00:00:00'),
  (2,'Milestone','0000-00-00 00:00:00','0000-00-00 00:00:00'),
  (3,'Appointment','2012-02-23 14:32:35','2012-02-23 14:32:41'),
  (4,'Email','2012-02-23 14:32:45','2012-02-23 14:32:50'),
  (5,'Todo','2012-02-23 14:32:35','2012-02-23 14:32:41'),
  (6,'Fax','2012-02-23 14:32:45','2012-02-23 14:32:50');


CREATE TABLE IF NOT EXISTS `#__events_cf` (
  `association_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `association_type` enum('person','deal','company') NOT NULL DEFAULT 'deal'
) ;


CREATE TABLE IF NOT EXISTS `#__goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `goal_type` enum('win_cash','win_deals','move_deals','complete_tasks','write_notes','create_deals') NOT NULL,
  `assigned_type` enum('member','team','company') NOT NULL,
  `assigned_id` int(11) NOT NULL,
  `stage_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `amount` float NOT NULL,
  `leaderboard` tinyint(2) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `created` datetime NOT NULL,
  `published` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ;


CREATE TABLE IF NOT EXISTS `#__history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('deal','person','company','event','task','goal','report','document','note') DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `old_value` text,
  `new_value` text,
  `action_type` enum('created','deleted','edited','uploaded','postponed','cancelled','completed') DEFAULT NULL,
  `field` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;


CREATE TABLE IF NOT EXISTS `#__login_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `#__notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deal_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `note` longtext NOT NULL,
  `category_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `owner_id` int(11) NOT NULL,
  `published` tinyint(2) DEFAULT '1',
  `event_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;


CREATE TABLE IF NOT EXISTS `#__notes_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ;


INSERT IGNORE INTO `#__notes_categories` (`id`, `name`, `created`, `modified`)
VALUES
  (1,'Phone Call','0000-00-00 00:00:00','0000-00-00 00:00:00'),
  (2,'Voicemail','0000-00-00 00:00:00','0000-00-00 00:00:00'),
  (3,'Appointments','0000-00-00 00:00:00','2012-02-14 10:38:24'),
  (4,'Cold Call','0000-00-00 00:00:00','0000-00-00 00:00:00'),
  (5,'Concerns','0000-00-00 00:00:00','0000-00-00 00:00:00'),
  (6,'Emails','0000-00-00 00:00:00','0000-00-00 00:00:00');


CREATE TABLE IF NOT EXISTS `#__people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `company_id` int(11) NOT NULL,
  `position` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `source_id` int(11) NOT NULL,
  `home_address_1` varchar(255) NOT NULL DEFAULT '',
  `home_address_2` varchar(255) NOT NULL DEFAULT '',
  `home_city` varchar(255) NOT NULL DEFAULT '',
  `home_state` varchar(255) NOT NULL DEFAULT '',
  `home_zip` int(11) NOT NULL,
  `home_country` varchar(255) NOT NULL DEFAULT '',
  `assignee_id` int(11) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `facebook_url` varchar(255) NOT NULL,
  `twitter_user` varchar(255) NOT NULL,
  `linkedin_url` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `status_id` int(11) NOT NULL,
  `tags` varchar(255) NOT NULL,
  `type` enum('contact','lead') NOT NULL DEFAULT 'contact',
  `info` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `work_address_1` varchar(255) NOT NULL DEFAULT '',
  `work_address_2` varchar(255) NOT NULL DEFAULT ' ',
  `work_city` varchar(255) NOT NULL DEFAULT ' ',
  `work_state` varchar(255) NOT NULL DEFAULT ' ',
  `work_zip` int(11) NOT NULL,
  `work_country` varchar(255) NOT NULL DEFAULT '',
  `assignment_note` varchar(255) NOT NULL DEFAULT '',
  `mobile_phone` int(255) NOT NULL,
  `home_email` varchar(255) NOT NULL DEFAULT '',
  `other_email` varchar(255) NOT NULL DEFAULT '',
  `home_phone` varchar(255) NOT NULL DEFAULT '',
  `avatar` varchar(255) DEFAULT NULL,
  `aim` varchar(255) DEFAULT NULL,
  `published` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ;


CREATE TABLE IF NOT EXISTS `#__people_cf` (
  `association_id` int(11) NOT NULL,
  `association_type` enum('deal') NOT NULL DEFAULT 'deal',
  `created` datetime NOT NULL,
  `person_id` int(11) DEFAULT NULL
) ;

CREATE TABLE IF NOT EXISTS `#__people_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;


INSERT IGNORE INTO `#__people_status` (`id`, `name`, `color`, `created`, `modified`)
VALUES
  (1,'Archived','199e76','0000-00-00 00:00:00','2012-02-21 00:44:15'),
  (2,'Hot','ff0004','0000-00-00 00:00:00','2012-02-17 10:59:07'),
  (3,'Warm','5510b5','0000-00-00 00:00:00','2012-02-16 19:58:14'),
  (4,'Follow-Up','52b354','0000-00-00 00:00:00','2012-02-17 01:46:57'),
  (5,'Cold','bababa','0000-00-00 00:00:00','2012-02-16 19:58:36');


CREATE TABLE IF NOT EXISTS `#__people_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ;


INSERT IGNORE INTO `#__people_tags` (`id`, `name`, `created`, `modified`)
VALUES
  (1,'Decision Makers','0000-00-00 00:00:00','2012-02-16 12:30:38'),
  (2,'Existing Customer','0000-00-00 00:00:00','0000-00-00 00:00:00'),
  (3,'Partner','0000-00-00 00:00:00','0000-00-00 00:00:00'),
  (4,'Vendor','0000-00-00 00:00:00','0000-00-00 00:00:00'),
  (5,'Vip','0000-00-00 00:00:00','0000-00-00 00:00:00');


CREATE TABLE IF NOT EXISTS `#__people_tags_cf` (
  `person_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ;


CREATE TABLE IF NOT EXISTS `#__reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fields` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ;


CREATE TABLE IF NOT EXISTS `#__sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('per','flat') NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `cost` float NOT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;


INSERT IGNORE INTO `#__sources` (`id`, `name`, `type`, `created`, `modified`, `cost`)
VALUES
  (1,'Cold Call','flat','0000-00-00 00:00:00','2012-02-16 13:16:55',20),
  (2,'Import','per','0000-00-00 00:00:00','0000-00-00 00:00:00',0),
  (3,'None','per','0000-00-00 00:00:00','2012-02-14 10:59:48',26),
  (4,'Referral','per','0000-00-00 00:00:00','0000-00-00 00:00:00',0),
  (5,'Trade','flat','0000-00-00 00:00:00','2012-03-01 14:51:27',20),
  (6,'Website','per','0000-00-00 00:00:00','0000-00-00 00:00:00',0);


CREATE TABLE IF NOT EXISTS `#__stages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `percent` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `ordering` int(11) DEFAULT NULL,
  `won` tinyint(2) DEFAULT '0',
  PRIMARY KEY (`id`)
) ;


INSERT IGNORE INTO `#__stages` (`id`, `name`, `percent`, `created`, `modified`, `color`)
VALUES
  (1,'Qualified Lead',12,'0000-00-00 00:00:00','2012-07-11 15:24:01','ffa200'),
  (2,'Request for Info',9,'0000-00-00 00:00:00','2012-07-11 15:24:14','00ffea'),
  (3,'Presentation',27,'0000-00-00 00:00:00','2012-07-11 15:23:43','009dff'),
  (4,'Negotiation',50,'0000-00-00 00:00:00','2012-07-11 15:23:33','ebe238'),
  (5,'Won',100,'0000-00-00 00:00:00','2012-07-11 15:24:20','12d900'),
  (6,'Lost',0,'0000-00-00 00:00:00','2012-07-11 15:23:19','cc2121');


CREATE TABLE IF NOT EXISTS `#__teams` (
  `team_id` int(11) NOT NULL AUTO_INCREMENT,
  `leader_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`team_id`)
) ;


CREATE TABLE IF NOT EXISTS `#__template_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `day` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ;


CREATE TABLE IF NOT EXISTS `#__templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('deal','person') DEFAULT 'deal',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `default` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ;

---- HANDLED BY JOOMLA
-- CREATE TABLE IF NOT EXISTS `#__users` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `uid` int(11) NOT NULL,
--   `role_type` enum('exec','manager','basic') NOT NULL,
--   `admin` tinyint(2) NOT NULL,
--   `exports` tinyint(2) NOT NULL,
--   `can_delete` tinyint(2) NOT NULL,
--   `team_id` int(11) NOT NULL,
--   `first_name` varchar(255) NOT NULL,
--   `last_name` varchar(255) NOT NULL,
--   `modified` datetime NOT NULL,
--   `created` datetime NOT NULL,
--   `time_zone` varchar(255) NOT NULL DEFAULT 'America/New_York',
--   `date_format` varchar(255) NOT NULL DEFAULT 'm/d/y',
--   `time_format` varchar(255) NOT NULL DEFAULT 'g:i A',
--   `daily_agenda` tinyint(2) NOT NULL DEFAULT '0',
--   `morning_coffee` tinyint(2) NOT NULL DEFAULT '0',
--   `weekly_team_report` tinyint(2) NOT NULL DEFAULT '0',
--   `weekly_personal_report` tinyint(2) NOT NULL DEFAULT '0',
--   `reminder_notifications` tinyint(2) NOT NULL DEFAULT '0',
--   `sms_number` int(11) NOT NULL,
--   `text_messages` tinyint(2) NOT NULL,
--   `home_page_chart` varchar(255) NOT NULL,
--   `commission_rate` int(11) NOT NULL,
--   `deals_columns` text NOT NULL,
--   `people_columns` text NOT NULL,
--   `companies_columns` text NOT NULL,
--   `fullscreen` tinyint(2) DEFAULT '0',
--   `color` varchar(255) DEFAULT NULL,
--   `published` tinyint(2) DEFAULT '1',
--   PRIMARY KEY (`id`)
-- ) ;


CREATE TABLE IF NOT EXISTS `#__users_email_cf` (
  `member_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL
) ;


CREATE TABLE IF NOT EXISTS `#__people_custom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `values` varchar(255) NOT NULL,
  `type` enum('number','text','currency','picklist','forecast','date') NOT NULL,
  `required` tinyint(2) NOT NULL,
  `multiple_selections` tinyint(2) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;


CREATE TABLE IF NOT EXISTS `#__people_custom_cf` (
  `people_id` int(11) NOT NULL,
  `custom_field_id` int(11) NOT NULL,
  `value` text NOT NULL,
  `modified` datetime NOT NULL
) ;


CREATE TABLE IF NOT EXISTS `#__company_custom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `values` varchar(255) NOT NULL,
  `type` enum('number','text','currency','picklist','forecast','date') NOT NULL,
  `required` tinyint(2) NOT NULL,
  `multiple_selections` tinyint(2) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ;

CREATE TABLE IF NOT EXISTS `#__company_custom_cf` (
  `company_id` int(11) NOT NULL,
  `custom_field_id` int(11) NOT NULL,
  `value` text NOT NULL,
  `modified` datetime NOT NULL
) ;

ALTER TABLE `#__companies` CHANGE `address_zip` `address_zip` VARCHAR( 255 ) NULL DEFAULT NULL ;
ALTER TABLE `#__people` CHANGE `home_zip` `home_zip` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `#__people` CHANGE `work_zip` `work_zip` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `#__deal_custom` CHANGE `values` `values` TEXT;
ALTER TABLE `#__people_custom` CHANGE `values` `values` TEXT;
ALTER TABLE `#__company_custom` CHANGE `values` `values` TEXT;