<?php
/**
 * @version     $Id$ 2.0.14
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.10.c
 * - the language is checked in the Joom!Fish cookie if not determined by the url
 * added/fixed in version 2.0.13
 * - cleared the output buffer before generating the CAPTCHA code
 * added/fixed in version 2.0.14
 * - filter variables read with JRequest::getVar
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// define the control_panel view class of aiContactSafe
class AiContactSafeViewCaptcha extends AiContactSafeViewDefault {

	// the profile informations
	var $profile = null;

	// function to display the default template
	function viewDefault() {
		$model = $this->getModel();
		$this->profile = $model->getProfile();

		// load the captcha class
		require_once( JPATH_ROOT.DS.'components'.DS.'com_aicontactsafe'.DS.'includes'.DS.'captcha'.DS.'captcha.php' );
		// captcha parameters
		$captchaParameters = array();

		$captchaParameters['width'] = $this->profile->captcha_width;
		$captchaParameters['height'] = $this->profile->captcha_height;

		$captchaParameters['use_random_letters'] = $this->profile->use_random_letters;

		$jfcookie = JRequest::getVar('jfcookie', null ,"COOKIE");
		$lang = '';
		if (isset($jfcookie["lang"]) && $jfcookie["lang"] != "") {
			$lang = JFilterInput::clean($jfcookie["lang"], 'cmd');
		}
		if (strlen($lang) == 0) {
			$lang = $this->_app->getUserState('application.lang', 'en');
			$lang = substr($lang,0,2);
		}
		$lang = JRequest::getCmd('lang', $lang);
		$captchaParameters['lang'] = $lang;

		$captchaParameters['minWordLength'] = $this->profile->min_word_length;
		$captchaParameters['maxWordLength'] = $this->profile->max_word_length;

		$captchaParameters['session_var'] = 'captcha_code_'.$this->profile->id;

		$captchaParameters['align_captcha'] = $this->profile->align_captcha;

		$captchaParameters['backgroundColor'] = $this->color_hex_to_rgb($this->profile->captcha_bgcolor);
		$captchaParameters['backgroundTransparent'] = $this->profile->captcha_backgroundTransparent;

		$captcha_colors = explode(';',$this->profile->captcha_colors);
		if ( count($captcha_colors) == 0 ) {
			$captcha_colors = array('#FF0000','#00FF00','#0000FF');
		}
		$captchaParameters['colors'] = array();
		foreach($captcha_colors as $color) {
			$captchaParameters['colors'][] = $this->color_hex_to_rgb($color);
		}

		// generate a new captcha
		$this->captcha = new SimpleCaptcha($captchaParameters);
		// write captcha
		$this->captcha->CreateImage();
		// stop the script here
		jexit();
	}

	function color_hex_to_rgb( $hex_code = '') {
		if ( substr(trim($hex_code),0,1) == '#' ) {
			$hex_code = substr(trim($hex_code),1);
		}
		$rgb = array(
						base_convert(substr($hex_code,0,2), 16, 10),
						base_convert(substr($hex_code,2,2), 16, 10),
						base_convert(substr($hex_code,4,2), 16, 10)
					);
		return $rgb;
	}
}
