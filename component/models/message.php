<?php
/**
 * @version     $Id$ 2.0.14
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.1
 * - added new field types Checkbox - List, Radio - List, Date, Emails, Contacts
 * - check if an email of contact custom field is used and if the message should be sent to default email or not
 * added/fixed in version 2.0.8
 * - new lines are replaced into <br /> tags only if the email is sent as html
 * - remove the subject html chars used to be sure no conflict is generated when the fields are saved into the database
 * added/fixed in version 2.0.10.b
 * - rename the files attached so all the spaces are replaced with "_"
 * - fixed the problem with "Always send to sender" from the profile
 * added/fixed in version 2.0.10.c
 * - the language is checked in the Joom!Fish cookie if not determined by the url
 * - added more information on possible file upload errors
 * added/fixed in version 2.0.12
 * - save as the sender of the message the correct email address, not the one from the control panel of aiContactSafe in case "Set the sender to the default Joomla email address" is activated
 * - removed the field to identify the user, the user is indetified by the log-in process
 * added/fixed in version 2.0.13
 * - initialized the contact information before reading it from the database
 * - added SqueezeBox for aiContactSafe feed-back
 * - added highlighting for fields with errors
 * - deactivated the check for allowed attachments types when the field from control panel if left empty
 * added/fixed in version 2.0.14
 * - filter variables read with JRequest::getVar
 * - follow the errors based on the profile
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the control_panel model class of aiContactSafe
class AiContactSafeModelMessage extends AiContactSafeModelDefault {
	// fields to display into the contact form
	var $fields = null;
	// the contact informations
	var $contactinformations = null;

	// function to delete an uploaded file ( on the cancel button is pressed )
	function deleteUploadedFile() {
		$r_id = JRequest::getVar('r_id', 0, 'request', 'int');
		$filename = JRequest::getVar('filename', '', 'request', 'cmd');
		// import joomla clases to manage file system
		jimport('joomla.filesystem.file');
		// get the path to attachments upload
		$upload_folder = str_replace('\\',DS,$this->_config_values['upload_attachments']);
		$upload_folder = str_replace('/',DS,$upload_folder);
		$upload_folder = str_replace('&#92;',DS,$upload_folder);
		$path_upload = JPATH_ROOT.DS.$upload_folder;

		$file = $path_upload.DS.$filename;
		if (JFile::exists($file)) {
			JFile::delete($file);
		}

		// initialize the database
		$db = JFactory::getDBO();

		$query = 'DELETE FROM #__aicontactsafe_messagefiles WHERE name = \'' . $filename . '\' AND message_id = 0 AND r_id = ' . $r_id;
		$db->setQuery($query);
		$db->query();
	}

	// function to upload an attached file
	function uploadFile() {
		// import joomla clases to manage file system
		jimport('joomla.filesystem.path');
		jimport('joomla.filesystem.file');

		$pf = JRequest::getVar('pf', 0, 'request', 'int');
		$field = JRequest::getVar('field', '', 'request', 'cmd');
		$r_id = JRequest::getVar('r_id', 0, 'request', 'int');
		$errorType = 0;
		$errorMessage = '';
		$attachedFileId = 0;
		$file_name = '';

		$file = JRequest::getVar( $field, 'post', 'files', 'array' );
		$format = strtolower(JFile::getExt($file['name']));
		$file_ext = JFile::getExt($file['name']);
		$file_stem = JFile::makeSafe(substr($file['name'],0,-1*strlen($file_ext)-1));
		if(strlen(trim($file_stem)) == 0) {
			$file_stem = 'file';
		}
		// make sure the file name has no spaces
		$file_stem = str_replace(' ','_',$file_stem);
		$file['name'] = JFilterInput::clean($file['name'], 'cmd');
		if ( strlen(trim($file['name'])) > 0 ) {
			// check the extension of the file
			$allowable = explode( ',', $this->_config_values['attachments_types']);
			$fileError = $_FILES[$field]['error'];
			$errorMessage = $_FILES[$field]['error'];
			if ($fileError > 0) {
				$errorType = 3;
		        switch ($fileError) {
					case 1:
						$errorMessage = JText::_('COM_AICONTACTSAFE_FILE_TO_LARGE_THAN_PHP_INI_ALLOWS');
						break;
					case 2:
						$errorMessage = JText::_('COM_AICONTACTSAFE_FILE_TO_LARGE_THAN_HTML_FORM_ALLOWS');
						break;
					case 3:
						$errorMessage = JText::_('COM_AICONTACTSAFE_ERROR_PARTIAL_UPLOAD');
						break;
			        case 4:
						$errorMessage = JText::_('COM_AICONTACTSAFE_ERROR_NO_FILE');
						break;
			        case 6:
						$errorMessage = JText::_('COM_AICONTACTSAFE_MISSING_THE_TEMPORARY_FOLDER');
						break;
			        case 7:
						$errorMessage = JText::_('COM_AICONTACTSAFE_FAILED_TO_WRITE_FILE_TO_DISK');
						break;
			        case 8:
						$errorMessage = JText::_('COM_AICONTACTSAFE_A_PHP_EXTENSION_STOPPED_THE_FILE_UPLOAD' );
						break;
				}
			} else if (strlen(trim($this->_config_values['attachments_types']))>0 && !in_array($format, $allowable)) {
				$errorType = 1;
				if (strlen($invalid_format_file) > 0) {
					$invalid_format_file .= ', ';
				}
				$invalid_format_file .= $format;
				$errorMessage = $invalid_format_file;
			} else {
				// check the size of the file
				$maxSize = (int) $this->_config_values['maximum_size'];
				if ($maxSize > 0 && (int) $file['size'] > $maxSize) {
					$errorType = 2;
					if (strlen($files_to_big) > 0) {
						$files_to_big .= ', ';
					}
					$files_to_big .= JFile::getName($file['name']);
					$errorMessage = $files_to_big;
				}
			}

			if ( $errorType == 0 ) {
				// get the path to attachments upload
				$upload_folder = str_replace('\\',DS,$this->_config_values['upload_attachments']);
				$upload_folder = str_replace('/',DS,$upload_folder);
				$upload_folder = str_replace('&#92;',DS,$upload_folder);
				$path_upload = JPATH_ROOT.DS.$upload_folder;
	
				$newFile = JTable::getInstance('aicontactsafe_messagefiles', 'Table');
	
				$newFile->id = null;
				$newFile->message_id = 0;
				$newFile->name = '';
				$newFile->r_id = $r_id;
				$datenow = JFactory::getDate();
				$newFile->date_added = $datenow->toMySQL();
				$newFile->last_update = $datenow->toMySQL();
				$newFile->published = 1;
				$newFile->checked_out = 0;
				$newFile->checked_out_time = '0000-00-00';
	
				if ($newFile->store()) {
					$file_name = JFile::makeSafe(strtolower($file_stem.'_'.$newFile->id.'.'.$file_ext));
					$attachedFileId = $newFile->id;

					// initialize the database
					$db = JFactory::getDBO();

					$query = 'UPDATE #__aicontactsafe_messagefiles SET name = \'' . $file_name . '\' WHERE id = ' . $newFile->id;
					$db->setQuery($query);
					$db->query();

					$filepath = JPath::clean($path_upload.DS.$file_name);
					JFile::upload($file['tmp_name'], $filepath);
				}
			}

		}

		$script = "
			<script language=\"javascript\" type=\"text/javascript\">
				this.parent.endUploadFile(".$pf.", '".$field."', '".$file_name."', ".$attachedFileId.", ".$errorType.", '".$errorMessage."');
			</script>
		";
		
		echo 'Attached file id '.$attachedFileId.'<hr />Error type '.$errorType.'<hr />';
		if ( $errorType > 0 ) {
			echo 'Error message '.$errorMessage.'<hr />';
		}
		echo $script;
	}

	// function to write the postdata into the session variable ( add in a function so the session variable can be modified for the message )
	function recordPostDataInSession( $postData ) {
		$r_id = JRequest::getInt( 'r_id' );
		$this->_app->_session->set( 'postData:' . $this->_sTask . '_' . $r_id, $postData );
	}

	// function to read the postdata from the session variable ( add in a function so the session variable can be modified for the message )
	function readPostDataFromSession() {
		$r_id = JRequest::getInt( 'r_id' );
		return $this->_app->_session->get( 'postData:' . $this->_sTask . '_' . $r_id );
	}

	// function to read the postdata from the session parameters variable ( add in a function so the session variable can be modified for the message )
	function readParametersDataFromSession() {
		$r_id = JRequest::getInt( 'r_id' );
		return $this->_app->_session->get( 'parameters:' . $this->_sTask . '_' . $r_id );
	}

	// function to read the fields to display into the contact form
	function readFields( $profile = null ) {
		if(!$this->fields) {
			$this->fields = array();
			// initialize the database
			$db = JFactory::getDBO();
	
			// get the fields to display
			if ( $profile->active_fields == 0 ) {
				$query = 'SELECT * FROM #__aicontactsafe_fields WHERE published = 1 ORDER by ordering';
			} else {
				$query = 'SELECT * FROM #__aicontactsafe_fields WHERE published = 1 and id IN ( ' . $profile->active_fields . ' ) ORDER by ordering';
			}
			$db->setQuery( $query );
			$records = $db->loadObjectList();
			// generate the response array
			$unsorted_fields = array();
			foreach($records as $record) {
				$unsorted_fields[$record->id] = $record;
			}
			// sort the response array
			$fields_order = explode(',',$profile->fields_order);
			foreach($fields_order as $field_id) {
				if (array_key_exists($field_id, $unsorted_fields)) {
					$this->fields[$unsorted_fields[$field_id]->name] = $unsorted_fields[$field_id];
					unset($unsorted_fields[$field_id]);
				}
			}
			foreach($unsorted_fields as $field) {
				$this->fields[$field->name] = $field;
			}
		}
		return $this->fields;
	}

	// function to read the contact informations
	function readContactInformations( $profile_id = 0, $r_id = 0 ) {
		if(!$this->contactinformations) {
			$this->contactinformations = array();

			$this->contactinformations['contact_info'] = '';
			$this->contactinformations['meta_description'] = '';
			$this->contactinformations['meta_keywords'] = '';
			$this->contactinformations['meta_robots'] = '';
			$this->contactinformations['thank_you_message'] = '';
			$this->contactinformations['required_field_notification'] = JText::_('COM_AICONTACTSAFE_FIELDS_MARKED_WITH').' %mark% '.JText::_('COM_AICONTACTSAFE_ARE_REQUIRED').'.';

			// initialize the database
			$db = JFactory::getDBO();
	
			// get the fields to display
			$query = 'SELECT * FROM #__aicontactsafe_contactinformations WHERE profile_id = '.$profile_id;
			$db->setQuery( $query );
			$contactinformations = $db->loadObjectList();
			if(is_array($contactinformations) && count($contactinformations) > 0) {
				foreach($contactinformations as $info) {
					$this->contactinformations[$info->info_key] = $this->revert_specialchars($info->info_value);
				}
			}




		}
		// set the thank you message
		$this->_app->_session->set( 'confirmationMessage:' . $this->_sTask . '_' . $r_id, array_key_exists('thank_you_message',$this->contactinformations)?$this->contactinformations['thank_you_message']:'' );

		return $this->contactinformations;
	}

	// function to protect against different security threats
	function securityCheck($postData) {
		$postData = parent::securityCheck($postData);

		// import joomla clases to manage file system
		jimport('joomla.filesystem.path');
		jimport('joomla.filesystem.file');

		// get the requested profile id
		$pf = $postData['pf'];
		$profile = $this->getProfile( $pf );

		// get the list of the fields from the contact form
		$fields = $this->readFields( $profile );

		// check if all required fields are completed
		$isOK = true;
		// if the values are sent from aiContactSafeLink deactivate highlight_errors
		$dt = JRequest::getVar('dt', 0, 'post', 'int');
		// record the fields with problems and the error message to display
		$fieldsWithErrors = array();
		// record rquired fields uncompleted
		$empty_required_fields = '';
		// record invalid email fields
		$invalid_email_fields = '';
		// record limit excede fields
		$limit_exceded_fields = '';
		// record invalid number fields
		$invalid_number_fields = '';
		// record invalid format file
		$invalid_format_file = '';
		// record files to bit
		$files_to_big = '';
		// record invalid cc fields
		$invalid_cc_fields = '';
		// check each field
		foreach($fields as $field) {
			$field->field_label = $this->revert_specialchars($field->field_label);
			// initialize the resoult of the check
			$isOKfield = true;
			// check if the field is required
			if ($field->field_required) {
				// check if the field is required
				switch($field->field_type) {
					case 'TX':
						// Textbox
						if ( array_key_exists($field->name,$postData) && strlen($postData[$field->name]) == 0 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name,$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'CK':
						// Checkbox
						if ( array_key_exists($field->name,$postData) ) {
							if (array_key_exists($field->name,$postData) && $postData[$field->name]) {
								// checked
							} else {
								$isOKfield = false;
							}
						} else if ( !array_key_exists($field->name,$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'CB':
						// Combobox
						if ( array_key_exists($field->name,$postData) && $postData[$field->name] == -1 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name,$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'ED':
						// Editbox
						if ( array_key_exists($field->name,$postData) && strlen($postData[$field->name]) == 0 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name,$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'CL' :
						// Checkbox - List
						$postDataValue = array();
						if (array_key_exists($field->name,$postData) && is_array($postData[$field->name])) {
							$field_values = explode(';',$this->revert_specialchars($field->field_values));
							foreach($postData[$field->name] as $i=>$v) {
								if ( $v == 1 ) {
									$postDataValue[] = $field_values[$i];
								}
							}
						}
						if ( array_key_exists($field->name,$postData) && count($postDataValue) == 0 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name,$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'RL' :
						// Radio - List
						if ( array_key_exists($field->name,$postData) && strlen($postData[$field->name]) == 0 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name,$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'DT' :
						// Date
						break;
					case 'EM' :
						// Email
						if ( array_key_exists($field->name,$postData) && strlen($postData[$field->name]) == 0 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name,$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'EL' :
						// Email - List
						if ( array_key_exists($field->name,$postData) && $postData[$field->name] == -1 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name,$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'JC' :
						// Joomla Contacts
						if ( array_key_exists($field->name,$postData) && $postData[$field->name] == 0 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name,$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'JU' :
						// Joomla Users
						if ( array_key_exists($field->name,$postData) && $postData[$field->name] == 0 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name,$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'SB' :
						// SOBI2 Entries
						if ( array_key_exists($field->name,$postData) && $postData[$field->name] == 0 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name,$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'HD':
						// Hidden
						break;
					case 'SP':
						// Separator
						break;
					case 'FL':
						// File
						if ( array_key_exists($field->name.'_attachment_name',$postData) && strlen(trim($postData[$field->name.'_attachment_name'])) == 0 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name.'_attachment_name',$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'NO':
						// Number
						if ( array_key_exists($field->name,$postData) && ( strlen($postData[$field->name]) == 0 || $postData[$field->name] == 0 ) ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name,$postData) ) {
							$isOKfield = false;
						}
						break;
					case 'HE' :
						// Hidden Email
						break;
					case 'UQ' :
						// Unique text
						break;
					case 'CC' :
						// Credit card
						if ( array_key_exists($field->name.'_creditcardnumber',$postData) && strlen($postData[$field->name.'_creditcardnumber']) == 0 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name.'_creditcardnumber',$postData) ) {
							$isOKfield = false;
						}
						if ( array_key_exists($field->name.'_creditcardverification',$postData) && strlen($postData[$field->name.'_creditcardverification']) == 0 ) {
							$isOKfield = false;
						} else if ( !array_key_exists($field->name.'_creditcardverification',$postData) ) {
							$isOKfield = false;
						}
						break;
				}
				// if the field is required check if it's a file
			}
			if (!$isOKfield) {
				$isOK = false;
				$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
				if (strlen($empty_required_fields) > 0) {
					$empty_required_fields .= ', ';
				}
				$empty_required_fields .= $field->field_label;
				$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_ALL_REQUIRED_FIELDS') . (strlen($empty_required_fields) > 0?' ( ' . $empty_required_fields . ' ) ':'') );
				if ($dt == 0 && $this->_config_values['highlight_errors']) {
					if(!array_key_exists($field->id,$fieldsWithErrors)) {
						$fieldsWithErrors[$field->id] = array();
					}
					$fieldsWithErrors[$field->id][] = JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_ALL_REQUIRED_FIELDS');
				}
			}
			if ($field->field_type == 'EM' && array_key_exists($field->name,$postData) && strlen($postData[$field->name]) > 0) {
				if (!$this->validateEmail($postData[$field->name])) {
					$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
					if (strlen($invalid_email_fields) > 0) {
						$invalid_email_fields .= ', ';
					}
					$invalid_email_fields .= $field->field_label;
					$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_A_VALID_EMAIL_ADDRESS') . ' ( ' . $invalid_email_fields . ' ) ' );
					if ($dt == 0 && $this->_config_values['highlight_errors']) {
						if(!array_key_exists($field->id,$fieldsWithErrors)) {
							$fieldsWithErrors[$field->id] = array();
						}
						$fieldsWithErrors[$field->id][] = JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_A_VALID_EMAIL_ADDRESS');
					}
				}
			}
			if ($field->field_type == 'DT' && array_key_exists($field->name.'_'.$pf,$postData)) {
				$postData[$field->name] = $postData[$field->name.'_'.$pf];
			}
			if ($field->field_type == 'ED' || $field->field_type == 'HD') {
				$field_value = JRequest::getVar($field->name, '', 'post', 'string', JREQUEST_ALLOWHTML);
				$postData[$field->name] = $field_value;
			}
			if (array_key_exists($field->name,$postData) && ( $field->field_type == 'TX' || $field->field_type == 'ED' || $field->field_type == 'ED')) {
				if(function_exists('mb_strlen')) {
					$current_field_length = (int)@mb_strlen($postData[$field->name], 'latin1');
					if($current_field_length == 0) {
						$current_field_length = strlen($postData[$field->name]);
					}
				} else {
					$current_field_length = strlen($postData[$field->name]);
				}
				if ($field->field_limit > 0 && $current_field_length > $field->field_limit) {
					$isOK = false;
					$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
					if (strlen($limit_exceded_fields) > 0) {
						$limit_exceded_fields .= ', ';
					}
					$limit_exceded_fields .= $field->field_label;
					$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_MAXIMUM_CHARACTERS_EXCEEDED') . ' ( ' . $limit_exceded_fields . ' ) ' );
					if ($dt == 0 && $this->_config_values['highlight_errors']) {
						if(!array_key_exists($field->id,$fieldsWithErrors)) {
							$fieldsWithErrors[$field->id] = array();
						}
						$fieldsWithErrors[$field->id][] = JText::_('COM_AICONTACTSAFE_MAXIMUM_CHARACTERS_EXCEEDED');
					}
				}
			}
			if (array_key_exists($field->name,$postData) && $field->field_type == 'NO' && strlen(trim($postData[$field->name])) > 0 && !is_numeric($postData[$field->name])) {
				$isOK = false;
				$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
				if (strlen($invalid_number_fields) > 0) {
					$invalid_number_fields .= ', ';
				}
				$invalid_number_fields .= $field->field_label;
				$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_A_VALID_NUMBER') . ' ( ' . $invalid_number_fields . ' ) '  );
				if ($dt == 0 && $this->_config_values['highlight_errors']) {
					if(!array_key_exists($field->id,$fieldsWithErrors)) {
						$fieldsWithErrors[$field->id] = array();
					}
					$fieldsWithErrors[$field->id][] = JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_A_VALID_NUMBER');
				}
			}
			// check if the cc field is valid
			if($field->field_type == 'CC') {
				if ( array_key_exists($field->name.'_creditcardnumber',$postData) && strlen($postData[$field->name.'_creditcardnumber']) > 0 ) {
					$cc_error = false;
					$cc_number = $postData[$field->name.'_creditcardnumber'];
					$cc_number = trim(str_replace('-','',str_replace(' ','',$cc_number)));
					$cc_error = strlen($cc_number) != 16;
					if(!$cc_error) {
						$valid_cc = 0;
						for($i=0;$i<15;$i++) {
							if ($i==0 || $i % 2) {
								$valid_cc += (int)substr($cc_number,$i,1);
							} else {
								$doubled_digit = (int)substr($cc_number,$i,1)*2;
								$doubled_digit = str_pad($doubled_digit, 2, '0', STR_PAD_LEFT);
								$valid_cc += (int)substr($doubled_digit,0,1)+(int)substr($doubled_digit,1,1);
							}
						}
						$valid_cc += (int)substr($cc_number,15,1);
						if($valid_cc % 10) {
							$cc_error = false;
						} else {
							$cc_error = true;
						}
					}
					if(!$cc_error) {
						$cc_error = strlen(trim(str_replace(' ','',$postData[$field->name.'_creditcardverification']))) != 3;
					}
					if($cc_error) {
						$isOK = false;
						$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
						if (strlen($invalid_cc_fields) > 0) {
							$invalid_cc_fields .= ', ';
						}
						$invalid_cc_fields .= $field->field_label;
						$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_A_VALID_CREDIT_CARD_NUMBER') . ' ( ' . $invalid_number_fields . ' ) '  );
						if ($dt == 0 && $this->_config_values['highlight_errors']) {
							if(!array_key_exists($field->id,$fieldsWithErrors)) {
								$fieldsWithErrors[$field->id] = array();
							}
							$fieldsWithErrors[$field->id][] = JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_A_VALID_CREDIT_CARD_NUMBER');
						}
					} else {
						if($postData[$field->name.'_creditcardexpirationyear'] == date('Y') && $postData[$field->name.'_creditcardexpirationmonth'] < date('m')) {
							$isOK = false;
							$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
							if (strlen($invalid_cc_fields) > 0) {
								$invalid_cc_fields .= ', ';
							}
							$invalid_cc_fields .= $field->field_label;
							$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_A_VALID_CREDIT_CARD_DATE') . ' ( ' . $invalid_number_fields . ' ) '  );
							if ($dt == 0 && $this->_config_values['highlight_errors']) {
								if(!array_key_exists($field->id,$fieldsWithErrors)) {
									$fieldsWithErrors[$field->id] = array();
								}
								$fieldsWithErrors[$field->id][] = JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_A_VALID_CREDIT_CARD_DATE');
							}
						}
					}
				}
			}
		}
		// test for captcha
		if ( $isOK ) {
			if ( $profile->use_captcha == 1 || ($profile->use_captcha == 2 && $this->_user_id == 0) ) {
				switch($profile->captcha_type) {
					case 0:
						$session = JFactory::getSession();
						$captcha_code = $session->get( 'captcha_code_'.$pf );
						if (array_key_exists('captcha-code',$postData) && ( $captcha_code != $postData['captcha-code'] || strlen($postData['captcha-code']) == 0 )) {
							$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
							$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_SECURITY_CODE_IS_NOT_CORRECT') );
							if ($dt == 0 && $this->_config_values['highlight_errors']) {
								if(!array_key_exists('captcha-code',$fieldsWithErrors)) {
									$fieldsWithErrors['captcha-code'] = array();
								}
								$fieldsWithErrors['captcha-code'][] = JText::_('COM_AICONTACTSAFE_SECURITY_CODE_IS_NOT_CORRECT');
							}
						}
						break;
					case 1:
						JPluginHelper::importPlugin('content', 'captcha');
						$dispatcher = JDispatcher::getInstance();

						$session = JFactory::getSession();
						$_SESSION['securimage_code_value'] = $session->get('securimage_code_value');
						$captchaParam = new JParameter('returnType=boolean');
						$validateCaptcha = $dispatcher->trigger('onValidateForm',array($captchaParam));
						if (is_array($validateCaptcha)) {
							$validateCaptcha = implode('',$validateCaptcha);
						}
						if ($validateCaptcha !== true && $validateCaptcha != 1 && $validateCaptcha != '1') {
							$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
							$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_SECURITY_CODE_IS_NOT_CORRECT') );
							if ($dt == 0 && $this->_config_values['highlight_errors']) {
								if(!array_key_exists('captcha-code',$fieldsWithErrors)) {
									$fieldsWithErrors['captcha-code'] = array();
								}
								$fieldsWithErrors['captcha-code'][] = JText::_('COM_AICONTACTSAFE_SECURITY_CODE_IS_NOT_CORRECT');
							}
						}
						break;
				}
			}
		}
		if ($dt == 0 && $this->_config_values['highlight_errors'] && !$this->_app->_session->get( 'isOK:' . $this->_sTask)) {
			$r_id = JRequest::getInt( 'r_id' );
			$this->_app->_session->set( 'fieldsWithErrors:' . $this->_sTask . '_' . $profile->id . '_' . $r_id, $fieldsWithErrors );
		}

		return $postData;
	}

	// function to validate fields before writing them to the database
	function checkBeforeWrite($postData) {
		$postData = parent::checkBeforeWrite($postData);

		$datenow = JFactory::getDate();
		$postData['date_added'] = $datenow->toMySQL();
		$postData['last_update'] = $datenow->toMySQL();

		return $postData;
	}

	// function to determin the next link to redirect the page
	function getReturnLink($new_values = array(), $use_ajax = 0) {
		if ($use_ajax) {
			$link = '';
		} else {
			// check for a request to return to a specified web page
			$link = JRequest::getVar('return_to', '', 'return_to', 'string');
		}
		// if no link was specified, generate one
		if ( $link == '' ) {
			// read the registered return task
			$return_task = $this->_app->_session->get( 'return_task:' . $this->_sTask );
			// force the return_task variable to be an array
			if (!is_array($return_task)) {
				$return_task = array();
			}
			// make sure the profile is in the link
			$pf_added = false;
			// if ajax is used format=raw has to be added
			if ($use_ajax) {
				$new_values['task'] = 'ajaxform';
				$new_values['format'] = 'raw';
				$new_values['use_ajax'] = '1';
			} else {
				$new_values['task'] = 'display';
			}
			// make sure Itemid is added
			$Itemid = JRequest::getInt( 'Itemid' );
			if ( $Itemid > 0 ) {
				$new_values['Itemid'] = $Itemid;
			}
			// add/modify the values from last_task to new_values
			foreach($new_values as $par_key => $par_value) {
				$return_task[$par_key] = $par_value;
				if ($par_key == 'pf') {
					$pf_added = true;
				}
			}
			// if the profile is not added, add it
			if (!$pf_added) {
				$pf = JRequest::getVar('pf', 0, 'request', 'int');
				$return_task['pf'] = $pf;
			}
			// generate the link
			$link = 'index.php?option=com_aicontactsafe';
			foreach($return_task as $par_key => $par_value) {
				$link .= '&' . $par_key . '=' . $par_value;
			}
			$jfcookie = JRequest::getVar('jfcookie', null ,"COOKIE");
			$lang = '';
			if (isset($jfcookie["lang"]) && $jfcookie["lang"] != "") {
				$lang = JFilterInput::clean($jfcookie["lang"], 'cmd');
			}
			if (strlen($lang) == 0) {
				$lang = $this->_app->getUserState('application.lang', 'en');
				$lang = substr($lang,0,2);
			}
			$lang = JRequest::getCmd('lang', $lang);
			if (strlen(trim($lang)) > 0) {
				$link .= '&lang=' . $lang;
			}
			// make the link seo frindly only for the frontend and if AJAX is not used
			if ($this->_sef == 1 && $this->_backend == 0 && !$use_ajax) {
				$link = JRoute::_($link, false);
			}
		}
		return $link;
	}

	// function to determin the link of the last visited page
	function getLastLink($new_values = array(), $use_ajax = 0) {
		// read the registered last task
		$last_task = $this->_app->_session->get( 'last_task' );
		// force the last_task variable to be an array
		if (!is_array($last_task)) {
			$last_task = array();
		}
		// if ajax is used format=raw has to be added
		if ($use_ajax) {
			$new_values['task'] = 'ajaxform';
			$new_values['format'] = 'raw';
			$new_values['use_ajax'] = '1';
		} else {
			$new_values['task'] = 'display';
		}
		$pf = JRequest::getVar('pf', 0, 'request', 'int');
		$new_values['pf'] = $pf;
		// make sure Itemid is added
		$Itemid = JRequest::getInt( 'Itemid' );
		if ( $Itemid > 0 ) {
			$new_values['Itemid'] = $Itemid;
		}
		// add/modify the values from last_task to new_values
		foreach($new_values as $par_key => $par_value) {
			$last_task[$par_key] = $par_value;
		}
		// generate the link
		$link = 'index.php?option=com_aicontactsafe';
		foreach($last_task as $par_key => $par_value) {
			$link .= '&' . $par_key . '=' . $par_value;
		}
		$jfcookie = JRequest::getVar('jfcookie', null ,"COOKIE");
		$lang = '';
		if (isset($jfcookie["lang"]) && $jfcookie["lang"] != "") {
			$lang = JFilterInput::clean($jfcookie["lang"], 'cmd');
		}
		if (strlen($lang) == 0) {
			$lang = $this->_app->getUserState('application.lang', 'en');
			$lang = substr($lang,0,2);
		}
		$lang = JRequest::getCmd('lang', $lang);
		if (strlen(trim($lang)) > 0) {
			$link .= '&lang=' . $lang;
		}
		// make the link seo frindly only for the frontend and if AJAX is not used
		if ($this->_sef == 1 && $this->_backend == 0 && !$use_ajax) {
			$link = JRoute::_($link, false);
		}
		return $link;
	}	

	// function to send the email with the fields from the contact form
	function sendEmail() {
		// get the information entered into the contact form
		$postData = $this->readPostDataFromSession();
		
		// import joomla clases to manage file system
		jimport('joomla.filesystem.path');
		jimport('joomla.filesystem.file');

		// initialize the database
		$db = JFactory::getDBO();

		// get the path to attachments upload
		$upload_folder = str_replace('\\',DS,$this->_config_values['upload_attachments']);
		$upload_folder = str_replace('/',DS,$upload_folder);
		$upload_folder = str_replace('&#92;',DS,$upload_folder);
		$path_upload = JPATH_ROOT.DS.$upload_folder;

		// get the requested profile id
		$pf = $postData['pf'];
		$profile = $this->getProfile( $pf );

		// get the list of the fields from the contact form
		$fields = $this->readFields( $profile );
		// get the email entered into the contact form
		$from = $this->_config_values['default_email'];
		// get the name entered into the contact form
		$fromname = $this->_config_values['default_name'];
		// get the email address where to send the message, if none is specified in control panel, use the one in joomla
		$recipient = $profile->email_address;
		if (strlen($recipient) == 0) {
			$recipient = $this->_app->getCfg('mailfrom');
		}
		// get the prefix to add to the email subject, if none is specified use the name of the joomla site
		$subject_prefix = $profile->subject_prefix;
		if (strlen($subject_prefix) == 0) {
			$subject_prefix = $this->_app->getCfg('fromname');
		}
		// generate the email subject
		$subject = $this->_config_values['default_subject'];
		// check if the email is sent to the sender
		$send_to_sender = 0;
		// record $send_to_sender to register it into the database
		// check if the message should be always sent to the sender
		if ($profile->send_to_sender_field_id == -1) {
			$send_to_sender = 1;
		}
		$record_send_to_sender = $send_to_sender;
		// generate the body of the message
		if ($profile->email_mode) {
			$body = '<table border="0" cellpadding="0" cellspacing="2">';
			$body .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
		} else {
			$body = " \n\n ";
		}
		// generate the body of the message recorded into the database
		$body_recorded = '<table border="0" cellpadding="0" cellspacing="2">';
		$body_recorded .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
		// initialize email recipients list
		$email_recipients = array();
		// initialize attachments
		$file_attachments = array();
		// read the form fields
		foreach($fields as $field_key=>$field) {
			$field_value = '';
			$field_email = '';
			switch($field->field_type) {
				case 'TX':
					// Textbox
					$field_value = array_key_exists($field->name,$postData)?$postData[$field->name]:'';
					break;
				case 'CK':
					// Checkbox
					$field_value = (array_key_exists($field->name,$postData) && $postData[$field->name])?JText::_('COM_AICONTACTSAFE_CHECKED'):JText::_('COM_AICONTACTSAFE_UNCHECKED');
					break;
				case 'CB':
					// Combobox
					if (array_key_exists($field->name,$postData) && $postData[$field->name] > -1) {
						$field_values = explode(';',$this->revert_specialchars($field->field_values));
						$field_value = $field_values[$postData[$field->name]];
					} else {
						$field_value = '...';
					}
					break;
				case 'ED':
					// Editbox
					$field_value = array_key_exists($field->name,$postData)?$postData[$field->name]:'';
					if ($profile->email_mode) {
						$field_value = str_replace("\n",'<br />', $field_value);
					}
					break;
				case 'CL' :
					// Checkbox - List
					$postDataValue = array();
					if (array_key_exists($field->name,$postData) && is_array($postData[$field->name])) {
						$field_values = explode(';',$this->revert_specialchars($field->field_values));
						foreach($postData[$field->name] as $i=>$v) {
							if ( $v == 1 ) {
								$postDataValue[] = $field_values[$i];
							}
						}
					}
					$field_value = implode(',', $postDataValue);
					break;
				case 'RL' :
					// Radio - List
					$field_value = array_key_exists($field->name,$postData)?$postData[$field->name]:'';
					break;
				case 'DT' :
					// Date
					$field_value = array_key_exists($field->name,$postData)?$postData[$field->name]:'';
					$year = substr($field_value,0,4);
					$month = substr($field_value,5,2);
					$day = substr($field_value,8,2);
					switch($profile->custom_date_format) {
						case 'mdy':
							$field_value = $month . ' ' . $day . ' ' . $year;
							break;
						case 'ymd':
							$field_value = $year . ' ' . $month . ' ' . $day;
							break;
						case 'dmy':
						default :
							$field_value = $day . ' ' . $month . ' ' . $year;
							break;
					}
					break;
				case 'EM' :
					// Email
					$field_value = array_key_exists($field->name,$postData)?$postData[$field->name]:'';
					$field_email = trim($postData[$field->name]);
					if ($field->send_message && strlen($field_email) > 0) {
						$email_recipients[] = $field_email;
					}
					break;
				case 'EL' :
					// Email - List
					if (array_key_exists($field->name,$postData) && $postData[$field->name] > -1) {
						$field_values = explode(';',$this->revert_specialchars($field->field_values));
						$field_value = $field_values[$postData[$field->name]];
						$field_email = trim(substr($field_value, strpos($field_value,':')+1));
						if ($field->send_message && strlen($field_email) > 0) {
							$email_recipients[] = $field_email;
						}
						$field_value = substr($field_value, 0, strpos($field_value,':'));
					} else {
						$field_value = '...';
					}
					break;
				case 'JC' :
					// Joomla Contacts
					if (array_key_exists($field->name,$postData) && $postData[$field->name] > 0) {
						$query = 'SELECT email_to as email, name FROM #__contact_details WHERE published = 1 and id = ' . $postData[$field->name];
						$db->setQuery($query);
						$emails = $db->loadObjectList();
						$email = $emails[0];
						$field_email = trim($email->email);
						if ($field->send_message && strlen($field_email) > 0) {
							$email_recipients[] = $field_email;
						}
						$field_value = $email->name;
					} else {
						$field_value = '...';
					}
					break;
				case 'JU' :
					// Joomla Users
					if (array_key_exists($field->name,$postData) && $postData[$field->name] > 0) {
						$query = 'SELECT email, name FROM #__users WHERE block = 0 and id = ' . $postData[$field->name];;
						$db->setQuery($query);
						$emails = $db->loadObjectList();
						$email = $emails[0];
						$field_email = trim($email->email);
						if ($field->send_message && strlen($field_email) > 0) {
							$email_recipients[] = $field_email;
						}
						$field_value = $email->name;
					} else {
						$field_value = '...';
					}
					break;
				case 'SB' :
					// SOBI2 Entries
					if (array_key_exists($field->name,$postData) && $postData[$field->name] > 0) {
						$sobi_email_field = trim($field->field_values);
						if (strlen($sobi_email_field) == 0) {
							$sobi_email_field = 'field_email';
						}
						$query = 'SELECT it.title, d.data_txt as email FROM #__sobi2_language l LEFT JOIN #__sobi2_fields_data d ON l.fieldid = d.fieldid LEFT JOIN #__sobi2_item it ON d.itemid = it.itemid WHERE l.langKey = \'' . $sobi_email_field . '\' and l.sobi2Section = \'fields\' and d.itemid = ' . $postData[$field->name];
						$db->setQuery($query);
						$emails = $db->loadObjectList();
						$field_value = $emails[0]->title;
						$field_email = trim($emails[0]->email);
						if ($field->send_message && strlen($field_email) > 0) {
							$email_recipients[] = $field_email;
						}
					} else {
						$field_value = '...';
					}
					break;
				case 'HD':
					// Hidden
					$field_value = array_key_exists($field->name,$postData)?$postData[$field->name]:'';
					$field_value = $this->revert_specialchars($field_value);
					break;
				case 'SP':
					// Separator
					$field_value = '';
					break;
				case 'FL':
					// File
					$fields[$field_key]->fld_link = '';
					if ( strlen(trim($postData[$field->name.'_attachment_name'])) > 0 ) {
						$file_id = $postData[$field->name.'_attachment_id'];
						$file_name = trim($postData[$field->name.'_attachment_name']);
						$field_link = JURI::root().'index.php?option=com_aicontactsafe&sTask=message&task=download&id='.$file_id.'&format=raw';
						$field_value = '<a class="aiContactSafe_attachment" href="'.$field_link.'" target="_blank">'.$file_name.'</a>';
						$fields[$field_key]->fld_link = $field_link;

						$filepath = JPath::clean($path_upload.DS.$file_name);
						if ($this->_config_values['attach_to_email']) {
							$file_attachments[] = $filepath;
						}
					}
					break;
				case 'NO':
					// Number
					$field_value = array_key_exists($field->name,$postData)?$postData[$field->name]:'';
					break;
				case 'HE' :
					// Hidden Email
					$field->field_in_message = false;
					$he_value = $field->field_values;
					if (array_key_exists($field->name,$postData)) {
						$he_value = $this->arata_sir($postData[$field->name]);
					}
					$field_values = explode(';',$he_value);
					if (count($field_values) > 0) {
						foreach($field_values as $field_value) {
							if(strlen(trim($field_value)) > 0) {
								$email_recipients[] = $field_value;
							}
						}
					}
					$field_value = $field->field_values;
					break;
				case 'UQ':
					// Unique text
					if($this->useUqField()) {
						$field_value = array_key_exists($field->name,$postData)?$postData[$field->name]:'';
					} else {
						$field_value = '';
					}
					break;
				case 'CC':
					// Credit card
					if($this->useCcField()) {
						$field_value_creditcardtype = array_key_exists($field->name.'_creditcardtype',$postData)?$postData[$field->name.'_creditcardtype']:'';
						$field_value_creditcardnumber = array_key_exists($field->name.'_creditcardnumber',$postData)?$postData[$field->name.'_creditcardnumber']:'';
						$field_value_creditcardnumber = str_replace(' ','',str_replace('-','',$field_value_creditcardnumber));
						$field_value_creditcardnumber = substr($field_value_creditcardnumber,0,4).'-'.substr($field_value_creditcardnumber,4,4).'-'.substr($field_value_creditcardnumber,8,4).'-'.substr($field_value_creditcardnumber,12,4);
						$field_value_creditcardverification = array_key_exists($field->name.'_creditcardverification',$postData)?$postData[$field->name.'_creditcardverification']:'';
						$field_value_creditcardverification = trim(str_replace(' ','',$field_value_creditcardverification));
						$field_value_creditcardexpirationmonth = array_key_exists($field->name.'_creditcardexpirationmonth',$postData)?$postData[$field->name.'_creditcardexpirationmonth']:'';
						$field_value_creditcardexpirationyear = array_key_exists($field->name.'_creditcardexpirationyear',$postData)?$postData[$field->name.'_creditcardexpirationyear']:'';
						$field_value_body = $field_value_creditcardtype.':||:'.substr($field_value_creditcardnumber,0,14).'-****'.':||:'.$field_value_creditcardverification.':||:'.$field_value_creditcardexpirationmonth.':||:'.$field_value_creditcardexpirationyear;
						$field_value_body_recorded = $field_value_creditcardtype.':||:'.'****-****-****'.substr($field_value_creditcardnumber,14).':||:***:||:'.$field_value_creditcardexpirationmonth.':||:'.$field_value_creditcardexpirationyear;
					} else {
						$field_value = '';
					}
					break;
			}
			$field->field_label_message = $this->revert_specialchars($field->field_label_message);
			$field->label_message_parameters = $this->revert_specialchars($field->label_message_parameters);
			$fields[$field_key]->value_to_save = $field_value;

			$body_recorded .= '<tr><td><span ' . $field->label_message_parameters . '  >' . $field->field_label_message . '</span>';
			if ($field->field_type == 'CC') {
				if ($profile->email_mode) {
					$body .= '<tr><td><span ' . $field->label_message_parameters . '  >' . $field->field_label_message . '</span></td><td>&nbsp;</td><td>' . str_replace(':||:','<br />',$field_value_body) . '</td></tr>';
				} else {
					$body .= $field->field_label_message . " \n " . str_replace(':||:',"\n",$field_value_body) . " \n\n ";
				}
				$body_recorded .= '</td><td>&nbsp;</td><td>' . str_replace(':||:','<br />',$field_value_body_recorded) . '</td></tr>';
			} else {
				if ( $field->field_in_message ) {
					$fields[$field_key]->fld_value = $field_value;
					if ($profile->email_mode) {
						$body .= '<tr><td><span ' . $field->label_message_parameters . '  >' . $field->field_label_message . '</span></td><td>&nbsp;</td><td>' . $field_value . '</td></tr>';
					} else {
						if ($field->field_type == 'FL') {
							$body .= $field->field_label_message . " \n " . $field_link . " \n\n ";
						} else {
							$body .= $field->field_label_message . " \n " . $field_value . " \n\n ";
						}
					}
				} else {
					$body_recorded .= '</br><span style="color:#FF0000">(' . JText::_('COM_AICONTACTSAFE_NOT_SENT_IN_THE_EMAIL') . ')</span>';
					unset($fields[$field_key]);
				}
				$body_recorded .= '</td><td>&nbsp;</td><td>' . $field_value . '</td></tr>';
			}
			if ($profile->name_field_id == $field->id) {
				$fromname = $field_value;
			}
			if ($profile->email_field_id == $field->id) {
				$from = $field_email;
			}
			if ($profile->subject_field_id == $field->id) {
				$subject = $this->revert_specialchars($field_value);
			}
			if ($profile->send_to_sender_field_id == $field->id) {
				$send_to_sender = (array_key_exists($field->name,$postData) && $postData[$field->name])?1:0;
				$record_send_to_sender = $send_to_sender;
			}
		}
		if ($profile->email_mode) {
			$body .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
			$body .= '</table>';
		} else {
			$body .= " \n\n ";
		}
		$body_recorded .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
		$body_recorded .= '</table>';
		// select the type of the message to be sent
		$mode = $profile->email_mode?true:false;
		// reset cc and bcc
		$cc=null;
		$bcc=null;
		// check if we send to recipient
		$send_recipient = $profile->always_send_to_email_address;
		$isOK = true;

		// check if the email field is specified, if it's not use the default one in Joomla and disable the send_to_sender variable
		if (strlen($from) == 0) {
			$from = $this->_app->getCfg('mailfrom');
			$send_to_sender = 0;
		}
		// check if the name is specified, use the default one in joomla if it's not
		if (strlen($fromname) == 0) {
			$fromname = $this->_app->getCfg('fromname');
		}

		// for security reasons some servers don't accept using as the sender a different address as one installed on the server
		// for this reason $from and $fromname will be set as the default address of Joomla and the name of the site and two new fields will be used:
		// $replyto, $replytoname
		$replyto = $from;
		$replytoname = $fromname;
		if ($this->_config_values['set_sender_joomla']) {
			$from = $this->_app->getCfg('mailfrom');
			$fromname = $this->_app->getCfg('fromname');
		}

		// generate the subject with the profix
		$subject = $subject_prefix . ' ' . $subject;
		// check if the message have to be sent to the email address specified in the contraol panel
		if ($send_recipient || count($email_recipients) == 0 ) {
			$email_recipients[] = $recipient;
		}
		// check if the message has to be sent to the sender
		if ( $send_to_sender ) {
			$email_recipients[] = $replyto;
		}
		// remove duplicated email addresses
		$email_recipients = array_unique($email_recipients);

		// check if the spam control is activated and if the message should be blocked
		$spam_control = $this->checkSpam($body);

		// initialize the message id so the attached files can be deleted
		$message_id = 0;
		// if the feature is activated in control panel, record the message into the database 
		// and the spam control is disabled or the message is not spam or the message is recorded even if the message is spam
		if ($profile->record_message && ($spam_control == 1 or $spam_control == 2 or $spam_control == 3)) {
			// initialize the row variables
			$dataRow = JTable::getInstance('aicontactsafe_messages', 'Table');
			// fill the name, email, subject and send_to_sender used with this message
			$postData['name'] = isset($replytoname)?$replytoname:$fromname;
			$postData['email'] = isset($replyto)?$replyto:$from;
			$postData['subject'] = $this->replace_specialchars($subject);
			if ( $record_send_to_sender == JText::_('COM_AICONTACTSAFE_UNCHECKED') ) {
				$record_send_to_sender = 0;
			}
			$postData['send_to_sender'] = $record_send_to_sender?1:0;
			// fill the message and senders_ip fields into the form data
			$postData['message'] = $body_recorded;
			$postData['sender_ip'] = $_SERVER['REMOTE_ADDR'];
			// record the profile's id
			$postData['profile_id'] = $profile->id;
			// set the default status to the one defined in the profile
			$postData['status_id'] = $profile->default_status_id;
			// record the email destination
			$postData['email_destination'] = implode(',', $email_recipients);
			// record the user id ( owner of the message )
			$postData['user_id'] = $this->_user_id;
			// bind the data sent from the form to the table fields
			$dataRow->bind($postData);
			// store the message
			$dataRow->store();

			// record the message id so the attached files can be deleted
			$message_id = (int)$dataRow->id;
			// generate a cookie with the last message id sent
			setcookie('aiContactSafeMessageId',$message_id,time()+60*10);

			$query = 'UPDATE #__aicontactsafe_messagefiles SET message_id = ' . $message_id . ' WHERE message_id = 0 and r_id = '.$postData['r_id'];
			$db->setQuery($query);
			$db->query();

			if ($profile->record_fields) {
				// initialize the row variables for the field values
				$fieldRow = JTable::getInstance('aicontactsafe_fieldvalues', 'Table');
				// save fields values
				foreach($fields as $field_key=>$field) {
					$fieldRow->id = null;
					$fieldRow->field_id = $field->id;
					$fieldRow->message_id = $dataRow->id;
					$fieldRow->field_value = $field->value_to_save;
					$datenow = JFactory::getDate();
					$fieldRow->date_added = $datenow->toMySQL();
					$fieldRow->last_update = $datenow->toMySQL();
					$fieldRow->published = 1;
					$fieldRow->checked_out = 0;
					$fieldRow->checked_out_time = '0000-00-00';
	
					// store the field
					$fieldRow->store();
				}
			}
		}

		// if the spam control is disabled or the message is not spam
		if ( $spam_control == 1 or $spam_control == 2 ) {
			// check if the use of the mail template is activated
			if ($profile->use_mail_template) {
				// generate the body of the message
				ob_start();
				// determine if to use the mail template from the template or from the component
				$template_name = $this->_app->getTemplate();
				$tPath = JPATH_ROOT.DS.'templates'.DS.$template_name.DS.'html'.DS.'com_aicontactsafe'.DS.'mail'.DS.'mail_'.$profile->id.'.php';

				$nameMailTemplate = '';
				if (JFile::exists($tPath)) {
					$nameMailTemplate = $tPath;
				} else {
					$tPath = JPATH_ROOT.DS.'media'.DS.'aicontactsafe'.DS.'mailtemplates'.DS.'mail_'.$profile->id.'.php';
					if (JFile::exists($tPath)) {
						$nameMailTemplate = $tPath;
					} else {
						$nameMailTemplate = JPATH_ROOT.DS.'media'.DS.'aicontactsafe'.DS.'mailtemplates'.DS.'mail.php';
					}
				}
				
				// run the template
				include($nameMailTemplate);
				$body = ob_get_contents();
				@ob_end_clean();
			}
			// clear body and subject
			jimport( 'joomla.mail.helper' );
			$body = JMailHelper::cleanBody($body);
			$subject = JMailHelper::cleanSubject($subject);

			// if there are emails or contacts in the custom fields, send the message to them
			if ( count($email_recipients) > 0 ) {
				foreach($email_recipients as $email_recipient) {
					// clean email address
					$email_recipient = JMailHelper::cleanAddress($email_recipient);
					// send the message
					$isOK = JUtility::sendMail($from, $fromname, $email_recipient, $subject, $body, $mode, $cc, $bcc, $file_attachments, $replyto, $replytoname);
				}
			}
		} else {
			// if the message is spam and this feature is activated ban the IP that sent it
			if ( $this->_config_values['ban_ips_blocked_words'] ) {
				$this->ban_ip($_SERVER['REMOTE_ADDR']);
			}
			$email_recipients = array(JText::_('COM_AICONTACTSAFE_MESSAGE_BANNED'));
		}

		if ( $isOK && $this->_config_values['delete_after_sent'] ) {
			$this->deleteFileAfterMessageSent( $message_id, $postData );
		}
		if ( $isOK ) {
			$this->resetFormFields();
		}
		return $isOK;
	}

	function getProfile( $pf = 0 ) {
		$selected_profile = null;
		$default_profile = null;
		if ( $pf == 0 ) {
			// get the requested profile id
			$pf = JRequest::getVar('pf', 0, 'request', 'int');
		}
		if ( $pf == 0 ) {
			$postData = $this->readPostDataFromSession();
			if (is_array($postData) && array_key_exists('pf', $postData)) {
				$pf = (int)$postData['pf'];
			}
		}

		// initialize the database
		$db = JFactory::getDBO();

		// get the profile values
		$query = 'SELECT * FROM #__aicontactsafe_profiles WHERE ( id = ' . $pf . ' and published = 1 ) or set_default = 1 ORDER by set_default';
		$db->setQuery( $query );
		$profiles = $db->loadObjectList();
		if ( count($profiles) > 0 ) {
			// read the profiles
			foreach($profiles as $profile) {
				if ( $profile->id == $pf ) {
					$selected_profile = $profile;
				}
				if ( $profile->set_default ) {
					$default_profile = $profile;
				}
			}
			// if no profile is selected, use the default one
			if (!$selected_profile) {
				$selected_profile = $default_profile;
			}
		}

		$selected_profile->required_field_mark = $this->revert_specialchars($selected_profile->required_field_mark);
		
		return $selected_profile;
	}

	// get the name of the month
	function getMonth( $i = 0 ) {
		$monthName = '';
		switch($i) {
			case 1:
				$monthName = JText::_('COM_AICONTACTSAFE_JANUARY');
				break;
			case 2:
				$monthName = JText::_('COM_AICONTACTSAFE_FEBRUARY');
				break;
			case 3:
				$monthName = JText::_('COM_AICONTACTSAFE_MARCH');
				break;
			case 4:
				$monthName = JText::_('COM_AICONTACTSAFE_APRIL');
				break;
			case 5:
				$monthName = JText::_('COM_AICONTACTSAFE_MAY');
				break;
			case 6:
				$monthName = JText::_('COM_AICONTACTSAFE_JUNE');
				break;
			case 7:
				$monthName = JText::_('COM_AICONTACTSAFE_JULY');
				break;
			case 8:
				$monthName = JText::_('COM_AICONTACTSAFE_AUGUST');
				break;
			case 9:
				$monthName = JText::_('COM_AICONTACTSAFE_SEPTEMBER');
				break;
			case 10:
				$monthName = JText::_('COM_AICONTACTSAFE_OCTOBER');
				break;
			case 11:
				$monthName = JText::_('COM_AICONTACTSAFE_NOVEMBER');
				break;
			case 12:
				$monthName = JText::_('COM_AICONTACTSAFE_DECEMBER');
				break;
		}
		return $monthName;
	}

	// function to check if the spam control is activated or the message is spam 
	// returns 
	//		1 - the control is disabled
	//		2 - the message is not spam
	// 		3 - the message is spam but the message is recorded
	// 		4 - the message is spam and the message is not recorded
	function checkSpam( $body = '' ) {
		$spam_response = 1;
		if ($this->_config_values['activate_spam_control']) {
			$is_spam = false;
			$block_words = explode(';',$this->_config_values['block_words']);
			foreach($block_words as $word) {
				if ( strlen(trim($word)) > 0 && !strpos($body,$word) === false ) {
					$is_spam = true;
					break;
				}
			}
			if ($is_spam) {
				if ($this->_config_values['record_blocked_messages']) {
					$spam_response = 3;
				} else {
					$spam_response = 4;
				}
			} else {
				$spam_response = 2;
			}
		}
		return $spam_response;
	}

	function checkBanIp() {
		$ban_ip = false;
		if ( $this->_config_values['activate_ip_ban'] ) {
			$current_ip = $_SERVER['REMOTE_ADDR'];
			$check_ip = explode('.',$current_ip);
			$ips_to_ban = explode(';',$this->_config_values['ban_ips']);
			foreach($ips_to_ban as $ip_to_ban) {
				if ( strlen(trim($ip_to_ban)) > 0 ) {
					$check_ip_to_ban = explode('.',$ip_to_ban);
					if ( trim($check_ip[1]) === trim($check_ip_to_ban[1]) && 
					    ( trim($check_ip[2]) === trim($check_ip_to_ban[2]) || trim($check_ip_to_ban[2]) === '*' ) && 
						( trim($check_ip[3]) === trim($check_ip_to_ban[3]) || trim($check_ip_to_ban[3]) === '*' ) && 
						( trim($check_ip[4]) === trim($check_ip_to_ban[4]) || trim($check_ip_to_ban[4]) === '*' )) {
						$ban_ip = true;
					}
				}
				if ( $ban_ip ) {
					break;
				}
			}
		}
		if ( $this->_config_values['activate_ip_ban'] && $this->_config_values['maximum_messages_ban_ip'] > 0 && $this->_config_values['maximum_minutes_ban_ip'] > 0 ) {
			$current_ip = $_SERVER['REMOTE_ADDR'];

			// initialize different variables
			$db = JFactory::getDBO();

			// get all messages from the current IP in the last "maximum_minutes_ban_ip" minutes
			$query = 'SELECT count(*) as count_messages FROM #__aicontactsafe_messages WHERE sender_ip = \''.$current_ip.'\' and date_added > DATE_SUB(now(),INTERVAL '.$this->_config_values['maximum_minutes_ban_ip'].' minute)';

			$db->setQuery($query);
			$count_messages = $db->loadResult();
			if ( $count_messages >= $this->_config_values['maximum_messages_ban_ip'] ) {
				$this->ban_ip($current_ip);
				$ban_ip = true;
			}
		}

		return $ban_ip;
	}

	// download a file attached to a message
	function downloadFile() {
		// get the id of the file
		$id = JRequest::getVar('id', 0, 'request', 'int');
		// get the path to attachments upload
		$upload_folder = str_replace('\\',DS,$this->_config_values['upload_attachments']);
		$upload_folder = str_replace('/',DS,$upload_folder);
		$upload_folder = str_replace('&#92;',DS,$upload_folder);
		$path_upload = JPATH_ROOT.DS.$upload_folder;
		// initialize the database
		$db = JFactory::getDBO();
		// get the name of the file to download
		$query = 'SELECT name FROM #__aicontactsafe_messagefiles where id = ' . $id;
		$db->setQuery($query);
		$file_name = $db->loadResult();
		$file = $path_upload.DS.$file_name;

		// start the file download
		if ( strlen(trim($file_name)) > 0 && file_exists($file) ){
			// Make sure there's not anything else left for download
			$this->ob_clean_all(); 

			header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.basename($file));
		    header('Content-Transfer-Encoding: binary');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file));
		    @ob_clean();
		    flush();

			readfile($file);
			exit;
		} else {
			echo '<script type="text/javascript" language="javascript">alert(\''.JText::_('COM_AICONTACTSAFE_FILE_WAS_DELETED').'\');</script>';
		}
	}

	// function to ban and IP and notify the administrator
	function ban_ip( $ips_list = '' ) {
		$ips_to_ban = explode(';',$ips_list);
		$ips_banned = explode(';',$this->_config_values['ban_ips']);
		$ips_banned = array_merge($ips_banned, $ips_to_ban);
		$ips_banned = array_unique($ips_banned);
		asort($ips_banned);

		$ban_ips = implode(';',$ips_banned);
		if (substr($ban_ips,0,1) == ';') {
			$ban_ips = substr($ban_ips,1);
		}

		// initialize different variables
		$db = JFactory::getDBO();

		$query = 'update `#__aicontactsafe_config` set config_value = \'' . $ban_ips . '\' where config_key = \'ban_ips\'';
		$db->setQuery( $query );
		$isOK = $db->query();
		if ($isOK) {
			// send a notification to the administrator
			$from = $this->_app->getCfg('mailfrom');
			$fromname = $this->_app->getCfg('fromname');
			$email_recipient = $this->_app->getCfg('mailfrom');
			$subject = JText::_('COM_AICONTACTSAFE_IP_AUTOMATICALLY_BLOCKED');
			$body = JText::_('COM_AICONTACTSAFE_IP_AUTOMATICALLY_BLOCKED') . '<br />' . $ips_list . '<br />';
			$mode = true;
			$isOK = JUtility::sendMail($from, $fromname, $email_recipient, $subject, $body, $mode);
		} else {
			$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}
		return true;
	}

	// function to delete attached files after the message is sent
	function deleteFileAfterMessageSent( $message_id = 0, $postData ) {
		$message_id = (int)$message_id;
		// initialize the database
		$db = JFactory::getDBO();
		// import joomla clases to manage file system
		jimport('joomla.filesystem.path');
		jimport('joomla.filesystem.file');
		// get the path to attachments upload
		$upload_folder = str_replace('\\',DS,$this->_config_values['upload_attachments']);
		$upload_folder = str_replace('/',DS,$upload_folder);
		$upload_folder = str_replace('&#92;',DS,$upload_folder);
		$path_upload = JPATH_ROOT.DS.$upload_folder;
		// get the files to delete
		$query = 'SELECT id, name FROM #__aicontactsafe_messagefiles WHERE message_id = '.$message_id.' and r_id = '.$postData['r_id'];
		$db->setQuery($query);
		$files = $db->loadObjectList();
		if (count($files) > 0) {
			foreach($files as $file) {
				$delete_file = $path_upload.DS.$file->name;
				JFile::delete($delete_file);
				$query = 'DELETE FROM #__aicontactsafe_messagefiles WHERE id = '.$file->id;
				$db->setQuery($query);
				$db->query();
			}
		}
	}

}
