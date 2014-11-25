<?php
/**
 * @version     $Id$ 2.0.9 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the default aiContactSafe controller class
class AiContactSafeControllerProfiles extends AiContactSafeController {

	// get the layout to use based on sTask and task
	function getSTaskLayout($sTask = '') {
		switch(true) {
			// in case a record is added or modified set the edit_record layout
			case $this->_task == 'add' or $this->_task == 'edit' :
				$layout = 'edit_record';
				break;
			// in case a record is deleted set the delete_record layout
			case $this->_task == 'delete' :
				$layout = 'delete_record';
				break;
			// in case a profile's contact information is edited
			case $this->_task == 'edit_contact' :
				$layout = 'edit_contact';
				break;
			// in case a profile's CSS is edited
			case $this->_task == 'edit_css' :
				$layout = 'edit_css';
				break;
			// in case a profile's mail template is edited
			case $this->_task == 'edit_email' :
				$layout = 'edit_email';
				break;
			// or else use the default layout
			case $this->_task == 'display' :
			default :
				$layout = $sTask;
		}
		return $layout;
	}

	// function to duplicate a profile
	function copyProfile() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->copyProfile();
		$link = $model->getReturnLink();
		$msg = JText::_('COM_AICONTACTSAFE_PROFILE_COPIED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to set the css code
	function setCSS() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$css_code = $model->getCSS();
		echo $css_code;
	}

	// function to controll the editing of the contact information
	function edit_contact() {
		$this->display();
	}
	
	// function to save the contact information
	function save_contact() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->getFormFields();
		if ($isOK) {
			$isOK = $model->writeContactInformation();
		}
		if ($isOK) {
			$link = $model->getReturnLink();
			$msg = $this->getConfirmationMessage();
			$msgType = 'message';
		} else {
			$link = $model->getLastLink();
			$msg = $this->_app->_session->get( 'errorMsg:' . $this->_sTask );
			$msgType = 'error';
		}
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to apply the contact information changes and open the edit window again
	function apply_contact() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->getFormFields();
		if ($isOK) {
			$isOK = $model->writeContactInformation();
		}
		// read the last task values
		$last_task = $this->_app->_session->get( 'last_task' );
		$last_id = JRequest::getVar( 'id', 0, 'post', 'int');
		// set the new values of the link
		$new_values = array();
		$new_values['task'] = 'edit_contact';
		$new_values['id'] = $last_id;
		// get the link with the new values
		$link = $model->getLastLink($new_values);
		// redirect to the new link
		if ($isOK) {
			$msg = $this->getConfirmationMessage();
			$msgType = 'message';
		} else {
			$msg = $this->_app->_session->get( 'errorMsg:' . $this->_sTask );
			$msgType = 'error';
		}
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to controll the editing of the CSS
	function edit_css() {
		$this->display();
	}

	// function to save the profile's CSS
	function save_css() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->getFormFields();
		if ($isOK) {
			$isOK = $model->writeCSS();
		}
		if ($isOK) {
			$link = $model->getReturnLink();
			$msg = $this->getConfirmationMessage();
			$msgType = 'message';
		} else {
			$link = $model->getLastLink();
			$msg = $this->_app->_session->get( 'errorMsg:' . $this->_sTask );
			$msgType = 'error';
		}
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to apply the CSS changes and open the edit window again
	function apply_css() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->getFormFields();
		if ($isOK) {
			$isOK = $model->writeCSS();
		}
		// read the last task values
		$last_task = $this->_app->_session->get( 'last_task' );
		$last_id = JRequest::getVar( 'id', 0, 'post', 'int');
		// set the new values of the link
		$new_values = array();
		$new_values['task'] = 'edit_css';
		$new_values['id'] = $last_id;
		// get the link with the new values
		$link = $model->getLastLink($new_values);
		// redirect to the new link
		if ($isOK) {
			$msg = $this->getConfirmationMessage();
			$msgType = 'message';
		} else {
			$msg = $this->_app->_session->get( 'errorMsg:' . $this->_sTask );
			$msgType = 'error';
		}
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to controll the editing of the email template
	function edit_email() {
		$this->display();
	}

	// function to save the profile's email template
	function save_email() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->getFormFields();
		if ($isOK) {
			$isOK = $model->writeEmail();
		}
		if ($isOK) {
			$link = $model->getReturnLink();
			$msg = $this->getConfirmationMessage();
			$msgType = 'message';
		} else {
			$link = $model->getLastLink();
			$msg = $this->_app->_session->get( 'errorMsg:' . $this->_sTask );
			$msgType = 'error';
		}
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to apply the email template changes and open the edit window again
	function apply_email() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->getFormFields();
		if ($isOK) {
			$isOK = $model->writeEmail();
		}
		// read the last task values
		$last_task = $this->_app->_session->get( 'last_task' );
		$last_id = JRequest::getVar( 'id', 0, 'post', 'int');
		// set the new values of the link
		$new_values = array();
		$new_values['task'] = 'edit_email';
		$new_values['id'] = $last_id;
		// get the link with the new values
		$link = $model->getLastLink($new_values);
		// redirect to the new link
		if ($isOK) {
			$msg = $this->getConfirmationMessage();
			$msgType = 'message';
		} else {
			$msg = $this->_app->_session->get( 'errorMsg:' . $this->_sTask );
			$msgType = 'error';
		}
		$this->setRedirect($link, $msg, $msgType);
	}

	function setdefault() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->setDefault();
		$link = $model->getLastLink();
		$this->setRedirect($link);
	}
}
