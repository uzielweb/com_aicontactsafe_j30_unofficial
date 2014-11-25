<?php
/**
 * @version     $Id$ 2.0.0 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the about view class of aiContactSafe
class AiContactSafeViewAbout extends AiContactSafeViewDefault {

	// construct function, it will iniaize the class variables
	function __construct( $default = array() )	{
		$this->_help_id = 'about';

		parent::__construct( $default );
	}

	// deactivate the buttons on this page
	function setToolbarButtons() {
	}

	// function to display the default template
	function viewDefault() {
		// set the toolbar buttons
		$this->setToolbar();
		// call the css file
		$this->callCssFile();
		// display the view template
		parent::display();
	}

}
