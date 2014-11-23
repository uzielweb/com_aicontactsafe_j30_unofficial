<?php
/**
 * @version     $Id$ 2.0.12 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.1
 * - added Artio activation
 * - added Joom!Fish activation
 * added/fixed in version 2.0.12
 * - removed the field to identify the user, the user is indetified by the log-in process
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the control_panel view class of aiContactSafe
class AiContactSafeViewControl_panel extends AiContactSafeViewDefault {

	// function to define the toolbar depending on the section
	function setToolbarButtons() {
		$bar = JToolBar::getInstance('toolbar');
		switch(true) {
			// in case a delete for database tables and uploaded files was requested, ask for a confirmation
			case $this->_task == 'confirm_delete_all' :
				JToolBarHelper::custom( 'delete_all_accepted',  'apply_ai.png', 'apply_ai.png', JText::_('COM_AICONTACTSAFE_CONFIRM'), false,  false );
				JToolBarHelper::custom( 'cancel', 'cancel_ai.gif', 'cancel_ai.gif', JText::_('COM_AICONTACTSAFE_CANCEL'), false,  false );
				break;
			// or else use the default layout
			case $this->_task == 'display' :
			default :
				JToolBarHelper::custom( 'save', 'save_ai.gif', 'save_ai.gif', JText::_('COM_AICONTACTSAFE_SAVE'), false,  false );
				JToolBarHelper::custom( 'cancel', 'cancel_ai.gif', 'cancel_ai.gif', JText::_('COM_AICONTACTSAFE_CLOSE'), false,  false );
		}
		$bar->appendButton( 'Separator', 'divider');
		$bar->appendButton( 'Popup', 'help', JText::_('COM_AICONTACTSAFE_HELP'), $this->help_url.'com_aicontactsafe_'.$this->_sTask.'_'.$this->_task, $this->help_width, $this->help_height );
	}

	// function to determine where to return the control when the current section is closed
	function setsTaskReturn() {
		// record the section to return to
		$return_task = array();
		$return_task['sTask'] = 'default';
		// record the section to return to
		$this->_app->_session->set( 'return_task:' . $this->_sTask, $return_task );
	}

	// function to initialize the variables used in the template
	function setVariables() {
		// initialize the database
		$db = JFactory::getDBO();
		// load the configuration variables
		$query = "select * from  `#__aicontactsafe_config`";
		$db->setQuery( $query );
		$config_values = $db->loadObjectList();
		if ( count($config_values) > 0) {
			foreach($config_values as $value) {
				$config_key = $value->config_key;
				$config_value = $value->config_value;
				$this->$config_key = $config_value;
			}
		}
		// load the contact informations variables
		$query = "select * from  `#__aicontactsafe_contactinformations`";
		$db->setQuery( $query );
		$info_values = $db->loadObjectList();
		if ( count($info_values) > 0) {
			foreach($info_values as $value) {
				$info_key = $value->info_key;
				$info_value = $value->info_value;
				$this->$info_key = $info_value;
			}
		}

		$this->select_default_status_filter = $this->selectStatus($this->default_status_filter, 'default_status_filter', 2 );

		// initialize the model
		$model = $this->getModel();
		// ckeck GD
		$this->gd = $model->checkGD();
		// check artion
		$activate_artio = $model->check_artio();
		$this->activate_artio = '';
		switch($activate_artio) {
			case 0:
				$this->activate_artio = '<font color="#808080">' . JText::_('COM_AICONTACTSAFE_ARTIO_NOT_INSTALLED') . '</font>';
				break;
			case 1:
				$this->activate_artio = '<button onclick="document.getElementById(\'task\').value=\'activate_artio\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_ACTIVATE') . '</button>';
				break;
			case 2:
				$this->activate_artio = '<font color="#008000">' . JText::_('COM_AICONTACTSAFE_ARTIO_ACTIVATED') . '</font>';
				$this->activate_artio .= '&nbsp;&nbsp;<button onclick="document.getElementById(\'task\').value=\'deactivate_artio\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_DEACTIVATE') . '</button>';
				break;
		}
		// check joomfish
		$activate_joomfish = $model->check_joomfish();
		$this->activate_joomfish = '';
		switch($activate_joomfish) {
			case 0:
				$this->activate_joomfish = '<font color="#808080">' . JText::_('COM_AICONTACTSAFE_JOOMFISH_NOT_INSTALLED') . '</font>';
				break;
			case 1:
				$this->activate_joomfish = '<button onclick="document.getElementById(\'task\').value=\'activate_joomfish\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_ACTIVATE') . '</button>';
				break;
			case 2:
				$this->activate_joomfish = '<font color="#008000">' . JText::_('COM_AICONTACTSAFE_JOOMFISH_ACTIVATED') . '</font>';
				$this->activate_joomfish .= '&nbsp;&nbsp;<button onclick="document.getElementById(\'task\').value=\'deactivate_joomfish\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_DEACTIVATE') . '</button>';
				break;
		}

		// check falang
		$activate_falang = $model->check_falang();
		$this->activate_falang = '';
		switch($activate_falang) {
			case 0:
				$this->activate_falang = '<font color="#808080">' . JText::_('COM_AICONTACTSAFE_FALANG_NOT_INSTALLED') . '</font>';
				break;
			case 1:
				$this->activate_falang = '<button onclick="document.getElementById(\'task\').value=\'activate_falang\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_ACTIVATE') . '</button>';
				break;
			case 2:
				$this->activate_falang = '<font color="#008000">' . JText::_('COM_AICONTACTSAFE_FALANG_ACTIVATED') . '</font>';
				$this->activate_falang .= '&nbsp;&nbsp;<button onclick="document.getElementById(\'task\').value=\'deactivate_falang\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_DEACTIVATE') . '</button>';
				break;
		}

		// generate the install/uninstall aiContactSafeModule button
		$checkAiContactSafeModule = $model->checkAiContactSafeModule();
		switch($checkAiContactSafeModule) {
			case 0:
				// aiContactSafeModule is not installed
				$this->aiContactSafeModule_button = '<button onclick="document.getElementById(\'task\').value=\'installAiContactSafeModule\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_INSTALL') . '</button>&nbsp;&nbsp;&nbsp;<font color="#FF0000">' . JText::_('COM_AICONTACTSAFE_DO_NOT_USE') . '</font> ' . JText::_('COM_AICONTACTSAFE_SEE_THIS_FOR_MORE_INFO') . ' : <a class="aicontactSafe_instructions" href="http://www.algisinfo.com/en/tutorials/aicontactsafe/4-aicontactsafemodule.html" target="_blank">aiContactSafeModule tutorial</a>';
				break;
			case 1:
				// aiContactSafeModule is installed but it is an older version
				$this->aiContactSafeModule_button = '<font color="#FF0000">aiContactSafeModule ' . JText::_('COM_AICONTACTSAFE_OLDER_VERSION') . '</font>&nbsp;&nbsp;<button onclick="document.getElementById(\'task\').value=\'reinstallAiContactSafeModule\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_UPGRADE') . '</button>&nbsp;&nbsp;<button onclick="document.getElementById(\'task\').value=\'uninstallAiContactSafeModule\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_UNINSTALL') . '</button>';
				break;
			case 2:
				// aiContactSafeModule is installed and it is the last version
				$this->aiContactSafeModule_button = '<font color="#008000">aiContactSafeModule ' . JText::_('COM_AICONTACTSAFE_INSTALLED') . '</font>&nbsp;&nbsp;<button onclick="document.getElementById(\'task\').value=\'uninstallAiContactSafeModule\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_UNINSTALL') . '</button>';
				break;
		}

		// generate the install/uninstall aiContactSafeForm button
		$checkAiContactSafeForm = $model->checkAiContactSafeForm();
		switch($checkAiContactSafeForm) {
			case 0:
				// aiContactSafeForm is not installed
				$this->aiContactSafeForm_button = '<button onclick="document.getElementById(\'task\').value=\'installAiContactSafeForm\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_INSTALL') . '</button>&nbsp;&nbsp;&nbsp;<font color="#FF0000">' . JText::_('COM_AICONTACTSAFE_DO_NOT_USE') . '</font> ' . JText::_('COM_AICONTACTSAFE_SEE_THIS_FOR_MORE_INFO') . ' : <a class="aicontactSafe_instructions" href="http://www.algisinfo.com/en/tutorials/aicontactsafe/6-aicontactsafeform.html" target="_blank">aiContactSafeForm tutorial</a>';
				break;
			case 1:
				// aiContactSafeForm is installed but it is an older version
				$this->aiContactSafeForm_button = '<font color="#FF0000">aiContactSafeForm ' . JText::_('COM_AICONTACTSAFE_OLDER_VERSION') . '</font>&nbsp;&nbsp;<button onclick="document.getElementById(\'task\').value=\'reinstallAiContactSafeForm\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_UPGRADE') . '</button>&nbsp;&nbsp;<button onclick="document.getElementById(\'task\').value=\'uninstallAiContactSafeForm\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_UNINSTALL') . '</button>';
				break;
			case 2:
				// aiContactSafeForm is installed and it is the last version
				$this->aiContactSafeForm_button = '<font color="#008000">aiContactSafeForm ' . JText::_('COM_AICONTACTSAFE_INSTALLED') . '</font>&nbsp;&nbsp;<button onclick="document.getElementById(\'task\').value=\'uninstallAiContactSafeForm\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_UNINSTALL') . '</button>';
				break;
		}

		// generate the install/uninstall AiContactSafeLink button
		$checkAiContactSafeLink = $model->checkAiContactSafeLink();
		switch($checkAiContactSafeLink) {
			case 0:
				// aiContactSafeLink is not installed
				$this->aiContactSafeLink_button = '<button onclick="document.getElementById(\'task\').value=\'installAiContactSafeLink\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_INSTALL') . '</button>&nbsp;&nbsp;&nbsp;<font color="#FF0000">' . JText::_('COM_AICONTACTSAFE_DO_NOT_USE') . '</font> ' . JText::_('COM_AICONTACTSAFE_SEE_THIS_FOR_MORE_INFO') . ' : <a class="aicontactSafe_instructions" href="http://www.algisinfo.com/en/tutorials/aicontactsafe/5-aicontactsafelink.html" target="_blank">aiContactSafeLink tutorial</a>';
				break;
			case 1:
				// aiContactSafeLink is installed but it is an older version
				$this->aiContactSafeLink_button = '<font color="#FF0000">aiContactSafeLink ' . JText::_('COM_AICONTACTSAFE_OLDER_VERSION') . '</font>&nbsp;&nbsp;<button onclick="document.getElementById(\'task\').value=\'reinstallAiContactSafeLink\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_UPGRADE') . '</button>&nbsp;&nbsp;<button onclick="document.getElementById(\'task\').value=\'uninstallAiContactSafeLink\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_UNINSTALL') . '</button>';
				break;
			case 2:
				// aiContactSafeLink is installed and it is the last version
				$this->aiContactSafeLink_button = '<font color="#008000">aiContactSafeLink ' . JText::_('COM_AICONTACTSAFE_INSTALLED') . '</font>&nbsp;&nbsp;<button onclick="document.getElementById(\'task\').value=\'uninstallAiContactSafeLink\';this.form.submit();">' . JText::_('COM_AICONTACTSAFE_UNINSTALL') . '</button>';
				break;
		}

		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$this->gid_list = $this->get_gid_list_1_6();
		} else {
			// generate the user types list
			$acl = JFactory::getACL();
			$gtree = $acl->get_group_children_tree( null, 'USERS', false );
			$this->gid_list = JHTML::_('select.genericlist',   $gtree, 'gid_messages', 'size="10"', 'value', 'text', $this->gid_messages );
		}

		// check for the message section in the templates
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$this->withoutMessageSection = array();
		} else {
			$this->withoutMessageSection = $model->checkMessageSection();
		}
	}

	function get_gid_list_1_6() {
		$db = JFactory::getDbo();
		$query = 'SELECT CONCAT( REPEAT(\'..\', COUNT(parent.id) - 1), node.title) as text, node.id as value'
		. ' FROM #__usergroups AS node, #__usergroups AS parent'
		. ' WHERE node.lft BETWEEN parent.lft AND parent.rgt'
		. ' GROUP BY node.id'
		. ' ORDER BY node.lft';
		
		$db->setQuery($query);
		$groups = $db->loadObjectList();
		$attribs   = ' ';
		$attribs   .= 'size="'.count($groups).'"';
		$attribs   .= 'class="inputbox"';
		$attribs   .= 'multiple="multiple"';
		
		return JHTML::_('select.genericlist', $groups, 'gid_messages', 'size="10"', 'value', 'text', $this->gid_messages );
	}
}
