<?php
/**
 * @version     $Id$ 2.0.12 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.1
 * - added custom field date format
 * - added mark_required_fields character
 * - added always_send_to_defaut
 * - added Artio activation
 * - added Joom!Fish activation
 * - removed all the fields moved to profile
 * added/fixed in version 2.0.8
 * - renamed the button to delete all the tables into the database to be more obvious 
 * - added a test for the GD library used by CAPTCHA
 * added/fixed in version 2.0.12
 * - removed the field to identify the user, the user is indetified by the log-in process
 * added/fixed in version 2.0.13
 * - added SqueezeBox for aiContactSafe feed-back
 * - added highlighting for fields with errors
 * - added the possibility to keep the session alive while the form is displayed
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
	<legend><?php echo JText::_('COM_AICONTACTSAFE_CONTROL_PANEL'); ?></legend>
	<table id="control_panel">
		<?php
			if ( count($this->withoutMessageSection) > 0 ) {
		?>
		<tr>
			<td class="key">
				<h3 style="color:#FF0000"><?php echo JText::_('COM_AICONTACTSAFE_WARNING'); ?></h3>
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<table border="0" cellpadding="0" cellspacing="0" style="color:#FF0000">
				<?php
					foreach($this->withoutMessageSection as $template) {
				?>
					<tr><td style="color:#333333; font-weight:bold;"><?php echo $template; ?></td><td>&nbsp;&nbsp;</td><td><?php echo JText::_('COM_AICONTACTSAFE_THIS_TEMPLATE_DOESNT_HAVE_THE_SECTION_MESSAGE_ACTIVATED'); ?></td></tr>
				<?php
					}
				?>
					<tr><td colspan="3">&nbsp;</td></tr>
					<tr><td colspan="3"><?php echo JText::_('COM_AICONTACTSAFE_IF_A_TEMPLATE_DOESNT_HAVE_THE_SECTION_MESSAGE_ACTIVATED_YOU_CANT_SEE_THE_COMPONENT_FEEDBACK_IN_FRONT_END'); ?></td></tr>
					<tr><td colspan="3"><?php echo JText::_('COM_AICONTACTSAFE_YOU_NEED_TO_ADD_THIS_TEXT'); ?>&nbsp;&lt;jdoc:include type="message" /&gt;&nbsp;<?php echo JText::_('COM_AICONTACTSAFE_SOMEWHERE_ABOVE_THIS_TEXT'); ?>&nbsp;&lt;jdoc:include type="component" /&gt;&nbsp;<?php echo JText::_('COM_AICONTACTSAFE_IN_THE_INDEXPHP_FILE_OF_THE_TEMPLATE'); ?></td></tr>
					<tr><td colspan="3"><?php echo JText::_('COM_AICONTACTSAFE_FOR_MORE_INFORMATION_SEE_THIS_WEB_PAGE'); ?> : <a href="http://docs.joomla.org/Jdoc_statements" target="_blank">http://docs.joomla.org/Jdoc_statements</a></td></tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<?php
			}
		?>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_GD_LIBRARY'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php
					if (count($this->gd) > 0) {
						echo '<span style="color:#009900">' . $this->gd['GD Version'] . '</span> ';
						if ($this->gd['FreeType Support']) {
							echo '<span style="color:#009900">' . JText::_('COM_AICONTACTSAFE_WITH');
						} else {
							echo '<span style="color:#FF0000">' . JText::_('COM_AICONTACTSAFE_WITHOUT');
						}
						echo ' ' . JText::_('COM_AICONTACTSAFE_FREETYPE_SUPPORT') . '</span>';
					} else {
						echo '<span style="color:#FF0000">' . JText::_('COM_AICONTACTSAFE_NOT_INSTALLED') . '</span>';
					}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_USE_AICONTACTSAFE_CSS_IN_BACKEND'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="use_css_backend" id="use_css_backend" value="1" <?php echo ($this->use_css_backend)?'checked':'' ?> />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_HIGHLIGHT_FIELDS_WITH_ERRORS'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="highlight_errors" id="highlight_errors" value="1" <?php echo ($this->highlight_errors)?'checked':'' ?> />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_USE_SQUEEZEBOX_TO_DISPLAY_THE_FEEDBACK'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="use_SqueezeBox" id="use_SqueezeBox" value="1" <?php echo ($this->use_SqueezeBox)?'checked':'' ?> />&nbsp;&nbsp;&nbsp;<font color="#FF0000"><?php echo JText::_('COM_AICONTACTSAFE_YOU_NEED_MOOTOOLS_12_WITH_THIS_REQUIREMENTS'); ?>&nbsp;:&nbsp;<a href="http://digitarald.de/project/squeezebox/#requirements" target="_blank">http://digitarald.de/project/squeezebox/#requirements</a></font>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_KEEP_SESSION_ALIVE_WHEN_FORM_IS_DISPLAYED'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="keep_session_alive" id="keep_session_alive" value="1" <?php echo ($this->keep_session_alive)?'checked':'' ?> />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_ACTIVATE_HELP'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="activate_help" id="activate_help" value="1" <?php echo ($this->activate_help)?'checked':'' ?> />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_DATE_FORMAT'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="date_format" id="date_format" value="<?php echo $this->date_format; ?>"  />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_DEFAULT_STATUS_FILTER'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->select_default_status_filter; ?>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_DEFAULT_EDITBOX_COLS'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="editbox_cols" id="editbox_cols" value="<?php echo $this->editbox_cols; ?>"  />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_DEFAULT_EDITBOX_ROWS'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="editbox_rows" id="editbox_rows" value="<?php echo $this->editbox_rows; ?>"  />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_DEFAULT_NAME'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="default_name" id="default_name" value="<?php echo $this->default_name; ?>"  />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_DEFAULT_EMAIL'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="default_email" id="default_email" value="<?php echo $this->default_email; ?>"  />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_DEFAULT_SUBJECT'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="default_subject" id="default_subject" value="<?php echo $this->default_subject; ?>"  />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_ACTIVATE_SPAM_CONTROL'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="activate_spam_control" id="activate_spam_control" value="1" <?php echo ($this->activate_spam_control)?'checked':'' ?> />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_BLOCK_MESSAGES_WITH'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<textarea class="inputbox" name="block_words" id="block_words" rows="6" cols="50"><?php echo $this->block_words; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_RECORD_BLOCKED_MESSAGES'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="record_blocked_messages" id="record_blocked_messages" value="1" <?php echo ($this->record_blocked_messages)?'checked':'' ?> />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_ACTIVATE_IP_BAN'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="activate_ip_ban" id="activate_ip_ban" value="1" <?php echo ($this->activate_ip_ban)?'checked':'' ?> />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_IPS_TO_BAN'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<textarea class="inputbox" name="ban_ips" id="ban_ips" rows="6" cols="50"><?php echo $this->ban_ips; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_REDIRECT_BANNED_IPS_TO'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="redirect_ips" id="redirect_ips" value="<?php echo $this->redirect_ips; ?>"  />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_BAN_IPS_SENDING_MESSAGES_WITH_BLOCKED_WORDS'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="ban_ips_blocked_words" id="ban_ips_blocked_words" value="1" <?php echo ($this->ban_ips_blocked_words)?'checked':'' ?> />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_MAXIMUM_BLOCKED_MESSAGES_BEFORE_IP_BAN'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="maximum_messages_ban_ip" id="maximum_messages_ban_ip" value="<?php echo $this->maximum_messages_ban_ip; ?>"  />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_MINUTES_TO_COUNT_THE_BLOCKED_MESSAGES'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="maximum_minutes_ban_ip" id="maximum_minutes_ban_ip" value="<?php echo $this->maximum_minutes_ban_ip; ?>"  />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_EMAIL_TO_NOTIFY_IP_BAN'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="email_ban_ip" id="email_ban_ip" value="<?php echo $this->email_ban_ip; ?>"  />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_SET_THE_SENDER_TO_THE_DEFAULT_JOOMLA_EMAIL_ADDRESS'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="set_sender_joomla" id="set_sender_joomla" value="1" <?php echo ($this->set_sender_joomla)?'checked':'' ?> />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_UPLOAD_ATTACHMENTS_FOLDER'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo JPATH_ROOT.DS; ?><input class="textbox" type="text" name="upload_attachments" id="upload_attachments" value="<?php echo $this->upload_attachments; ?>"  />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_MAXIMUM_ATTACHMENTS_SIZE_IN_BYTES'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="maximum_size" id="maximum_size" value="<?php echo $this->maximum_size; ?>"  />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_ALLOWED_ATTACHMENTS_TYPES'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="attachments_types" id="attachments_types" value="<?php echo $this->attachments_types; ?>"  />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_ATTACH_TO_EMAIL'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="attach_to_email" id="attach_to_email" value="1" <?php echo ($this->attach_to_email)?'checked':'' ?> />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_DELETE_FILES_AFTER_THE_MESSAGE_IS_SENT'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="delete_after_sent" id="delete_after_sent" value="1" <?php echo ($this->delete_after_sent)?'checked':'' ?> />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_CHECK_LANGUAGE_FILES'); ?>
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<button onclick="document.getElementById('task').value='check_language';this.form.submit();"><?php echo JText::_('COM_AICONTACTSAFE_CHECK'); ?></button>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_ACTIVATE_ARTIO'); ?>
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->activate_artio; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_ACTIVATE_JOOMFISH'); ?>
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->activate_joomfish; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_ACTIVATE_FALANG'); ?>
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->activate_falang; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				aiContactSafeModule
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->aiContactSafeModule_button; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				aiContactSafeForm
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->aiContactSafeForm_button; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				aiContactSafeLink
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->aiContactSafeLink_button; ?>
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_MESSAGES_IN_FRONT_END_CAN_BE_SEEN_BY'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<?php echo $this->gid_list; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_USERS_CAN_SEE_ALL_MESSAGES'); ?>
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="users_all_messages" id="users_all_messages" value="1" <?php echo ($this->users_all_messages)?'checked':'' ?> />
			</td>
		</tr>
		<tr>
			<td colspan="3" class="space">&nbsp;</td>
		</tr>
		<tr>
			<td class="key">&nbsp;
				
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<button onclick="document.getElementById('task').value='confirm_delete_all';this.form.submit();" style="color:#FF0000;"><?php echo JText::_('COM_AICONTACTSAFE_DELETE_DATABASE_TABLES'); ?></button>&nbsp;&nbsp;&nbsp;<font color="#FF0000"><?php echo JText::_('COM_AICONTACTSAFE_DO_NOT_USE'); ?></font>
			</td>
		</tr>
	</table>
</fieldset>

<?php 
	// footer of the adminForm
	// don't remove this line
	echo $this->getTmplFooter();
?>
