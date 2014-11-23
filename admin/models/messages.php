<?php
/**
 * @version     $Id$ 2.0.14 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.8
 * - added the possibility to send the reply in plain text
 * added/fixed in version 2.0.13
 * - fixed the problem with displaying the messages in the front-page even if the access rights is set higher to level of the user accessing the page
 * - added a button to download the CSV file after the messages were exported to it
 * added/fixed in version 2.0.14
 * - filter variables read with JRequest::getVar
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the control_panel model class of aiContactSafe
class AiContactSafeModelMessages extends AiContactSafeModelDefault {

	// list of IPs selected for ban
	var $ban_ips_rowlist = null;
	// filter records that where sent using a specific profile
	var $filter_profile = null;
	// filter records that where sent from a specific email address
	var $filter_email = null;
	// filter records that where sent using a specific subject
	var $filter_subject = null;
	// filter records that where sent using a specific status
	var $filter_status = null;

	// construct function, it will iniaize the class variables
	function __construct( $default = array() )	{
		parent::__construct( $default );
		$this->filter_profile = $this->getSessionStateFromRequest( $this->_sTask.'filter_profile', 'filter_profile', 0, 'int' );
		$this->filter_email = $this->getSessionStateFromRequest( $this->_sTask.'filter_email', 'filter_email', '' );
		$this->filter_subject = $this->getSessionStateFromRequest( $this->_sTask.'filter_subject', 'filter_subject', '' );
		$this->filter_status = $this->getSessionStateFromRequest( $this->_sTask.'filter_status', 'filter_status', $this->_config_values['default_status_filter'], 'int' );
		// if no order is used, use the 'date_added' field
		if (strlen($this->filter_order) == 0) {
			$this->filter_order = 'date_added';
			$this->filter_order_Dir = 'DESC';
		}
	}

	// function to define the sql command to count the records to display
	function setCountSelect() {
		$this->count_select_sql = 'SELECT count(*) FROM #__aicontactsafe_messages m LEFT JOIN #__aicontactsafe_profiles p ON m.profile_id = p.id LEFT JOIN #__aicontactsafe_statuses s ON m.status_id = s.id';
		return $this->count_select_sql;
	}

	// function to define the sql command to display records
	function setSelect() {
		$this->select_sql = 'SELECT m.*, p.name as profile, s.name as status, s.color FROM #__aicontactsafe_messages m LEFT JOIN #__aicontactsafe_profiles p ON m.profile_id = p.id LEFT JOIN #__aicontactsafe_statuses s ON m.status_id = s.id';
		return $this->select_sql;
	}

	// function to generate the condition records have to respect to be displayed
	function getWhere() {
		$db = JFactory::getDBO();
		// get the current user GID
		$user = JFactory::getUser();
		$allow_user_access_to_messages = false;
		if($this->_backend) {
			$allow_user_access_to_messages = true;
		} else {
			if(version_compare(JVERSION, '1.6.0', 'ge')) {
				$gids_user = JFactory::getUser()->getAuthorisedGroups();
				$user_access_groups = array();
				foreach($gids_user as $user_gid) {
					$user_access_groups[] = $user_gid;
					$user_access_groups = $this->getUserGroupChildrens($user_gid, $user_access_groups);
				}
				foreach($user_access_groups as $g) {
					if($g == $this->_config_values['gid_messages']) {
						$allow_user_access_to_messages = true;
						break;
					}
				}
			} else {
				$gid_user_id = $user->get('gid');
				$allow_user_access_to_messages = $gid_user_id < $this->_config_values['gid_messages'] && $this->_config_values['gid_messages'] != 29;
			}
		}
		if (!$allow_user_access_to_messages) {
			$where = ' WHERE 0 ';
		} else {
			if ( strlen($this->filter_condition) == 0 ) {
				$where = ' WHERE 1 ';
			} else {
				$where = ' WHERE ' . $this->filter_condition . ' ';
			}
			if ( strlen($this->filter_string) > 0 ) {
				$where .= ' AND LOWER( m.name ) LIKE ' . $db->quote('%'.$this->filter_string.'%');
			}
			if ( strlen($this->filter_email) > 0 ) {
				$where .= ' AND LOWER( m.email ) LIKE ' . $db->quote('%'.$this->filter_email.'%');
			}
			if ( strlen($this->filter_subject) > 0 ) {
				$where .= ' AND LOWER( m.subject ) LIKE ' . $db->quote('%'.$this->filter_subject.'%');
			}
			if ( $this->filter_profile > 0 ) {
				$where .= ' AND m.profile_id = '.(int)$this->filter_profile;
			}
			if ( $this->filter_status > 0 ) {
				$where .= ' AND m.status_id = '.(int)$this->filter_status;
			}
			if (!$this->_config_values['users_all_messages'] && !$this->_backend) {
				$where .= ' AND m.user_id = '.(int)$this->_user_id . ' AND m.user_id != 0';
			}
		}

		return $where;
	}

	// function to add/modify values in the record list
	function setRowValues($rowlist) {
		$n = count($rowlist);
		for ($i = 0; $i < $n; $i++ ) {
			$rowlist[$i]->view = JRoute::_('index.php?option=com_aicontactsafe&sTask=' . $this->_sTask . '&task=view&id=' . $rowlist[$i]->id, false);
		}
	}

	// function to define the sql command to select records to delete
	function setDeleteSelect() {
		$field_separator = '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
		$this->delete_select_sql = 'SELECT id, CONCAT(TRIM(name),\''.$field_separator.'\',TRIM(email),\''.$field_separator.'\',TRIM(subject),\''.$field_separator.'\',date_added) as name FROM #__aicontactsafe_messages %where% order by name';
		return $this->delete_select_sql;
	}

	// function to delete selected records
	function deleteData() {
		$wasDeleted = parent::deleteData();
		// initialize different variables
		if ($wasDeleted) {
			// read the ids of the records seleted for deletion
			$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
			JArrayHelper::toInteger($cid);
			if (count($cid) > 0) {
				$cids = implode(',', $cid);
			} else {
				$cids = '-1';
			}
			$this->deleteOtherInfo($cids);
		}
		return true;
	}

	// function to delete other information from the database related to messages
	function deleteOtherInfo( $cids = '' ) {
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
		$query = 'SELECT id, name FROM #__aicontactsafe_messagefiles WHERE message_id IN ( '.$cids.' )';
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
		// delete all field values for the deleted messages
		// get the files to delete
		$query = 'DELETE FROM #__aicontactsafe_fieldvalues WHERE message_id IN ( '.$cids.' )';
		$db->setQuery($query);
		$db->query();
	}

	// function to select the IPs to ban
	function readSelectedIps() {
		if(!$this->ban_ips_rowlist) {
			// initialize the database
			$db = JFactory::getDBO();

			// get the condition for the selected records
			$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
			JArrayHelper::toInteger($cid);
			if (count($cid) > 0) {
				$cids = implode(',', $cid);
			} else {
				$cids = '-1';
			}

			// get the IPs to ban
			$query = 'SELECT * FROM #__aicontactsafe_messages WHERE id IN ( ' . $cids . ') GROUP BY sender_ip ORDER BY sender_ip';

			$this->ban_ips_rowlist = $this->_getList($query, 0, 0);
			if (!is_array($this->ban_ips_rowlist)) {
				$this->ban_ips_rowlist = array();
			}
		}
		return $this->ban_ips_rowlist;
	}

	// function to ban an IP
	function banIP() {
		// initialize different variables
		$db = JFactory::getDBO();
		// read the ids of the records seleted to ban IP
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count($cid) > 0) {
			$cids = implode(',', $cid);
		} else {
			$cids = '-1';
		}
		// get all the IPs to ban
		$query = 'SELECT DISTINCT sender_ip FROM #__aicontactsafe_messages WHERE id IN ( ' . $cids . ') ORDER BY sender_ip';
		$db->setQuery($query);
		$ips_to_ban = $db->loadResultArray();
		$ips_banned = explode(';',$this->_config_values['ban_ips']);
		$ips_banned = array_merge($ips_banned, $ips_to_ban);
		$ips_banned = array_unique($ips_banned);
		asort($ips_banned);

		$ban_ips = implode(';',$ips_banned);
		if (substr($ban_ips,0,1) == ';') {
			$ban_ips = substr($ban_ips,1);
		}
		$query = 'UPDATE `#__aicontactsafe_config` set config_value = \'' . $ban_ips . '\' where config_key = \'ban_ips\'';
		$db->setQuery( $query );
		$isOK = $db->query();
		if (!$isOK) {
			$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}

		return true;
	}

	// function to read the profile name used to send the message
	function getProfileName( $id = 0 ) {
		$id = (int)$id;
		// initialize different variables
		$db = JFactory::getDBO();
		$query = 'SELECT name FROM #__aicontactsafe_profiles WHERE id = '.$id;
		$db->setQuery($query);
		$profile = $this->revert_specialchars($db->loadResult());
		return $profile;
	}

	// function to send the reply
	function sendReply() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		// read the data from the form
		$postData = JRequest::get('post');
		$postData = $this->securityCheck($postData);

		// clear body and subject
		jimport( 'joomla.mail.helper' );
		// make sure the data is valid
		$isOk = true;
		if( !JMailHelper::isEmailAddress($postData['reply_email_address']) ) {
			$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
			$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_A_VALID_EMAIL_ADDRESS') );
		} else if ( strlen(trim($postData['reply_subject'])) == 0 ) {
			$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
			$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_PLEASE_SPECIFY_A_SUBJECT') );
		} else if ( strlen(trim($postData['reply_message'])) == 0 ) {
			$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
			$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_PLEASE_SPECIFY_A_MESSAGE') );
		}
		$isOk = $this->_app->_session->get( 'isOK:' . $this->_sTask );
		if ( $isOk ) {
			$from = $this->_app->getCfg('mailfrom');
			$fromname = $this->_app->getCfg('fromname');
			$email_recipient = JMailHelper::cleanAddress($postData['reply_email_address']);
			$subject = JMailHelper::cleanSubject($postData['reply_subject']);
			if ( array_key_exists('send_plain_text',$postData) && $postData['send_plain_text'] ) {
				$mode = false;
				$body = JMailHelper::cleanBody($postData['reply_message']);
			} else {
				$mode = true;
				$body = JMailHelper::cleanBody(str_replace("\n",'<br />', $postData['reply_message']));
			}
			$cc=null;
			$bcc=null;
			$replyto = $from;
			$replytoname = $fromname;
			$file_attachments = null;

			$isOK = JUtility::sendMail($from, $fromname, $email_recipient, $subject, $body, $mode, $cc, $bcc, $file_attachments, $replyto, $replytoname);
		}
		if ( $isOk ) {
			// initialize the database
			$db = JFactory::getDBO();
			// update the reply
			$query = 'UPDATE #__aicontactsafe_messages SET email_reply = \'' . $this->replace_specialchars($email_recipient) . '\', subject_reply = \'' . $this->replace_specialchars($subject) . '\' , message_reply = \'' . $this->replace_specialchars($body) . '\' WHERE id = '.(int)$postData['id'];
			$db->setQuery($query);
			$db->query();
			// modify the status of the message accordingly
			$this->changeStatusToReplied((int)$postData['id']);
		}

		return $isOk;
	}

	// genetare the csv sting with messages
	function generateCSV() {
		$csv_text = '';
		$field_separator = ',';
		$line_separator = "<br/>";
		// initialize the database
		$db = JFactory::getDBO();

		// read the ids of the records seleted for deletion
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count($cid) > 0) {
			$cids = implode(',', $cid);
		} else {
			$cids = '-1';
		}
		if ($cids == '-1') {
			$where = $this->getWhere();
		} else {
			$where = 'WHERE m.id IN ( '.$cids.' )';
		}

		$query = 'SELECT m.*, p.name as profile, s.name as status FROM #__aicontactsafe_messages m LEFT JOIN #__aicontactsafe_profiles p ON m.profile_id = p.id LEFT JOIN #__aicontactsafe_statuses s ON m.status_id = s.id '.$where;
		$db->setQuery($query);
		$messages = $db->loadObjectList();
		foreach($messages as $message) {
			if( $this->checkAccessToMessage($message->id) ) {
				$csv_text .= $message->id .$field_separator;
				$csv_text .= $this->generate_csv_value($message->name) .$field_separator;
				$csv_text .= $this->generate_csv_value($message->email) .$field_separator;
				$csv_text .= $this->generate_csv_value($message->subject) .$field_separator;
				$csv_text .= $message->sender_ip .$field_separator;
				$csv_text .= $this->generate_csv_value($message->profile) .$field_separator;
				$csv_text .= $this->generate_csv_value($message->status) .$field_separator;
	
				$query = 'SELECT f.id, f.name, f.field_label, f.field_label_message, fv.field_value FROM #__aicontactsafe_fieldvalues fv LEFT JOIN #__aicontactsafe_fields f ON fv.field_id = f.id WHERE fv.message_id = '.$message->id.' ORDER BY f.id';
				$db->setQuery($query);
				$fields = $db->loadObjectList();
				foreach($fields as $field) {
					$csv_text .= $field->id . $field_separator;
					$csv_text .= $field->name . $field_separator;
					$csv_text .= $this->generate_csv_value($field->field_label) . $field_separator;
					$csv_text .= $this->generate_csv_value($field->field_label_message) . $field_separator;
					$csv_text .= $this->generate_csv_value($field->field_value) . $field_separator;
				}
	
				$csv_text .= $message->date_added .$line_separator;
			}
		}

		$this->_app->_session->set( 'csv_text:' . $this->_sTask, $csv_text );
		return $csv_text;
	}

	// generate a csv field value
	function generate_csv_value( $csv_source = '' ) {
		$csv_source = str_replace('"', '""', $csv_source);
		if ( strpos($csv_source, "\r\n") !== false || strpos($csv_source, "\r") !== false || strpos($csv_source, "\n") !== false || strpos($csv_source, ',') !== false ) {
			$csv_source = '"' . $csv_source . '"';
		}
		return $csv_source;
	}

	// delete all selected messages
	function deleteSelected() {
		// read the ids of the records seleted for deletion
		$cids = JRequest::getVar( 'cids', '', 'post', 'string' );
		JArrayHelper::toInteger($cid);

		// initialize the database
		$db = JFactory::getDBO();

		$query = 'DELETE FROM #__aicontactsafe_messages WHERE id IN ( '.$cids.' )';
		$db->setQuery($query);
		$isOK = $db->query();
		if (!$isOK) {
			$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		} else {
			$this->deleteOtherInfo($cids);
		}
		return true;
	}

	// function to detect if a message has a reply
	function hasReply( $id = 0 ) {
		$id = (int)$id;
		// initialize the database
		$db = JFactory::getDBO();
		$query = 'SELECT message_reply FROM #__aicontactsafe_messages WHERE id = '.$id;
		$db->setQuery($query);
		$message_reply = $db->loadResult();
		$has_reply = strlen(trim($message_reply))>0?1:0;
		return $has_reply;
	}

	// function to change the status of the selected messages
	function changeStatus() {
		// read the ids of the records seleted for deletion
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		$new_status = JRequest::getVar( 'new_status', 0, 'post', 'int' );
		if ($new_status > 0 && is_array($cid) && count($cid)>0) {
			// initialize the database
			$db = JFactory::getDBO();
			$cids = implode(',', $cid);
			$query = 'UPDATE #__aicontactsafe_messages SET status_id = '.$new_status.', manual_status = 1 WHERE id IN ( '.$cids.' )';
			$db->setQuery($query);
			$db->query();
		}
	}

	// function to change the status of a message to read
	function changeStatusToRead( $id = 0 ) {
		$id = (int)$id;
		if ( $id > 0 ) {
			// initialize the database
			$db = JFactory::getDBO();
			$query = 'SELECT m.status_id, m.manual_status, m.profile_id, p.read_status_id, p.reply_status_id FROM #__aicontactsafe_messages m LEFT JOIN #__aicontactsafe_profiles p ON m.profile_id = p.id WHERE m.id = '.$id;
			$db->setQuery($query);
			$results = $db->loadObject();
			if (!$results->manual_status && $results->status_id != $results->read_status_id && $results->status_id != $results->reply_status_id) {
				$query = 'UPDATE #__aicontactsafe_messages SET status_id = '.$results->read_status_id.' WHERE id = '.$id;
				$db->setQuery($query);
				$db->query();
			}
		}
	}

	// function to change the status of a message to replied
	function changeStatusToReplied( $id = 0 ) {
		$id = (int)$id;
		if ( $id > 0 ) {
			// initialize the database
			$db = JFactory::getDBO();
			$query = 'SELECT m.status_id, m.manual_status, m.profile_id, p.reply_status_id FROM #__aicontactsafe_messages m LEFT JOIN #__aicontactsafe_profiles p ON m.profile_id = p.id WHERE m.id = '.$id;
			$db->setQuery($query);
			$results = $db->loadObject();
			if (!$results->manual_status && $results->status_id != $results->reply_status_id) {
				$query = 'UPDATE #__aicontactsafe_messages SET status_id = '.$results->reply_status_id.' WHERE id = '.$id;
				$db->setQuery($query);
				$db->query();
			}
		}
	}

	// function to check if a user has access to a message
	function checkAccessToMessage( $id = 0 ) {
		$id = (int)$id;
		if ($this->_backend) {
			$user_has_access = true;
		} else {
			$user_has_access = false;
			if(version_compare(JVERSION, '1.6.0', 'ge')) {
				$gids_user = JFactory::getUser()->getAuthorisedGroups();
				$user_access_groups = array();
				foreach($gids_user as $user_gid) {
					$user_access_groups[] = $user_gid;
					$user_access_groups = $this->getUserGroupChildrens($user_gid, $user_access_groups);
				}
				foreach($user_access_groups as $g) {
					if($g == $this->_config_values['gid_messages']) {
						$user_has_access = true;
						break;
					}
				}
			} else {
				$user = JFactory::getUser();
				$gid_user_id = $user->get('gid');
				if ($gid_user_id < $this->_config_values['gid_messages'] && $this->_config_values['gid_messages'] != 29) {
					$user_has_access = false;
				}
			}

			if ($user_has_access) {
				if ( $this->_config_values['users_all_messages'] ) {
					$user_has_access = true;
				} else {
					$db = JFactory::getDBO();
					$query = 'SELECT id FROM #__aicontactsafe_messages WHERE user_id = '.$id;
					$db->setQuery($query);
					$user_has_access = (int)$db->loadResult() > 0;
				}
			}
		}
		
		return $user_has_access;
	}

	// download a file from the attachments folder
	function downloadCSV() {
		// Make sure there's not anything else left for download
		$this->ob_clean_all(); 

		$csv_text = $this->_app->_session->get( 'csv_text:' . $this->_sTask );
		$csv_text = str_replace('<br/>',"\r\n",$csv_text);

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=aicontactsafe.csv');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		@ob_clean();
		flush();

		echo $csv_text;
		exit;
	}

	function getUserGroupChildrens($group_id = 0, $group_childrens = array()) {
		$db = JFactory::getDBO();
		$query = 'SELECT lft, rgt FROM #__usergroups WHERE id = '.$group_id.' LIMIT 1';
		$db->setQuery( $query );
		$group = $db->loadObject();
		$query = 'SELECT id FROM #__usergroups WHERE lft > '.$group->lft.' AND rgt < '.$group->rgt;
		$db->setQuery( $query );
		$childrens = $db->loadObjectList();
		foreach($childrens as $c) {
			$group_childrens[] = $c->id;
		}

		return $group_childrens;
	}
}
