<?php
/**
 * @version     $Id$ 2.0.0 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the default aiContactSafe controller class
class AiContactSafeControllerCaptcha extends AiContactSafeController {

	// function to record the last task in a temporary variable
	function recordLastTask() {
		// dissable the function to record the last task for captcha
	}

	// generate a new captcha code ( IE6 only )
	function newCaptcha() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->newCaptcha();
	}


}
