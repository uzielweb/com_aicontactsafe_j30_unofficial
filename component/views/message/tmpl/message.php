<?php
/**
 * @version     $Id$ 2.0.10 b
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.10.b
 * - replaced sufix with prefix as it is the correct order
 * - added the posibility to use either fixed or procentual width for the contact form and the contact information ( you can specify it in the profile )
 * added/fixed in version 2.0.13
 * - added SqueezeBox for aiContactSafe feed-back
 * - added highlighting for fields with errors
 *
 * Modified by NVYush on 03.2014
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

switch ($this->profile->align_buttons) {
	case 1:
		// left
		$this->buttons = '<div id="aiContactSafeButtons_left" style="clear:both; display:block; width:100%; text-align:left;"><div id="aiContactSafeSend" style="float:left;"><div id="aiContactSafeSend_loading_' . $this->profile->id . '" style="float:left; margin:2px;">&nbsp;</div><input class="btn" type="submit" id="aiContactSafeSendButton" value="' . JText::_('COM_AICONTACTSAFE_SEND') . '" style="float:left; margin:2px;" /></div>';
		if ($this->back_button) {
			$this->buttons .= '<div id="aiContactSafeBack" style="float:left;"><input type="button" class="btn" onclick="javascript:document.getElementById(\'adminForm_' . $this->profile->id . '\').elements[\'send_mail\'].value=2;document.getElementById(\'adminForm_' . $this->profile->id . '\').submit();" value="' . JText::_('COM_AICONTACTSAFE_BACK') . '" style="float:left; margin:2px;" /></div>';
		}
		$this->buttons .= '</div>';
		break;
	case 2:
		// center
		$this->buttons = '<div id="aiContactSafeButtons_center" style="clear:both; display:block; text-align:center;">';
		$this->buttons .= '<table style="border-width:0; padding:2; border-spacing:0; margin-left:auto; margin-right:auto;">';
		$this->buttons .= '<tr>';
		$this->buttons .= '<td><div id="aiContactSafeSend_loading_' . $this->profile->id . '">&nbsp;</div></td>';
		$this->buttons .= '<td id="td_aiContactSafeSendButton"><input class="btn" type="submit" id="aiContactSafeSendButton" value="' . JText::_('COM_AICONTACTSAFE_SEND') . '" /></td>';
		if ($this->back_button) {
			$this->buttons .= '<td id="td_aiContactSafeBack"><input class="btn" type="button" onclick="javascript:document.getElementById(\'adminForm_' . $this->profile->id . '\').elements[\'send_mail\'].value=2;document.getElementById(\'adminForm_' . $this->profile->id . '\').submit();" value="' . JText::_('COM_AICONTACTSAFE_BACK') . '" /></td>';
		}
		$this->buttons .= '</tr>';
		$this->buttons .= '</table>';
		$this->buttons .= '</div>';
		break;
	case 3:
		// right
		$this->buttons = '<div id="aiContactSafeButtons_right" style="clear:both; display:block; width:100%; text-align:right;">';
		if ($this->back_button) {
			$this->buttons .= '<div id="aiContactSafeBack" style="float:right;"><input class="btn" type="button" onclick="javascript:document.getElementById(\'adminForm_' . $this->profile->id . '\').elements[\'send_mail\'].value=2;document.getElementById(\'adminForm_' . $this->profile->id . '\').submit();" value="' . JText::_('COM_AICONTACTSAFE_BACK') . '" style="float:right; margin:2px;" /></div>';
		}
		$this->buttons .= '<div id="aiContactSafeSend" style="float:right;"><input class="btn" type="submit" id="aiContactSafeSendButton" value="' . JText::_('COM_AICONTACTSAFE_SEND') . '" style="float:right; margin:2px;" /><div id="aiContactSafeSend_loading_' . $this->profile->id . '" style="float:right; margin:2px;">&nbsp;</div></div>';
		$this->buttons .= '</div>';
		break;
	case 0:
	default :
		// none
		$this->buttons = '<div id="aiContactSafeSend"><div id="aiContactSafeSend_loading_' . $this->profile->id . '">&nbsp;</div><input class="btn" type="submit" id="aiContactSafeSendButton" value="' . JText::_('COM_AICONTACTSAFE_SEND') . '" /></div>';
		if ($this->back_button) {
			$this->buttons .= '<div id="aiContactSafeBack"><input class="btn" type="button" onclick="javascript:document.getElementById(\'adminForm_' . $this->profile->id . '\').elements[\'send_mail\'].value=2;document.getElementById(\'adminForm_' . $this->profile->id . '\').submit();" value="' . JText::_('COM_AICONTACTSAFE_BACK') . '" /></div>';
		}
		break;
}

function writeContactForm($_this) {
	?>
	<?php
	if (!$_this->returnAjaxForm) {
		// header of the adminForm
		// don't remove this line
		echo $_this->getTmplHeader();
		?>
		<div id="displayAiContactSafeForm_<?php echo $_this->profile->id; ?>">
		<?php } ?>
		<?php
		if ($_this->returnAjaxForm) {
			$doc = JDocument::getInstance();
			$renderer = $doc->loadRenderer('message');
			echo '<div class="error">';
			echo $renderer->render('message');
			echo '</div>';
			if ($_this->_app->_session->get('isOK:' . $_this->_sTask)) {
				$message = $_this->_app->_session->get('confirmationMessage:' . $_this->_sTask . '_' . $_this->r_id);
				if (strlen($message) > 0) {
					echo '<input type="hidden" id="ajax_message_sent" name="ajax_message_sent" value="1" />';
				}
			}
		}
		?>
		<div class="aiContactSafe" id="aiContactSafe_contact_form">
			<?php if ($_this->requested_fields) { ?>
				<div class="aiContactSafe" id="aiContactSafe_info"><?php echo $_this->contactinformations['required_field_notification']; ?></div>
			<?php } ?>
			<?php
			foreach ($_this->fields as $field) {
				if (is_null($field->html_label)) {
					?>
					<div class="aiContactSafe_row_hidden" id="aiContactSafe_row_<?php echo $field->name; ?>"><div class="aiContactSafe_contact_form_field_right"><?php echo $field->html_tag; ?></div></div>
					<?php
				} else {
					if ($_this->profile->bottom_row_space > 0) {
						$row_space = '<div class="row_space" style="clear:both; height:' . $_this->profile->bottom_row_space . 'px; line-height:' . $_this->profile->bottom_row_space . 'px;">&nbsp;</div>';
					} else {
						$row_space = '';
					}
					if ($field->label_after_field) {
						?>
						<div class="aiContactSafe_row<?php echo $field->has_errors ? ' with_errors' : ''; ?>" id="aiContactSafe_row_<?php echo $field->name; ?>"><div class="aiContactSafe_contact_form_field_left"><?php echo $field->html_tag; ?></div><div class="aiContactSafe_contact_form_field_label_right"><?php echo $field->html_label; ?>&nbsp;<?php echo (($field->field_required) ? '<label class="required_field">' . $_this->profile->required_field_mark . '</label>' : ''); ?></div>
							<?php
							if ($field->has_errors) {
								echo '<div class="aiContactSafe_error_msg"><ul>';
								foreach ($field->error_msg as $msg) {
									echo '<li>' . $msg . '</li>';
								}
								echo '</ul></div>';
							}
							?>
								<?php echo $row_space; ?></div>
							<?php
						} else {
							?>
						<div class="aiContactSafe_row<?php echo $field->has_errors ? ' with_errors' : ''; ?>" id="aiContactSafe_row_<?php echo $field->name; ?>"><div class="aiContactSafe_contact_form_field_label_left"><?php echo $field->html_label; ?><?php echo (($field->field_required) ? '<label class="required_field">' . $_this->profile->required_field_mark . '</label>' : ''); ?></div><div class="aiContactSafe_contact_form_field_right"><?php echo $field->html_tag; ?></div>
							<?php
							if ($field->has_errors) {
								echo '<div class="aiContactSafe_error_msg"><ul>';
								foreach ($field->error_msg as $msg) {
									echo '<li>' . $msg . '</li>';
								}
								echo '</ul></div>';
							}
							?>
							<?php echo $row_space; ?></div>
						<?php
					}
				}
			}
			?>
		</div>
		<?php if ($_this->returnAjaxForm) { ?>
			<br style="clear:all;" />
		<?php } else { ?>
		</div>
		<br style="clear:all;" />
		<?php $_this->writeCaptcha(); ?>
		<br style="clear:all;" />
		<div id="aiContactSafeBtns"><?php echo $_this->buttons; ?></div>
		<br style="clear:all;" />
		<?php
		// footer of the adminForm
		// don't remove this line
		echo $_this->getTmplFooter();
	}
	?>
	<?php
}
?>

<?php if ($this->show_page_title && !$this->returnAjaxForm) { ?>
	<div class="componentheading<?php echo $this->pageclass_sfx; ?>">
		<?php
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			echo '<h1>' . $this->page_title . '</h1>';
		} else {
			echo $this->page_title;
		}
		?>
	</div>
<?php } ?>
<div class="contentpaneopen<?php echo $this->pageclass_sfx; ?>">
	<?php
	if ($this->returnAjaxForm) {
		header('Content-Type: text/html; charset=UTF-8');
		writeContactForm($this);
		jexit();
	} else {
		switch ($this->profile->display_format) {
			case 1 :
				?>
				<table id="aiContactSafeForm" style="border-width:0; padding:0; border-spacing:5;">
					<tr><td style="vertical-align:top;" <?php echo $this->profile->contact_info_width > 0 ? 'style="width:' . $this->profile->contact_info_width . ';"' : ''; ?> ><?php echo $this->contactinformations['contact_info']; ?></td></tr>
					<tr>
						<td style="vertical-align:top;" <?php echo $this->profile->contact_form_width > 0 ? 'style="width:' . $this->profile->contact_form_width . ';"' : ''; ?> >
							<br style="clear:all;" />
							<?php writeContactForm($this); ?>
						</td>
					</tr>
				</table>
				<?php
				break;
			case 2 :
				?>
				<table id="aiContactSafeForm" style="border-width:0; padding:0; border-spacing:5;">
					<tr>
						<td style="vertical-align:top;" <?php echo $this->profile->contact_form_width > 0 ? 'style="width:' . $this->profile->contact_form_width . ';"' : ''; ?> >
							<?php writeContactForm($this); ?>
						</td>
						<td style="vertical-align:top;" <?php echo $this->profile->contact_info_width > 0 ? 'style="width:' . $this->profile->contact_info_width . ';"' : ''; ?> ><?php echo $this->contactinformations['contact_info']; ?></td>
					</tr>
				</table>
				<?php
				break;
			case 3 :
				?>
				<table id="aiContactSafeForm" style="border-width:0; padding:0; border-spacing:5;">
					<tr>
						<td style="vertical-align:top;" <?php echo $this->profile->contact_form_width > 0 ? 'style="width:' . $this->profile->contact_form_width . ';"' : ''; ?> >
							<?php writeContactForm($this); ?>
						</td>
					</tr>
					<tr><td style="vertical-align:top;" <?php echo $this->profile->contact_info_width > 0 ? 'style="width:' . $this->profile->contact_info_width . ';"' : ''; ?> ><?php echo $this->contactinformations['contact_info']; ?></td></tr>
				</table>
				<?php
				break;
			case 4 :
				?>
				<table id="aiContactSafeForm" style="border-width:0; padding:0; border-spacing:5;">
					<tr>
						<td style="vertical-align:top;" <?php echo $this->profile->contact_info_width > 0 ? 'style="width:' . $this->profile->contact_info_width . ';"' : ''; ?> ><?php echo $this->contactinformations['contact_info']; ?></td>
						<td style="vertical-align:top;" <?php echo $this->profile->contact_form_width > 0 ? 'style="width:' . $this->profile->contact_form_width . ';"' : ''; ?> >
							<?php writeContactForm($this); ?>
						</td>
					</tr>
				</table>
				<?php
				break;
			case 5 :
				?>
				<div id="aiContactSafeForm">
					<div id="aiContactSafeForm_contact_info" <?php echo $this->profile->contact_info_width > 0 ? 'style="width:' . $this->profile->contact_info_width . ';"' : ''; ?> ><?php echo $this->contactinformations['contact_info']; ?></div>
					<div id="aiContactSafeForm_contact_form" <?php echo $this->profile->contact_form_width > 0 ? 'style="width:' . $this->profile->contact_form_width . ';"' : ''; ?> >
						<?php writeContactForm($this); ?>
					</div>
				</div>
				<?php
				break;
			case 6 :
				?>
				<div id="aiContactSafeForm">
					<div id="aiContactSafeForm_contact_form" <?php echo $this->profile->contact_form_width > 0 ? 'style="width:' . $this->profile->contact_form_width . ';"' : ''; ?> >
						<?php writeContactForm($this); ?>
					</div>
					<div id="aiContactSafeForm_contact_info" <?php echo $this->profile->contact_info_width > 0 ? 'style="width:' . $this->profile->contact_info_width . ';"' : ''; ?> ><?php echo $this->contactinformations['contact_info']; ?></div>
				</div>
				<?php
				break;
			case 0 :
			default :
				?>
				<div id="aiContactSafeForm" <?php echo $this->profile->contact_form_width > 0 ? 'style="width:' . $this->profile->contact_form_width . ';"' : ''; ?> >
					<?php writeContactForm($this); ?>
				</div>
			<?php
		}
	}
	?>
</div>
