<?php
/**
 * @version     $Id$ 2.0.9 0
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
	<legend><?php echo JText::_('COM_AICONTACTSAFE_EDIT_CONTACT_INFORMATION'); ?></legend>
	<table id="edit_css">
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_CONTACT_INFORMATION'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php
					$editor = JFactory::getEditor();
					echo $editor->display('contact_info', $this->contact_info, '550', '400', '60', '20', false);
				?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_DISPLAY_FORMAT'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->combo_display_format; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_ACTIVATE_PLUGINS_ON_THE_CONTACT_INFORMATION'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="plg_contact_info" id="plg_contact_info" value="1" <?php echo ($this->plg_contact_info)?'checked="checked"':''; ?>  />
			</td>
		</tr>
	</table>
</fieldset>

<?php 
	// footer of the adminForm
	// don't remove this line
	echo $this->getTmplFooter();
?>
