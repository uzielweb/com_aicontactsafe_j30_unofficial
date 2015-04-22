<?php
/**
 * @version     $Id$ 2.0.13 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.1
 * - added the function nameUsed
 * - replace spaces from the name of the field to '_'
 * - remove the ';' at the end of the field values in case it was entered
 * - message can be renamed, published and made not required
 * - added an error message on name modification, unpublish or made not required the fields "name", "email" and "subject"
 * - "name", "email" and "subject" can't be deleted and an error message is displayed
 * - added 'aics_' in front of each field name
 * added/fixed in version 2.0.13
 * - added the possibility to duplicate one or more fields
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the control_panel model class of aiContactSafe
class AiContactSafeModelFields extends AiContactSafeModelDefault {

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
		// check if a name was entered
		if (strlen(trim($postData['name'])) == 0) {
			$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
			$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_PLEASE_ENTER_THE_NAME_OF_THE_FIELD') );
		} else {
			$postData['name'] = $this->onlyLettersAndNumbers($this->revert_specialchars(str_replace(' ','_',$postData['name'])));
			if ( substr($postData['name'],0,5) != 'aics_' ) {
				$postData['name'] = 'aics_' . $postData['name'];
			}
			if ( $this->nameUsed(trim($postData['name']), $postData['id']) ) {
				$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
				$this->_app->_session->set( 'errorMsg:' . $this->_sTask, JText::_('COM_AICONTACTSAFE_THIS_NAME_WAS_USED_ON_ANOTHER_FIELD_PLEASE_USE_A_DIFFERENT_ONE') );
			} else {
				$postData = parent::checkBeforeWrite($postData);
			}
		}
		$postData['field_values'] = JRequest::getVar('field_values', '', 'post', 'string', JREQUEST_ALLOWRAW);
		if (array_key_exists('field_values',$postData) && substr(trim($postData['field_values']),-1,1) == ';') {
			$postData['field_values'] = substr(trim($postData['field_values']),0,-1);
		}

		$postData['field_label'] = JRequest::getVar('field_label', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$postData['field_label'] = $this->replace_specialchars($postData['field_label']);

		$postData['label_parameters'] = $this->replace_specialchars($postData['label_parameters']);

		$postData['field_label_message'] = JRequest::getVar('field_label_message', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$postData['field_label_message'] = $this->replace_specialchars($postData['field_label_message']);

		$postData['label_message_parameters'] = $this->replace_specialchars($postData['label_message_parameters']);

		$postData['field_parameters'] = $this->replace_specialchars($postData['field_parameters']);

		$postData['field_sufix'] = $this->replace_specialchars($postData['field_sufix']);
		$postData['field_prefix'] = $this->replace_specialchars($postData['field_prefix']);
		
		$postData['label_after_field'] = (array_key_exists('label_after_field',$postData) && $postData['label_after_field'])?1:0;
		$postData['send_message'] = (array_key_exists('send_message',$postData) && $postData['send_message'])?1:0;
		$postData['field_required'] = (array_key_exists('field_required',$postData) && $postData['field_required'])?1:0;
		$postData['field_in_message'] = (array_key_exists('field_in_message',$postData) && $postData['field_in_message'])?1:0;
		$postData['published'] = (array_key_exists('published',$postData) && $postData['published'])?1:0;

		return $postData;
	}

	// function check if the field name is used on the database for a different field
	function nameUsed( $checkName = '', $checkId = 0) {
		// initialize different variables
		$db = JFactory::getDBO();

		// count all the fileds with the same name and a different id
		$query = 'select count(*) as count_fields from #__aicontactsafe_fields where name = \'' . $checkName . '\' and id != ' . (int)$checkId;

		$db->setQuery($query);
		$count_fields = $db->loadResult();

		return $count_fields;
	}
	
	// function to generate the new name of a duplicated field
	function checkDuplicatedName($current_name = '') {
		$dup_no = 2;
		while($this->nameUsed(trim($current_name).'_'.$dup_no)) {
			$dup_no += 1;
		}
		return trim($current_name).'_'.$dup_no;
	}

	// function to duplicate one or more fields
	function copyField() {
		// read the ids of the records seleted to duplicate
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count($cid) > 0) {
			$cids = implode(',', $cid);
		} else {
			$cids = '-1';
		}

		// initialize different variables
		$db = JFactory::getDBO();
		// read the fields from profiles table
		$query = 'SELECT * FROM `#__aicontactsafe_fields` WHERE id IN ( ' . $cids . ' )';
		$db->setQuery( $query );
		$fields_to_duplicate = $db->loadObjectList();
		if( count($fields_to_duplicate) > 0 ) {
			$datenow = JFactory::getDate();
			foreach($fields_to_duplicate as $field_to_duplicate) {
				// reset the id
				$field_to_duplicate->id = null;
				// modify the name
				$field_to_duplicate->name = $this->checkDuplicatedName($field_to_duplicate->name);
				// reset the date added and modified
				$field_to_duplicate->date_added = $datenow->toSQL();
				$field_to_duplicate->last_update = $datenow->toSQL();
				// duplicate the field
				$db->insertObject('#__aicontactsafe_fields', $field_to_duplicate);
			}
		}
	}
}
