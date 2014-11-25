<?php
/**
 * @version     $Id$ 2.0.14 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.13
 * - added link to download any attachment in the "Attachmenets" window
 * added/fixed in version 2.0.14
 * - filter variables read with JRequest::getVar
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the control_panel model class of aiContactSafe
class AiContactSafeModelAttachments extends AiContactSafeModelDefault {

	// function to read all the attachments used by aiContactSafe
	function getAttachments() {
		$files = array();

		// initialize the database
		$db = JFactory::getDBO();

		// get the files from the databse
		$query = 'SELECT mf.*, ms.name as ms_name, ms.email as ms_email, ms.subject as ms_subject, ms.sender_ip as ms_sender_ip FROM #__aicontactsafe_messagefiles mf LEFT JOIN #__aicontactsafe_messages ms ON mf.message_id = ms.id ORDER by mf.name';
		$db->setQuery( $query );
		$recorded_files = $db->loadObjectList();

		// import joomla clases to manage the folder
		jimport('joomla.filesystem.folder');
		// import joomla clases to manage file system
		jimport('joomla.filesystem.file');

		// get the path to attachments upload
		$upload_folder = '/'.$this->_config_values['upload_attachments'];

		$path_upload = JPATH_ROOT.'/'.$upload_folder;

		// an array to record the files in the database and exclude them from the array with the files from the upload folder 
		// so I get only the files not in the database when I read files from the upload folder
		$exclude_files = array('.htaccess', 'index.html');

		// check the files in the databse
		foreach($recorded_files as $recorded_file) {
			// check if the file exists
			$file = $path_upload.'/'.$recorded_file->name;
			if (JFile::exists($file)) {
				if ($recorded_file->message_id > 0) {
					$recorded_file->recorded = 1;
					$recorded_file->recorded_text = '<font color="#006600">' . JText::_('COM_AICONTACTSAFE_OK') . '</font>';
				} else {
					$recorded_file->recorded = 4;
					$recorded_file->recorded_text = '<font color="#FFCC00">' . JText::_('COM_AICONTACTSAFE_NOT_SENT_IN_A_MESSAGE') . '</font>';
				}
				$files[] = $recorded_file;
				$exclude_files[] = $recorded_file->name;
			} else {
				$recorded_file->recorded = 2;
				$recorded_file->recorded_text = '<font color="#FF0000">' . JText::_('COM_AICONTACTSAFE_ONLY_IN_THE_DATABASE') . '</font>';
				$files[] = $recorded_file;
			}
		}

		// get the files from the attachments folder
		$not_recorded_files = JFolder::files($path_upload, '.', false, false, $exclude_files );

		// check the files in the upload folder
		foreach($not_recorded_files as $not_recorded_file) {
			$file = new stdClass;
			$file->id = null;
			$file->message_id = null;
			$file->name = $not_recorded_file;
			$file->r_id = null;
			$file->date_added = null;
			$file->last_update = null;
			$file->published = null;
			$file->checked_out = null;
			$file->checked_out_time = null;
			$file->ms_name = null;
			$file->ms_email = null;
			$file->ms_subject = null;
			$file->ms_sender_ip = null;
			$file->recorded = 0;
			$file->recorded_text = '<font color="#FF0000">' . JText::_('COM_AICONTACTSAFE_ONLY_AS_FILE') . '</font>';
			$files[] = $file;
		}

		if ( strlen($this->filter_string) > 0 ) {
			$files = $this->filterFiles($files, $this->filter_string);
		}
		if (strlen($this->filter_order) > 0) {
			$files = $this->sortFiles($files, $this->filter_order, $this->filter_order_Dir);
		}

		$files_to_display = array();

		// import the pagination class
		jimport('joomla.html.pagination');

		$total = count($files);
		$this->pageNav = new JPagination( $total, $this->limitstart, $this->limit );
		if ( $this->limit > 0 ) {
			for($i=$this->limitstart;$i<$this->limitstart+$this->limit;$i++) {
				if (array_key_exists($i,$files)) {
					$files_to_display[] = $files[$i];
				}
			}
		} else {
			$files_to_display = $files;
		}

		return $files_to_display;
	}

	// function to filter files by a string
	function filterFiles($files, $string) {
		$string = strtolower($string);
		foreach($files as $key=>$file) {
			if (strpos(strtolower($file->name), $string) === false) {
				unset($files[$key]);
			}
		}
		return $files;
	}

	// function to sort the files array
	function sortFiles($files, $field, $order_Dir) {
		if (strtolower(trim($order_Dir)) == 'desc') {
			usort($files, create_function('$a,$b', 'if ($a->' . $field . '== $b->' . $field .') return 0; return ($a->' . $field . '> $b->' . $field .') ? -1 : 1;'));
		} else {
			usort($files, create_function('$a,$b', 'if ($a->' . $field . '== $b->' . $field .') return 0; return ($a->' . $field . '< $b->' . $field .') ? -1 : 1;'));
		}
		return $files;
	}

	// function to delete one or more attached files
	function delete() {
		// initialize the database
		$db = JFactory::getDBO();

		// import joomla clases to manage file system
		jimport('joomla.filesystem.file');

		// get the path to attachments upload
		$upload_folder = '/'.$this->_config_values['upload_attachments'];

		$path_upload = JPATH_ROOT.'/'.$upload_folder;

		// read the ids of the records seleted for deletion
		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		
		// delete files from the database and from the upload folder
		foreach($cid as $id) {
			// get the file name
			$file_name = JRequest::getCmd( 'file_'.$id, '', 'post');
			// delete it from the database
			$query = 'DELETE FROM #__aicontactsafe_messagefiles WHERE name = \''.$file_name.'\' AND id = '.$id.';';
			$db->setQuery( $query );
			$db->query();
			// delete it from the upload folder
			$file = $path_upload.'/'.$file_name;
			if (JFile::exists($file)) {
				JFile::delete($file);
			}
		}
	}

	// download a file from the attachments folder
	function downloadFile() {
		// get the name of the file
		$file_name = JRequest::getCmd('file', '');
		// get the path to attachments upload
		$upload_folder = '/'.$this->_config_values['upload_attachments'];

		$path_upload = JPATH_ROOT.'/'.$upload_folder;

		$file = $path_upload.'/'.$file_name;

		// start the file download
		if ( strlen(trim($file_name)) > 0 && file_exists($file) ){
			// Make sure there's not anything else left for download
			$this->ob_clean_all(); 

			header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.basename($file));
		    header('Content-Transfer-Encoding: binary');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file));
		    @ob_clean();
		    flush();

			readfile($file);
			exit;
		} else {
			echo '<script type="text/javascript" language="javascript">alert(\''.JText::_('COM_AICONTACTSAFE_FILE_WAS_DELETED').'\');</script>';
		}
	}

}
