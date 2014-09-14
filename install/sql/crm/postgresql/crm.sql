-- 
-- Table: #__banter_rooms
--
CREATE TABLE "#__banter_rooms" (
	"room_id" int,
	"association_id" int,
	"association_type" varchar(250)
);

-- 
-- Table: #__branding
--
CREATE TABLE "#__branding" (
	"id" int NOT NULL,
	"header" varchar(255) NOT NULL,
	"tabs_hover" varchar(255) NOT NULL,
	"tabs_hover_text" varchar(255) NOT NULL,
	"table_header_row" varchar(255) NOT NULL,
	"table_header_text" varchar(255) NOT NULL,
	"link" varchar(255),
	"link_hover" varchar(255),
	"assigned" int NOT NULL,
	"modified" timestamp NOT NULL,
	"site_logo" varchar(255) NOT NULL,
	"feature_btn_bg" varchar(255),
	"feature_btn_border" varchar(255),
	"block_btn_border" varchar(255),
	"site_name" varchar(255) NOT NULL,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__companies
--
CREATE TABLE "#__companies" (
	"id" int NOT NULL,
	"owner_id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"description" text NOT NULL,
	"address_1" varchar(255) NOT NULL,
	"address_2" varchar(255) NOT NULL,
	"address_city" varchar(255),
	"address_state" varchar(255),
	"address_zip" varchar(255),
	"address_country" varchar(255),
	"website" varchar(255) NOT NULL,
	"created" timestamp NOT NULL,
	"notes" text NOT NULL,
	"phone" varchar(255) NOT NULL,
	"modified" timestamp NOT NULL,
	"avatar" varchar(255),
	"published" int,
	"fax" varchar(255),
	"email" varchar(255),
	"twitter_user" text,
	"facebook_url" text,
	"flickr_url" text,
	"youtube_url" text,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__config
--
CREATE TABLE "#__config" (
	"id" int NOT NULL,
	"timezone" varchar(255),
	"imap_host" varchar(255),
	"imap_user" varchar(255),
	"imap_pass" varchar(255),
	"users_add" int,
	"config_default" int,
	"templates_edit" int,
	"menu_default" int,
	"import_default" int,
	"launch_default" int,
	"show_help" int,
	"import_sample" text,
	"currency" varchar(255),
	"lang_deal" varchar(255),
	"lang_person" varchar(255),
	"lang_company" varchar(255),
	"lang_contact" varchar(255),
	"lang_lead" varchar(255),
	"lang_task" varchar(255),
	"lang_event" varchar(255),
	"lang_goal" varchar(255),
	"welcome_message" varchar(255),
	"time_format" varchar(255),
	PRIMARY KEY ("id")
);

-- 
-- Table: #__conversations
--
CREATE TABLE "#__conversations" (
	"id" int NOT NULL,
	"deal_id" int NOT NULL,
	"author" int NOT NULL,
	"created" timestamp NOT NULL,
	"conversation" text NOT NULL,
	"modified" timestamp NOT NULL,
	"published" int,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__deal_custom
--
CREATE TABLE "#__deal_custom" (
	"id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"values" varchar(255) NOT NULL,
	"type" varchar(250) NOT NULL,
	"required" int NOT NULL,
	"reported" int NOT NULL,
	"multiple_selections" int NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"ordering" int,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__deal_custom_cf
--
CREATE TABLE "#__deal_custom_cf" (
	"deal_id" int NOT NULL,
	"custom_field_id" int NOT NULL,
	"value" text NOT NULL,
	"modified" timestamp NOT NULL
);

-- 
-- Table: #__deal_status
--
CREATE TABLE "#__deal_status" (
	"id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"ordering" int,
	"class" varchar(255),
	PRIMARY KEY ("id")
);

-- 
-- Table: #__deals
--
CREATE TABLE "#__deals" (
	"id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"summary" text NOT NULL,
	"company_id" int NOT NULL,
	"amount" float NOT NULL,
	"stage_id" int NOT NULL,
	"source_id" int NOT NULL,
	"probability" varchar(255) NOT NULL,
	"status_id" int NOT NULL,
	"expected_close" date NOT NULL,
	"created" timestamp NOT NULL,
	"notes" text NOT NULL,
	"category" varchar(255) NOT NULL,
	"owner_id" int NOT NULL,
	"modified" timestamp NOT NULL,
	"archived" int NOT NULL,
	"actual_close" timestamp NOT NULL,
	"primary_contact_id" int NOT NULL,
	"published" int,
	"last_viewed" timestamp,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__documents
--
CREATE TABLE "#__documents" (
	"id" int NOT NULL,
	"filename" varchar(255) NOT NULL,
	"name" varchar(255),
	"association_id" int NOT NULL,
	"association_type" varchar(250),
	"created" timestamp NOT NULL,
	"filetype" varchar(255) NOT NULL,
	"size" int NOT NULL,
	"owner_id" int NOT NULL,
	"modified" timestamp NOT NULL,
	"shared" int NOT NULL,
	"email" int,
	"is_image" int,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__events
--
CREATE TABLE "#__events" (
	"id" int NOT NULL,
	"owner_id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"description" text NOT NULL,
	"created" timestamp NOT NULL,
	"type" varchar(250) NOT NULL,
	"assignee_id" int NOT NULL,
	"due_date" timestamp NOT NULL,
	"end_date" date,
	"repeats" varchar(255) NOT NULL,
	"repeat_end" timestamp NOT NULL,
	"start_time" timestamp NOT NULL,
	"end_time" timestamp NOT NULL,
	"all_day" int NOT NULL,
	"category_id" int NOT NULL,
	"modified" timestamp NOT NULL,
	"completed" int NOT NULL,
	"actual_close" timestamp NOT NULL,
	"excludes" text NOT NULL,
	"parent_id" int NOT NULL,
	"published" int,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__events_categories
--
CREATE TABLE "#__events_categories" (
	"id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__events_cf
--
CREATE TABLE "#__events_cf" (
	"association_id" int NOT NULL,
	"event_id" int NOT NULL,
	"association_type" varchar(250) NOT NULL
);

-- 
-- Table: #__formwizard
--
CREATE TABLE "#__formwizard" (
	"id" int NOT NULL,
	"name" varchar(255),
	"description" text,
	"type" varchar(250),
	"modified" timestamp,
	"created" timestamp,
	"modified_by" int,
	"created_by" int,
	"fields" text,
	"html" text,
	"return_url" text,
	"owner_id" int,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__goals
--
CREATE TABLE "#__goals" (
	"id" int NOT NULL,
	"owner_id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"goal_type" varchar(250) NOT NULL,
	"assigned_type" varchar(250) NOT NULL,
	"assigned_id" int NOT NULL,
	"stage_id" int NOT NULL,
	"category_id" int NOT NULL,
	"amount" float NOT NULL,
	"leaderboard" int NOT NULL,
	"start_date" timestamp NOT NULL,
	"end_date" timestamp NOT NULL,
	"created" timestamp NOT NULL,
	"published" int,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__history
--
CREATE TABLE "#__history" (
	"id" int NOT NULL,
	"type" varchar(250),
	"type_id" int,
	"user_id" int,
	"date" timestamp,
	"old_value" text,
	"new_value" text,
	"action_type" varchar(250),
	"field" varchar(255),
	PRIMARY KEY ("id")
);

-- 
-- Table: #__login_history
--
CREATE TABLE "#__login_history" (
	"id" int NOT NULL,
	"user_id" int,
	"date" date,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__menu
--
CREATE TABLE "#__menu" (
	"id" int NOT NULL,
	"menu_items" text,
	"modified" timestamp,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__notes
--
CREATE TABLE "#__notes" (
	"id" int NOT NULL,
	"deal_id" int NOT NULL,
	"person_id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"note" text NOT NULL,
	"category_id" int NOT NULL,
	"company_id" int NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"owner_id" int NOT NULL,
	"published" int,
	"event_id" int,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__notes_categories
--
CREATE TABLE "#__notes_categories" (
	"id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__people
--
CREATE TABLE "#__people" (
	"id" int NOT NULL,
	"uid" int NOT NULL,
	"owner_id" int NOT NULL,
	"first_name" varchar(255) NOT NULL,
	"last_name" varchar(255) NOT NULL,
	"company_id" int NOT NULL,
	"position" varchar(255) NOT NULL,
	"phone" varchar(255) NOT NULL,
	"email" varchar(255) NOT NULL,
	"source_id" varchar(255) NOT NULL,
	"home_address_1" varchar(255) NOT NULL,
	"home_address_2" varchar(255) NOT NULL,
	"home_city" varchar(255) NOT NULL,
	"home_state" varchar(255) NOT NULL,
	"home_zip" int NOT NULL,
	"home_country" varchar(255) NOT NULL,
	"assignee_id" int NOT NULL,
	"fax" varchar(255) NOT NULL,
	"website" varchar(255) NOT NULL,
	"facebook_url" varchar(255) NOT NULL,
	"twitter_user" varchar(255) NOT NULL,
	"linkedin_url" varchar(255) NOT NULL,
	"created" timestamp NOT NULL,
	"status_id" int NOT NULL,
	"tags" varchar(255) NOT NULL,
	"type" varchar(250) NOT NULL,
	"info" varchar(255) NOT NULL,
	"modified" timestamp NOT NULL,
	"work_address_1" varchar(255) NOT NULL,
	"work_address_2" varchar(255) NOT NULL,
	"work_city" varchar(255) NOT NULL,
	"work_state" varchar(255) NOT NULL,
	"work_zip" int NOT NULL,
	"work_country" varchar(255) NOT NULL,
	"assignment_note" varchar(255) NOT NULL,
	"mobile_phone" int NOT NULL,
	"home_email" varchar(255) NOT NULL,
	"other_email" varchar(255) NOT NULL,
	"home_phone" varchar(255) NOT NULL,
	"avatar" varchar(255),
	"aim" varchar(255),
	"published" int,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__people_cf
--
CREATE TABLE "#__people_cf" (
	"association_id" int NOT NULL,
	"association_type" varchar(250) NOT NULL,
	"created" timestamp NOT NULL,
	"person_id" int
);

-- 
-- Table: #__people_status
--
CREATE TABLE "#__people_status" (
	"id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"color" varchar(255) NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"ordering" int,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__people_tags
--
CREATE TABLE "#__people_tags" (
	"id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__people_tags
--
CREATE TABLE "#__people_tags_cf" (
	"person_id" int NOT NULL,
	"tag_id" int NOT NULL
);

-- 
-- Table: #__reports
--
CREATE TABLE "#__reports" (
	"id" int NOT NULL,
	"owner_id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"fields" text NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__shared
--
CREATE TABLE "#__shared" (
	"item_id" int NOT NULL,
	"item_type" varchar(250),
	"user_id" int
);

-- 
-- Table: #__sources
--
CREATE TABLE "#__sources" (
	"id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"type" varchar(250) NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"cost" float NOT NULL,
	"ordering" int,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__stages
--
CREATE TABLE "#__stages" (
	"id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"percent" int NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"color" varchar(255),
	"ordering" int,
	"won" int,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__teams
--
CREATE TABLE "#__teams" (
	"team_id" int NOT NULL,
	"leader_id" int NOT NULL,
	"name" varchar(255),
	PRIMARY KEY (team_id)
);

-- 
-- Table: #__template_data
--
CREATE TABLE "#__template_data" (
	"id" int NOT NULL,
	"template_id" int NOT NULL,
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"name" varchar(255) NOT NULL,
	"day" int NOT NULL,
	"type" varchar(255) NOT NULL,
	PRIMARY KEY ("id")
);

-- 
-- Table: #__templates
--
CREATE TABLE "#__templates" (
	"id" int NOT NULL,
	"name" varchar(255) NOT NULL,
	"type" varchar(250),
	"created" timestamp NOT NULL,
	"modified" timestamp NOT NULL,
	"default" int NOT NULL,
	PRIMARY KEY ("id")
);

INSERT INTO "#__branding" ("id", "header", "tabs_hover", "tabs_hover_text", "table_header_row", "table_header_text", "link", "link_hover", "assigned", "modified", "site_logo", "feature_btn_bg", "feature_btn_border", "block_btn_border", "site_name") VALUES (1, 'eff6f7', 'd6edf2', '000000', 'd6edf2', '6793a7', '1E759E', '1E759E', 0, '2012-02-21 16:11:03', 'cobalt-3d.png', null, null, null, 'COBALT');
INSERT INTO "#__branding" ("id", "header", "tabs_hover", "tabs_hover_text", "table_header_row", "table_header_text", "link", "link_hover", "assigned", "modified", "site_logo", "feature_btn_bg", "feature_btn_border", "block_btn_border", "site_name") VALUES (2, 'eff6f7', 'd6edf2', '000000', 'd6edf2', '6793a7', '1E759E', '1E759E', 1, '2012-07-18 17:30:10', 'cobalt-3d.png', null, null, null, 'COBALT');
INSERT INTO "#__config" ("id", "timezone", "imap_host", "imap_user", "imap_pass", "users_add", "config_default", "templates_edit", "menu_default", "import_default", "launch_default", "show_help", "import_sample", "currency", "lang_deal", "lang_person", "lang_company", "lang_contact", "lang_lead", "lang_task", "lang_event", "lang_goal", "welcome_message", "time_format") VALUES (1, 'America/New_York', '', '', '', 0, 0, 0, 0, 0, 0, 1, null, '$', 'deal', 'person', 'company', 'contact', 'lead', 'task', 'event', 'goal', 'Hello', 'H:i');
INSERT INTO "#__deal_status" ("id", "name", "ordering", "class") VALUES (1, 'bad', null, 'bad');
INSERT INTO "#__deal_status" ("id", "name", "ordering", "class") VALUES (2, 'good', null, 'good');
INSERT INTO "#__deal_status" ("id", "name", "ordering", "class") VALUES (3, 'question', null, 'question');
INSERT INTO "#__events_categories" ("id", "name", "created", "modified") VALUES (1, 'Call', '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO "#__events_categories" ("id", "name", "created", "modified") VALUES (2, 'Milestone', '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO "#__events_categories" ("id", "name", "created", "modified") VALUES (3, 'Appointment', '2012-02-23 14:32:35', '2012-02-23 14:32:41');
INSERT INTO "#__events_categories" ("id", "name", "created", "modified") VALUES (4, 'Email', '2012-02-23 14:32:45', '2012-02-23 14:32:50');
INSERT INTO "#__events_categories" ("id", "name", "created", "modified") VALUES (5, 'Todo', '2012-02-23 14:32:35', '2012-02-23 14:32:41');
INSERT INTO "#__events_categories" ("id", "name", "created", "modified") VALUES (6, 'Fax', '2012-02-23 14:32:45', '2012-02-23 14:32:50');
INSERT INTO "#__menu" ("id", menu_items, modified) VALUES (1, 'a:8:{i:0;s:9:"dashboard";i:1;s:5:"deals";i:2;s:6:"people";i:3;s:9:"companies";i:4;s:8:"calendar";i:5;s:9:"documents";i:6;s:5:"goals";i:7;s:7:"reports";}', '2012-07-19 09:19:46');
INSERT INTO "#__notes_categories" ("id", "name", "created", "modified") VALUES (1, 'Phone Call', '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO "#__notes_categories" ("id", "name", "created", "modified") VALUES (2, 'Voicemail', '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO "#__notes_categories" ("id", "name", "created", "modified") VALUES (3, 'Appointments', '1970-01-01 00:00:00', '2012-02-14 10:38:24');
INSERT INTO "#__notes_categories" ("id", "name", "created", "modified") VALUES (4, 'Cold Call', '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO "#__notes_categories" ("id", "name", "created", "modified") VALUES (5, 'Concerns', '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO "#__notes_categories" ("id", "name", "created", "modified") VALUES (6, 'Emails', '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO "#__people_status" ("id", "name", "color", "created", "modified", "ordering") VALUES (1, 'Archived', '199e76', '1970-01-01 00:00:00', '2012-02-21 00:44:15', null);
INSERT INTO "#__people_status" ("id", "name", "color", "created", "modified", "ordering") VALUES (2, 'Hot', 'ff0004', '1970-01-01 00:00:00', '2012-02-17 10:59:07', null);
INSERT INTO "#__people_status" ("id", "name", "color", "created", "modified", "ordering") VALUES (3, 'Warm', '5510b5', '1970-01-01 00:00:00', '2012-02-16 19:58:14', null);
INSERT INTO "#__people_status"("id", "name", "color", "created", "modified", "ordering") VALUES (4, 'Follow-Up', '52b354', '1970-01-01 00:00:00', '2012-02-17 01:46:57', null);
INSERT INTO "#__people_status" ("id", "name", "color", "created", "modified", "ordering") VALUES (5, 'Cold', 'bababa', '1970-01-01 00:00:00', '2012-02-16 19:58:36', null);
INSERT INTO "#__people_tags" ("id", "name", "created", "modified") VALUES (1, 'Decision Makers', '1970-01-01 00:00:00', '2012-02-16 12:30:38');
INSERT INTO "#__people_tags" ("id", "name", "created", "modified") VALUES (2, 'Existing Customer', '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO "#__people_tags" ("id", "name", "created", "modified") VALUES (3, 'Partner', '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO "#__people_tags" ("id", "name", "created", "modified") VALUES (4, 'Vendor', '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO "#__people_tags" ("id", "name", "created", "modified") VALUES (5, 'Vip', '1970-01-01 00:00:00', '1970-01-01 00:00:00');
INSERT INTO "#__sources" ("id", "name", "type", "created", "modified", "cost", "ordering") VALUES (1, 'Cold Call', 'flat', '1970-01-01 00:00:00', '2012-02-16 13:16:55', 20, null);
INSERT INTO "#__sources" ("id", "name", "type", "created", "modified", "cost", "ordering") VALUES (2, 'Import', 'per', '1970-01-01 00:00:00', '1970-01-01 00:00:00', 0, null);
INSERT INTO "#__sources" ("id", "name", "type", "created", "modified", "cost", "ordering") VALUES (3, 'None', 'per', '1970-01-01 00:00:00', '2012-02-14 10:59:48', 26, null);
INSERT INTO "#__sources" ("id", "name", "type", "created", "modified", "cost", "ordering") VALUES (4, 'Referral', 'per', '1970-01-01 00:00:00', '1970-01-01 00:00:00', 0, null);
INSERT INTO "#__sources" ("id", "name", "type", "created", "modified", "cost", "ordering") VALUES (5, 'Trade', 'flat', '1970-01-01 00:00:00', '2012-03-01 14:51:27', 20, null);
INSERT INTO "#__sources" ("id", "name", "type", "created", "modified", "cost", "ordering") VALUES (6, 'Website', 'per', '1970-01-01 00:00:00', '1970-01-01 00:00:00', 0, null);
INSERT INTO "#__stages" ("id", "name", "percent", "created", "modified", "color", "ordering", "won") VALUES (1, 'Qualified Lead', 12, '1970-01-01 00:00:00', '2012-07-11 15:24:01', 'ffa200', null, 0);
INSERT INTO "#__stages" ("id", "name", "percent", "created", "modified", "color", "ordering", "won") VALUES (2, 'Request for Info', 9, '1970-01-01 00:00:00', '2012-07-11 15:24:14', '00ffea', null, 0);
INSERT INTO "#__stages" ("id", "name", "percent", "created", "modified", "color", "ordering", "won") VALUES (3, 'Presentation', 27, '1970-01-01 00:00:00', '2012-07-11 15:23:43', '009dff', null, 0);
INSERT INTO "#__stages" ("id", "name", "percent", "created", "modified", "color", "ordering", "won") VALUES (4, 'Negotiation', 50, '1970-01-01 00:00:00', '2012-07-11 15:23:33', 'ebe238', null, 0);
INSERT INTO "#__stages" ("id", "name", "percent", "created", "modified", "color", "ordering", "won") VALUES (5, 'Won', 100, '1970-01-01 00:00:00', '2012-07-11 15:24:20', '12d900', null, 0);
INSERT INTO "#__stages" ("id", "name", "percent", "created", "modified", "color", "ordering", "won") VALUES (6, 'Lost', 0, '1970-01-01 00:00:00', '2012-07-11 15:23:19', 'cc2121', null, 0);
