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

echo '<div id="aicontactsafe_toolbar">' . $this->toolbar . '</div><br clear="all" />';
?>

<?php 
	// header of the adminForm
	// don't remove this line
	echo $this->getTmplHeader();
?>

<table>
	<tr><td width="100%"><h3><?php echo JText::_('COM_AICONTACTSAFE_MESSAGES'); ?></h3></td></tr>
	<tr><td>
		<table border="0" cellpadding="0" cellspacing="2">
			<tr>
				<td>
					<?php echo JText::_('COM_AICONTACTSAFE_NAME'); ?>
				</td>
				<td>
					<input type="text" name="filter_string" id="filter_string" value="<?php echo $this->escape($this->filter_string); ?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_AICONTACTSAFE_FILTER_BY_NAME');?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_AICONTACTSAFE_EMAIL'); ?>
				</td>
				<td>
					<input type="text" name="filter_email" id="filter_email" value="<?php echo $this->escape($this->filter_email); ?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_AICONTACTSAFE_FILTER_BY_EMAIL');?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_AICONTACTSAFE_SUBJECT'); ?>
				</td>
				<td>
					<input type="text" name="filter_subject" id="filter_subject" value="<?php echo $this->escape($this->filter_subject); ?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_AICONTACTSAFE_FILTER_BY_SUBJECT');?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_AICONTACTSAFE_PROFILE'); ?>
				</td>
				<td>
					<?php echo $this->filter_profile; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_AICONTACTSAFE_STATUS'); ?>
				</td>
				<td>
					<?php echo $this->filter_status; ?>
				</td>
			</tr>
			<tr>
				<td>
					<button onclick="this.form.submit();"><?php echo JText::_('COM_AICONTACTSAFE_GO'); ?></button>
					<button onclick="document.getElementById('filter_string').value='';document.getElementById('filter_email').value='';document.getElementById('filter_subject').value='';document.getElementById('filter_profile').value='0';document.getElementById('filter_status').value='<?php echo $this->escape($this->_config_values['default_status_filter']); ?>';document.getElementById('filter_order').value='';document.getElementById('filter_order_Dir').value='';this.form.submit();"><?php echo JText::_('COM_AICONTACTSAFE_RESET'); ?></button>
				</td>
			</tr>
		</table>
	</td></tr>
</table>

<table class="adminlist" cellspacing="1">
<thead>
	<tr>
		<th width="1">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
		</th>
		<th class="title">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_NAME'), 'name', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
		<th nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_EMAIL'), 'email', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
		<th nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_SUBJECT'), 'subject', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
		<th>
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_PROFILE'), 'profile', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
		<th width="60" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_STATUS'), 'status', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
		<th align="center" width="80">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_DATE_ADDED'), 'date_added', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
	</tr>
</thead>
<tfoot>
	<tr>
		<td colspan="7">
			<?php echo $this->pageNav->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
<tbody>
	<?php
	if (count($this->rows) == 0) {
	?>
		<tr><td colspan="7" id="no_record">
			<?php echo JText::_('COM_AICONTACTSAFE_NO_RECORD_FOUND'); ?>
		</td></tr>
	<?php
	} else {
		$k = 0;
		$i = 0;

		if ($this->_config_values['activate_ip_ban']) {
			// get the array with banned ips
			$ips_banned = explode(';',$this->_config_values['ban_ips']);
			$img_banned = JURI::root().'administrator/components/com_aicontactsafe/images/ip_banned.gif';
		}

		foreach($this->rows as $row) {
			$checked = JHTML::_('grid.id', $i, $row->id, false, 'cid');
			?>
			<tr class="row<?php echo $k; ?>"  style="color:<?php echo $row->color; ?>;">
				<td width="1" align="center"><?php echo $checked; ?></td>
				<td><a href="<?php echo $row->view; ?>" class="aicontactsafe_edit"><?php echo $row->name; ?></a></td>
				<td align="left"><?php echo $row->email; ?></td>
				<td align="left"><?php echo $row->subject; ?></td>
				<td align="left"><?php echo $row->profile; ?></td>
				<td align="left"><?php echo $row->status; ?></td>
				<td nowrap="nowrap" align="center">
					<?php echo JHTML::_('date',  $row->date_added, $this->_config_values['date_format'] ); ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
			$i += 1;
		}
	}
	?>
</tbody>
</table>

<?php 
	// footer of the adminForm
	// don't remove this line
	echo $this->getTmplFooter();
?>
