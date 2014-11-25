<?php
/**
 * @version     $Id$ 2.0.14
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.10.b
 * - added Bulgarian translation
 * added/fixed in version 2.0.12
 * - removed the field to identify the user, the user is indetified by the log-in process
 * - removed the comments on the fields
 * added/fixed in version 2.0.12.b
 * - set the default access to the messages in front-end to "super administrator"
 * added/fixed in version 2.0.13
 * - modified the update procedure so it will not generate errors when debug mode is activated
 * - added SqueezeBox for aiContactSafe feed-back
 * - added highlighting for fields with errors
 * - added the possibility to keep the session alive while the form is displayed
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// function called when the component is installed
function com_install() {
	$_version = '3 - Unofficial - by Ponto Mega';

	// make sure the sys language file is loaded
	$lang = JFactory::getLanguage();
$lang->load('com_aicontactsafe.sys', JPATH_ROOT . '/administrator');

	// import joomla clases to manage file system
	jimport('joomla.filesystem.file');

	// initialize the database
	$db = JFactory::getDBO();

// create tables ( in case the sql is not executed with joomla 1.7 )

	$query = 'CREATE TABLE IF NOT EXISTS `#__aicontactsafe_config` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`config_key` varchar(50) NOT NULL default \'\',
				`config_value` text NOT NULL,
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
	$db->setQuery( $query );
	$db->query();

	$query = 'CREATE TABLE IF NOT EXISTS `#__aicontactsafe_fields` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`name` varchar(50) NOT NULL default \'\',
				`field_label` text NOT NULL,
				`label_parameters` text NOT NULL,
				`field_label_message` text NOT NULL,
				`label_message_parameters` text NOT NULL,
				`label_after_field` tinyint(1) unsigned NOT NULL default \'0\',
				`field_type` varchar(2) NOT NULL default \'TX\',
				`field_parameters` text NOT NULL,
				`field_values` text NOT NULL,
				`field_limit` int(11) NOT NULL default \'0\',
				`default_value` varchar(150) NOT NULL default \'\',
				`auto_fill` varchar(10) NOT NULL default \'\',
				`field_sufix` text NOT NULL,
				`field_prefix` text NOT NULL,
				`ordering` int(11) NOT NULL default \'0\',
				`field_required` tinyint(1) unsigned NOT NULL default \'0\',
				`field_in_message` tinyint(1) unsigned NOT NULL default \'1\',
				`send_message` tinyint(1) unsigned NOT NULL default \'0\',
				`date_added` datetime NOT NULL default \'0000-00-00 00:00:00\',
				`last_update` datetime NOT NULL default \'0000-00-00 00:00:00\',
				`published` tinyint(1) unsigned NOT NULL default \'1\',
				`checked_out` tinyint(1) unsigned NOT NULL default \'0\',
				`checked_out_time` date NOT NULL default \'0000-00-00\',
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
	$db->setQuery( $query );
	$db->query();

	$query = 'CREATE TABLE IF NOT EXISTS `#__aicontactsafe_contactinformations` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`profile_id` int(11) unsigned NOT NULL,
				`info_key` varchar(50) NOT NULL default \'\',
				`info_label` varchar(250) NOT NULL default \'\',
				`info_value` text NOT NULL,
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
	$db->setQuery( $query );
	$db->query();

	$query = 'CREATE TABLE IF NOT EXISTS `#__aicontactsafe_messages` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`name` varchar(50) NOT NULL default \'\',
				`email` varchar(100) NOT NULL default \'\',
				`subject` varchar(200) NOT NULL default \'\',
				`message` text NOT NULL default \'\',
				`send_to_sender` tinyint(1) unsigned NOT NULL default \'0\',
				`sender_ip` varchar(20) NOT NULL default \'\',
				`profile_id` int(11) unsigned NOT NULL,
				`status_id` int(11) unsigned NOT NULL,
				`manual_status` tinyint(1) unsigned NOT NULL default \'0\',
				`email_destination` text NOT NULL default \'\',
				`email_reply` varchar(100) NOT NULL default \'\',
				`subject_reply` text NOT NULL default \'\',
				`message_reply` text NOT NULL default \'\',
				`user_id` int(11) NOT NULL default \'0\',
				`date_added` datetime NOT NULL default \'0000-00-00 00:00:00\',
				`last_update` datetime NOT NULL default \'0000-00-00 00:00:00\',
				`published` tinyint(1) unsigned NOT NULL default \'1\',
				`checked_out` tinyint(1) unsigned NOT NULL default \'0\',
				`checked_out_time` date NOT NULL default \'0000-00-00\',
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
	$db->setQuery( $query );
	$db->query();

	$query = 'CREATE TABLE IF NOT EXISTS `#__aicontactsafe_profiles` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`name` varchar(50) NOT NULL default \'\',
				`use_ajax` tinyint(1) unsigned NOT NULL default \'0\',
				`use_message_css` tinyint(1) unsigned NOT NULL default \'0\',
				`contact_form_width` int(11) NOT NULL default \'0\',
				`bottom_row_space` int(11) NOT NULL default \'0\',
				`align_buttons` tinyint(1) unsigned NOT NULL default \'1\',
				`contact_info_width` int(11) NOT NULL default \'0\',
				`use_captcha` tinyint(1) unsigned NOT NULL default \'1\',
				`captcha_type` tinyint(1) unsigned NOT NULL default \'0\',
				`align_captcha` tinyint(1) unsigned NOT NULL default \'1\',
				`email_address` varchar(100) NOT NULL default \'\',
				`always_send_to_email_address` tinyint(1) unsigned NOT NULL default \'1\',
				`subject_prefix` varchar(100) NOT NULL default \'\',
				`email_mode` tinyint(1) unsigned NOT NULL default \'1\',
				`record_message` tinyint(1) unsigned NOT NULL default \'1\',
				`record_fields` tinyint(1) unsigned NOT NULL default \'0\',
				`custom_date_format` varchar(30) NOT NULL default \'%d %B %Y\',
				`custom_date_years_back` int(11) NOT NULL default \'70\',
				`custom_date_years_forward` int(11) NOT NULL default \'0\',
				`required_field_mark` text NOT NULL,
				`display_format` int(11) NOT NULL default \'2\',
				`plg_contact_info` tinyint(1) unsigned NOT NULL default \'0\',
				`use_random_letters` tinyint(1) unsigned NOT NULL default \'0\',
				`min_word_length` tinyint(2) unsigned NOT NULL default \'5\',
				`max_word_length` tinyint(2) unsigned NOT NULL default \'8\',
				`set_default` tinyint(1) unsigned NOT NULL default \'0\',
				`active_fields` text NOT NULL,
				`captcha_width` smallint(4) NOT NULL default \'400\',
				`captcha_height` smallint(4) NOT NULL default \'55\',
				`captcha_bgcolor` varchar(10) NOT NULL default \'#FFFFFF\',
				`captcha_backgroundTransparent` tinyint(1) unsigned NOT NULL default \'1\',
				`captcha_colors` text NOT NULL,
				`name_field_id` int(11) unsigned NOT NULL,
				`email_field_id` int(11) unsigned NOT NULL,
				`subject_field_id` int(11) unsigned NOT NULL,
				`send_to_sender_field_id` int(11) unsigned NOT NULL,
				`redirect_on_success` text NOT NULL,
				`fields_order` text NOT NULL,
				`use_mail_template` tinyint(1) unsigned NOT NULL default \'0\',
				`default_status_id` int(11) unsigned NOT NULL,
				`read_status_id` int(11) unsigned NOT NULL,
				`reply_status_id` int(11) unsigned NOT NULL,
				`date_added` datetime NOT NULL default \'0000-00-00 00:00:00\',
				`last_update` datetime NOT NULL default \'0000-00-00 00:00:00\',
				`published` tinyint(1) unsigned NOT NULL default \'1\',
				`checked_out` tinyint(1) unsigned NOT NULL default \'0\',
				`checked_out_time` date NOT NULL default \'0000-00-00\',
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
	$db->setQuery( $query );
	$db->query();

	$query = 'CREATE TABLE IF NOT EXISTS `#__aicontactsafe_messagefiles` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`message_id` int(11) unsigned NOT NULL,
				`name` text NOT NULL,
				`r_id` int(21) unsigned NOT NULL,
				`date_added` datetime NOT NULL default \'0000-00-00 00:00:00\',
				`last_update` datetime NOT NULL default \'0000-00-00 00:00:00\',
				`published` tinyint(1) unsigned NOT NULL default \'1\',
				`checked_out` tinyint(1) unsigned NOT NULL default \'0\',
				`checked_out_time` date NOT NULL default \'0000-00-00\',
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
	$db->setQuery( $query );
	$db->query();

	$query = 'CREATE TABLE IF NOT EXISTS `#__aicontactsafe_fieldvalues` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`field_id` int(11) unsigned NOT NULL,
				`message_id` int(11) unsigned NOT NULL,
				`field_value` text NOT NULL,
				`date_added` datetime NOT NULL default \'0000-00-00 00:00:00\',
				`last_update` datetime NOT NULL default \'0000-00-00 00:00:00\',
				`published` tinyint(1) unsigned NOT NULL default \'1\',
				`checked_out` tinyint(1) unsigned NOT NULL default \'0\',
				`checked_out_time` date NOT NULL default \'0000-00-00\',
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
	$db->setQuery( $query );
	$db->query();

	$query = 'CREATE TABLE IF NOT EXISTS `#__aicontactsafe_statuses` (
				`id` int(11) unsigned NOT NULL auto_increment,
				`name` varchar(20) NOT NULL default \'\',
				`color` varchar(10) NOT NULL default \'#FFFFFF\',
				`ordering` int(11) NOT NULL default \'0\',
				`date_added` datetime NOT NULL default \'0000-00-00 00:00:00\',
				`last_update` datetime NOT NULL default \'0000-00-00 00:00:00\',
				`published` tinyint(1) unsigned NOT NULL default \'1\',
				`checked_out` tinyint(1) unsigned NOT NULL default \'0\',
				`checked_out_time` date NOT NULL default \'0000-00-00\',
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
	$db->setQuery( $query );
	$db->query();

// 2.0.1 modifications in database 
	// add the field profile_id into contactinformations table

	$fields = $db->getTableFields('#__aicontactsafe_contactinformations');
	$aicontactsafe_contactinformations = $fields['#__aicontactsafe_contactinformations'];

	$fields = $db->getTableFields('#__aicontactsafe_fields');
	$aicontactsafe_fields = $fields['#__aicontactsafe_fields'];

	$fields = $db->getTableFields('#__aicontactsafe_profiles');
	$aicontactsafe_profiles = $fields['#__aicontactsafe_profiles'];

	$fields = $db->getTableFields('#__aicontactsafe_messages');
	$aicontactsafe_messages = $fields['#__aicontactsafe_messages'];

	$fields = $db->getTableFields('#__aicontactsafe_messagefiles');
	$aicontactsafe_messagefiles = $fields['#__aicontactsafe_messagefiles'];

	if(!isset($aicontactsafe_contactinformations['profile_id'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_contactinformations` ADD `profile_id` INT( 11 ) NOT NULL AFTER `id`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_fields['label_parameters'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_fields` ADD `label_parameters` TEXT NOT NULL AFTER `field_label`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_fields['field_label_message'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_fields` ADD `field_label_message` varchar(150) NOT NULL default \'\';';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_fields['label_message_parameters'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_fields` ADD `label_message_parameters` TEXT NOT NULL;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_fields['field_in_message'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_fields` ADD `field_in_message` tinyint(1) unsigned NOT NULL default \'1\';';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_fields['default_value'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_fields` ADD `default_value` varchar(150) NOT NULL default \'\';';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['captcha_backgroundTransparent'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `captcha_backgroundTransparent` tinyint(1) unsigned NOT NULL default \'1\' AFTER `captcha_bgcolor`;';
		$db->setQuery( $query );
		$db->query();
	}

	// remove spaces in the field name
	$query = 'UPDATE `#__aicontactsafe_fields` SET `name` = replace(name,\'\',\'_\');';
	$db->setQuery( $query );
	$db->query();

// 2.0.2 modifications in database 
	if(!isset($aicontactsafe_fields['field_limit'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_fields` ADD `field_limit` int(11) NOT NULL default \'0\' AFTER `field_values`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_fields['auto_fill'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_fields` ADD `auto_fill` varchar(10) NOT NULL default \'\' AFTER `default_value`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_fields['send_message'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_fields` ADD `send_message` tinyint(1) unsigned NOT NULL default \'0\' AFTER `field_in_message`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['name_field_id'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `name_field_id` int(11) unsigned NOT NULL AFTER `captcha_colors`;';
		$db->setQuery( $query );
		$db->query();
	}
	$query = 'ALTER TABLE `#__aicontactsafe_fields` CHANGE `field_label` `field_label` text NOT NULL;';
	$db->setQuery( $query );
	$db->query();
	$query = 'ALTER TABLE `#__aicontactsafe_fields` CHANGE `field_label_message` `field_label_message` text NOT NULL;';
	$db->setQuery( $query );
	$db->query();
	if(!isset($aicontactsafe_profiles['email_field_id'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `email_field_id` int(11) unsigned NOT NULL AFTER `name_field_id`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['subject_field_id'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `subject_field_id` int(11) unsigned NOT NULL AFTER `email_field_id`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['send_to_sender_field_id'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `send_to_sender_field_id` int(11) unsigned NOT NULL AFTER `subject_field_id`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['use_ajax'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `use_ajax` tinyint(1) unsigned NOT NULL default \'0\' AFTER `name`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['redirect_on_success'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `redirect_on_success` text NOT NULL AFTER `send_to_sender_field_id`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(isset($aicontactsafe_profiles['use_profile_css'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` DROP `use_profile_css`';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['contact_form_width'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `contact_form_width` int(11) NOT NULL default \'0\' AFTER `use_message_css`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['contact_info_width'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `contact_info_width` int(11) NOT NULL default \'0\' AFTER `contact_form_width`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['email_mode'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `email_mode` tinyint(1) unsigned NOT NULL default \'1\' AFTER `subject_prefix`;';
		$db->setQuery( $query );
		$db->query();
	}
	$query = 'SELECT id FROM `#__aicontactsafe_fields` WHERE `name` = \'name\' and `id` = 1';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 1) {
		$query = 'UPDATE `#__aicontactsafe_fields` SET `name` = \'aics_name\' WHERE `name` = \'name\' and `id` = 1';
		$db->setQuery( $query );
		$db->query();
		$query = 'UPDATE `#__aicontactsafe_profiles` SET `name_field_id` = 1';
		$db->setQuery( $query );
		$db->query();
	}
	$query = 'SELECT id FROM `#__aicontactsafe_fields` WHERE `name` = \'email\' and `id` = 2';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 2) {
		$query = 'UPDATE `#__aicontactsafe_fields` SET `name` = \'aics_email\', `field_type` = \'EM\' WHERE `name` = \'email\' and `id` = 2';
		$db->setQuery( $query );
		$db->query();
		$query = 'UPDATE `#__aicontactsafe_profiles` SET `email_field_id` = 2';
		$db->setQuery( $query );
		$db->query();
	}
	$query = 'SELECT id FROM `#__aicontactsafe_fields` WHERE `name` = \'phone\' and `id` = 3';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 3) {
		$query = 'UPDATE `#__aicontactsafe_fields` SET `name` = \'aics_phone\' WHERE `name` = \'phone\' and `id` = 3';
		$db->setQuery( $query );
		$db->query();
	}
	$query = 'SELECT id FROM `#__aicontactsafe_fields` WHERE `name` = \'subject\' and `id` = 4';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 4) {
		$query = 'UPDATE `#__aicontactsafe_fields` SET `name` = \'aics_subject\' WHERE `name` = \'subject\' and `id` = 4';
		$db->setQuery( $query );
		$db->query();
		$query = 'UPDATE `#__aicontactsafe_profiles` SET `subject_field_id` = 4';
		$db->setQuery( $query );
		$db->query();
	}
	$query = 'SELECT id FROM `#__aicontactsafe_fields` WHERE `name` = \'message\' and `id` = 5';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 5) {
		$query = 'UPDATE `#__aicontactsafe_fields` SET `name` = \'aics_message\' WHERE `name` = \'message\' and `id` = 5';
		$db->setQuery( $query );
		$db->query();
	}
	$query = 'SELECT id FROM `#__aicontactsafe_fields` WHERE `name` = \'send_to_sender\' and `id` = 6';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 6) {
		$query = 'UPDATE `#__aicontactsafe_fields` SET `name` = \'aics_send_to_sender\' WHERE `name` = \'send_to_sender\' and `id` = 6';
		$db->setQuery( $query );
		$db->query();
		$query = 'UPDATE `#__aicontactsafe_profiles` SET `send_to_sender_field_id` = 6';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_messages['profile_id'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_messages` ADD `profile_id` int(11) unsigned NOT NULL AFTER `sender_ip`;';
		$db->setQuery( $query );
		$db->query();
	}
	$query = 'SELECT id FROM `#__aicontactsafe_profiles`';
	$db->setQuery( $query );
	$ids = $db->loadObjectList();
	if (count($ids) == 1) {
		$query = 'UPDATE `#__aicontactsafe_messages` SET `profile_id` = '.$ids[0]->id.' WHERE `profile_id` = 0';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['plg_contact_info'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `plg_contact_info` tinyint(1) unsigned NOT NULL default \'0\' AFTER `display_format`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['use_random_letters'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `use_random_letters` tinyint(1) unsigned NOT NULL default \'0\' AFTER `plg_contact_info`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['min_word_length'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `min_word_length` tinyint(2) unsigned NOT NULL default \'5\' AFTER `use_random_letters`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['max_word_length'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `max_word_length` tinyint(2) unsigned NOT NULL default \'8\' AFTER `min_word_length`;';
		$db->setQuery( $query );
		$db->query();
	}

// 2.0.3 modifications in database 
	if(!isset($aicontactsafe_profiles['fields_order'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `fields_order` text NOT NULL AFTER `redirect_on_success`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['use_mail_template'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `use_mail_template` tinyint(1) unsigned NOT NULL default \'0\' AFTER `fields_order`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_messagefiles['r_id'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_messagefiles` ADD `r_id` int(21) unsigned NOT NULL AFTER `name`;';
		$db->setQuery( $query );
		$db->query();
	}

// 2.0.5 modifications in database 
	if(!isset($aicontactsafe_profiles['captcha_type'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `captcha_type` tinyint(1) unsigned NOT NULL default \'0\' AFTER `use_captcha`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['record_fields'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `record_fields` tinyint(1) unsigned NOT NULL default \'0\' AFTER `record_message`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_fields['field_sufix'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_fields` ADD `field_sufix` text NOT NULL AFTER `auto_fill`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_fields['field_prefix'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_fields` ADD `field_prefix` text NOT NULL AFTER `field_sufix`;';
		$db->setQuery( $query );
		$db->query();
	}
/*
	if(!isset($aicontactsafe_messages['email_replay'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_messages` ADD `email_replay` varchar(100) NOT NULL default \'\' AFTER `profile_id`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_messages['subject_replay'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_messages` ADD `subject_replay` text NOT NULL default \'\' AFTER `email_replay`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_messages['message_replay'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_messages` ADD `message_replay` text NOT NULL default \'\' AFTER `subject_replay`;';
		$db->setQuery( $query );
		$db->query();
	}
*/

// 2.0.6 modifications in database 
	if(isset($aicontactsafe_messages['email_replay']) && !isset($aicontactsafe_messages['email_reply'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_messages` CHANGE `email_replay` `email_reply` varchar(100) NOT NULL default \'\';';
		$db->setQuery( $query );
		$db->query();
	} else {
		if(!isset($aicontactsafe_messages['email_reply'])) {
			$query = 'ALTER TABLE `#__aicontactsafe_messages` ADD `email_reply` varchar(100) NOT NULL default \'\' AFTER `profile_id`;';
			$db->setQuery( $query );
			$db->query();
		}
	}
	if(isset($aicontactsafe_messages['subject_replay']) && !isset($aicontactsafe_messages['subject_reply'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_messages` CHANGE `subject_replay` `subject_reply` text NOT NULL default \'\';';
		$db->setQuery( $query );
		$db->query();
	} else {
		if(!isset($aicontactsafe_messages['subject_reply'])) {
			$query = 'ALTER TABLE `#__aicontactsafe_messages` ADD `subject_reply` text NOT NULL default \'\' AFTER `email_reply`;';
			$db->setQuery( $query );
			$db->query();
		}
	}
	if(isset($aicontactsafe_messages['message_replay']) && !isset($aicontactsafe_messages['message_reply'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_messages` CHANGE `message_replay` `message_reply` text NOT NULL default \'\';';
		$db->setQuery( $query );
		$db->query();
	} else {
		if(!isset($aicontactsafe_messages['message_reply'])) {
			$query = 'ALTER TABLE `#__aicontactsafe_messages` ADD `message_reply` text NOT NULL default \'\' AFTER `subject_reply`;';
			$db->setQuery( $query );
			$db->query();
		}
	}

// 2.0.7 modifications in database 
	if(!isset($aicontactsafe_messages['status_id'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_messages` ADD `status_id` int(11) unsigned NOT NULL AFTER `profile_id`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_messages['manual_status'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_messages` ADD `manual_status` tinyint(1) unsigned NOT NULL default \'0\' AFTER `status_id`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_messages['email_destination'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_messages` ADD `email_destination` text NOT NULL default \'\' AFTER `status_id`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['bottom_row_space'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `bottom_row_space` int(11) NOT NULL default \'0\' AFTER `contact_form_width`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['default_status_id'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `default_status_id` int(11) unsigned NOT NULL AFTER `use_mail_template`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['read_status_id'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `read_status_id` int(11) unsigned NOT NULL AFTER `default_status_id`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['reply_status_id'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `reply_status_id` int(11) unsigned NOT NULL AFTER `read_status_id`;';
		$db->setQuery( $query );
		$db->query();
	}
	if(!isset($aicontactsafe_profiles['align_buttons'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `align_buttons` tinyint(1) unsigned NOT NULL default \'1\' AFTER `bottom_row_space`;';
		$db->setQuery( $query );
		$db->query();
	}
	$query = 'ALTER TABLE `#__aicontactsafe_profiles` CHANGE `send_to_sender_field_id` `send_to_sender_field_id` INT( 11 ) NOT NULL;';
	$db->setQuery( $query );
	$db->query();

// 2.0.9 modifications in database 
	if(!isset($aicontactsafe_profiles['align_captcha'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_profiles` ADD `align_captcha` tinyint(1) unsigned NOT NULL default \'1\' AFTER `captcha_type`;';
		$db->setQuery( $query );
		$db->query();
	}

// 2.0.10 modifications in database 
	if(!isset($aicontactsafe_messages['user_id'])) {
		$query = 'ALTER TABLE `#__aicontactsafe_messages` ADD `user_id` int(11) NOT NULL default \'0\' AFTER `message_reply`;';
		$db->setQuery( $query );
		$db->query();
	}

// import joomla clases to manage file system
	jimport('joomla.filesystem.folder');
	jimport('joomla.filesystem.file');

// create the folder structure in media folder
	$att_folder = JPATH_ROOT.'/'.'media'.'/'.'aicontactsafe'.'/'.'attachments';
	if (!JFolder::exists($att_folder)) {
		JFolder::create($att_folder);
	}
	$src = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'index.html');
	$dest = JPath::clean($att_folder.'/'.'index.html');
	if (!JFile::exists($dest)) {
		JFile::copy($src, $dest);
	}
	$src = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'htaccess'.'/'.'.htaccess');
	$dest = JPath::clean($att_folder.'/'.'.htaccess');
	if (!JFile::exists($dest)) {
		JFile::copy($src, $dest);
	}

	$css_folder = JPATH_ROOT.'/'.'media'.'/'.'aicontactsafe'.'/'.'cssprofiles';
	if (!JFolder::exists($css_folder)) {
		JFolder::create($css_folder);
	}
	$src = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'index.html');
	$dest = JPath::clean($css_folder.'/'.'index.html');
	if (!JFile::exists($dest)) {
		JFile::copy($src, $dest);
	}
	$email_folder = JPATH_ROOT.'/'.'media'.'/'.'aicontactsafe'.'/'.'mailtemplates';
	if (!JFolder::exists($email_folder)) {
		JFolder::create($email_folder);
	}
	$src = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'index.html');
	$dest = JPath::clean($email_folder.'/'.'index.html');
	if (!JFile::exists($dest)) {
		JFile::copy($src, $dest);
	}

	// use_css_backend
	$key = 'use_css_backend';
	$value = '1';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// use_SqueezeBox
	$key = 'use_SqueezeBox';
	$value = '0';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// highlight_errors
	$key = 'highlight_errors';
	$value = '1';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// keep_session_alive
	$key = 'keep_session_alive';
	$value = '0';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// activate_help
	$key = 'activate_help';
	$value = '1';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// date_format
	$key = 'date_format';
	if(version_compare(JVERSION, '1.6.0', 'ge')) {
		$value = 'l, d F Y H:i';
	} else {
		$value = '%d %B %Y %H:%M';
	}
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// default_status_filter
	$key = 'default_status_filter';
	$value = '0';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// editbox_cols
	$key = 'editbox_cols';
	$value = '40';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// editbox_rows
	$key = 'editbox_rows';
	$value = '10';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// default_name
	$key = 'default_name';
	$value = '';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// default_email
	$key = 'default_email';
	$value = '';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// default_subject
	$key = 'default_subject';
	$value = '';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// activate_spam_control
	$key = 'activate_spam_control';
	$value = '0';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// block_words
	$key = 'block_words';
	$value = 'url=';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// record_blocked_messages
	$key = 'record_blocked_messages';
	$value = '1';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// activate_ip_ban
	$key = 'activate_ip_ban';
	$value = '0';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// ban_ips
	$key = 'ban_ips';
	$value = '';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// redirect_ips
	$key = 'redirect_ips';
	$value = '';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// ban_ips_blocked_words
	$key = 'ban_ips_blocked_words';
	$value = '0';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// maximum_messages_ban_ip
	$key = 'maximum_messages_ban_ip';
	$value = '0';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// maximum_minutes_ban_ip
	$key = 'maximum_minutes_ban_ip';
	$value = '0';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// email_ban_ip
	$key = 'email_ban_ip';
	$value = '';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// set_sender_joomla
	$key = 'set_sender_joomla';
	$value = '0';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// upload_attachments
	$key = 'upload_attachments';
	$value = 'media'.'/'.'aicontactsafe'.'/'.'attachments';
	$value = str_replace('\\','&#92;',$value);
	$query = 'SELECT config_value FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$config_value = $db->loadResult();
	if (strlen(trim($config_value)) == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	} else if (trim($config_value) == 'components&#92;com_aicontactsafe&#92;attachments' || trim($config_value) == 'components'.'/'.'com_aicontactsafe'.'/'.'attachments') {
		$query = 'UPDATE `#__aicontactsafe_config` SET `config_value` =  \'' . $value . '\' WHERE config_key =  \'' . $key . '\'';
		$db->setQuery( $query );
		$db->query();
	}
	// maximum_size
	$key = 'maximum_size';
	$value = '5000000';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// attachments_types
	$key = 'attachments_types';
	$value = 'rar,zip,doc,xls,txt,gif,jpg,png,bmp';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// attach_to_email
	$key = 'attach_to_email';
	$value = '1';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// delete_after_sent
	$key = 'delete_after_sent';
	$value = '0';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// gid_messages
	$key = 'gid_messages';
	if(version_compare(JVERSION, '1.6.0', 'ge')) {
		$value = '8';
	} else {
		$value = '25';
	}
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// users_all_messages
	$key = 'users_all_messages';
	$value = '0';
	$query = 'SELECT id FROM `#__aicontactsafe_config` WHERE `config_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_config` (`id`, `config_key`, `config_value`) VALUES ( null, \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}

	// add default profile
	$query = 'SELECT id FROM `#__aicontactsafe_profiles` WHERE `set_default` = 1';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_profiles` (`id`, `name`, `use_message_css`, `contact_form_width`, `contact_info_width`, `use_captcha`, `email_address`, `always_send_to_email_address`, `subject_prefix`, `record_message`, `custom_date_format`, `custom_date_years_back`, `custom_date_years_forward`, `required_field_mark`, `display_format`, `set_default`, `active_fields`, `captcha_width`, `captcha_height`, `captcha_bgcolor`, `captcha_backgroundTransparent`, `captcha_colors`, `name_field_id`, `email_field_id`, `subject_field_id`, `send_to_sender_field_id`, `date_added`, `last_update`, `published`, `checked_out`, `checked_out_time`) VALUES (1, \'Default form\', 1, 0, 0, 1, \'\', 1, \'\', 1, \'%d %B %Y\', 60, 0, \'( ! )\', 2, 1, \'0\', 300, 55, \'#FFFFFF\', 1, \'#FF0000;#00FF00;#0000FF\', 1, 2, 4, 6, \'2009-01-01 00:00:00\', \'2009-01-01 00:00:00\', 1, 0, \'0000-00-00\');';
		$db->setQuery( $query );
		$db->query();

		$query = 'INSERT INTO `#__aicontactsafe_profiles` (`id`, `name`, `use_message_css`, `contact_form_width`, `contact_info_width`, `use_captcha`, `email_address`, `always_send_to_email_address`, `subject_prefix`, `record_message`, `custom_date_format`, `custom_date_years_back`, `custom_date_years_forward`, `required_field_mark`, `display_format`, `set_default`, `active_fields`, `captcha_width`, `captcha_height`, `captcha_bgcolor`, `captcha_backgroundTransparent`, `captcha_colors`, `name_field_id`, `email_field_id`, `subject_field_id`, `send_to_sender_field_id`, `date_added`, `last_update`, `published`, `checked_out`, `checked_out_time`) VALUES (2, \'Module form\', 1, 0, 0, 1, \'\', 1, \'\', 1, \'%d %B %Y\', 60, 0, \'( ! )\', 1, 0, \'0\', 180, 55, \'#FFFFFF\', 1, \'#FF0000;#00FF00;#0000FF\', 1, 2, 4, 6, \'2009-01-01 00:00:00\', \'2009-01-01 00:00:00\', 1, 0, \'0000-00-00\');';
		$db->setQuery( $query );
		$db->query();
	}

	// check all profiles for profile's CSS and mail templates
	$query = 'SELECT id FROM `#__aicontactsafe_profiles`';
	$db->setQuery( $query );
	$profiles = $db->loadObjectList();
	foreach($profiles as $profile) {
		// add the profile's CSS if not already there
		$src_file = JPath::clean(JPATH_ROOT.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'views'.'/'.'message'.'/'.'tmpl'.'/'.'profile_align_margin.css');
		$dst_file = JPath::clean(JPATH_ROOT.'/'.'media'.'/'.'aicontactsafe'.'/'.'cssprofiles'.'/'.'profile_css_'.$profile->id.'.css');
		if (!is_file($dst_file)) {
			$profile_css_code = JFile::read($src_file);
			$profile_css_code = str_replace('aiContactSafe_mainbody_1','aiContactSafe_mainbody_'.$profile->id,$profile_css_code);
			JFile::write($dst_file, $profile_css_code);
		}
		// add the profile's mail template if not already there
		$src_file = JPath::clean(JPATH_ROOT.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'views'.'/'.'mail'.'/'.'tmpl'.'/'.'mail.php');
		$dst_file = JPath::clean(JPATH_ROOT.'/'.'media'.'/'.'aicontactsafe'.'/'.'mailtemplates'.'/'.'mail_'.$profile->id.'.php');
		if (!is_file($dst_file)) {
			JFile::copy($src_file, $dst_file);
		}
		
	}

	// insert default statuses if none were added
	$query = 'SELECT id FROM `#__aicontactsafe_statuses`;';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_statuses` (`id`, `name`, `ordering`, `color`, `date_added`, `last_update`, `published`, `checked_out`, `checked_out_time`) VALUES
							(1, \'New\', 1, \'#FF0000\', now(), now(), 1, 0, \'0000-00-00\'),
							(2, \'Read\', 2, \'#000000\', now(), now(), 1, 0, \'0000-00-00\'),
							(3, \'Replied\', 3, \'#009900\', now(), now(), 1, 0, \'0000-00-00\'),
							(4, \'Archived\', 4, \'#CCCCCC\', now(), now(), 1, 0, \'0000-00-00\');';
		$db->setQuery( $query );
		$db->query();
	}
	// set the default status on the profiles
	$query = 'UPDATE `#__aicontactsafe_profiles` SET `default_status_id` = 1 WHERE `default_status_id` = 0';
	$db->setQuery( $query );
	$db->query();
	$query = 'UPDATE `#__aicontactsafe_profiles` SET `read_status_id` = 2 WHERE `read_status_id` = 0';
	$db->setQuery( $query );
	$db->query();
	$query = 'UPDATE `#__aicontactsafe_profiles` SET `reply_status_id` = 3 WHERE `reply_status_id` = 0';
	$db->setQuery( $query );
	$db->query();


	// add fields if the table is empty
	$query = 'SELECT count(*) as fields FROM `#__aicontactsafe_fields`';
	$db->setQuery( $query );
	$count = $db->loadResult();
	if ($count == 0) {
		$query = 'INSERT INTO `#__aicontactsafe_fields` (`id`, `name`, `field_label`, `label_parameters`, `field_label_message`, `label_message_parameters`, `label_after_field`, `field_type`, `field_parameters`, `field_values`, `auto_fill`, `field_limit`, `ordering`, `field_required`, `field_in_message`, `send_message`, `date_added`, `last_update`, `published`, `checked_out`, `checked_out_time`) VALUES
											   (1, \'aics_name\', \'Name\', \'\', \'Name\', \'\', 0, \'TX\', "class=\'textbox\'", \'\', \'UN\', 0, 1, 1, 1, 0, now(), now(), 1, 0, \'0000-00-00\'),
											   (2, \'aics_email\', \'Email\', \'\', \'Email\', \'\', 0, \'EM\', "class=\'email\'", \'\', \'UE\', 0, 2, 1, 1, 0, now(), now(), 1, 0, \'0000-00-00\'),
											   (3, \'aics_phone\', \'Phone\', \'\', \'Phone\', \'\', 0, \'TX\', "class=\'textbox\'", \'\', \'\', 15, 3, 0, 1, 0, now(), now(), 1, 0, \'0000-00-00\'),
											   (4, \'aics_subject\', \'Subject\', \'\', \'Subject\', \'\', 0, \'TX\', "class=\'textbox\'", \'\', \'\', 0, 4, 1, 1, 0, now(), now(), 1, 0, \'0000-00-00\'),
											   (5, \'aics_message\', \'Message\', \'\', \'Message\', \'\', 0, \'ED\', "class=\'editbox\'", \'\', \'\', 500, 5, 1, 1, 0, now(), now(), 1, 0, \'0000-00-00\'),
											   (6, \'aics_send_to_sender\', \'Send a copy of this message to yourself\', \'\', \'Send a copy of this message to yourself\', \'\', 1, \'CK\', "class=\'checkbox\'", \'\', \'\', 0, 6, 0, 0, 0, now(), now(), 1, 0, \'0000-00-00\');';
		$db->setQuery( $query );
		$db->query();
	}

	// add contact_info
	// contact information
	$key = 'contact_info';
	$query = 'SELECT id FROM `#__aicontactsafe_contactinformations` WHERE `info_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$value = '<img style="margin-left: 10px; float: right;" alt="powered by joomla" src="images/powered_by.png" width="165" height="68" />';
		} else {
			$value = '<img style="margin-left: 10px; float: right;" alt="articles" src="images/stories/articles.jpg" width="128" height="96" />';
		}
		$value .= '<div style="width: 150px; float: left;">Algis Info Grup SRL<br />Str. Hărmanului Nr.63<br />bl.1A sc.A ap.8<br />Brașov, România<br />500232<br /><a target="_blank" href="http://www.algisinfo.com/">www.algisinfo.com</a></div>';

		$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES ( null, 1, \'' . $key . '\', \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();

		$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES ( null, 2, \'' . $key . '\', \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// meta_description
	$key = 'meta_description';
	$query = 'SELECT id FROM `#__aicontactsafe_contactinformations` WHERE `info_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$value = '';
		$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES ( null, 1, \'' . $key . '\', \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();

		$value = '';
		$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES ( null, 2, \'' . $key . '\', \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// meta_keywords
	$key = 'meta_keywords';
	$query = 'SELECT id FROM `#__aicontactsafe_contactinformations` WHERE `info_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$value = '';
		$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES ( null, 1, \'' . $key . '\', \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();

		$value = '';
		$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES ( null, 2, \'' . $key . '\', \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// meta_robots
	$key = 'meta_robots';
	$query = 'SELECT id FROM `#__aicontactsafe_contactinformations` WHERE `info_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$value = '';
		$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES ( null, 1, \'' . $key . '\', \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();

		$value = '';
		$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES ( null, 2, \'' . $key . '\', \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// thank_you_message
	$key = 'thank_you_message';
	$query = 'SELECT id FROM `#__aicontactsafe_contactinformations` WHERE `info_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$value = 'Email sent. Thank you for your message.';
		$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES ( null, 1, \'' . $key . '\', \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();

		$value = 'Email sent. Thank you for your message.';
		$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES ( null, 2, \'' . $key . '\', \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}
	// required_field_notification
	$key = 'required_field_notification';
	$query = 'SELECT id FROM `#__aicontactsafe_contactinformations` WHERE `info_key` = \'' . $key . '\'';
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id == 0) {
		$value = 'Fields marked with %mark% are required.';
		$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES ( null, 1, \'' . $key . '\', \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();

		$value = 'Fields marked with %mark% are required.';
		$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES ( null, 2, \'' . $key . '\', \'' . $key . '\', \'' . $value . '\')';
		$db->setQuery( $query );
		$db->query();
	}

	// copy joomfish contentelements
	$contentelements = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_joomfish'.'/'.'contentelements');
	if (is_dir($contentelements)) {
		$src = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'joomfish'.'/'.'aicontactsafe_contactinformations.xml');
		$dest = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_joomfish'.'/'.'contentelements'.'/'.'aicontactsafe_contactinformations.xml');
		JFile::copy($src, $dest);
		$src = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'joomfish'.'/'.'aicontactsafe_fields.xml');
		$dest = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_joomfish'.'/'.'contentelements'.'/'.'aicontactsafe_fields.xml');
		JFile::copy($src, $dest);
		$src = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'joomfish'.'/'.'aicontactsafe_profiles.xml');
		$dest = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_joomfish'.'/'.'contentelements'.'/'.'aicontactsafe_profiles.xml');
		JFile::copy($src, $dest);
	}

	// copy falang contentelements
	$contentelements = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_falang'.'/'.'contentelements');
	if (is_dir($contentelements)) {
		$src = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'falang'.'/'.'aicontactsafe_contactinformations.xml');
		$dest = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_falang'.'/'.'contentelements'.'/'.'aicontactsafe_contactinformations.xml');
		JFile::copy($src, $dest);
		$src = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'falang'.'/'.'aicontactsafe_fields.xml');
		$dest = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_falang'.'/'.'contentelements'.'/'.'aicontactsafe_fields.xml');
		JFile::copy($src, $dest);
		$src = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'falang'.'/'.'aicontactsafe_profiles.xml');
		$dest = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_falang'.'/'.'contentelements'.'/'.'aicontactsafe_profiles.xml');
		JFile::copy($src, $dest);
	}

	// copy artio plugin
	$artio = JPath::clean(JPATH_ROOT.'/'.'components'.'/'.'com_sef'.'/'.'sef_ext');
	if (is_dir($artio)) {
		$src = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'artio'.'/'.'com_aicontactsafe.php');
		$dest = JPath::clean(JPATH_ROOT.'/'.'components'.'/'.'com_sef'.'/'.'sef_ext'.'/'.'com_aicontactsafe.php');
		JFile::copy($src, $dest);
		$src = JPath::clean(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'artio'.'/'.'com_aicontactsafe.xml');
		$dest = JPath::clean(JPATH_ROOT.'/'.'components'.'/'.'com_sef'.'/'.'sef_ext'.'/'.'com_aicontactsafe.xml');
		JFile::copy($src, $dest);
	}

	if(version_compare(JVERSION, '1.6.0', 'ge')) {
		// restore menu links for 1.6 ( and newer ) version
	} else {
		// restore menu link
		$query = 'SELECT id FROM `#__components` WHERE `name` = \'aiContactSafe\' OR `name` = \'COM_AICONTACTSAFE\'';
		$db->setQuery( $query );
		$aiContactSafe_ids = $db->loadObjectList();
		if(count($aiContactSafe_ids) > 0) {
			foreach($aiContactSafe_ids as $aiContactSafe_id) {
				$query = 'UPDATE `#__menu` SET componentid = '.(int)$aiContactSafe_id->id.' WHERE substr(link,1,34) = \'index.php?option=com_aicontactsafe\' AND type = \'component\'';
			}
		}
		$db->setQuery( $query );
		$db->query();
	}

	// check if the plugins and module are updated
	jimport('joomla.installer.installer');
	$installer = new JInstaller();
	$extensions_updated = array();

	$xml_file = JPath::clean(JPATH_ROOT.'/'.'modules'.'/'.'mod_aicontactsafe'.'/'.'mod_aicontactsafe.xml');
	if (is_file($xml_file)) {
		@$installer->install(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'mod_aicontactsafe');
		$extensions_updated[] = 'mod_aicontactsafe';
	}
	if(version_compare(JVERSION, '1.6.0', 'ge')) {
		$xml_file = JPath::clean(JPATH_ROOT.'/'.'plugins'.'/'.'content'.'/'.'aicontactsafeform'.'/'.'aicontactsafeform.xml');
	} else {
		$xml_file = JPath::clean(JPATH_ROOT.'/'.'plugins'.'/'.'content'.'/'.'aicontactsafeform.xml');
	}
	if (is_file($xml_file)) {
		@$installer->install(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'plg_aicontactsafeform');
		$extensions_updated[] = 'plg_aicontactsafeform';
	}
	if(version_compare(JVERSION, '1.6.0', 'ge')) {
		$xml_file = JPath::clean(JPATH_ROOT.'/'.'plugins'.'/'.'content'.'/'.'aicontactsafelink'.'/'.'aicontactsafelink.xml');
	} else {
		$xml_file = JPath::clean(JPATH_ROOT.'/'.'plugins'.'/'.'content'.'/'.'aicontactsafelink.xml');
	}
	if (is_file($xml_file)) {
		@$installer->install(JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'plg_aicontactsafelink');
		$extensions_updated[] = 'plg_aicontactsafelink';
	}

?>
	<div class="header">Congratulations, aiContactSafe is now installed!</div>
	<br/>
	<br/>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="50%" valign="top">
				<img src="<?php echo JURI::root().'administrator/components/com_aicontactsafe/images/logo.gif' ;?>" border="0" /><br/>
				<br/>
				A contact form system developed by <a href="http://www.algis.ro/" target="_blank">Algis Info</a>, released under a <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU/GPL License</a>.<br/>
				<?php echo JText::_('COM_AICONTACTSAFE_VERSION'); ?>&nbsp;<?php echo $_version; ?><br/>
				<?php
					if (count($extensions_updated) > 0) {
						echo 'aiContactSafe extensions updated ('.implode(',',$extensions_updated).').<br/>';
					}
				?>
				Programmer : Alexandru Dobrin &lt;alex@algis.ro&gt;<br/><br/>
				<br/>
			</td>
			<td width="50%" valign="top">
				<h3>Credits</h3><br/>
				<br/>
				<b>CAPTCHA system</b> : <br/>
				- developed by Jose Rodriguez &lt;jose.rodriguez@exec.cl&gt;<br/>
				- implemented in joomla by Alexandru Dobrin &lt;alex@algis.ro&gt;<br/>
				<br>
				You can download the original version here : <a href="http://code.google.com/p/cool-php-captcha" target="_blank">http://code.google.com/p/cool-php-captcha</a>.<br/>
				<br/>
				<b>Icons pack</b> : <br/>
				- made by Freeiconsdownload from <a href="http://www.freeiconsweb.com" target="_blank">www.freeiconsweb.com</a><br/>
				- adapted and implemented in aiContactSafe by Alexandru Dobrin &lt;alex@algis.ro&gt;<br/>
				<br/>
				<b>SqueezeBox</b> : <br/>
				- made by Harald Kirschner from <a href="http://digitarald.de/project/squeezebox/" target="_blank">digitarald.de/project/squeezebox/</a><br/>
				- implemented in aiContactSafe by Alexandru Dobrin &lt;alex@algis.ro&gt;<br/>
				<br/>
				<b>Arabic translation</b> : <br/>- Dr. Ossama Abou Issa<br/>
				<br/>
				<b>Bulgarian translation</b> : <br/>- Eli Jeleva <a href="http://harrisonroyce.com" target="_blank">harrisonroyce.com</a><br/>
				<br/>
				<b>Czech translation</b> : <br/>- Martin Halík<br/>
				<br/>
				<b>Danish translation</b> : <br/>- Mads Andersen <a href="http://madsandersen.dk" target="_blank">madsandersen.dk</a><br/>
				<br/>
				<b>German translation</b> : <br/>- Pawel Koch <a href="http://www.le5.ch" target="_blank">www.le5.ch</a><br/>
				<br/>
				<b>Greek translation</b> : <br/>- Themistoklis Georgiadis <a href="http://www.globalinfoweb.com" target="_blank">www.globalinfoweb.com</a><br/>
				<br/>
				<b>English translation</b> : <br/>- Nic Irvine <a href="http://www.swanshops.com" target="_blank">www.swanshops.com</a><br/>
				<br/>
				<b>Persian translation</b> : <br/>- Mohammad Hasani Eghtedar<br/>
				<br/>
				<b>Spanish translation</b> : <br/>- Pablo Soto <a href="http://www.tecnoartestudio.com" target="_blank">www.tecnoartestudio.com</a><br/>
				<br/>
				<b>French translation</b> : <br/>- Mihàly Marti <a href="http://www.sarki.ch" target="_blank">www.sarki.ch</a><br/>
				<br/>
				<b>Hungarian translation</b> : <br/>- Balogh Zoltán <a href="http://birdcreation.com" target="_blank">birdcreation.com</a><br/>
				<br/>
				<b>Italian translation</b> : <br/>- Fabrizio Degni <a href="http://www.trioptimumcorporation.com" target="_blank">www.trioptimumcorporation.com</a><br/>
				<br/>
				<b>Lithuanian translation</b> : <br/>- Andrius Barkauskas <a href="http://www.barkauskas.lt" target="_blank">www.barkauskas.lt</a><br/>
				<br/>
				<b>Norvegian translation</b> : <br/>- Goran Aasen <a href="http://www.gaatec.com" target="_blank">www.gaatec.com</a><br/>
				<br/>
				<b>Dutch translation</b> : <br/>- Christof Vandewalle <a href="http://www.plus-it.be" target="_blank">www.plus-it.be</a><br/>
				<br/>
				<b>Polish translation</b> : <br/>- Stefan Wajda <a href="http://www.joomla.pl" target="_blank">www.joomla.pl</a><br/>
				<br/>
				<b>Brazilian Portuguese translation</b> : <br/>- Éder Almeida Costa<br/>
				<br/>
				<b>Portuguese translation</b> : <br/>- Rui Pedro <a href="http://www.elojasonline.com" target="_blank">www.elojasonline.com</a><br/>
				<br/>
				<b>Russian translation</b> : <br/>- Gruz <a href="http://ukrstyle.com" target="_blank">www.ukrstyle.com</a><br/>
				<br/>
				<b>Slovak translation</b> : <br/>- Peter Tanuska<br/>
				<br/>
				<b>Serbian (Cyrillic) translation</b> : <br/>- krca437<br/>
				<br/>
				<b>Swedish translation</b> : <br/>- Janne Sandgren <a href="http://www.hippijannessilverringar.se" target="_blank">www.hippijannessilverringar.se</a><br/>
				<br/>
				<b>Turkish translation</b> : <br/>- Kazım Çolpan <a href="http://www.tatlisubalikavi.net" target="_blank">www.tatlisubalikavi.net</a><br/>
				<br/>
				<b>Ukrainian translation</b> : <br/>- Gruz <a href="http://ukrstyle.com" target="_blank">www.ukrstyle.com</a><br/>
				<br/>
				<b>Tranditional Chinese translation</b> : <br/>- Jerry Chiu <a href="http://blog.cmsart.net/" target="_blank">blog.cmsart.net/</a><br/>
				<br/>
				<br/>
			</td>
		</tr>
	</table>
<?php
}
