-- #__aicontactsafe_config

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_config` (

  `id` int(11) unsigned NOT NULL auto_increment,

  `config_key` varchar(50) NOT NULL default '',

  `config_value` text NOT NULL,

  PRIMARY KEY  (`id`)

) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES
(1, 'use_css_backend', '1'),
(2, 'use_SqueezeBox', '0'),
(3, 'highlight_errors', '1'),
(4, 'keep_session_alive', '0'),
(5, 'activate_help', '1'),
(6, 'date_format', 'l, d F Y H:i'),
(7, 'default_status_filter', '0'),
(8, 'editbox_cols', '40'),
(9, 'editbox_rows', '10'),
(10, 'default_name', ''),
(11, 'default_email', ''),
(12, 'default_subject', ''),
(13, 'activate_spam_control', '0'),
(14, 'block_words', 'url='),
(15, 'record_blocked_messages', '1'),
(16, 'activate_ip_ban', '0'),
(17, 'ban_ips', ''),
(18, 'redirect_ips', ''),
(19, 'ban_ips_blocked_words', '0'),
(20, 'maximum_messages_ban_ip', '0'),
(21, 'maximum_minutes_ban_ip', '0'),
(22, 'email_ban_ip', ''),
(23, 'set_sender_joomla', '0'),
(24, 'upload_attachments', 'media/aicontactsafe/uploads'),
(25, 'maximum_size', '5000000'),
(26, 'attachments_types', 'rar,zip,doc,xls,txt,gif,jpg,png,bmp'),
(27, 'attach_to_email', '1'),
(28, 'delete_after_sent', '0'),
(29, 'gid_messages', '8'),
(30, 'users_all_messages', '0');



-- #__aicontactsafe_fields

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_fields` (

  `id` int(11) unsigned NOT NULL auto_increment,

  `name` varchar(50) NOT NULL default '',

  `field_label` text NOT NULL,

  `label_parameters` text NOT NULL,

  `field_label_message` text NOT NULL,

  `label_message_parameters` text NOT NULL,

  `label_after_field` tinyint(1) unsigned NOT NULL default '0',

  `field_type` varchar(2) NOT NULL default 'TX',

  `field_parameters` text NOT NULL,

  `field_values` text NOT NULL,

  `field_limit` int(11) NOT NULL default '0',

  `default_value` varchar(150) NOT NULL default '',

  `auto_fill` varchar(10) NOT NULL default '',

  `field_sufix` text NOT NULL,

  `field_prefix` text NOT NULL,

  `ordering` int(11) NOT NULL default '0',

  `field_required` tinyint(1) unsigned NOT NULL default '0',

  `field_in_message` tinyint(1) unsigned NOT NULL default '1',

  `send_message` tinyint(1) unsigned NOT NULL default '0',

  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',

  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',

  `published` tinyint(1) unsigned NOT NULL default '1',

  `checked_out` tinyint(1) unsigned NOT NULL default '0',

  `checked_out_time` date NOT NULL default '0000-00-00',

  PRIMARY KEY  (`id`)

) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;





-- #__aicontactsafe_contactinformations

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_contactinformations` (

  `id` int(11) unsigned NOT NULL auto_increment,

  `profile_id` int(11) unsigned NOT NULL,

  `info_key` varchar(50) NOT NULL default '',

  `info_label` varchar(250) NOT NULL default '',

  `info_value` text NOT NULL,

  PRIMARY KEY  (`id`)

) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;





-- #__aicontactsafe_messages

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_messages` (

  `id` int(11) unsigned NOT NULL auto_increment,

  `name` varchar(50) NOT NULL default '',

  `email` varchar(100) NOT NULL default '',

  `subject` varchar(200) NOT NULL default '',

  `message` text NOT NULL default '',

  `send_to_sender` tinyint(1) unsigned NOT NULL default '0',

  `sender_ip` varchar(20) NOT NULL default '',

  `profile_id` int(11) unsigned NOT NULL,

  `status_id` int(11) unsigned NOT NULL,

  `manual_status` tinyint(1) unsigned NOT NULL default '0',

  `email_destination` text NOT NULL default '',

  `email_reply` varchar(100) NOT NULL default '',

  `subject_reply` text NOT NULL default '',

  `message_reply` text NOT NULL default '',

  `user_id` int(11) NOT NULL default '0',

  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',

  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',

  `published` tinyint(1) unsigned NOT NULL default '1',

  `checked_out` tinyint(1) unsigned NOT NULL default '0',

  `checked_out_time` date NOT NULL default '0000-00-00',

  PRIMARY KEY  (`id`)

) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;





-- #__aicontactsafe_profiles

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_profiles` (

  `id` int(11) unsigned NOT NULL auto_increment,

  `name` varchar(50) NOT NULL default '',

  `use_ajax` tinyint(1) unsigned NOT NULL default '0',

  `use_message_css` tinyint(1) unsigned NOT NULL default '0',

  `contact_form_width` int(11) NOT NULL default '0',

  `bottom_row_space` int(11) NOT NULL default '0',

  `align_buttons` tinyint(1) unsigned NOT NULL default '1',

  `contact_info_width` int(11) NOT NULL default '0',

  `use_captcha` tinyint(1) unsigned NOT NULL default '1',

  `captcha_type` tinyint(1) unsigned NOT NULL default '0',

  `align_captcha` tinyint(1) unsigned NOT NULL default '1',

  `email_address` varchar(100) NOT NULL default '',

  `always_send_to_email_address` tinyint(1) unsigned NOT NULL default '1',

  `subject_prefix` varchar(100) NOT NULL default '',

  `email_mode` tinyint(1) unsigned NOT NULL default '1',

  `record_message` tinyint(1) unsigned NOT NULL default '1',

  `record_fields` tinyint(1) unsigned NOT NULL default '0',

  `custom_date_format` varchar(30) NOT NULL default '%d %B %Y',

  `custom_date_years_back` int(11) NOT NULL default '70',

  `custom_date_years_forward` int(11) NOT NULL default '0',

  `required_field_mark` text NOT NULL,

  `display_format` int(11) NOT NULL default '2',

  `plg_contact_info` tinyint(1) unsigned NOT NULL default '0',

  `use_random_letters` tinyint(1) unsigned NOT NULL default '0',

  `min_word_length` tinyint(2) unsigned NOT NULL default '5',

  `max_word_length` tinyint(2) unsigned NOT NULL default '8',

  `set_default` tinyint(1) unsigned NOT NULL default '0',

  `active_fields` text NOT NULL,

  `captcha_width` smallint(4) NOT NULL default '400',

  `captcha_height` smallint(4) NOT NULL default '55',

  `captcha_bgcolor` varchar(10) NOT NULL default '#FFFFFF',

  `captcha_backgroundTransparent` tinyint(1) unsigned NOT NULL default '1',

  `captcha_colors` text NOT NULL,

  `name_field_id` int(11) unsigned NOT NULL,

  `email_field_id` int(11) unsigned NOT NULL,

  `subject_field_id` int(11) unsigned NOT NULL,

  `send_to_sender_field_id` int(11) unsigned NOT NULL,

  `redirect_on_success` text NOT NULL,

  `fields_order` text NOT NULL,

  `use_mail_template` tinyint(1) unsigned NOT NULL default '0',

  `default_status_id` int(11) unsigned NOT NULL,

  `read_status_id` int(11) unsigned NOT NULL,

  `reply_status_id` int(11) unsigned NOT NULL,

  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',

  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',

  `published` tinyint(1) unsigned NOT NULL default '1',

  `checked_out` tinyint(1) unsigned NOT NULL default '0',

  `checked_out_time` date NOT NULL default '0000-00-00',

  PRIMARY KEY  (`id`)

) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;





-- #__aicontactsafe_messagefiles

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_messagefiles` (

  `id` int(11) unsigned NOT NULL auto_increment,

  `message_id` int(11) unsigned NOT NULL,

  `name` text NOT NULL,

  `r_id` int(21) unsigned NOT NULL,

  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',

  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',

  `published` tinyint(1) unsigned NOT NULL default '1',

  `checked_out` tinyint(1) unsigned NOT NULL default '0',

  `checked_out_time` date NOT NULL default '0000-00-00',

  PRIMARY KEY  (`id`)

) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;





-- #__aicontactsafe_fieldvalues

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_fieldvalues` (

  `id` int(11) unsigned NOT NULL auto_increment,

  `field_id` int(11) unsigned NOT NULL,

  `message_id` int(11) unsigned NOT NULL,

  `field_value` text NOT NULL,

  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',

  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',

  `published` tinyint(1) unsigned NOT NULL default '1',

  `checked_out` tinyint(1) unsigned NOT NULL default '0',

  `checked_out_time` date NOT NULL default '0000-00-00',

  PRIMARY KEY  (`id`)

) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;





-- #__aicontactsafe_statuses

CREATE TABLE IF NOT EXISTS `#__aicontactsafe_statuses` (

  `id` int(11) unsigned NOT NULL auto_increment,

  `name` varchar(20) NOT NULL default '',

  `color` varchar(10) NOT NULL default '#FFFFFF',

  `ordering` int(11) NOT NULL default '0',

  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',

  `last_update` datetime NOT NULL default '0000-00-00 00:00:00',

  `published` tinyint(1) unsigned NOT NULL default '1',

  `checked_out` tinyint(1) unsigned NOT NULL default '0',

  `checked_out_time` date NOT NULL default '0000-00-00',

  PRIMARY KEY  (`id`)

) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;



