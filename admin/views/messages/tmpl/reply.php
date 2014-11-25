<?php
/**
 * @version     $Id$ 2.0.8 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.8
 * - added the possibility to send the reply in plain text
 *
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
	<legend><?php echo JText::_('COM_AICONTACTSAFE_REPLY'); ?></legend>
	<table id="reply">
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_EMAIL_ADDRESS'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="reply_email_address" id="reply_email_address" value="<?php echo $this->reply_email_address; ?>"  />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_SUBJECT'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="reply_subject" id="reply_subject" value="<?php echo $this->reply_subject;?>" />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_MESSAGE'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<textarea name="reply_message" id="reply_message" cols="60" rows="12"><?php echo $this->reply_message; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="key">&nbsp;
				
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td><input class="checkbox" type="checkbox" name="send_plain_text" id="send_plain_text" value="1" /></td>
						<td>&nbsp;</td>
						<td><?php echo JText::_('COM_AICONTACTSAFE_SEND_AS_PLAIN_TEXT'); ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</fieldset>
	
<?php 
	// footer of the adminForm
	// don't remove this line
	echo $this->getTmplFooter();
?>
