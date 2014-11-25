<?php
/**
 * @version     $Id$ 2.0.13 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.13
 * - added the possibility to duplicate one or more fields
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the default aiContactSafe controller class
class AiContactSafeControllerFields extends AiContactSafeController {

	// function to duplicate one or more fields
	function copyField() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->copyField();
		$link = $model->getReturnLink();
		$msg = JText::_('COM_AICONTACTSAFE_FIELD_COPIED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

}
