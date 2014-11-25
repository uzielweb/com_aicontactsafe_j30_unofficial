<?php
/**
 * @version     $Id$ 2.0.9 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the profiles view class of aiContactSafe
class AiContactSafeViewProfiles extends AiContactSafeViewDefault {

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
			case $this->_task == 'edit_contact' :
				JToolBarHelper::custom( 'save_contact',  'save_ai.png', 'save_ai.png', JText::_('COM_AICONTACTSAFE_SAVE'), false,  false );
				JToolBarHelper::custom( 'apply_contact', 'apply_ai.gif', 'apply_ai.gif', JText::_('COM_AICONTACTSAFE_APPLY'), false,  false );
				JToolBarHelper::custom( 'cancel', 'cancel_ai.gif', 'cancel_ai.gif', JText::_('COM_AICONTACTSAFE_CANCEL'), false,  false );
				break;
			case $this->_task == 'edit_css' :
				JToolBarHelper::custom( 'save_css',  'save_ai.png', 'save_ai.png', JText::_('COM_AICONTACTSAFE_SAVE'), false,  false );
				JToolBarHelper::custom( 'apply_css', 'apply_ai.gif', 'apply_ai.gif', JText::_('COM_AICONTACTSAFE_APPLY'), false,  false );
				JToolBarHelper::custom( 'cancel', 'cancel_ai.gif', 'cancel_ai.gif', JText::_('COM_AICONTACTSAFE_CANCEL'), false,  false );
				break;
			case $this->_task == 'edit_email' :
				JToolBarHelper::custom( 'save_email',  'save_ai.png', 'save_ai.png', JText::_('COM_AICONTACTSAFE_SAVE'), false,  false );
				JToolBarHelper::custom( 'apply_email', 'apply_ai.gif', 'apply_ai.gif', JText::_('COM_AICONTACTSAFE_APPLY'), false,  false );
				JToolBarHelper::custom( 'cancel', 'cancel_ai.gif', 'cancel_ai.gif', JText::_('COM_AICONTACTSAFE_CANCEL'), false,  false );
				break;
			case $this->_task == 'display' :
				JToolBarHelper::custom( 'setdefault', 'favorite_ai.gif', 'favorite_ai.gif', JText::_('COM_AICONTACTSAFE_DEFAULT'), true,  false );
				JToolBarHelper::custom( 'add', 'add_ai.gif', 'add_ai.gif', JText::_('COM_AICONTACTSAFE_ADD_NEW'), false,  false );
				JToolBarHelper::custom( 'edit', 'edit_ai.gif', 'edit_ai.gif', JText::_('COM_AICONTACTSAFE_EDIT'), true,  false );
				JToolBarHelper::custom( 'delete', 'delete_ai.gif', 'delete_ai.gif', JText::_('COM_AICONTACTSAFE_DELETE'), true,  false );
				JToolBarHelper::custom( 'copyprofile', 'copy_ai.gif', 'copy_ai.gif', JText::_('COM_AICONTACTSAFE_COPY'), true,  false );
				JToolBarHelper::custom( 'edit_contact', 'contact_ai.gif', 'contact_ai.gif', JText::_('COM_AICONTACTSAFE_EDIT_CONTACT'), true,  false );
				JToolBarHelper::custom( 'edit_css', 'css_ai.gif', 'css_ai.gif', JText::_('COM_AICONTACTSAFE_EDIT_CSS'), true,  false );
				JToolBarHelper::custom( 'edit_email', 'email_ai.gif', 'email_ai.gif', JText::_('COM_AICONTACTSAFE_EDIT_EMAIL'), true,  false );
				JToolBarHelper::custom( 'publish', 'publish_ai.gif', 'publish_ai.gif', JText::_('COM_AICONTACTSAFE_PUBLISH'), true,  false );
				JToolBarHelper::custom( 'unpublish', 'unpublish_ai.gif', 'unpublish_ai.gif', JText::_('COM_AICONTACTSAFE_UNPUBLISH'), true,  false );
				break;
		}
		$bar->appendButton( 'Separator', 'divider');
		$bar->appendButton( 'Popup', 'help', JText::_('COM_AICONTACTSAFE_HELP'), $this->help_url.'com_aicontactsafe_'.$this->_sTask.'_'.$this->_task, $this->help_width, $this->help_height );
	}

	// function to generate the footer of the template to display
	function getTmplFooter() {
		$footer = '';
		switch(true) {
			case $this->_task == 'add' or $this->_task == 'edit' or $this->_task == 'edit_contact' or $this->_task == 'edit_css' or $this->_task == 'edit_email' :
				$footer .= '<input type="hidden" id="option" name="option" value="com_aicontactsafe" />';
				$footer .= '<input type="hidden" id="sTask" name="sTask" value="' . $this->escape($this->_sTask) . '" />';
				$footer .= '<input type="hidden" id="task" name="task" value="save" />';
				$footer .= '<input type="hidden" id="last_task" name="last_task" value="' . $this->escape($this->_task) . '" />';
				$footer .= '<input type="hidden" id="id" name="id" value="' . (int)$this->id . '" />';
				$footer .= JHTML::_( 'form.token' );
				$footer .= '</form>';
				break;
			case $this->_task == 'delete' :
				$footer .= '<input type="hidden" id="option" name="option" value="com_aicontactsafe" />';
				$footer .= '<input type="hidden" id="sTask" name="sTask" value="' . $this->escape($this->_sTask) . '" />';
				$footer .= '<input type="hidden" id="task" name="task" value="save" />';
				$footer .= '<input type="hidden" id="last_task" name="last_task" value="' . $this->escape($this->_task) . '" />';
				$footer .= '<input type="hidden" id="boxchecked" name="boxchecked" value="0" />';
				$footer .= JHTML::_( 'form.token' );
				$footer .= '</form>';
				break;
			case $this->_task == 'display' :
			default:
				$footer .= '<input type="hidden" id="option" name="option" value="com_aicontactsafe" />';
				$footer .= '<input type="hidden" id="sTask" name="sTask" value="' . $this->escape($this->_sTask) . '" />';
				$footer .= '<input type="hidden" id="task" name="task" value="' . $this->escape($this->_task) . '" />';
				$footer .= '<input type="hidden" id="last_task" name="last_task" value="' . $this->escape($this->_task) . '" />';
				$footer .= '<input type="hidden" id="boxchecked" name="boxchecked" value="0" />';
				$footer .= '<input type="hidden" id="filter_order" name="filter_order" value="' . $this->escape($this->filter_order) . '" />';
				$footer .= '<input type="hidden" id="filter_order_Dir" name="filter_order_Dir" value="" />';
				$footer .= JHTML::_( 'form.token' );
				$footer .= '</form>';
				break;
		}

		return $footer;
	}

// function to initialize the variables used in the template  

	function setVariables() {
JHtml::_('behavior.framework');
		parent::setVariables();
		$model = $this->getModel();
		if ( $this->_task == 'add' ) {
			$this->use_message_css = 1;
			$this->use_captcha = 1;
			$this->captcha_width = 400;
			$this->captcha_height = 55;
			$this->captcha_bgcolor = '#FFFFFF';
			$this->captcha_colors = '#FF0000;#00FF00;#0000FF';
			$this->always_send_to_email_address = 1;
			$this->record_message = 1;
			$this->record_fields = 1;
			$this->custom_date_format = 'dmy';
			$this->custom_date_years_back = 80;
			$this->custom_date_years_forward = 0;
			$this->required_field_mark = '*';
			$this->required_field_notification = 'Fields marked with %mark% are required.';
			$this->display_format = 2;
			$this->published = 1;
			$this->contact_info = '<img style="margin-left: 10px; float: right;" alt="articles" src="images/stories/articles.jpg" width="128" height="96" /><div style="width: 150px; float: left;">Algis Info SRL<br />Str. Harmanului Nr.63<br />bl.1A sc.A ap.8<br />Brasov, Romania<br />500232<br /><a target="_blank" href="http://www.algisinfo.com/">www.algisinfo.com</a></div>';
			$this->meta_description = '';
			$this->meta_keywords = '';
			$this->meta_robots = '';
			$this->thank_you_message = 'Email sent. Thank you for your message.';
			$this->all_fields = 1;
		}
		if ( $this->_task == 'edit' ) {
			$this->getContactInformation( $this->id );
			$this->all_fields = ($this->active_fields == 0)?1:0;
		}
		if ( $this->_task == 'add' or $this->_task == 'edit' ) {
			// generate the buttons alignment combo
			$align_buttons = array();
			// none - no settings are made
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_NONE');
			$txtSelect->type = 0;
			$align_buttons[] = $txtSelect;
			// to left
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_TO_LEFT');
			$txtSelect->type = 1;
			$align_buttons[] = $txtSelect;
			// to center
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_TO_CENTER');
			$txtSelect->type = 2;
			$align_buttons[] = $txtSelect;
			// to right
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_TO_RIGHT');
			$txtSelect->type = 3;
			$align_buttons[] = $txtSelect;
			// generate the combobox
			$this->comboAlignButtons = JHTML::_('select.genericlist',  $align_buttons, 'align_buttons', 'class="inputbox" size="1"', 'type', 'name', $this->align_buttons, false, false);

			// generate the CAPTCHA code alignment combo
			$align_captcha = array();
			// none - no settings are made
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_NONE');
			$txtSelect->type = 0;
			$align_captcha[] = $txtSelect;
			// to left
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_TO_LEFT');
			$txtSelect->type = 1;
			$align_captcha[] = $txtSelect;
			// to center
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_TO_CENTER');
			$txtSelect->type = 2;
			$align_captcha[] = $txtSelect;
			// to right
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_TO_RIGHT');
			$txtSelect->type = 3;
			$align_captcha[] = $txtSelect;
			// generate the combobox
			$this->comboAlignCaptcha = JHTML::_('select.genericlist',  $align_captcha, 'align_captcha', 'class="inputbox" size="1"', 'type', 'name', $this->align_captcha, false, false);

			// generate the use captcha combo
			$use_captcha = array();
			// Only for unregistered users
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_ONLY_FOR_UNREGISTERED_USERS');
			$txtSelect->type = '2';
			$use_captcha[] = $txtSelect;
			// Always
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_ALWAYS');
			$txtSelect->type = '1';
			$use_captcha[] = $txtSelect;
			// Never
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_NEVER');
			$txtSelect->type = '0';
			$use_captcha[] = $txtSelect;
			// generate the combobox
			$this->comboUseCaptcha = JHTML::_('select.genericlist',  $use_captcha, 'use_captcha', 'class="inputbox" size="1"', 'type', 'name', $this->use_captcha, false, false);

			// generate the type of captcha combo
			$captcha_type = array();
			// aiContactSafe native
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_AICONTACTSAFE_NATIVE');
			$txtSelect->type = '0';
			$captcha_type[] = $txtSelect;
			// the ones generated by Multiple CAPTCHA Engine plugin
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_MULTIPLE_CAPTCHA_ENGINE_PLUGIN');
			$txtSelect->type = '1';
			$captcha_type[] = $txtSelect;
			// generate the combobox
			$this->comboTypeOfCaptcha = JHTML::_('select.genericlist',  $captcha_type, 'captcha_type', 'class="inputbox" size="1"', 'type', 'name', $this->captcha_type, false, false);

			// generate the email_mode combo
			$email_mode = array();
			// plain text
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_PLAIN_TEXT');
			$txtSelect->id = '0';
			$email_mode[] = $txtSelect;
			// html
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_HTML');
			$txtSelect->id = '1';
			$email_mode[] = $txtSelect;
			// generate the combobox
			$this->comboEmail_mode = JHTML::_('select.genericlist',  $email_mode, 'email_mode', 'class="inputbox" size="1"', 'id', 'name', $this->email_mode, false, false);

			// generate the custom_date_format
			$custom_date_format = array();
			// dmy
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_DAYMONTHYEAR');
			$txtSelect->type = 'dmy';
			$custom_date_format[] = $txtSelect;
			// mdy
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_MONTHDAYYEAR');
			$txtSelect->type = 'mdy';
			$custom_date_format[] = $txtSelect;
			// ymd
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_YEARMONTHDAY');
			$txtSelect->type = 'ymd';
			$custom_date_format[] = $txtSelect;
			// generate the combobox
			$this->comboField_custom_date_format = JHTML::_('select.genericlist',  $custom_date_format, 'custom_date_format', 'class="inputbox" size="1"', 'type', 'name', $this->custom_date_format, false, false);

			$this->comboDefaultStatus = $this->selectStatus($this->default_status_id, 'default_status_id', 0 );
			$this->comboReadStatus = $this->selectStatus($this->read_status_id, 'read_status_id', 0 );
			$this->comboReplyStatus = $this->selectStatus($this->reply_status_id, 'reply_status_id', 0 );

			$field_types = array('TX','CB','RL','DT','EM','EL','JC','JU','SB','HD','NO');
			$this->comboField_name = $this->selectField($this->name_field_id, $field_types, 'name_field_id', 5);
			$field_types = array('EM','EL','JC','JU','SB','HE');
			$this->comboField_email = $this->selectField($this->email_field_id, $field_types, 'email_field_id', 5);
			$field_types = array('TX','CB','RL','DT','EM','EL','JC','JU','SB','HD','NO');
			$this->comboField_subject = $this->selectField($this->subject_field_id, $field_types, 'subject_field_id', 5);
			$field_types = array('CK');
			$this->comboField_send_to_sender = $this->selectFieldSendToSender($this->send_to_sender_field_id, $field_types, 'send_to_sender_field_id');

			$this->selected_fields_info = '&nbsp;&nbsp;&nbsp;<font color="#FF0000">' . JText::_('COM_AICONTACTSAFE_THIS_FIELD_HAS_TO_BE_SELECTED_IN_THE_FIELDS_LIST_BELOW_UNLESS_THE_DEFAULT_VALUE_IS_SELECTED') . '</font>';

			$this->select_fields = $model->getFields( $this->active_fields, $this->id );
		}

		if ( $this->_task == 'edit_contact' ) {
			$this->setRowData($this->_id);
			$this->getContactInformation( $this->id );

			// generate the display_format
			$display_format = array();
			// Without contact information
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_WITHOUT_CONTACT_INFORMATION');
			$txtSelect->id = 0;
			$display_format[] = $txtSelect;
			// Contact information on top
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_CONTACT_INFORMATION_ON_TOP');
			$txtSelect->id = 1;
			$display_format[] = $txtSelect;
			// Contact information on the right side
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_CONTACT_INFORMATION_ON_THE_RIGHT_SIDE');
			$txtSelect->id = 2;
			$display_format[] = $txtSelect;
			// Contact information on bottom
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_CONTACT_INFORMATION_ON_BOTTOM');
			$txtSelect->id = 3;
			$display_format[] = $txtSelect;
			// Contact information on the left side
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_CONTACT_INFORMATION_ON_THE_LEFT_SIDE');
			$txtSelect->id = 4;
			$display_format[] = $txtSelect;
			// In DIV tags contact information first
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_IN_DIV_TAGS_CONTACT_INFORMATION_FIRST');
			$txtSelect->id = 5;
			$display_format[] = $txtSelect;
			// In DIV tags contact information last
			$txtSelect = new stdClass;
			$txtSelect->name = JText::_('COM_AICONTACTSAFE_IN_DIV_TAGS_CONTACT_INFORMATION_LAST');
			$txtSelect->id = 6;
			$display_format[] = $txtSelect;
			// generate the combobox
			$this->combo_display_format = JHTML::_('select.genericlist',  $display_format, 'display_format', 'class="inputbox" size="1"', 'id', 'name', $this->display_format, false, false);
		}

		if ( $this->_task == 'edit_css' ) {
			$this->setRowData($this->_id);

			// read the profile's css code
			$this->profile_css_code = $model->readProfileCssFile( $this->id );
		}

		if ( $this->_task == 'edit_email' ) {
			$this->setRowData($this->_id);

			// read the mail template
			$this->mail_template = $model->readMailTemplate( $this->id );
		}

	}

	// function to get the contact informations of the current profile
	function getContactInformation( $profile_id = 0 ) {
		$model = $this->getModel();
		$contact_info = $model->getContactInformation( $profile_id );
		
		$this->contact_info = $contact_info['contact_info'];
		$this->meta_description = $contact_info['meta_description'];
		$this->meta_keywords = $contact_info['meta_keywords'];
		$this->meta_robots = $contact_info['meta_robots'];
		$this->thank_you_message = $contact_info['thank_you_message'];
		$this->required_field_notification = $contact_info['required_field_notification'];
	}

	// function to generate the html code to select a field
	function selectField( $id = 0, $field_types = array(), $html_name = 'select_field', $addSelect = 1, $onlyPublished = 0, $html_params = 'class="inputbox" size="1"', $key = 'id', $text = 'name', $idtag = false, $translate = true ) {
		$db = JFactory::getDBO();
		$query_condition = '';
		if ($onlyPublished) {
			$query_condition = ' where published = 1 ';
		} else {
			$query_condition = ' where 1 ';
		}
		if (count($field_types) > 0) {
			$query_condition .= ' and field_type IN ( \''.implode('\',\'', $field_types).'\' )';
		}
		$query = 'SELECT CONCAT(TRIM(name),\' - \',SUBSTRING(TRIM(field_label),1,50)) as ' . $text . ', id as ' . $key . ' FROM #__aicontactsafe_fields ' . $query_condition . ' ORDER BY ' . $text;
		$db->setQuery($query);
		$select_combo = $db->loadObjectList();
		if ($addSelect > 0) {
			$txtSelect = new stdClass;
			$txtSelect->$text = $this->setSelectText($addSelect);
			$txtSelect->$key = 0;
			array_unshift($select_combo, $txtSelect);
		}
		$html_select_combo = JHTML::_('select.genericlist',  $select_combo, $html_name, $html_params, $key, $text, $id, $idtag, $translate);

		return $html_select_combo;
	}

	// function to generate the html code to select a field used for send to sender
	function selectFieldSendToSender( $id = 0, $field_types = array(), $html_name = 'select_field', $onlyPublished = 0, $html_params = 'class="inputbox" size="1"', $key = 'id', $text = 'name', $idtag = false, $translate = true ) {
		$db = JFactory::getDBO();
		$query_condition = '';
		if ($onlyPublished) {
			$query_condition = ' where published = 1 ';
		} else {
			$query_condition = ' where 1 ';
		}
		if (count($field_types) > 0) {
			$query_condition .= ' and field_type IN ( \''.implode('\',\'', $field_types).'\' )';
		}
		$query = 'SELECT CONCAT(TRIM(name),\' - \',SUBSTRING(TRIM(field_label),1,50)) as ' . $text . ', id as ' . $key . ' FROM #__aicontactsafe_fields ' . $query_condition . ' ORDER BY ' . $text;
		$db->setQuery($query);
		$select_combo = $db->loadObjectList();

		$txtSelect = new stdClass;
		$txtSelect->$text = JText::_('COM_AICONTACTSAFE_ALWAYS_SEND_TO_SENDER');
		$txtSelect->$key = -1;
		array_unshift($select_combo, $txtSelect);

		$txtSelect = new stdClass;
		$txtSelect->$text = $this->setSelectText(4);
		$txtSelect->$key = 0;
		array_unshift($select_combo, $txtSelect);

		$html_select_combo = JHTML::_('select.genericlist',  $select_combo, $html_name, $html_params, $key, $text, $id, $idtag, $translate);

		return $html_select_combo;
	}

}
