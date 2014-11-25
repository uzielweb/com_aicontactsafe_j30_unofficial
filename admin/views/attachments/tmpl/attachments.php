<?php
/**
 * @version     $Id$ 2.0.7 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.13
 * - added link to download any attachment in the "Attachmenets" window
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php 
	// import joomla clases to manage file system
	jimport('joomla.filesystem.file');

	// header of the adminForm
	// don't remove this line
	echo $this->getTmplHeader();
?>

<table>
	<tr><td width="100%"><h3><?php echo JText::_('COM_AICONTACTSAFE_ATTACHMENTS'); ?></h3></td></tr>
	<tr><td>
		<input type="text" name="filter_string" id="filter_string" value="<?php echo $this->filter_string;?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_AICONTACTSAFE_FILTER_BY_NAME');?>"/>
		<button onclick="this.form.submit();"><?php echo JText::_('COM_AICONTACTSAFE_GO'); ?></button>
		<button onclick="document.getElementById('filter_string').value='';document.getElementById('filter_order').value='';document.getElementById('filter_order_Dir').value='';this.form.submit();"><?php echo JText::_('COM_AICONTACTSAFE_RESET'); ?></button>
	</td></tr>
</table>

<table class="adminlist" cellspacing="1">
<thead>
	<tr>
		<th width="1">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
		</th>
		<th class="title">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_FILE'), 'name', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
		<th class="title">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_NAME'), 'ms_name', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
		<th width="120" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_EMAIL'), 'ms_email', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
		<th nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_SUBJECT'), 'ms_subject', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
		<th width="80" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_SENDERS_IP'), 'ms_sender_ip', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
		<th class="title">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_STATUS'), 'recorded_text', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
		<th width="60" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_ID'), 'id', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
		<th align="center" width="80">
			<?php echo JHTML::_('grid.sort', JText::_('COM_AICONTACTSAFE_DATE_ADDED'), 'date_added', @$this->filter_order_Dir, @$this->filter_order ); ?>
		</th>
	</tr>
</thead>
<tfoot>
	<tr>
		<td colspan="9">
			<?php echo $this->pageNav->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
<tbody>
	<?php
	if (count($this->rows) == 0) {
	?>
		<tr><td colspan="9" id="no_record">
			<?php echo JText::_('COM_AICONTACTSAFE_NO_RECORD_FOUND'); ?>
		</td></tr>
	<?php
	} else {
		$k = 0;
		$i = 0;

		foreach($this->rows as $row) {
			$checked = JHTML::_('grid.id', $i, $row->id, false, 'cid');
			?>
			<tr class="row<?php echo $k; ?>">
				<td width="1" align="center"><?php echo $checked; ?><input type="hidden" id="file_<?php echo (int)$row->id; ?>" name="file_<?php echo (int)$row->id; ?>" value="<?php echo htmlspecialchars($row->name); ?>" /></td>
				<?php
					$file = $this->path_upload.'/'.$row->name;
					if (JFile::exists($file)) {
				?>
				<td align="left"><a href="<?php echo JURI::base().'index.php?option=com_aicontactsafe&sTask=attachments&task=download&file='.$row->name.'&format=raw'; ?>" target="_blank" class="attachments_download"><?php echo $row->name; ?></a></td>
				<?php
					} else {
				?>
				<td align="left"><?php echo $row->name; ?></td>
				<?php
					}
				?>
				<td align="left"><?php echo $row->ms_name; ?></td>
				<td align="left"><?php echo $row->ms_email; ?></td>
				<td align="left"><?php echo $row->ms_subject; ?></td>
				<td align="left"><a class="aiContactSafe" href="http://whois.domaintools.com/<?php echo $row->ms_sender_ip; ?>" target="_blank"><?php echo $row->ms_sender_ip; ?></a></td>
				<td align="left"><?php echo $row->recorded_text; ?></td>
				<td align="center"><?php echo $row->id; ?></td>
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
