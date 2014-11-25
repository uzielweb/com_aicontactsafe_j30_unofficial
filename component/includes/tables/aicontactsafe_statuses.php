<?php
/**
 * @version     $Id$ 2.0.7 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableAiContactSafe_statuses extends JTable {
	var $id = null;
	var $name = null;
	var $ordering = null;
	var $color = null;
	var $date_added = null;
	var $last_update = null;
	var $published = null;
	var $checked_out = null;
	var $checked_out_time = null;

	function __construct(&$db) {
		parent::__construct( '#__aicontactsafe_statuses', 'id', $db );
	}
}
