<?php
/**
 * @version     $Id$ 2.0.5 0
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
<?php if ($this->format == 'raw') { ?>
	<?php echo $this->csv_text; ?>
<?php } else { ?>
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_AICONTACTSAFE_EXPORT'); ?></legend>
		<div><?php echo $this->csv_text; ?></div>
	</fieldset>
<?php } ?>	
<?php 
	// footer of the adminForm
	// don't remove this line
	echo $this->getTmplFooter();
?>
