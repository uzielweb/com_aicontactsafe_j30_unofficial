<?php
/**
 * @version     $Id$ 2.0.13 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.13
 * - added link to download any attachment in the "Attachmenets" window
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the default aiContactSafe controller class
class AiContactSafeControllerAttachments extends AiContactSafeController {

	// function to delete one or more attached files
	function delete() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->delete();
		$link = $model->getReturnLink();
		$msg = JText::_('COM_AICONTACTSAFE_ATTACHMENTS_DELETED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to controll the task 'download'
	function download() {
		// get the current model and start the download
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->downloadFile();
	}

}
