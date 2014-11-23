<?php
/**
 * @version     $Id$ 2.0.0 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableAiContactSafe_config extends JTable {
	var $id = null;
	var $config_key = null;
	var $config_value = null;

	function __construct(&$db) {
		parent::__construct( '#__aicontactsafe_config', 'id', $db );
	}
}
