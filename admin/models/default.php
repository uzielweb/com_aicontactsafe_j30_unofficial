<?php
/**
 * @version     $Id$ 2.0.14
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.1
 * - fixed the problem with warning message: Call-time pass-by-reference has been deprecated;
 * added/fixed in version 2.0.14
 * - filter variables read with JRequest::getVar
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// load the default component model class
jimport( 'joomla.application.component.model' );

// define the default aiContactSafe model class
class AiContactSafeModelDefault extends JModelLegacy {
	// component version
	var $_version = null;
	// mainframe (application) reference
	var $_app = null;
	// current task
	var $_task = null;
	// current aiContactSafe section
	var $_sTask = null;
	// this class is used in backend (1) or frontend(0)
	var $_backend = null;
	// id of the current user logged in
	var $_user_id = null;
	// sql command to count the records to display
	var $count_select_sql = null;
	// sql command to display records
	var $select_sql = null;
	// list of the records to display
	var $rowlist = null;
	// order in which to place the records
	var $filter_order = null;
	// order direction  in which to place the records
	var $filter_order_Dir = null;
	// add a filter condition to the displayed records
	var $filter_condition = null;
	// filter records that have a specific string in a field
	var $filter_string = null;
	// the field in which to look for $filter_string
	var $filter_field = 'name';
	// activate or deactivate pagination
	var $withPagNav = true;
	// pagination object
	var $pageNav = null;
	// number of records / page
	var $limit = null;
	// starting record
	var $limitstart = null;
	// sql command to select the records to delete
	var $delete_select_sql = null;
	// list of the records to delete
	var $delete_rowlist = null;
	// sql command to delete selected records
	var $delete_sql = null;
	// configuration values
	var $_config_values = null;
	// sef is activated or not
	var $_sef = null;
	// parameters array
	var $_parameters = array();

	// construct function, it will iniaize the class variables
	function __construct( $default = array() )	{
		$this->_parameters = $default;

		$this->_version = $default['_version'];
		$this->_app = $default['_app'];
		$this->_task = $default['_task'];
		$this->_sTask = $default['_sTask'];
		$this->_backend = $default['_backend'];
		$this->_user_id = $default['_user_id'];
		$this->_sef = $default['_sef'];
		$this->_config_values = $default['_config_values'];
		$this->filter_order = $this->getSessionStateFromRequest( $this->_sTask.'filter_order', 'filter_order', '', 'cmd' );
		$this->filter_order_Dir = $this->getSessionStateFromRequest( $this->_sTask.'filter_order_Dir', 'filter_order_Dir', '', 'word' );
		$this->limit = $this->getSessionStateFromRequest( 'global.list.limit', 'limit', $this->_app->getCfg('list_limit'), 'int' );
		$this->limitstart = $this->getSessionStateFromRequest( $this->_sTask.'limitstart', 'limitstart', 0, 'int' );
		$this->filter_condition = $this->setFilterCondition();
		$this->filter_string = $this->getSessionStateFromRequest( $this->_sTask.'filter_string', 'filter_string', '' );

		parent::__construct( $default );

		$this->count_select_sql = $this->setCountSelect();
		$this->select_sql = $this->setSelect();
		$this->delete_select_sql = $this->setDeleteSelect();
		$this->delete_sql = $this->setDelete();
	}

	// function to get the information from the form
	function getFormFields() {
		// read the data from the form
		$postData = JRequest::get('post');

		// reset the OK variable and error message, it will be modified in the securityCheck, checkBeforeWrite or writeData function
		$this->_app->_session->set( 'isOK:' . $this->_sTask, true );
		$this->_app->_session->set( 'errorMsg:' . $this->_sTask, '' );
		$this->_app->_session->set( 'idSaved:' . $this->_sTask, 0 );

		// make the security check
		$postData = $this->securityCheck($postData);
		if ($this->_app->_session->get( 'isOK:' . $this->_sTask )) {
			// make the validation of fields
			$postData = $this->checkBeforeWrite($postData);
		}

		// record the variables in session so it can be restored in the form in case of an error
		$this->recordPostDataInSession( $postData );

		return $this->_app->_session->get( 'isOK:' . $this->_sTask );
	}

	// function to write the postdata into the session variable ( add in a function so the session variable can be modified for the message )
	function recordPostDataInSession( $postData ) {
		$this->_app->_session->set( 'postData:' . $this->_sTask, $postData );
	}

	// function to read the postdata from the session variable ( add in a function so the session variable can be modified for the message )
	function readPostDataFromSession() {
		return $this->_app->_session->get( 'postData:' . $this->_sTask );
	}

	// function that resets the session variable that records the fields sent when submitting a form
	function resetFormFields() {
		$this->recordPostDataInSession( '' );
		$this->_app->_session->set( 'isOK:' . $this->_sTask, true );
		$this->_app->_session->set( 'errorMsg:' . $this->_sTask, '' );
		$this->_app->_session->set( 'idSaved:' . $this->_sTask, 0 );
	}

	// function to protect against different security threats
	function securityCheck($postData) {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// replace special chars
		foreach($postData as $key=>$data) {
			if (is_string($data)) {
				$postData[$key] = $this->replace_specialchars($data);
			}
		}

		return $postData;
	}

	// function to validate fields before writing them to the database
	function checkBeforeWrite($postData) {
		// modify the fields 'date_added' and 'last_update' if the last task was to add or edit a record
		switch(true) {
			case array_key_exists('last_task', $postData) && $postData['last_task'] == 'add':
				$datenow = JFactory::getDate();
				$postData['date_added'] = $datenow->toSql();
				$postData['last_update'] = $datenow->toSql();
				break;
			case array_key_exists('last_task', $postData) && $postData['last_task'] == 'edit':
				$datenow = JFactory::getDate();
				$postData['last_update'] = $datenow->toSql();
				break;
		}

		return $postData;
	}

	// function to determine the table to write to
	function getTableName($sTask = '', $reason = '') {
		if (strlen($sTask) == 0) {
			$sTask = $this->_sTask;
		}
		switch (true) {
			case $reason == 'writeData' || $reason == 'getRowData' :
				$table_name = 'aicontactsafe_' . $sTask;
				break;
			default :
				$table_name = '#__aicontactsafe_' . $sTask;
		}
		return $table_name;
	}

	// function to write data to database
	function writeData() {
		// get the table name
		$ctablename = $this->getTableName($this->_sTask, 'writeData');

		$postData = $this->readPostDataFromSession();
		$dataRow = JTable::getInstance($ctablename, 'Table');
		// bind the data sent from the form to the table fields
		if ($dataRow->bind($postData)) {
			if (!$dataRow->store()) {
				$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
				$this->_app->_session->set( 'errorMsg:' . $this->_sTask, $dataRow->getError() );
			} else {
				$this->writeOtherTables($postData, $dataRow->id);
			}
		} else {
			$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
			$this->_app->_session->set( 'errorMsg:' . $this->_sTask, $dataRow->getError() );
		}
		$isOK = $this->_app->_session->get( 'isOK:' . $this->_sTask );
		if ($isOK) {
			$this->_app->_session->set( 'idSaved:' . $this->_sTask, $dataRow->id );
		}
		return $isOK;
	}

	//function to write data in other tables then the default one of the current sTask
	function writeOtherTables( $postData=array(), $id = 0 ) {
		return true;
	}
	
	// function to get the data from a row of the table
	// it will return an array with the fields as keys
	function getRowData( $id = 0) {
		$ctablename = $this->getTableName($this->_sTask, 'getRowData');

		// initialize the row array
		$dataRow = JTable::getInstance($ctablename, 'Table');
		$id = (int)$id;
		// load the row data
		$dataRow->load($id);

		return $dataRow;
	}

	// function to define the sql command to count the records to display
	function setCountSelect() {
		$ctablename = $this->getTableName($this->_sTask, 'setCountSelect');
		$this->count_select_sql = 'SELECT count(*) FROM ' . $ctablename;
		return $this->count_select_sql;
	}

	// function to define the sql command to display records
	function setSelect() {
		$ctablename = $this->getTableName($this->_sTask, 'setSelect');
		$this->select_sql = 'SELECT * FROM ' . $ctablename;
		return $this->select_sql;
	}

	// function to read the records to display
	function readRows() {
		if(!$this->rowlist) {
			// initialize the database
			$db = JFactory::getDBO();

			// get the condition for the sql command
			$where = $this->getWhere();
			// count the records to display
			$query = $this->count_select_sql . $where;
			$db->setQuery( $query );
			$total = $db->loadResult();

			// import the pagination class
			jimport('joomla.html.pagination');
			// generate the pagination object
			// pagination can be disabled by modifying the variable withPagNav
			if ($this->withPagNav) {
				$this->pageNav = new JPagination( $total, $this->limitstart, $this->limit );
			} else {
				$this->pageNav = new JPagination( 0, 0, 0 );
			}
			
			// get the records to display
			$query = $this->select_sql . $where;
			if (strlen($this->filter_order) > 0) {
				$query .= ' ORDER BY ' . $this->filter_order . ' ' . (is_null($this->filter_order_Dir)?'':$this->filter_order_Dir);
			}
			if ($this->withPagNav) {
				$this->rowlist = $this->_getList($query, $this->pageNav->limitstart, $this->pageNav->limit);
			} else {
				$this->rowlist = $this->_getList($query, 0, 0);
			}
			// add/modify values from the list
			$this->setRowValues($this->rowlist);
			if (!is_array($this->rowlist)) {
				$this->rowlist = array();
			}
		}
		return $this->rowlist;
	}


	// function to add/modify values in the record list
	// the default function is adding a new property used to call the edit function for each record
	function setRowValues($rowlist) {
		$n = count($rowlist);
		for ($i = 0; $i < $n; $i++ ) {
			$rowlist[$i]->edit = JRoute::_('index.php?option=com_aicontactsafe&sTask=' . $this->_sTask . '&task=edit&id=' . $rowlist[$i]->id, false);
		}
	}

	
	// function to generate the condition records have to respect to be displayed
	function getWhere() {
		$db = JFactory::getDBO();
		if ( strlen($this->filter_condition) == 0 ) {
			$where = ' WHERE 1 ';
		} else {
			$where = ' WHERE ' . $this->filter_condition . ' ';
		}
		if ( strlen($this->filter_string) > 0 ) {
			$where .= ' AND LOWER( ' . $this->filter_field . ' ) LIKE ' . $db->quote('%'.$this->filter_string.'%');
		}
		return $where;
	}

	// function to set a filter condition for the records
	function setFilterCondition() {
		return '';
	}

	// function to define the sql command to select records to delete
	function setDeleteSelect() {
		$ctablename = $this->getTableName($this->_sTask, 'setDeleteSelect');
		$this->delete_select_sql = 'SELECT * FROM ' . $ctablename . ' %where% order by name';
		return $this->delete_select_sql;
	}

	// function to generate the condition records have to respect to be selected for deletion
	function getDeleteWhere($cids = '-1') {
		$ctablename = $this->getTableName($this->_sTask, 'getDeleteWhere');
		$where = ' where ' . $ctablename . '.id IN ( ' . $cids . ' )';
		return $where;
	}

	// function to read the records to delete
	function readDeleteRows() {
		if(!$this->delete_rowlist) {
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
			$where = $this->getDeleteWhere($cids);
			// get the records to delete
			$query = str_replace('%where%', $where, $this->delete_select_sql);

			$this->delete_rowlist = $this->_getList($query, 0, 0);
			if (!is_array($this->delete_rowlist)) {
				$this->delete_rowlist = array();
			}
		}
		return $this->delete_rowlist;
	}

	// function to define the sql command to delete selected records
	function setDelete() {
		$ctablename = $this->getTableName($this->_sTask, 'setDelete');
		$this->delete_sql = 'DELETE FROM ' . $ctablename . ' where id IN ( %cids% ) AND ( checked_out = 0 OR (checked_out = %uid% ) )';
		return $this->delete_sql;
	}

	// function to delete selected records
	function deleteData() {
		// initialize different variables
		$db = JFactory::getDBO();
		$uid = $this->_user_id;
		// read the ids of the records seleted for deletion
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count($cid) > 0) {
			$cids = implode(',', $cid);
		} else {
			$cids = '-1';
		}
		// generate the sql command
		$query = str_replace('%cids%', $cids, $this->delete_sql);
		$query = str_replace('%uid%', $uid, $query);
		// delete records
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}

		return true;
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
		$query = 'update '.$ctablename.' set published = ' . $state . ' where id IN ( ' . $cids . ' )';
		$db->setQuery($query);
		if (!$db->query()) {
			JError::raiseError( 500, $db->getErrorMsg() );
			return false;
		}

		return true;
	}

	//function used to move a record up or down (based on the field ordering)
	function changeOrder($direction = 0) {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// read the id of the record seleted for move
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		if (count($cid) > 0) {
			$cid = (int)$cid[0];
		} else {
			$cid = -1;
		}

		// if there is a record selected and a direction specified continue the procedure
		if ($cid > 0 && $direction != 0) {
			// get an object with the values of the fields of the selected record
			$row = $this->getRowData($cid);
			// move the record to the new position
			if (!$row->move( $direction, '' )) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	// function used to save the new order of records
	function saveorder() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// read the id of the record seleted for move
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		$order = JRequest::getVar( 'order', array(), 'post', 'array' );
		JArrayHelper::toInteger($order);

		$n = count($cid);
		$this->_app->_session->set( 'isOK:' . $this->_sTask, true );
		// for each record in the database read the fields, modify the 'ordering' field and save it
		for ($i = 0; $i<$n; $i++) {
			// get an object with the values of the fields of the selected record
			$row = $this->getRowData($cid[$i]);
			$row->ordering = $order[$i];
			if (!$row->store()) {
				$this->_app->_session->set( 'isOK:' . $this->_sTask, false );
				$this->_app->_session->set( 'errorMsg:' . $this->_sTask, $dataRow->getError() );
				break;
			}
		}

		$isOK = $this->_app->_session->get( 'isOK:' . $this->_sTask );
		return $isOK;
	}

	// function to get the next value for the field ordering
	function getNextOrdering() {
		// initialize the database
		$db = JFactory::getDBO();
		// get the maximum value of the fiel 'ordering'
		$ctablename = $this->getTableName($this->_sTask, 'getNextOrdering');
		$query = 'SELECT max(ordering) as ordering FROM ' . $ctablename;
		$db->setQuery( $query );
		$max_ordering = (int)$db->loadResult();
		$max_ordering += 1;
		return $max_ordering;
	}

	// function to determin the next link to redirect the page
	function getReturnLink($new_values = array()) {
		// read the registered return task
		$return_task = $this->_app->_session->get( 'return_task:' . $this->_sTask );
		// force the return_task variable to be an array
		if (!is_array($return_task)) {
			$return_task = array();
		}
		// add/modify the values from last_task to new_values
		foreach($new_values as $par_key => $par_value) {
			$return_task[$par_key] = $par_value;
		}
		// generate the link
		$link = 'index.php?option=com_aicontactsafe';
		foreach($return_task as $par_key => $par_value) {
			$link .= '&' . $par_key . '=' . $par_value;
		}
		// make the link seo friendly only for the frontend
		if ($this->_backend == 0) {
			$link = JRoute::_($link, false);
		}
		return $link;
	}

	// function to determin the link of the last visited page
	function getLastLink($new_values = array()) {
		// read the registered last task
		$last_task = $this->_app->_session->get( 'last_task' );
		// force the last_task variable to be an array
		if (!is_array($last_task)) {
			$last_task = array();
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
		// make the link seo frindly only for the frontend
		if ($this->_sef == 1 && $this->_backend == 0) {
			$link = JRoute::_($link, false);
		}
		return $link;
	}	

	// function to read a variable from request and register it into the session if it was found
	function getSessionStateFromRequest( $key, $request, $default = null, $type = 'none' ) {
		$old_state = $this->_app->_session->get( $key );
		$cur_state = (!is_null($old_state)) ? $old_state : $default;
		$new_state = JRequest::getVar($request, $cur_state, 'request', $type);

		// Save the new value only if it was set in this request
		if ($new_state !== null) {
			$this->_app->_session->set($key, $new_state);
		} else {
			$new_state = $cur_state;
		}

		return $new_state;
	}

	// function to replace special chars
	function replace_specialchars( $source_string = '' ) {
		$source_string = str_replace('"','&quot;',$source_string);
		$source_string = str_replace('\'','&#039;',$source_string);
		$source_string = str_replace('<','&lt;',$source_string);
		$source_string = str_replace('>','&gt;',$source_string);
		return $source_string;
	}

	// function to revert the special chars encoding
	function revert_specialchars( $source_string = '' ) {
		$source_string = str_replace('&quot;','"',$source_string);
		$source_string = str_replace('&#039;','\'',$source_string);
		$source_string = str_replace('&lt;','<',$source_string);
		$source_string = str_replace('&gt;','>',$source_string);
		return $source_string;
	}

	// function to remove from a string all characters that are not letters, numbers or the "_" character
	function onlyLettersAndNumbers( $stringToCheck = '' ) {
		$responseString = '';
		if (is_string($stringToCheck)) {
			$n = strlen($stringToCheck);
			for( $i=0; $i<$n; $i++ ) {
				$chr = substr($stringToCheck,$i,1);
				$ascii_code = ord($chr);
				switch(true) {
					case $ascii_code >= 48 && $ascii_code <= 57 :
						$responseString .= $chr;
						break;
					case $ascii_code >= 65 && $ascii_code <= 90 :
						$responseString .= $chr;
						break;
					case $ascii_code == 95 :
						$responseString .= $chr;
						break;
					case $ascii_code >= 97 && $ascii_code <= 122 :
						$responseString .= $chr;
						break;
				}
			}
		}
		return $responseString;
	}

	function ascunde_sir( $str = '' ) {
		$str = str_replace('@','&#64;',$str);
		$str = str_replace('.','&#46;',$str);
		$n = strlen($str);
		// initialize the database
		$db = JFactory::getDBO();
		$key = '';
		$cfgName = trim($this->_app->getCfg('fromname'));
		$query = 'SELECT name FROM #__aicontactsafe_profiles ORDER BY id';
		$db->setQuery($query);
		$names = $db->loadObjectList();
		foreach($names as $name) {
			$key .= trim($name->name).$cfgName;
		}
		$m = strlen($key);
		while ($m < $n) {
			$key .= $key;
			$m = strlen($key);
		}
		$result = '';
		for($i = 0;$i<$n;$i++) {
			$result .= substr($str,$i,1).substr($key,$i,1);
		}
		return $result;
	}

	function arata_sir( $str = '' ) {
		$n = strlen($str);
		$result = '';
		for($i = 0;$i<$n;$i=$i+2) {
			$result .= substr($str,$i,1);
		}
		$result = str_replace('&#64;','@',$result);
		$result = str_replace('&#46;','.',$result);
		return $result;
	}

	// Make sure there's not anything else left to download
	function ob_clean_all() {
		$ob_active = ob_get_length() !== false;
		while($ob_active) {
			@ob_end_clean();
			$ob_active = ob_get_length() !== false;
		}
	
		return true;
	} 

	// function to validate an email address
	function validateEmail($email_address) {
		// canceled the Joomla's API test
		//jimport( 'joomla.mail.helper' );
		//$email_valid = JMailHelper::isEmailAddress($email_address);

		$atom = '[a-zA-Z0-9!#$%&\'*+\-\/=?^_`{|}~]+';
		$quoted_string = '"[^"\\\\\r\n]*"';
		$word = "$atom(\.$atom)*";
		$domain = "$atom(\.$atom)+";
		$email_valid = strlen($email_address) < 256 && preg_match("/^($word|$quoted_string)@{$domain}\$/", $email_address);

		return $email_valid;
	}

	function useUqField() {
		$uq_ini = JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'uq.ini';
		return file_exists($uq_ini);
	}

	function useCcField() {
		$cc_ini = JPATH_ROOT.'/'.'administrator'.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'cc.ini';
		return file_exists($cc_ini);
	}

}
