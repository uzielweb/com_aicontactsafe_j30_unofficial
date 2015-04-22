<?php
/**
 * @version     $Id$ 2.0.14
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.10.c
 * - the language is checked in the Joom!Fish cookie if not determined by the url
 * added/fixed in version 2.0.14
 * - filter variables read with JRequest::getVar
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the control_panel model class of aiContactSafe
class AiContactSafeModelCaptcha extends AiContactSafeModelDefault {

	// generate a new captcha code ( IE6 only )
	function newCaptcha() {
		$set_rand = JRequest::getVar('set_rand', 0, 'request', 'int');
		$pf = JRequest::getVar('pf', 0, 'request', 'int');
		$r_id = JRequest::getVar('r_id', 0, 'request', 'int');
		$jfcookie = JRequest::getVar('jfcookie', null ,"COOKIE");
		$lang = '';
		if (isset($jfcookie["lang"]) && $jfcookie["lang"] != "") {
			$lang = JFilterInput::clean($jfcookie["lang"], 'cmd');
		}
		if (strlen($lang) == 0) {
			$lang = $this->_app->getUserState('application.lang', 'en');
			$lang = substr($lang,0,2);
		}
		$lg = JRequest::getCmd('lang', $lang);
		$captcha_file = JURI::root().'index.php?option=com_aicontactsafe&sTask=captcha&task=captcha&pf='.$pf.'&r_id='.$r_id.'&lang='.$lg.'&format=raw&set_rand='.$set_rand;
		echo '<img src="'.$captcha_file.'" alt="&nbsp;" id="captcha" border="0" />';
	}

	// function to write the postdata into the session variable ( add in a function so the session variable can be modified for the message )
	function recordPostDataInSession( $postData ) {
		$r_id = JRequest::getInt( 'r_id' );
		$this->_app->_session->set( 'postData:' . $this->_sTask . '_' . $r_id, $postData );
	}

	// function to read the postdata from the session variable ( add in a function so the session variable can be modified for the message )
	function readPostDataFromSession() {
		$r_id = JRequest::getInt( 'r_id' );
		return $this->_app->_session->get( 'postData:' . $this->_sTask . '_' . $r_id );
	}

	function getProfile( $pf = 0 ) {
		$selected_profile = null;
		$default_profile = null;
		if ( $pf == 0 ) {
			// get the requested profile id
			$pf = JRequest::getVar('pf', 0, 'request', 'int');
		}
		if ( $pf == 0 ) {
			$postData = $this->readPostDataFromSession();
			if (is_array($postData) && array_key_exists('pf', $postData)) {
				$pf = (int)$postData['pf'];
			}
		}

		// initialize the database
		$db = JFactory::getDBO();

		// get the profile values
		$query = 'SELECT * FROM #__aicontactsafe_profiles WHERE ( id = ' . $pf . ' and published = 1 ) or set_default = 1 ORDER by set_default';
		$db->setQuery( $query );
		$profiles = $db->loadObjectList();
		if ( count($profiles) > 0 ) {
			// read the profiles
			foreach($profiles as $profile) {
				if ( $profile->id == $pf ) {
					$selected_profile = $profile;
				}
				if ( $profile->set_default ) {
					$default_profile = $profile;
				}
			}
			// if no profile is selected, use the default one
			if (!$selected_profile) {
				$selected_profile = $default_profile;
			}
		}

		return $selected_profile;
	}
}
