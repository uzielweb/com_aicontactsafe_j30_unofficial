<?php
/**
 * @version     $Id$ 2.0.7 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the statuses view class of aiContactSafe
class AiContactSafeViewStatuses extends AiContactSafeViewDefault {

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
				JToolBarHelper::custom( 'delete', 'delete_ai.gif', 'delete_ai.gif', JText::_('COM_AICONTACTSAFE_DELETE'), true,  false );
				break;
		}
		$bar->appendButton( 'Separator', 'divider');
		$bar->appendButton( 'Popup', 'help', JText::_('COM_AICONTACTSAFE_HELP'), $this->help_url.'com_aicontactsafe_'.$this->_sTask.'_'.$this->_task, $this->help_width, $this->help_height );
	}

	// function to initialize the variables used in the template
	function setVariables() {
		parent::setVariables();
		if ( $this->_task == 'add' ) {
			$model = $this->getModel();
			$this->color = '#000000';
			$this->published = 1;
			$this->ordering = $model->getNextOrdering();
		}
		if ( $this->_task == 'add' or $this->_task == 'edit' ) {
			$document = JFactory::getDocument();
			$document->addScript( JURI::root().'administrator/components/com_aicontactsafe/includes/fcp/201a.js' );
		}
	}

}
