<?php
/**
 * @version     $Id$ 2.0.14
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.13
 * - fixed the problem with CSS file when adding a new profile ( was not generated )
 * added/fixed in version 2.0.14
 * - filter variables read with JRequest::getVar
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the control_panel model class of aiContactSafe
class AiContactSafeModelProfiles extends AiContactSafeModelDefault {

	// construct function, it will iniaize the class variables
	function __construct( $default = array() )	{
		parent::__construct( $default );
		// if no order is used, use the 'date_added' field
		if (strlen($this->filter_order) == 0) {
			$this->filter_order = 'date_added';
			$this->filter_order_Dir = 'ASC';
		}
	}

	// function to check/add/modify different fields before writing them to the database
	function checkBeforeWrite($postData) {
		$postData = parent::checkBeforeWrite($postData);
		switch( true ) {
			case $this->_task == 'save_contact' or $this->_task == 'apply_contact' :
				$postData['contact_info'] = JRequest::getVar('contact_info', '', 'post', 'string', JREQUEST_ALLOWRAW);
				$postData['contact_info'] = $this->replace_specialchars($postData['contact_info']);
				$postData['plg_contact_info'] = (array_key_exists('plg_contact_info',$postData) && $postData['plg_contact_info'])?1:0;
				break;
			case $this->_task == 'save_css' or $this->_task == 'apply_css' :
				$postData['use_message_css'] = (array_key_exists('use_message_css',$postData) && $postData['use_message_css'])?1:0;
				$postData['profile_css_code'] = JRequest::getVar('profile_css_code', '', 'post', 'string', JREQUEST_ALLOWRAW);
				break;
			case $this->_task == 'save_email' or $this->_task == 'apply_email' :
				$postData['use_mail_template'] = (array_key_exists('use_mail_template',$postData) && $postData['use_mail_template'])?1:0;
				$postData['mail_template'] = JRequest::getVar('mail_template', '', 'post', 'string', JREQUEST_ALLOWRAW);
				break;
			default :
				// check if a name was entered
				if (strlen(trim($postData['name'])) == 0) {
					$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
					$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_THE_NAME_OF_THE_PROFILE') );
				}

				$postData['use_ajax'] = (array_key_exists('use_ajax',$postData) && $postData['use_ajax'])?1:0;
				$postData['captcha_backgroundTransparent'] = (array_key_exists('captcha_backgroundTransparent',$postData) && $postData['captcha_backgroundTransparent'])?1:0;
				$postData['always_send_to_email_address'] = (array_key_exists('always_send_to_email_address',$postData) && $postData['always_send_to_email_address'])?1:0;
				$postData['record_message'] = (array_key_exists('record_message',$postData) && $postData['record_message'])?1:0;
				$postData['record_fields'] = (array_key_exists('record_fields',$postData) && $postData['record_fields'])?1:0;
				if ($postData['record_fields']) {
					$postData['record_message'] = 1;
				}
		
				$postData['use_random_letters'] = (array_key_exists('use_random_letters',$postData) && $postData['use_random_letters'])?1:0;
		
				$postData['required_field_mark'] = JRequest::getVar('required_field_mark', '', 'post', 'string', JREQUEST_ALLOWHTML);
				$postData['required_field_mark'] = $this->replace_specialchars($postData['required_field_mark']);
		
				$postData['published'] = (array_key_exists('published',$postData) && $postData['published'])?1:0;
				$postData['set_default'] = (array_key_exists('set_default',$postData) && $postData['set_default'])?1:0;
				if ( $postData['set_default'] == 1 ) {
					$postData['published'] = 1;
					$this->resetDefaultProfile();
				} else {
					$postData['set_default'] = $this->checkDefaultProfile( $postData['id'] );
				}
				if ( array_key_exists('all_fields',$postData) && $postData['all_fields'] ) {
					$postData['active_fields'] = 0;
				} else {
					$fieldError = false;
					if ( $postData['name_field_id'] >0 && array_search($postData['name_field_id'],$postData['select_fields']) === false ) {
						$postData['select_fields'][] = $postData['name_field_id'];
						$fieldError = true;
					}
					if ( $postData['email_field_id'] > 0 && array_search($postData['email_field_id'],$postData['select_fields']) === false ) {
						$postData['select_fields'][] = $postData['email_field_id'];
						$fieldError = true;
					}
					if ( $postData['subject_field_id'] > 0 && array_search($postData['subject_field_id'],$postData['select_fields']) === false ) {
						$postData['select_fields'][] = $postData['subject_field_id'];
						$fieldError = true;
					}
					if ( $postData['send_to_sender_field_id'] > 0 && array_search($postData['send_to_sender_field_id'],$postData['select_fields']) === false ) {
						$postData['select_fields'][] = $postData['send_to_sender_field_id'];
						$fieldError = true;
					}
					if ( $fieldError ) {
						$this->_app->enqueueMessage(JText::_('COM_AICONTACTSAFE_SELECTED_FIELDS_ERROR'), 'error');
					}
					$postData['active_fields'] = implode(',', $postData['select_fields']);
				}
				// read the order of each field that can be selected
				$fields_id = array();
				$select_fields_count = $postData['select_fields_count'];
				for($i=0;$i<$select_fields_count;$i++) {
					$fields_id[$postData['order_field_id_'.$i]] = $postData['order_field_'.$i];
				}
				asort($fields_id);
				foreach($fields_id as $key=>$order) {
					$fields_id[$key] = $key;
				}
				$postData['fields_order'] = implode(',',$fields_id);

				break;
		}

		return $postData;
	}

	// function to reset the default profile
	function resetDefaultProfile() {
		// initialize the database
		$db = JFactory::getDBO();
		// reset field set_default to 0 for all records
		$query = 'UPDATE `#__aicontactsafe_profiles` SET set_default = 0';
		$db->setQuery( $query );
		$db->query();
	}

	// function to check if the default profile was deactivated
	function checkDefaultProfile( $id = 0 ) {
		$id = (int)$id;
		// initialize the database
		$db = JFactory::getDBO();
		// reset field set_default to 0 for all records
		$query = 'SELECT set_default FROM `#__aicontactsafe_profiles` WHERE id = '.$id;
		$db->setQuery( $query );
		$set_default = $db->loadResult();
		if ($set_default == 1) {
			$this->_app->enqueueMessage(JText::_('COM_AICONTACTSAFE_DEACTIVATE_DEFAULT_ERROR'), 'error');
		}
		return $set_default;
	}

	//function to write data in other tables then the default one of the current sTask
	function writeOtherTables( $postData = array(), $id = 0 ) {
		// get the information in the form
		$id = (int)$id;
		$name = $postData['name'];
		$meta_description = $postData['meta_description'];
		$meta_keywords = $postData['meta_keywords'];
		$meta_robots = $postData['meta_robots'];
		$thank_you_message = JRequest::getVar('thank_you_message', '', 'post', 'string', JREQUEST_ALLOWHTML);
		if (strlen(trim($thank_you_message)) == 0) {
			$thank_you_message = '&nbsp;';
		}
		$thank_you_message = $this->replace_specialchars($thank_you_message);
		$required_field_notification = JRequest::getVar('required_field_notification', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$required_field_notification = $this->replace_specialchars($required_field_notification);

		// initialize the database
		$db = JFactory::getDBO();

		// save meta_description
		$query = 'SELECT id FROM #__aicontactsafe_contactinformations WHERE profile_id = ' . $id . ' and info_key = \'meta_description\'';
		$db->setQuery( $query );
		$added = $db->loadResult();
		if ($added > 0) {
			$query = 'UPDATE `#__aicontactsafe_contactinformations` set info_label = \'meta_description (' . $name . ')\', info_value = \'' . $meta_description . '\' WHERE profile_id = ' . $id . ' and info_key = \'meta_description\'';
		} else {
			$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES (null, ' . $id . ', \'meta_description\', \'meta_description (' . $name . ')\', \'' . $meta_description . '\')';
		}
		$db->setQuery( $query );
		$db->query();

		// save meta_keywords
		$query = 'SELECT id FROM #__aicontactsafe_contactinformations WHERE profile_id = ' . $id . ' and info_key = \'meta_keywords\'';
		$db->setQuery( $query );
		$added = $db->loadResult();
		if ($added > 0) {
			$query = 'UPDATE `#__aicontactsafe_contactinformations` set info_label = \'meta_keywords (' . $name . ')\', info_value = \'' . $meta_keywords . '\' WHERE profile_id = ' . $id . ' and info_key = \'meta_keywords\'';
		} else {
			$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES (null, ' . $id . ', \'meta_keywords\', \'meta_keywords (' . $name . ')\', \'' . $meta_keywords . '\')';
		}
		$db->setQuery( $query );
		$db->query();

		// save meta_robots
		$query = 'SELECT id FROM #__aicontactsafe_contactinformations WHERE profile_id = ' . $id . ' and info_key = \'meta_robots\'';
		$db->setQuery( $query );
		$added = $db->loadResult();
		if ($added > 0) {
			$query = 'UPDATE `#__aicontactsafe_contactinformations` set info_label = \'meta_robots (' . $name . ')\', info_value = \'' . $meta_robots . '\' WHERE profile_id = ' . $id . ' and info_key = \'meta_robots\'';
		} else {
			$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES (null, ' . $id . ', \'meta_robots\', \'meta_robots (' . $name . ')\', \'' . $meta_robots . '\')';
		}
		$db->setQuery( $query );
		$db->query();

		// save thank you message
		$query = 'SELECT id FROM #__aicontactsafe_contactinformations WHERE profile_id = ' . $id . ' and info_key = \'thank_you_message\'';
		$db->setQuery( $query );
		$added = $db->loadResult();
		if ($added > 0) {
			$query = 'UPDATE `#__aicontactsafe_contactinformations` set info_label = \'thank_you_message (' . $name . ')\', info_value = \'' . $thank_you_message . '\' WHERE profile_id = ' . $id . ' and info_key = \'thank_you_message\'';
		} else {
			$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES (null, ' . $id . ', \'thank_you_message\', \'thank_you_message (' . $name . ')\', \'' . $thank_you_message . '\')';
		}
		$db->setQuery( $query );
		$db->query();

		// save required_field_notification
		$query = 'SELECT id FROM #__aicontactsafe_contactinformations WHERE profile_id = ' . $id . ' and info_key = \'required_field_notification\'';
		$db->setQuery( $query );
		$added = $db->loadResult();
		if ($added > 0) {
			$query = 'UPDATE `#__aicontactsafe_contactinformations` set info_label = \'required_field_notification (' . $name . ')\', info_value = \'' . $required_field_notification . '\' WHERE profile_id = ' . $id . ' and info_key = \'required_field_notification\'';
		} else {
			$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES (null, ' . $id . ', \'required_field_notification\', \'required_field_notification (' . $name . ')\', \'' . $required_field_notification . '\')';
		}
		$db->setQuery( $query );
		$db->query();

		// if the profile was just added create the css and email template files 
		if ($postData['id'] != $id) {
			// write contact information
			$postData['display_format'] = 0;
			$postData['plg_contact_info'] = 0;
			$postData['contact_info'] = '';
			$this->writeContactInformation($postData,$id);

			// write CSS
			$postData['use_message_css'] = 1;
			$postData['profile_css_code'] = $this->readProfileCssFile();
			$postData['profile_css_code'] = str_replace('aiContactSafe_mainbody_1 ', 'aiContactSafe_mainbody_'.$id.' ', $postData['profile_css_code']);
			$this->writeCSS($postData,$id);

			// write email template
			$postData['use_mail_template'] = 1;
			$postData['mail_template'] = $this->readMailTemplate();
			$this->writeEmail($postData,$id);
		}

		return true;
	}

	// function to get the contact informations of the current profile
	function getContactInformation( $profile_id = 0 ) {
		// initialize contact informations
		$contact_info = array();
		$contact_info['contact_info'] = '';
		$contact_info['meta_description'] = '';
		$contact_info['meta_keywords'] = '';
		$contact_info['meta_robots'] = '';
		$contact_info['thank_you_message'] = '';
		$contact_info['required_field_notification'] = JText::_('COM_AICONTACTSAFE_FIELDS_MARKED_WITH').' %mark% '.JText::_('COM_AICONTACTSAFE_ARE_REQUIRED').'.';

		// initialize the database
		$db = JFactory::getDBO();

		// get contact informations
		$query = 'SELECT info_key, info_value FROM #__aicontactsafe_contactinformations WHERE profile_id = ' . $profile_id;
		$db->setQuery( $query );
		$records = $db->loadObjectList();
		if ( count($records) > 0 ) {
			foreach($records as $record) {
				$record->info_value = $this->revert_specialchars($record->info_value);
				switch($record->info_key) {
					case 'contact_info' :
						$contact_info['contact_info'] = $record->info_value;
						break;
					case 'meta_description' :
						$contact_info['meta_description'] = $record->info_value;
						break;
					case 'meta_keywords' :
						$contact_info['meta_keywords'] = $record->info_value;
						break;
					case 'meta_robots' :
						$contact_info['meta_robots'] = $record->info_value;
						break;
					case 'thank_you_message' :
						$contact_info['thank_you_message'] = $record->info_value;
						break;
					case 'required_field_notification' :
						$contact_info['required_field_notification'] = $record->info_value;
						break;
				}
			}
		}
		
		return $contact_info;
	}

	// function to write the contact information
	function writeContactInformation( $postData = array(), $id = 0 ) {
		// check to see if any postdata was sent
		if (count($postData) == 0) {
			$postData = $this->readPostDataFromSession();
			$id = $postData['id'];
		}
		// get the information in the form
		$id = (int)$id;
		$name = $this->getProfileName($id);

		// initialize the database
		$db = JFactory::getDBO();

		// save contact_info in the profiles table
		$query = 'UPDATE `#__aicontactsafe_profiles` set display_format = '.$postData['display_format'].', plg_contact_info = '.$postData['plg_contact_info'].' WHERE id = ' . $id;
		$db->setQuery( $query );
		$db->query();

		// save contact_info
		$query = 'SELECT id FROM #__aicontactsafe_contactinformations WHERE profile_id = ' . $id . ' and info_key = \'contact_info\'';
		$db->setQuery( $query );
		$added = $db->loadResult();
		if ($added > 0) {
			$query = 'UPDATE `#__aicontactsafe_contactinformations` set info_label = \'contact_info (' . $name . ')\', info_value = \'' . $postData['contact_info'] . '\' WHERE profile_id = ' . $id . ' and info_key = \'contact_info\'';
		} else {
			$query = 'INSERT INTO `#__aicontactsafe_contactinformations` (`id`, `profile_id`, `info_key`, `info_label`, `info_value`) VALUES (null, ' . $id . ', \'contact_info\', \'contact_info (' . $name . ')\', \'' . $postData['contact_info'] . '\')';
		}
		$db->setQuery( $query );
		$db->query();

		return true;
	}

	// function to write the CSS file
	function writeCSS( $postData = array(), $id = 0 ) {
		// check to see if any postdata was sent
		if (count($postData) == 0) {
			$postData = $this->readPostDataFromSession();
			$id = $postData['id'];
		}
		// get the information in the form
		$id = (int)$id;

		// initialize the database
		$db = JFactory::getDBO();

		// save profile field use_message_css
		$query = 'UPDATE `#__aicontactsafe_profiles` set use_message_css = '.$postData['use_message_css'].' WHERE id = ' . $id;
		$db->setQuery( $query );
		$db->query();

		// import joomla clases to manage file system
		jimport('joomla.filesystem.file');
		// write the css code of the profile
		$css_file = JPath::clean(JPATH_ROOT.'/'.'media'.'/'.'aicontactsafe'.'/'.'cssprofiles'.'/'.'profile_css_'.$id.'.css');
		$postData['profile_css_code'] = str_replace('aiContactSafe_mainbody_1 ', 'aiContactSafe_mainbody_'.$id.' ', $postData['profile_css_code']);
		JFile::write($css_file, $this->revert_specialchars($postData['profile_css_code']));

		return true;
	}

	// function to write the email template file
	function writeEmail( $postData = array(), $id = 0 ) {
		// check to see if any postdata was sent
		if (count($postData) == 0) {
			$postData = $this->readPostDataFromSession();
			$id = $postData['id'];
		}
		// get the information in the form
		$id = (int)$id;

		// initialize the database
		$db = JFactory::getDBO();

		// save profile field use_mail_template
		$query = 'UPDATE `#__aicontactsafe_profiles` set use_mail_template = '.$postData['use_mail_template'].' WHERE id = ' . $id;
		$db->setQuery( $query );
		$db->query();

		// import joomla clases to manage file system
		jimport('joomla.filesystem.file');
		// write the mail template of the profile
		$mail_file = JPath::clean(JPATH_ROOT.'/'.'media'.'/'.'aicontactsafe'.'/'.'mailtemplates'.'/'.'mail_'.$id.'.php');
		JFile::write($mail_file, $this->revert_specialchars($postData['mail_template']));

		return true;
	}

	// function to retrive the fields to select the ones active in a profile
	function getFields( $active_fields = '', $profile_id = 0 ) {
		$active_fields = explode(',',$active_fields);
		// initialize the response array
		$fields = array();
		// initialize the database
		$db = JFactory::getDBO();
		// get the order of fields from the profile
		$query = 'SELECT fields_order FROM #__aicontactsafe_profiles WHERE id = '.(int)$profile_id;
		$db->setQuery( $query );
		$fields_order = $db->loadResult();
		$fields_order = explode(',',$fields_order);

		// get all the fields
		$query = 'SELECT id, name, field_label FROM #__aicontactsafe_fields ORDER BY ordering';
		$db->setQuery( $query );
		$records = $db->loadObjectList();
		// generate the response array
		$unsorted_fields = array();
		foreach($records as $record) {
			$unsorted_fields[$record->id] = array('id'=>$record->id, 'name'=>$record->name, 'field_label'=>$record->field_label, 'selected'=>(array_search($record->id, $active_fields) === false)?'0':'1');
		}
		// sort the response array
		foreach($fields_order as $field_id) {
			if (array_key_exists($field_id, $unsorted_fields)) {
				$fields[] = $unsorted_fields[$field_id];
				unset($unsorted_fields[$field_id]);
			}
		}
		foreach($unsorted_fields as $field) {
			$fields[] = $field;
		}
		return $fields;
	}

	// function to generate the condition records have to respect to be selected for deletion
	function getDeleteWhere($cids = '-1') {
		// initialize the database
		$db = JFactory::getDBO();
		// get the default record
		$query = 'SELECT id FROM `#__aicontactsafe_profiles` WHERE set_default = 1';
		$db->setQuery( $query );
		$id = $db->loadResult();

		$ctablename = $this->getTableName($this->_sTask, 'getDeleteWhere');
		$cids = explode(',', $cids);
		$countCids = count($cids);
		for($i=0;$i<$countCids;$i++){
			if ( $cids[$i] == $id ) {
				$this->_app->enqueueMessage(JText::_('COM_AICONTACTSAFE_DEFAULT_PROFILE_DELETE_ERROR'), 'error');
				$cids[$i] = 0;
			}
		}
		$cids = implode(',', $cids);
		$where = ' where ' . $ctablename . '.id IN ( ' . $cids . ' ) AND set_default = 0';
		return $where;
	}

	// function to delete selected records
	function deleteData() {
		parent::deleteData();

		// initialize different variables
		$db = JFactory::getDBO();
		// read the ids of the records seleted for deletion
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count($cid) > 0) {
			$profiles_ids = $cid;
			$cids = implode(',', $cid);
		} else {
			$profiles_ids = null;
			$cids = '-1';
		}

		$query = 'DELETE FROM `#__aicontactsafe_contactinformations` where profile_id IN ( '.$cids.' )';
		// delete records
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}

		if (is_array($profiles_ids)) {
			// import joomla clases to manage file system
			jimport('joomla.filesystem.file');
			foreach($profiles_ids as $pf_id){
				$css_file = JPath::clean(JPATH_ROOT.'/'.'media'.'/'.'aicontactsafe'.'/'.'cssprofiles'.'/'.'profile_css_'.$pf_id.'.css');
				if (JFile::exists($css_file)) {
					JFile::delete($css_file);
				}
				$mail_file = JPath::clean(JPATH_ROOT.'/'.'media'.'/'.'aicontactsafe'.'/'.'mailtemplates'.'/'.'mail_'.$pf_id.'.php');
				if (JFile::exists($mail_file)) {
					JFile::delete($mail_file);
				}
			}
		}

		return true;
	}

	// function to read the content of the css file of a profile
	function readProfileCssFile( $id = 0 ) {
		$css_code = '';
		// if id is 0, then a new profile is edited and the default CSS should be read
		// import joomla clases to manage file system
		jimport('joomla.filesystem.file');

		$css_file = JPath::clean(JPATH_ROOT.'/'.'media'.'/'.'aicontactsafe'.'/'.'cssprofiles'.'/'.'profile_css_'.$id.'.css');
		if (!is_file($css_file)) {
			$src_file = JPath::clean(JPATH_ROOT.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'views'.'/'.'message'.'/'.'tmpl'.'/'.'profile_align_margin.css');
			JFile::copy($src_file, $css_file);
		}
		$css_code = JFile::read($css_file);
		return $css_code;
	}

	// function to duplicate a profile
	function copyProfile() {
		// read the ids of the records seleted to duplicate
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		// only the first selected profile will be duplicated
		$id_to_duplicate = $cid[0];

		// initialize different variables
		$db = JFactory::getDBO();
		// read the fields from profiles table
		$query = 'SELECT * FROM `#__aicontactsafe_profiles` WHERE id = '.$id_to_duplicate;
		$db->setQuery( $query );
		$profile_to_duplicate = $db->loadObject();
		// reset the id
		$profile_to_duplicate->id = null;
		// modify the name
		$profile_to_duplicate->name = 'Copy of '.$profile_to_duplicate->name;
		// make sure the default profile is not modified
		$profile_to_duplicate->set_default = 0;
		// duplicate the profile
		$db->insertObject('#__aicontactsafe_profiles', $profile_to_duplicate);
		// get the id of the new profile
		$profile_to_duplicate->id = $db->insertid();

		// read the other fields from profiles table
		$query = 'SELECT * FROM `#__aicontactsafe_contactinformations` WHERE profile_id = '.$id_to_duplicate;
		$db->setQuery( $query );
		$other_info = $db->loadObjectList();

		// initialize the array to send the other fields into the database
		$postData = array();
		$postData['name'] = $profile_to_duplicate->name;
		$postData['meta_description'] = '';
		$postData['meta_keywords'] = '';
		$postData['meta_robots'] = '';
		$postData['display_format'] = $profile_to_duplicate->display_format;
		$postData['plg_contact_info'] = $profile_to_duplicate->plg_contact_info;

		$postData['use_message_css'] = $profile_to_duplicate->use_message_css;
		$postData['profile_css_code'] = $this->readProfileCssFile( $id_to_duplicate );
		$postData['profile_css_code'] = str_replace('aiContactSafe_mainbody_'.$id_to_duplicate.' ', 'aiContactSafe_mainbody_'.$profile_to_duplicate->id.' ', $postData['profile_css_code']);

		$postData['use_mail_template'] = $profile_to_duplicate->use_mail_template;
		$postData['mail_template'] = $this->readMailTemplate( $id_to_duplicate );


		foreach($other_info as $info) {
			switch($info->info_key) {
				case 'contact_info' :
					$postData['contact_info'] = $info->info_value;
					break;
				case 'meta_description' :
					$postData['meta_description'] = $info->info_value;
					break;
				case 'meta_keywords' :
					$postData['meta_keywords'] = $info->info_value;
					break;
				case 'meta_robots' :
					$postData['meta_robots'] = $info->info_value;
					break;
				case 'thank_you_message' :
					JRequest::setVar('thank_you_message', $info->info_value, 'post', true);
					break;
				case 'required_field_notification' :
					JRequest::setVar('required_field_notification', $info->info_value, 'post', true);
					break;
			}
		}
		// write the other information
		$this->writeOtherTables($postData,$profile_to_duplicate->id);
		// write contact information
		$this->writeContactInformation($postData,$profile_to_duplicate->id);
		// write CSS
		$this->writeCSS($postData,$profile_to_duplicate->id);
		// write email template
		$this->writeEmail($postData,$profile_to_duplicate->id);
	}

	// function to read the mail template file of a profile
	function readMailTemplate( $id = 0 ) {
		$mail_code = '';
		// if id = 0 then a new profile is edited and the default template should be read
		// import joomla clases to manage file system
		jimport('joomla.filesystem.file');
		
		$mail_file = JPath::clean(JPATH_ROOT.'/'.'media'.'/'.'aicontactsafe'.'/'.'mailtemplates'.'/'.'mail_'.$id.'.php');
		if (!is_file($mail_file)) {
			$src_file = JPath::clean(JPATH_ROOT.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'views'.'/'.'mail'.'/'.'tmpl'.'/'.'mail.php');
			JFile::copy($src_file, $mail_file);
		}
		$mail_code = JFile::read($mail_file);
		return $mail_code;
	}

	// function to get the css code for a type of alignement
	function getCSS() {
		// read the profile id for which to get the CSS code
		$id = JRequest::getInt( 'id' );
		// read the type of css to generate
		$css_type = JRequest::getCmd( 'css_type', '' );
		// reset the css code
		$css_code = '';

		// import joomla clases to manage file system
		jimport('joomla.filesystem.file');
			
		$css_file = JPATH_ROOT.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'views'.'/'.'message'.'/'.'tmpl'.'/'.'profile_'.$css_type.'.css';
		if (JFile::exists($css_file)) {
			$css_code = JFile::read($css_file);
			$css_code = str_replace('aiContactSafe_mainbody_1 ', 'aiContactSafe_mainbody_'.$id.' ', $css_code);
		}

		return $css_code;
	}

	// function to read the profile's name
	function getProfileName( $id = 0 ) {
		// initialize different variables
		$db = JFactory::getDBO();
		$id = (int)$id;

		$query = 'SELECT name FROM `#__aicontactsafe_profiles` WHERE id = '.$id;
		$db->setQuery( $query );
		$name = $db->loadResult();
		
		return $name;
	}

	// function to set the default profile
	function setDefault() {
		$this->resetDefaultProfile();
		// initialize different variables
		$db = JFactory::getDBO();
		// read the ids of the records seleted for deletion
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		$id = (int)$cid[0];
		// set the default profile
		$query = 'UPDATE `#__aicontactsafe_profiles` SET set_default = 1, published = 1 WHERE id = '.$id;
		$db->setQuery( $query );
		$db->query();
	}

	// function used to modify the field published to 1 or 0
	function changePublish($state = 0) {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// initialize different variables
		$db = JFactory::getDBO();
		// read the ids of the records seleted for publishing / unpublishing
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count($cid) > 0) {
			$cids = implode(',', $cid);
		} else {
			$cids = '-1';
		}
		// update the value of the field published
		$ctablename = $this->getTableName($this->_sTask, 'changePublish');
		$query = 'UPDATE '.$ctablename.' SET published = ' . $state . ' WHERE id IN ( ' . $cids . ' )'.($state == 0?' AND set_default = 0':'');
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}

		return true;
	}

}
