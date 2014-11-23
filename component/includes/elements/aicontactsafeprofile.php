<?php
/**
 * @version     $Id$ 2.0.1 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(version_compare(JVERSION, '1.6.0', 'ge')) {
	JFormHelper::loadFieldClass('list'); 
	class JFormFieldAiContactSafeProfile extends JFormFieldList {
		public $type = 'AiContactSafeProfile';
		protected function getInput() {
			$db = JFactory::getDBO();
			$query = 'SELECT name, id from `#__aicontactsafe_profiles` WHERE published = 1';
			$db->setQuery($query);
			$profiles = $db->loadObjectList();
	
			$attr = '';
			$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
			$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
			$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
			$attr .= $this->multiple ? ' multiple="multiple"' : '';

			// Get the field options.
			$options = $this->getOptions();

			return JHTML::_('select.genericlist',  $profiles, ''.$this->name, $attr, 'id', 'name', $this->value, $this->id );
		}
	}
} else {
	class JElementAiContactSafeProfile extends JElement {
	
		var	$_name = 'aiContactSafeProfile';
	
		function fetchElement($name, $value, &$node, $control_name) {
			$db = JFactory::getDBO();
	
			$class		= $node->attributes('class');
			if (!$class) {
				$class = "inputbox";
			}
	
			$query = 'SELECT name, id from `#__aicontactsafe_profiles` WHERE published = 1';
			$db->setQuery($query);
			$profiles = $db->loadObjectList();
	
			return JHTML::_('select.genericlist',  $profiles, ''.$control_name.'['.$name.']', 'class="'.$class.'"', 'id', 'name', $value, $control_name.$name );
		}
	
	}
}
