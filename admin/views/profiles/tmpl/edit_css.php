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

<script type="text/javascript" language="javascript">
	function setProfileCSS(css_type) {
		var id = document.getElementById('id').value;
		var url = '<?php echo JURI::base(); ?>index.php?option=com_aicontactsafe&sTask=profiles&task=setcss&id='+id+'&css_type='+css_type+'&format=raw';

		<?php if(version_compare(JVERSION, '1.6.0', 'ge')) { ?>
		var xCaptcha = new Request({
			url: url, 
			method: 'get', 
			onRequest: function(){ $('wait_for_css_change').innerHTML = '<?php echo JText::_('COM_AICONTACTSAFE_PLEASE_WAIT') . '&nbsp;&nbsp;<img id="imgLoading" border="0" src="'.JURI::root().'administrator/components/com_aicontactsafe/images/load.gif" />&nbsp;&nbsp;'; ?>'; },
			onComplete: function(){ $('profile_css_code').value=this.response.text;$('wait_for_css_change').innerHTML = '&nbsp;'; }
		}).send();
		<?php } else { ?>
		new Ajax(url, {
			method: 'get',
			onRequest: function(){ $('wait_for_css_change').innerHTML = '<?php echo JText::_('COM_AICONTACTSAFE_PLEASE_WAIT') . '&nbsp;&nbsp;<img id="imgLoading" border="0" src="'.JURI::root().'administrator/components/com_aicontactsafe/images/load.gif" />&nbsp;&nbsp;'; ?>'; },
			onComplete: function(){ $('profile_css_code').value=this.response.text;$('wait_for_css_change').innerHTML = '&nbsp;'; }
		}).request();
		<?php } ?>
	}
</script>

<fieldset class="adminform">
	<legend><?php echo JText::_('COM_AICONTACTSAFE_EDIT_THE_CSS_OF_THE_PROFILE'); ?></legend>
	<table id="edit_css">
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_USE_AICONTACTSAFE_CSS_IN_FRONTEND'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="checkbox" type="checkbox" name="use_message_css" id="use_message_css" value="1" <?php echo ($this->use_message_css)?'checked="checked"':''; ?>  />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_ALIGN_LABEL_AND_FIELDS'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<table border="0" id="aiContactSafe_align_label_and_fields" border="0" cellpadding="0" cellspacing="2">
					<tr>
						<td><a href="javascript:void(0);" onclick="setProfileCSS('align_margin');"><img src="<?php echo JURI::root().'administrator/components/com_aicontactsafe/images/align_margin.gif'; ?>" border="0" alt="<?php echo JText::_('COM_AICONTACTSAFE_TO_MARGIN'); ?>" title="<?php echo JText::_('COM_AICONTACTSAFE_TO_MARGIN'); ?>" /></a></td>
						<td><a href="javascript:void(0);" onclick="setProfileCSS('align_center');"><img src="<?php echo JURI::root().'administrator/components/com_aicontactsafe/images/align_center.gif'; ?>" border="0" alt="<?php echo JText::_('COM_AICONTACTSAFE_TO_CENTER'); ?>" title="<?php echo JText::_('COM_AICONTACTSAFE_TO_CENTER'); ?>" /></a></td>
						<td><a href="javascript:void(0);" onclick="setProfileCSS('align_left');"><img src="<?php echo JURI::root().'administrator/components/com_aicontactsafe/images/align_left.gif'; ?>" border="0" alt="<?php echo JText::_('COM_AICONTACTSAFE_TO_LEFT'); ?>" title="<?php echo JText::_('COM_AICONTACTSAFE_TO_LEFT'); ?>" /></a></td>
						<td><a href="javascript:void(0);" onclick="setProfileCSS('align_right');"><img src="<?php echo JURI::root().'administrator/components/com_aicontactsafe/images/align_right.gif'; ?>" border="0" alt="<?php echo JText::_('COM_AICONTACTSAFE_TO_RIGHT'); ?>" title="<?php echo JText::_('COM_AICONTACTSAFE_TO_RIGHT'); ?>" /></a></td>
						<td><a href="javascript:void(0);" onclick="setProfileCSS('align_all_left');"><img src="<?php echo JURI::root().'administrator/components/com_aicontactsafe/images/align_all_left.gif'; ?>" border="0" alt="<?php echo JText::_('COM_AICONTACTSAFE_ALL_LEFT'); ?>" title="<?php echo JText::_('COM_AICONTACTSAFE_ALL_LEFT'); ?>" /></a></td>
						<td><a href="javascript:void(0);" onclick="setProfileCSS('align_all_right');"><img src="<?php echo JURI::root().'administrator/components/com_aicontactsafe/images/align_all_right.gif'; ?>" border="0" alt="<?php echo JText::_('COM_AICONTACTSAFE_ALL_RIGHT'); ?>" title="<?php echo JText::_('COM_AICONTACTSAFE_ALL_RIGHT'); ?>" /></a></td>
						<td><a href="javascript:void(0);" onclick="setProfileCSS('align_all_center');"><img src="<?php echo JURI::root().'administrator/components/com_aicontactsafe/images/align_all_center.gif'; ?>" border="0" alt="<?php echo JText::_('COM_AICONTACTSAFE_ALL_CENTER'); ?>" title="<?php echo JText::_('COM_AICONTACTSAFE_ALL_CENTER'); ?>" /></a></td>
						<td><div id="wait_for_css_change">&nbsp;</div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_CSS_CODE_OF_THE_PROFILE'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<textarea name="profile_css_code" id="profile_css_code" cols="60" rows="60"><?php echo $this->profile_css_code; ?></textarea>
			</td>
		</tr>
	</table>
</fieldset>

<?php 
	// footer of the adminForm
	// don't remove this line
	echo $this->getTmplFooter();
?>
