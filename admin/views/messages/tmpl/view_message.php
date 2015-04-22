<?php
/**
 * @version     $Id$ 2.0.1 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.1
 * - added link to whois.domaintools.com to see more informations about the sender's IP
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
	<legend><?php echo JText::_('COM_AICONTACTSAFE_VIEW_MESSAGE'); ?></legend>
	<table id="message">
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_NAME'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->name;?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_EMAIL'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->email;?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_SUBJECT'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->subject;?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_MESSAGE'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->message;?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_SENT_TO_SENDER'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php
				if ($this->send_to_sender) {
					$img = JURI::root().'administrator/components/com_aicontactsafe/images/ok.gif';
					$alt = JText::_('COM_AICONTACTSAFE_SENT_TO_SENDER');
				} else {
					$img = JURI::root().'administrator/components/com_aicontactsafe/images/not_ok.gif';
					$alt = JText::_('COM_AICONTACTSAFE_NOT_SENT_TO_SENDER');
				}
				?>
				<img src="<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_PROFILE'); ?>
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->profile;?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_SENDERS_IP'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<a class="aiContactSafe" href="http://whois.domaintools.com/<?php echo $this->sender_ip;?>" target="_blank"><?php echo $this->sender_ip;?></a>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_DATE_ADDED'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo JHTML::_('date', $this->date_added, $this->_config_values['date_format'] ); ?>
			</td>
		</tr>
		<?php if(strlen(trim($this->message_reply))>0) { ?>
		<tr>
			<td class="key">&nbsp;
				
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">&nbsp;
				
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_REPLY'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<table id="reply">
					<tr>
						<td class="key_reply">
							<?php echo JText::_('COM_AICONTACTSAFE_EMAIL_ADDRESS'); ?>:
						</td>
						<td class="space_reply">&nbsp;</td>
						<td class="value_reply">
							<?php echo $this->email_reply; ?>
						</td>
					</tr>
					<tr>
						<td class="key_reply">
							<?php echo JText::_('COM_AICONTACTSAFE_SUBJECT'); ?>:
						</td>
						<td class="space_reply">&nbsp;</td>
						<td class="value_reply">
							<?php echo $this->subject_reply;?>
						</td>
					</tr>
					<tr>
						<td class="key_reply">
							<?php echo JText::_('COM_AICONTACTSAFE_MESSAGE'); ?>:
						</td>
						<td class="space_reply">&nbsp;</td>
						<td class="value_reply">
							<?php echo htmlspecialchars_decode($this->message_reply); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php } ?>
	</table>
</fieldset>

<?php 
	// footer of the adminForm
	// don't remove this line
	echo $this->getTmplFooter();
?>
