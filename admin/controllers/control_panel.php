<?php
/**
 * @version     $Id$ 2.0.0 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the control_panel controller class of aiContactSafe
class AiContactSafeControllerControl_panel extends AiContactSafeController {

	// get the layout to use - always use the same layout
	function getSTaskLayout($sTask = '') {
		switch(true) {
			// in case a delete for database tables and uploaded files was requested, ask for a confirmation
			case $this->_task == 'confirm_delete_all' :
				$layout = 'confirm_delete_all';
				break;
			// or else use the default layout
			case $this->_task == 'display' :
			default :
				$layout = 'control_panel';
		}
		return $layout;
	}

	// function to get the confirmation message when the data is saved
	function getConfirmationMessage() {
		return JText::_('COM_AICONTACTSAFE_CONFIGURATION_SAVED');
	}

	// function to delete all tables and files uploaded by aiContactSafe
	function delete_all_accepted() {
		// get the model for the current controller
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		// delete tables and files
		$model->deleteTablesAndFiles();
		// redirect the page after the tables and files were deleted
		$model->resetFormFields();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$link = 'index.php?option=com_installer&view=manage';
		} else {
			$link = 'index.php?option=com_installer&task=manage&type=components';
		}

		$this->setRedirect($link);
	}

	// function to discard all changes and return to the section defined in return_task
	function cancel() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->resetFormFields();
		$new_values = array('sTask'=>'default');
		$link = $model->getReturnLink($new_values);
		$this->setRedirect($link);
	}

	// function to activate aiContactSafe in Artio
	function activate_artio() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->activate_artio();
		$link = $model->getReturnLink();
		$msg = JText::_('COM_AICONTACTSAFE_ARTIO_ACTIVATED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to deactivate aiContactSafe in Artio
	function deactivate_artio() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->deactivate_artio();
		$link = $model->getReturnLink();
		$msg = JText::_('COM_AICONTACTSAFE_ARTIO_DEACTIVATED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to activate aiContactSafe in Joom!Fish
	function activate_joomfish() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->activate_joomfish();
		$link = $model->getReturnLink();
		$msg = JText::_('COM_AICONTACTSAFE_JOOMFISH_ACTIVATED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to deactivate aiContactSafe in Joom!Fish
	function deactivate_joomfish() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->deactivate_joomfish();
		$link = $model->getReturnLink();
		$msg = JText::_('COM_AICONTACTSAFE_JOOMFISH_DEACTIVATED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to activate aiContactSafe in FaLang
	function activate_falang() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->activate_falang();
		$link = $model->getReturnLink();
		$msg = JText::_('COM_AICONTACTSAFE_FALANG_ACTIVATED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to deactivate aiContactSafe in FaLang
	function deactivate_falang() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->deactivate_falang();
		$link = $model->getReturnLink();
		$msg = JText::_('COM_AICONTACTSAFE_FALANG_DEACTIVATED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to check language files
	function check_language() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$response = $model->check_language();
		$link = $model->getReturnLink();
		$msg = JText::_('COM_AICONTACTSAFE_LANGUAGE_FILES_CHECKED').'<br/>'.$response;
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to install aiContactSafeModule
	function installAiContactSafeModule() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		if ($model->installAiContactSafeModule()) {
			$msg = 'aiContactSafeModule '.JText::_('COM_AICONTACTSAFE_INSTALLED');
			$msgType = 'message';
		} else {
			$msg = 'aiContactSafeModule '.JText::_('COM_AICONTACTSAFE_INSTALLATION_FAILED');
			$msgType = 'error';
		}
		$link = $model->getReturnLink();
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to uninstall aiContactSafeModule
	function uninstallAiContactSafeModule() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		if ($model->uninstallAiContactSafeModule()){
			$msg = 'aiContactSafeModule '.JText::_('COM_AICONTACTSAFE_UNINSTALLED');
			$msgType = 'message';
		} else {
			$msg = 'aiContactSafeModule '.JText::_('COM_AICONTACTSAFE_UNINSTALLING_FAILED');
			$msgType = 'error';
		}
		$link = $model->getReturnLink();
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to reinstall aiContactSafeModule
	function reinstallAiContactSafeModule() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		if ($model->reinstallAiContactSafeModule()){
			$msg = 'aiContactSafeModule '.JText::_('COM_AICONTACTSAFE_UPGRADED');
			$msgType = 'message';
		} else {
			$msg = 'aiContactSafeModule '.JText::_('COM_AICONTACTSAFE_UPGRADE_FAILED');
			$msgType = 'error';
		}
		$link = $model->getReturnLink();
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to install aiContactSafeForm
	function installAiContactSafeForm() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->installAiContactSafeForm();
		$link = $model->getReturnLink();
		$msg = 'aiContactSafeForm '.JText::_('COM_AICONTACTSAFE_INSTALLED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to uninstall aiContactSafeForm
	function uninstallAiContactSafeForm() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->uninstallAiContactSafeForm();
		$link = $model->getReturnLink();
		$msg = 'aiContactSafeForm '.JText::_('COM_AICONTACTSAFE_UNINSTALLED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to reinstall aiContactSafeForm
	function reinstallAiContactSafeForm() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		if ($model->reinstallAiContactSafeForm()){
			$msg = 'aiContactSafeForm '.JText::_('COM_AICONTACTSAFE_UPGRADED');
			$msgType = 'message';
		} else {
			$msg = 'aiContactSafeForm '.JText::_('COM_AICONTACTSAFE_UPGRADE_FAILED');
			$msgType = 'error';
		}
		$link = $model->getReturnLink();
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to install aiContactSafeLink
	function installAiContactSafeLink() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->installAiContactSafeLink();
		$link = $model->getReturnLink();
		$msg = 'aiContactSafeLink '.JText::_('COM_AICONTACTSAFE_INSTALLED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to uninstall aiContactSafeLink
	function uninstallAiContactSafeLink() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->uninstallAiContactSafeLink();
		$link = $model->getReturnLink();
		$msg = 'aiContactSafeLink '.JText::_('COM_AICONTACTSAFE_UNINSTALLED');
		$msgType = 'message';
		$this->setRedirect($link, $msg, $msgType);
	}

	// function to reinstall aiContactSafeLink
	function reinstallAiContactSafeLink() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		if ($model->reinstallAiContactSafeLink()){
			$msg = 'aiContactSafeLink '.JText::_('COM_AICONTACTSAFE_UPGRADED');
			$msgType = 'message';
		} else {
			$msg = 'aiContactSafeLink '.JText::_('COM_AICONTACTSAFE_UPGRADE_FAILED');
			$msgType = 'error';
		}
		$link = $model->getReturnLink();
		$this->setRedirect($link, $msg, $msgType);
	}

}
