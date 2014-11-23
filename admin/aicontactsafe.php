<?php
/**
 * @version     $Id$ 2.0.0 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// start the session if no session was started
if ( session_id() == '' ) {
	session_start();
}

// load the main controller
require_once( JPATH_COMPONENT.DS.'controller.php' );

// load the main model
require_once( JPATH_COMPONENT.DS.'models'.DS.'default.php' );

// load the main view
require_once( JPATH_COMPONENT.DS.'views'.DS.'default'.DS.'view.html.php' );

// include the table directory
JTable::addIncludePath(JPATH_ROOT.DS.'components'.DS.'com_aicontactsafe'.DS.'includes'.DS.'tables');

// get the current task, default is 'display'
$task = JRequest::getCmd('task', 'display');
// get the section of the component
$sTask = JRequest::getCmd( 'sTask', '' );

// check the task for commands from the main toolbar
switch($task) {
	case 'control_panel':
		$sTask = 'control_panel';
		$task = 'display';
		break;
}

// it the sTask variable is 'default' reset it to ''
if ($sTask == 'default' or $sTask == '' or ( $sTask != 'messages' && $sTask != 'attachments' && $sTask != 'profiles' && $sTask != 'fields' && $sTask != 'statuses' && $sTask != 'control_panel' && $sTask != 'about' )){
	$sTask = 'messages';
}

if(strlen(trim($task)) == 0) {
	$task = 'display';
}

// if a section is selected the coresponding controller is loaded
if (strlen($sTask) > 0){
	require_once( JPATH_COMPONENT.DS.'controllers'.DS.$sTask.'.php' );
}
$controllerName = 'AiContactSafeController'.$sTask;

// load the submenu so all the sections of the component are always on the screen
setSubMenu($sTask);

// generate the parameters for the controller
$controller_parameters = array('task'=>$task,'sTask'=>$sTask);
// load the controller and execute the current task
$controller = new $controllerName($controller_parameters);
$controller->execute( $task );
$controller->redirect();

// define the submenu (it should contain all the section of the component)
function setSubMenu($sTask = '') {
	JSubMenuHelper::addEntry(JText::_('COM_AICONTACTSAFE_MESSAGES'), 'index.php?option=com_aicontactsafe&sTask=messages', $sTask == 'messages');
	JSubMenuHelper::addEntry(JText::_('COM_AICONTACTSAFE_ATTACHMENTS'), 'index.php?option=com_aicontactsafe&sTask=attachments', $sTask == 'attachments');
	JSubMenuHelper::addEntry(JText::_('COM_AICONTACTSAFE_PROFILES'), 'index.php?option=com_aicontactsafe&sTask=profiles', $sTask == 'profiles');
	JSubMenuHelper::addEntry(JText::_('COM_AICONTACTSAFE_FIELDS'), 'index.php?option=com_aicontactsafe&sTask=fields', $sTask == 'fields');
	JSubMenuHelper::addEntry(JText::_('COM_AICONTACTSAFE_MESSAGE_STATUSES'), 'index.php?option=com_aicontactsafe&sTask=statuses', $sTask == 'statuses');
	JSubMenuHelper::addEntry(JText::_('COM_AICONTACTSAFE_CONTROL_PANEL'), 'index.php?option=com_aicontactsafe&sTask=control_panel', $sTask == 'control_panel');
	JSubMenuHelper::addEntry(JText::_('COM_AICONTACTSAFE_ABOUT'), 'index.php?option=com_aicontactsafe&sTask=about', $sTask == 'about');
}
