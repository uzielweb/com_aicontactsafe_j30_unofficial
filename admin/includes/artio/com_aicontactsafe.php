<?php
/**
 * @version     $Id$ 2.0.0 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class SefExt_com_aicontactsafe extends SefExt {

    function beforeCreate(&$uri) {
        // Set the default sTask if not set
        if( is_null($uri->getVar('sTask')) ) {
            $uri->setVar('sTask', 'message');
        }
        // Set the default task if not set
        if( is_null($uri->getVar('task')) ) {
            $uri->setVar('task', 'display');
        }
    }

    function create(&$uri) {
        $vars = $uri->getQuery(true);
        extract($vars);
        
        $title = array();
		$profile_name = $this->getAiContactSafeProfileName($pf);
		if (strlen(trim($profile_name)) > 0) {
			$title[] = $profile_name;
		} else {
	        $title[] = JoomSEF::_getMenuTitle(@$option, null, @$Itemid);
		}

        $newUri = $uri;
        if (count($title) > 0) {
            $newUri = JoomSEF::_sefGetLocation($uri, $title, null, null, null, @$lang);
        }
        
        return $newUri;
    }

	function getAiContactSafeProfileName($pf = 0) {
		$pf = (int)$pf;
		// initialize the database
		$db = JFactory::getDBO();
		// reset field set_default to 0 for all records
		$query = 'SELECT name, id FROM `#__aicontactsafe_profiles` WHERE id = '.$pf;
		$db->setQuery( $query );
		$profile_name = $db->loadResult();
		
		return $profile_name;
	}
}
