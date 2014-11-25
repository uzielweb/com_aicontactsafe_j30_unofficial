<?php
/**
 * @version     $Id$ 2.0.13 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.13
 * - added the button to download the CSV file
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the default aiContactSafe controller class
class AiContactSafeControllerMessages extends AiContactSafeController {

	// get the layout to use based on sTask and task
	function getSTaskLayout($sTask = '') {
		switch(true) {
			// in case a message is viewed
			case $this->_task == 'view' :
				$layout = 'view_message';
				break;
			// in case a record is deleted set the delete_record layout
			case $this->_task == 'delete' :
				$layout = 'delete_record';
				break;
			// in case a record is deleted set the delete_record layout
			case $this->_task == 'ban_ip' :
				$layout = 'ban_ip';
				break;
			// in case a record is replyed set the reply layout
			case $this->_task == 'reply' :
				$layout = 'reply';
				break;
			// in case the records are exported set the export layout
			case $this->_task == 'export' :
				$layout = 'export';
				break;
			// in case the records are deleted set the delete_selected layout
			case $this->_task == 'delete_selected' :
				$layout = 'delete_selected';
				break;
			// or else use the default layout
			case $this->_task == 'display' :
			default :
				$layout = $sTask;
		}
		return $layout;
	}

	// function to controll the task 'ban_ip' - command to ban one or more IPs
	function ban_ip() {
		$this->display();
	}

	// function to get the confirmation message when the data is saved
	function getConfirmationBanIp() {
		return JText::_('COM_AICONTACTSAFE_IP_BANNED');
	}

	// function to controll the task 'confirmDelete' - action to delete one or more records from a table
	function confirmBanIp() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->banIP();
		if ($isOK) {
			$link = $model->getReturnLink();
			$msg = $this->getConfirmationBanIp();
			$msgType = 'message';
		} else {
			$link = $model->getLastLink();
			$msg = $this->_app->_session->get( 'errorMsg:' . $this->_sTask );
			$msgType = 'error';
		}
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to controll the task 'reply' - short reply to a message
	function reply() {
		$this->display();
	}

	// function to controll the task 'confirmDelete' - action to delete one or more records from a table
	function confirmReply() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->sendReply();
		if ($isOK) {
			$link = $model->getReturnLink();
			$msg = JText::_('COM_AICONTACTSAFE_REPLY_SENT');
			$msgType = 'message';
		} else {
			$link = $model->getLastLink();
			$msg = $this->_app->_session->get( 'errorMsg:' . $this->_sTask );
			$msgType = 'error';
		}
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to controll the task 'export' - export all messages into a csv file
	function export() {
		$this->display();
	}

	// function to controll the task 'delete_selected' - delete all selected messages
	function delete_selected() {
		$this->display();
	}

	// function to delete all selected records
	function confirmDeleteSelected() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->deleteSelected();
		if ($isOK) {
			$link = $model->getReturnLink();
			$msg = JText::_('COM_AICONTACTSAFE_SELECTED_MESSAGES_WERE_DELETED');
			$msgType = 'message';
		} else {
			$link = $model->getLastLink();
			$msg = $this->_app->_session->get( 'errorMsg:' . $this->_sTask );
			$msgType = 'error';
		}
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to change the status of the selected messages
	function changeStatus() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->changeStatus();
		$link = $model->getReturnLink();
		$msg = JText::_('COM_AICONTACTSAFE_MESSAGES_STATUS_CHANGED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to controll the task 'download'
	function download() {
		// get the current model and start the download
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->downloadCSV();
	}
}
