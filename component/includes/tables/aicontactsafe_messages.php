<?php
/**
 * @version     $Id$ 2.0.0 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableAiContactSafe_messages extends JTable {
	var $id = null;
	var $name = null;
	var $email = null;
	var $subject = null;
	var $message = null;
	var $send_to_sender = null;
	var $sender_ip = null;
	var $profile_id = null;
	var $status_id = null;
	var $manual_status = null;
	var $email_destination = null;
	var $email_reply = null;
	var $subject_reply = null;
	var $message_reply = null;
	var $user_id = null;
	var $date_added = null;
	var $last_update = null;
	var $published = null;
	var $checked_out = null;
	var $checked_out_time = null;

	function __construct(&$db) {
		parent::__construct( '#__aicontactsafe_messages', 'id', $db );
	}
}
