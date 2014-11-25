<?php
/**
 * @version     $Id$ 2.0.1 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableAiContactSafe_profiles extends JTable {
	var $id = null;
	var $name = null;
	var $use_ajax = null;
	var $use_message_css = null;
	var $contact_form_width = null;
	var $bottom_row_space = null;
	var $align_buttons = null;
	var $contact_info_width = null;
	var $use_captcha = null;
	var $captcha_type = null;
	var $align_captcha = null;
	var $email_address = null;
	var $always_send_to_email_address = null;
	var $subject_prefix = null;
	var $email_mode = null;
	var $record_message = null;
	var $record_fields = null;
	var $custom_date_format = null;
	var $custom_date_years_back = null;
	var $custom_date_years_forward = null;
	var $required_field_mark = null;
	var $display_format = null;
	var $plg_contact_info = null;
	var $use_random_letters = null;
	var $min_word_length = null;
	var $max_word_length = null;
	var $set_default = null;
	var $active_fields = null;
	var $captcha_width = null;
	var $captcha_height = null;
	var $captcha_bgcolor = null;
	var $captcha_backgroundTransparent = null;
	var $captcha_colors = null;
	var $name_field_id = null;
	var $email_field_id = null;
	var $subject_field_id = null;
	var $send_to_sender_field_id = null;
	var $redirect_on_success = null;
	var $fields_order = null;
	var $use_mail_template = null;
	var $default_status_id = null;
	var $read_status_id = null;
	var $reply_status_id = null;
	var $date_added = null;
	var $last_update = null;
	var $published = null;
	var $checked_out = null;
	var $checked_out_time = null;

	function __construct(&$db) {
		parent::__construct( '#__aicontactsafe_profiles', 'id', $db );
	}
}
