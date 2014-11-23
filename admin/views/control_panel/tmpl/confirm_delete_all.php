<?php
/**
 * @version     $Id$ 2.0.0 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php 
	// header of the adminForm
	// don't remove this line
	echo $this->getTmplHeader();
?>

<fieldset class="adminform">
	<legend><?php echo JText::_('COM_AICONTACTSAFE_CONTROL_PANEL'); ?></legend>
	<table id="control_panel">
		<tr>
			<td colspan="3">
				<div id="confirmation_message"><?php echo JText::_('COM_AICONTACTSAFE_PLEASE_CONFIRM_THE_DELETION_OF_DATABASE_TABLES'); ?></div><br/>
				<br/>
				<div id="explanation_message"><?php echo JText::_('COM_AICONTACTSAFE_DELETE_TABLES_FILES_EXPLANATION'); ?></div><br/>
			</td>
		</tr>
	</table>
</fieldset>

<?php 
	// footer of the adminForm
	// don't remove this line
	echo $this->getTmplFooter();
?>
