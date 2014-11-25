<?php
/**
 * @version     $Id$ 2.0.9
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// load the default component controller class
jimport( 'joomla.application.component.controller' );

// define the default aiContactSafe controller class
class AiContactSafeController extends JController {
	// component version
	var $_version = '2.0.21c.stable';
	// mainframe (application) reference
	var $_app = null;
	// current task
	var $_task = null;
	// current aiContactSafe section
	var $_sTask = null;
	// this class is used in backend (1) or frontend(0)
	var $_backend = null;
	// current aiContactSafe model based on the section
	var $_sTaskModel = null;
	// current aiContactSafe view based on the section
	var $_sTaskView = null;
	// current aiContactSafe layout based on the section
	var $_sTaskLayout = null;
	// id of the current user logged in
	var $_user_id = null;
	// parameters array
	var $_parameters = array();
	// configuration values
	var $_config_values = null;
	// sef is activated or not
	var $_sef = null;

	// construct function, it will iniaize the class variables
	function __construct( $default = array() )	{
		$this->_app = JFactory::getApplication();
		$this->_app->_session = JFactory::getSession();
		$this->_task = $default['task'];
		$this->_sTask = $default['sTask'];
		$this->_backend = $this->_app->getClientId();
		$this->_sef = $this->_app->getCfg('sef');
		// get the current user
		$user = JFactory::getUser();
		$this->_user_id = $user->get('id');

		// security check
		$this->securityCheck();

		// record the current parameters
		$this->_parameters['_version'] = $this->_version;
		$this->_parameters['_app'] = $this->_app;
		$this->_parameters['_task'] = $this->_task;
		$this->_parameters['_sTask'] = $this->_sTask;
		$this->_parameters['_backend'] = $this->_backend;
		$this->_parameters['_user_id'] = $this->_user_id;
		$this->_parameters['_sef'] = $this->_sef;

		// get the model class, view class and view template
		$this->_sTaskModel = $this->getSTaskModel($this->_sTask);
		$this->_sTaskView = $this->getSTaskView($this->_sTask);
		$this->_sTaskLayout = $this->getSTaskLayout($this->_sTask);

		// get the configuration values
		$this->_config_values = $this->getConfiguration();
		// add configuration to parameters sent to model and view
		$this->_parameters['_config_values'] = $this->_config_values;

		parent::__construct( $default );
		// record the last task performed
		$this->checkLastTask();

		// reset the session variable which records the last status of a form
		$isOK = $this->_app->_session->get( 'isOK:' . $this->_sTask );
		if (!is_bool($isOK)) {
			// get the model for the current controller
			$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
			$model->resetFormFields();
		}
	}

	// function for a security check
	// main purpose is to force a user to be logged in
	// it will modify $this->_task and $this->_sTask
	function securityCheck() {
	}

	// function to get last task
	function getLastTask() {
		$last_task = array();
		$last_task['sTask'] = $this->_sTask;
		$last_task['task'] = $this->_task;
		$id = JRequest::getVar('id', 0, 'request', 'int');
		if ($id > 0) {
			$last_task['id'] = $id;
		}
		return $last_task;
	}

	// function to record the last task in a temporary variable
	function recordLastTask() {
		$last_task = $this->getLastTask();
		// record the last task executed in a temporary variable
		$this->_app->_session->set( 'last_task_temp', $last_task );
	}
	
	// function to compare the temporary last task with the current task
	// if it is different, modify the last task variable
	// this is a protection for the refresh button on the browser
	function checkLastTask() {
		$last_task_temp = $this->_app->_session->get( 'last_task_temp' );
		if (is_array($last_task_temp)) {
			$last_task = $this->getLastTask();
			if ( count(array_diff($last_task_temp, $last_task)) + count(array_diff($last_task, $last_task_temp)) > 0 ) {
				$this->_app->_session->set( 'last_task', $last_task_temp );
			}
		}
	}
	
	// get the model to use based on sTask
	function getSTaskModel($sTask = '') {
		// if no sTask is called use the default model
		if (strlen($sTask) == 0) {
			$model = 'default';
		} else {
			$model = $sTask;
		}
		return $model;
	}

	// get the view to use based on sTask
	function getSTaskView($sTask = '') {
		// if no sTask is called use the default view
		if (strlen($sTask) == 0) {
			$view = 'default';
		} else {
			$view = $sTask;
		}
		return $view;
	}

	// get the layout to use based on sTask and task
	function getSTaskLayout($sTask = '') {
		// if no sTask is called use the default layout
		if (strlen($sTask) == 0) {
			$layout = 'default';
		} else {
			switch(true) {
				// in case a record is added or modified set the edit_record layout
				case $this->_task == 'add' or $this->_task == 'edit' :
					$layout = 'edit_record';
					break;
				// in case a record is deleted set the delete_record layout
				case $this->_task == 'delete' :
					$layout = 'delete_record';
					break;
				// or else use the default layout
				case $this->_task == 'display' :
				default :
					$layout = $sTask;
			}
		}
		return $layout;
	}

	// default function to call when a task is not specified
	function display($cachable = false, $urlparams = false) {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$view = $this->getView( $this->_sTaskView, 'html', '', $this->_parameters );
		$view->setModel( $model, true );
		$view->setLayout( $this->_sTaskLayout );
		$view->viewDefault();
		$this->recordLastTask();
	}

	// function to discard all changes and return to the section defined in return_task
	function cancel() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->resetFormFields();
		$link = $model->getReturnLink();
		$this->setRedirect($link);
	}

	// function to get the confirmation message when the data is saved
	function getConfirmationMessage() {
		return JText::_('COM_AICONTACTSAFE_MODIFICATIONS_SAVED');
	}

	// function to save all changes and return to the section defined in return_task
	function save() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->getFormFields();
		if ($isOK) {
			$isOK = $model->writeData();
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

	// function to save all changes and keep the window open for more changes
	function apply() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->getFormFields();
		if ($isOK) {
			$isOK = $model->writeData();
		}
		// read the last task values
		$last_task = $this->_app->_session->get( 'last_task' );
		// if last task is 'add' get the last id saved, else get the id from the post variables
		if ($last_task['task'] == 'add') {
			$last_id = $this->_app->_session->get( 'idSaved:' . $this->_sTask );
		} else {
			$last_id = JRequest::getVar( 'id', 0, 'post', 'int');
		}
		// set the new values of the link
		$new_values = array();
		$new_values['task'] = 'edit';
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

	// function to controll the task 'add' - add a new record in a table
	function add() {
		JRequest::setVar('id',null);
		JRequest::setVar('cid',null);
		$this->display();
	}

	// function to controll the task 'edit' - edit a record in a table
	function edit() {
		$this->display();
	}

	// function to controll the task 'delete' - command to delete one or more records from a table
	function delete() {
		$this->display();
	}

	// function to get the confirmation message when the data is saved
	function getConfirmationDeleteMessage() {
		return JText::_('COM_AICONTACTSAFE_RECORDS_DELETED');
	}

	// function to controll the task 'confirmDelete' - action to delete one or more records from a table
	function confirmDelete() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->deleteData();
		if ($isOK) {
			$link = $model->getReturnLink();
			$msg = $this->getConfirmationDeleteMessage();
			$msgType = 'message';
		} else {
			$link = $model->getLastLink();
			$msg = $this->_app->_session->get( 'errorMsg:' . $this->_sTask );
			$msgType = 'error';
		}
		$this->setRedirect($link, $msg, $msgType);
	}

	// function used to modify the field published to 1
	function publish() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->changePublish(1);
		$link = $model->getLastLink();
		$this->setRedirect($link);
	}

	// function used to modify the field published to 0
	function unpublish() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->changePublish(0);
		$link = $model->getLastLink();
		$this->setRedirect($link);
	}


	// function used to move a record up (based on the field ordering)
	function orderup() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->changeOrder(-1);
		$link = $model->getLastLink();
		$this->setRedirect($link);
	}

	// function used to move a record down (based on the field ordering)
	function orderdown() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->changeOrder(1);
		$link = $model->getLastLink();
		$this->setRedirect($link);
	}

	// function used to save the new order of records
	function saveorder() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$isOK = $model->saveOrder();
		$link = $model->getLastLink();
		if ($isOK) {
			$msg = JText::_('COM_AICONTACTSAFE_ORDER_SAVED');
			$msgType = 'message';
		} else {
			$msg = $this->_app->_session->get( 'errorMsg:' . $this->_sTask );
			$msgType = 'error';
		}
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to get the configuartin values
	function getConfiguration() {
		$config_aiContactSafe = array();
	
		// initialize the database
		$db = JFactory::getDBO();
	
		$query = "select * from  `#__aicontactsafe_config`";
		$db->setQuery( $query );
		$config_values = $db->loadObjectList();
		if (count($config_values) > 0) {
			foreach($config_values as $value) {
				$config_aiContactSafe[$value->config_key] = $this->revert_specialchars($value->config_value);
			}
		}
		return $config_aiContactSafe;
	}

	// function to revert the special chars encoding
	function revert_specialchars( $source_string = '' ) {
		$source_string = str_replace('&quot;','"',$source_string);
		$source_string = str_replace('&#039;','\'',$source_string);
		$source_string = str_replace('&lt;','<',$source_string);
		$source_string = str_replace('&gt;','>',$source_string);
		return $source_string;
	}

}
