<?php
/**
 * @version     $Id$ 2.0.13 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.1
 * - added new field types Checkbox - List, Radio - List, Date, Email, Email - List, Joomla Contacts, Joomla Users, Hidden, Separator
 * added/fixed in version 2.0.13
 * - added the possibility to duplicate one or more fields
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the fields view class of aiContactSafe
class AiContactSafeViewFields extends AiContactSafeViewDefault {

	// function to define the toolbar depending on the section
	function setToolbarButtons() {
		$bar = JToolBar::getInstance('toolbar');
		switch(true) {
			case $this->_task == 'add' or $this->_task == 'edit' :
				JToolBarHelper::custom( 'save', 'save_ai.gif', 'save_ai.gif', JText::_('COM_AICONTACTSAFE_SAVE'), false,  false );
				JToolBarHelper::custom( 'apply', 'apply_ai.gif', 'apply_ai.gif', JText::_('COM_AICONTACTSAFE_APPLY'), false,  false );
				JToolBarHelper::custom( 'cancel', 'cancel_ai.gif', 'cancel_ai.gif', JText::_('COM_AICONTACTSAFE_CANCEL'), false,  false );
				break;
			case $this->_task == 'delete' :
				JToolBarHelper::custom( 'confirmDelete',  'apply_ai.png', 'apply_ai.png', JText::_('COM_AICONTACTSAFE_CONFIRM'), true,  false );
				JToolBarHelper::custom( 'cancel', 'cancel_ai.gif', 'cancel_ai.gif', JText::_('COM_AICONTACTSAFE_CANCEL'), false,  false );
				break;
			case $this->_task == 'display' :
				JToolBarHelper::custom( 'add', 'add_ai.gif', 'add_ai.gif', JText::_('COM_AICONTACTSAFE_ADD_NEW'), false,  false );
				JToolBarHelper::custom( 'edit', 'edit_ai.gif', 'edit_ai.gif', JText::_('COM_AICONTACTSAFE_EDIT'), true,  false );
				JToolBarHelper::custom( 'copyfield', 'copy_ai.gif', 'copy_ai.gif', JText::_('COM_AICONTACTSAFE_COPY'), true,  false );
				JToolBarHelper::custom( 'delete', 'delete_ai.gif', 'delete_ai.gif', JText::_('COM_AICONTACTSAFE_DELETE'), true,  false );
				JToolBarHelper::custom( 'publish', 'publish_ai.gif', 'publish_ai.gif', JText::_('COM_AICONTACTSAFE_PUBLISH'), true,  false );
				JToolBarHelper::custom( 'unpublish', 'unpublish_ai.gif', 'unpublish_ai.gif', JText::_('COM_AICONTACTSAFE_UNPUBLISH'), true,  false );
				break;
		}
		$bar->appendButton( 'Separator', 'divider');
		$bar->appendButton( 'Popup', 'help', JText::_('COM_AICONTACTSAFE_HELP'), $this->help_url.'com_aicontactsafe_'.$this->_sTask.'_'.$this->_task, $this->help_width, $this->help_height );
	}

	// function to initialize the variables used in the template
	function setVariables() {
		parent::setVariables();
		$model = $this->getModel();
		if ( $this->_task == 'add' ) {
			$this->send_message = 1;
			$this->field_in_message = 1;
			$this->published = 1;
			$this->ordering = $model->getNextOrdering();
		}
		if ( $this->_task == 'add' or $this->_task == 'edit' ) {
			// generate the field type combo
			$select_combo = array();
			// textbox - TX
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_TEXTBOX');
			$txtSelect->type = 'TX';
			$select_combo[] = $txtSelect;
			// checkbox - CK
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_CHECKBOX');
			$txtSelect->type = 'CK';
			$select_combo[] = $txtSelect;
			// combobox - CB
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_COMBOBOX');
			$txtSelect->type = 'CB';
			$select_combo[] = $txtSelect;
			// editbox - ED
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_EDITBOX');
			$txtSelect->type = 'ED';
			$select_combo[] = $txtSelect;
			// checkbox list - CL
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_CHECKBOX_LIST');
			$txtSelect->type = 'CL';
			$select_combo[] = $txtSelect;
			// radio list - RL
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_RADIO_LIST');
			$txtSelect->type = 'RL';
			$select_combo[] = $txtSelect;
			// date - DT
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_DATE');
			$txtSelect->type = 'DT';
			$select_combo[] = $txtSelect;
			// email - EM
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_EMAIL');
			$txtSelect->type = 'EM';
			$select_combo[] = $txtSelect;
			// email - EL
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_EMAIL_LIST');
			$txtSelect->type = 'EL';
			$select_combo[] = $txtSelect;
			// contact - JC
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_JOOMLA_CONTACTS');
			$txtSelect->type = 'JC';
			$select_combo[] = $txtSelect;
			// contact - JU
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_JOOMLA_USERS');
			$txtSelect->type = 'JU';
			$select_combo[] = $txtSelect;
			// contact - SB
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_SOBI2_ENTRIES');
			$txtSelect->type = 'SB';
			$select_combo[] = $txtSelect;
			// hidden - HD
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_HIDDEN');
			$txtSelect->type = 'HD';
			$select_combo[] = $txtSelect;
			// hidden - SP
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_SEPARATOR');
			$txtSelect->type = 'SP';
			$select_combo[] = $txtSelect;
			// file - FL
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_FILE');
			$txtSelect->type = 'FL';
			$select_combo[] = $txtSelect;
			// file - NO
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_NUMBER');
			$txtSelect->type = 'NO';
			$select_combo[] = $txtSelect;
			// file - HE
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_HIDDEN_EMAIL');
			$txtSelect->type = 'HE';
			$select_combo[] = $txtSelect;
			if($model->useUqField()) {
				// textbox - UQ
				$txtSelect = new stdClass;
				$txtSelect->name = JText::_('COM_AICONTACTSAFE_UNIQUETEXT');
				$txtSelect->type = 'UQ';
				$select_combo[] = $txtSelect;
			}
			if($model->useCcField()) {
				// credit card - CC
				$txtSelect = new stdClass;
				$txtSelect->name = JText::_('COM_AICONTACTSAFE_CREDIT_CARD');
				$txtSelect->type = 'CC';
				$select_combo[] = $txtSelect;
			}

			// generate the html tag
			$this->comboField_type = JHTML::_('select.genericlist',  $select_combo, 'field_type', 'class="inputbox" size="1" onchange="checkFieldValues();"', 'type', 'name', $this->field_type, false, false);
	
			$script = "
				function checkFieldValues() {
					var field_type = document.getElementById('field_type').value;
					if (field_type == 'CB' || field_type == 'CL' || field_type == 'RL' || field_type == 'EL' || field_type == 'SB' || field_type == 'HD' || field_type == 'SP' || field_type == 'HE') {
						document.getElementById('field_values').removeAttribute('disabled');
					} else {
						document.getElementById('field_values').value = '';
						document.getElementById('field_values').setAttribute('disabled', true); 
					}
					if (field_type == 'EM' || field_type == 'EL' || field_type == 'JC' || field_type == 'JU' || field_type == 'SB' || field_type == 'SB') {
						document.getElementById('send_message').removeAttribute('disabled');
					} else {
						document.getElementById('send_message').checked = true;
						document.getElementById('send_message').setAttribute('disabled', true); 
					}
				}
				function copyLabel() {
					var field_label_message = document.getElementById('field_label_message').value;
					if ( field_label_message.length == 0 && document.getElementById('id').value == 0 ) {
						document.getElementById('field_label_message').value = document.getElementById('field_label').value;
					}
				}
				function copyParameters() {
					var label_message_parameters = document.getElementById('label_message_parameters').value;
					if ( label_message_parameters.length == 0 && document.getElementById('id').value == 0 ) {
						document.getElementById('label_message_parameters').value = document.getElementById('label_parameters').value;
					}
				}";

			$document = JFactory::getDocument();
			$document->addScriptDeclaration($script);

			// generate the field type combo
			$select_combo = array();
			// none
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_NONE');
			$txtSelect->type = '';
			$select_combo[] = $txtSelect;
			// Joomla User name
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_JOOMLA_USER_NAME');
			$txtSelect->type = 'UN';
			$select_combo[] = $txtSelect;
			// Joomla User email
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_JOOMLA_USER_EMAIL');
			$txtSelect->type = 'UE';
			$select_combo[] = $txtSelect;

			// generate the html tag
			$this->comboAutoFill = JHTML::_('select.genericlist',  $select_combo, 'auto_fill', 'class="inputbox" size="1"', 'type', 'name', $this->auto_fill, false, false);

		}
		if ( $this->_task == 'display' ) {
			$countRows = count($this->rows);
			for ($i = 0; $i<$countRows; $i++) {
				switch($this->rows[$i]->field_type) {
					case 'TX' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_TEXTBOX');
						break;
					case 'CK' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_CHECKBOX');
						break;
					case 'CB' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_COMBOBOX');
						break;
					case 'ED' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_EDITBOX');
						break;
					case 'CL' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_CHECKBOX_LIST');
						break;
					case 'RL' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_RADIO_LIST');
						break;
					case 'DT' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_DATE');
						break;
					case 'EM' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_EMAIL');
						break;
					case 'EL' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_EMAIL_LIST');
						break;
					case 'JC' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_JOOMLA_CONTACTS');
						break;
					case 'JU' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_JOOMLA_USERS');
						break;
					case 'SB' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_SOBI2_ENTRIES');
						break;
					case 'HD' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_HIDDEN');
						break;
					case 'SP' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_SEPARATOR');
						break;
					case 'FL' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_FILE');
						break;
					case 'NO' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_NUMBER');
						break;
					case 'HE' :
						$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_HIDDEN_EMAIL');
						break;
					case 'UQ' :
						if($model->useUqField()) {
							$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_UNIQUETEXT');
						}
						break;
					case 'CC' :
						if($model->useCcField()) {
							$this->rows[$i]->field_type_text = JText::_('COM_AICONTACTSAFE_CREDIT_CARD');
						}
						break;
				}
			}
		}
	}

}
