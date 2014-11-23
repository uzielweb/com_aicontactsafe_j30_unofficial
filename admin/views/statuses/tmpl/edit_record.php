<?php
/**
 * @version     $Id$ 2.0.7 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
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
	<legend><?php echo JText::_('COM_AICONTACTSAFE_MESSAGE_STATUS'); ?></legend>
	<table id="type">
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_STATUS_NAME'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="name" id="name" value="<?php echo $this->name;?>" />
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_COLOR'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<div id="colorpicker201" class="colorpicker201"></div>
				<input class="textbox" type="text" name="color" id="color" value="<?php echo $this->color;?>" />&nbsp;
				<input type="text" id="color_sample" size="2" value="&nbsp;" style="background-color:<?php echo $this->color;?>;">&nbsp;
				<img src="<?php echo JURI::root();?>administrator/components/com_aicontactsafe/includes/fcp/sel.gif" onclick="showColorGrid2('color','color_sample');" border="0" style="cursor:pointer" alt="<?php echo JText::_('COM_AICONTACTSAFE_SELECT_COLOR'); ?>" title="<?php echo JText::_('COM_AICONTACTSAFE_SELECT_COLOR'); ?>">
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_AICONTACTSAFE_ORDER'); ?>:
			</td>
			<td class="space">&nbsp;</td>
			<td class="value">
				<input class="textbox" type="text" name="ordering" id="ordering" value="<?php echo $this->ordering;?>" />
			</td>
		</tr>
	</table>
</fieldset>
	
<?php 
	// footer of the adminForm
	// don't remove this line
	echo $this->getTmplFooter();
?>
