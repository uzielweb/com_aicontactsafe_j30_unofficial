<?php
/**
 * @version     $Id$ 2.0.5 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

echo '<div id="aicontactsafe_toolbar">' . $this->toolbar . '</div><br clear="all" />';
?>

<?php 
	// header of the adminForm
	// don't remove this line
	echo $this->getTmplHeader();
?>

<fieldset class="adminform">
	<legend><?php echo JText::_('COM_AICONTACTSAFE_DELETE_SELECTED'); ?></legend>
	<div>
		<?php echo JText::_('COM_AICONTACTSAFE_PLEASE_CONFIRM_YOU_WANT_TO_DELETE_SELECTED_MESSAGES'); ?><br />
		<br />
		<font color="#FF0000"><strong><?php echo JText::_('COM_AICONTACTSAFE_WARNING'); ?></strong></font><br />
		<?php echo JText::_('COM_AICONTACTSAFE_YOU_WILL_NOT_BE_ABLE_TO_RESTORE_THE_DELETED_MESSAGES'); ?><br />
		<br />
	</div>
</fieldset>
	
<?php 
	// footer of the adminForm
	// don't remove this line
	echo $this->getTmplFooter();
?>
