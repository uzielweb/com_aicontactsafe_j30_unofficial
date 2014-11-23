<?php
/**
 * @version     $Id$ 2.0.0 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableAiContactSafe_contactinformations extends JTable {
	var $id = null;
	var $info_key = null;
	var $info_value = null;

	function __construct(&$db) {
		parent::__construct( '#__aicontactsafe_contactinformations', 'id', $db );
	}
}
