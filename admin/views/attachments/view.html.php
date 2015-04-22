<?php
/**
 * @version     $Id$ 2.0.7 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.13
 * - added link to download any attachment in the "Attachmenets" window
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the about view class of aiContactSafe
class AiContactSafeViewAttachments extends AiContactSafeViewDefault {

	// deactivate the buttons on this page
	function setToolbarButtons() {
		$bar = JToolBar::getInstance('toolbar');
		switch(true) {
			case $this->_task == 'delete' :
				JToolBarHelper::custom( 'confirmDelete',  'apply_ai.png', 'apply_ai.png', JText::_('COM_AICONTACTSAFE_CONFIRM'), true,  false );
				JToolBarHelper::custom( 'cancel', 'cancel_ai.gif', 'cancel_ai.gif', JText::_('COM_AICONTACTSAFE_CANCEL'), false,  false );
				break;
			case $this->_task == 'display' :
				JToolBarHelper::custom( 'delete', 'delete_ai.gif', 'delete_ai.gif', JText::_('COM_AICONTACTSAFE_DELETE'), true,  false );
				break;
		}
		$bar->appendButton( 'Separator', 'divider');
		$bar->appendButton( 'Popup', 'help', JText::_('COM_AICONTACTSAFE_HELP'), $this->help_url.'com_aicontactsafe_'.$this->_sTask.'_'.$this->_task, $this->help_width, $this->help_height );
	}

	// function to initialize the variables used in the template
	function setVariables() {
		$model = $this->getModel();

		$this->filter_order = $model->filter_order;
		$this->filter_order_Dir = $model->filter_order_Dir;
		$this->limit = $model->limit;
		$this->limitstart = $model->limitstart;
		$this->filter_condition = $model->filter_condition;
		$this->filter_string = $model->filter_string;

		$this->rows = $model->getAttachments();
		$this->pageNav = $model->pageNav;

		// get the path to attachments upload

$upload_folder = '/'.$this->_config_values['upload_attachments'];

$path_upload = JPATH_ROOT.'/'.$upload_folder;

		$script = "
			//<![CDATA[
			<!--
				function submitbutton(pressbutton) {
					if(confirm('".JText::_('COM_AICONTACTSAFE_PLEASE_CONFIRM_YOU_WANT_TO_DELETE_THE_SELECTED_FILES')."')){
						document.adminForm.task.value=pressbutton;
						submitform(pressbutton);
					}
				}
			//-->
			//]]>
		";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);


	}

}
