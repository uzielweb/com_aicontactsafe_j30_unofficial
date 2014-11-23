<?php
/**
 * @version     $Id$ 2.0.0 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableAiContactSafe_fields extends JTable {
	var $id = null;
	var $name = null;
	var $field_label = null;
	var $label_parameters = null;
	var $field_label_message = null;
	var $label_message_parameters = null;
	var $label_after_field = null;
	var $field_type = null;
	var $field_parameters = null;
	var $field_values = null;
	var $field_limit = null;
	var $default_value = null;
	var $auto_fill = null;
	var $field_sufix = null;
	var $field_prefix = null;
	var $ordering = null;
	var $field_required = null;
	var $field_in_message = null;
	var $send_message = null;
	var $date_added = null;
	var $last_update = null;
	var $published = null;
	var $checked_out = null;
	var $checked_out_time = null;

	function __construct(&$db) {
		parent::__construct( '#__aicontactsafe_fields', 'id', $db );
	}
}
