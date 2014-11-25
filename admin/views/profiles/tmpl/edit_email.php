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
	<legend><?php echo JText::_('COM_AICONTACTSAFE_EDIT_EMAIL_TEMPLATE'); ?></legend>
	<table id="edit_email">
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_USE_MAIL_TEMPLATE'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="use_mail_template" id="use_mail_template" value="1" <?php echo ($this->use_mail_template)?'checked="checked"':''; ?>  />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_MAIL_TEMPLATE'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<textarea name="mail_template" id="mail_template" cols="60" rows="60"><?php echo $this->mail_template; ?></textarea>
			</td>
		</tr>
	</table>
</fieldset>

<?php 
	// footer of the adminForm
	// don't remove this line
	echo $this->getTmplFooter();
?>
