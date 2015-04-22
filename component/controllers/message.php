<?php
/**
 * @version     $Id$ 2.0.12 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.12
 * - if the thank you message is empty no message is sent to Joomla
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the default aiContactSafe controller class
class AiContactSafeControllerMessage extends AiContactSafeController {

	// generate only the contact form without buttons and contact information
	function ajaxform() {
		$this->display(false, false, true);
	}

	// upload an attachment file
	function uploadFile() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->uploadFile();

	}

	// upload an attachment file
	function deleteUploadedFile() {
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->deleteUploadedFile();

	}

	// default function to call when a task is not specified
	function display( $cachable = false, $urlparams = false, $returnAjaxForm = false ) {
		// get the model for this task and sTask
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		// check if the form is using ajax
		$use_ajax = JRequest::getVar( 'use_ajax', 0, 'request', 'int');
		$send_mail = JRequest::getVar( 'send_mail', 0, 'post', 'int');
		// force the browser to mozilla so the redirect will be done with php headers not with javascript ( which is now done in Joomla 1.6 )
		jimport('joomla.environment.browser');
		$navigator = JBrowser::getInstance();
		$navigator->setBrowser('mozilla');
		// check if the IP used to access this page is banned
		$ban_ip = $model->checkBanIp();
		if ( $ban_ip ) {
			$link = $this->_config_values['redirect_ips'];
			if (strlen(trim($link)) == 0) {
				$link = JURI::base();
			}
			if ($use_ajax) {
				echo '<input type="hidden" id="ajax_return_to" name="ajax_return_to" value="'.htmlspecialchars($link).'" />'.JText::_('COM_AICONTACTSAFE_PLEASE_WAIT');
			} else {
				$this->_app->redirect($link);
				jexit();
			}
		} else {
			// the send button was pressed
			if ($send_mail == 1) {
				// read the fields sent in the form
				$isOK = $model->getFormFields();
				// if the fields are read without any error, the message is sent
				if ($isOK) {
					$isOK = $model->SendEmail();
				}
				$new_values = array();
				$new_values['r_id'] = JRequest::getInt( 'r_id', mt_rand() );
				if ($isOK) {
					$link = $model->getReturnLink($new_values, $use_ajax);
					$pf = JRequest::getInt( 'pf' );
					$contactinformations = $model->readContactInformations( $pf, $new_values['r_id'] );
					$msg = array_key_exists('thank_you_message',$contactinformations)?$contactinformations['thank_you_message']:'';
					$msgType = 'message';
				} else {
					$link = $model->getLastLink($new_values, $use_ajax);
					$msg = $this->_app->_session->get( 'errorMsg:' . $this->_sTask );
					$msgType = 'error';
				}
				if($use_ajax) {
					if (strlen($msg) > 6) {
						// send the feedback message
						$this->_app->enqueueMessage($msg, $msgType);
					}
					// read the fields sent when the contact form was called
					$dt = JRequest::getVar('dt', 0, 'post', 'int');
					if ( $dt ) {
						$model->getFormFields();
					}
					// generate the view
					$view = $this->getView( $this->_sTaskView, 'html', '', $this->_parameters );
					$view->setModel( $model, true );
					$view->setLayout( $this->_sTaskLayout );
					$view->viewDefault( $returnAjaxForm );
				} else {
					if (strlen($msg) > 6) {
						$this->_app->redirect($link, $msg, $msgType);
						jexit();
					} else {
						$this->_app->redirect($link);
						jexit();
					}
				}
			// if back button was pressed
			} elseif ($send_mail == 2) {
				$link = $model->getReturnLink();
				$this->_app->redirect($link);
				jexit();
			// the form is displayed for the first time
			} else {
				// read the fields sent when the contact form was called
				$dt = JRequest::getVar('dt', 0, 'post', 'int');
				if ( $dt ) {
					$model->getFormFields();
				}
				// generate the view
				$view = $this->getView( $this->_sTaskView, 'html', '', $this->_parameters );
				$view->setModel( $model, true );
				$view->setLayout( $this->_sTaskLayout );
				$view->viewDefault( $returnAjaxForm );
			}
		}
		$this->recordLastTask();
	}

	// function to controll the task 'download'
	function download() {
		// get the current model and start the download
		$model = $this->getModel( $this->_sTaskModel, '', $this->_parameters );
		$model->downloadFile();
	}

}
