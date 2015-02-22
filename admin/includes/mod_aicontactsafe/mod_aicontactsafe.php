<?php
/**
 * @version     $Id$ 1.0.13 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * 1.0.1  - fixed the problem with the redirect link
 * 1.0.1  - fixed the problem with the calendar image from a date field
 * 1.0.2  - fixed the problem with the language
 * 1.0.3  - replaced JHTMLBehavior::calendar(); with JHTML::_('behavior.calendar');
 * 1.0.4  - calendar setup has to be done based on profile id
 * 1.0.5  - moved CSS to media folder
 * 1.0.6  - added the loading of mootools
 * 1.0.7  - replaced domready with load
 * 1.0.8  - added module parameters into the session
 * 1.0.9  - loading joomla.javascript.js to fix some problems with firefox on mac
 * 1.0.9  - the language is checked in the Joom!Fish cookie if not determined by the url
 * 1.0.10 - filter variables read with JRequest::getVar
 * 1.0.11 - added the support for joomla 1.6 and newer
 * 1.0.12 - sending the Itemid to the form
 * 1.0.13 - send a parameter to the form when it is loaded from a module
 *
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
//profile id
$pf = $params->get( 'pf', 0 );

$document = JFactory::getDocument();
$app = JFactory::getApplication();

// check if css is activated/deactivated in the module
$use_css = $params->get( 'use_css', 0 );

// if css is activated and there is a css file to call, call the css file
if ($use_css) {
	// generate the css file name based on the profile
	$css_file = 'profile_css_'.$pf.'.css';

	$nameCssGeneral = JURI::root().'components/com_aicontactsafe/includes/css/aicontactsafe_general.css';
	$document->addStyleSheet($nameCssGeneral);

	// import joomla clases to manage file system
	jimport('joomla.filesystem.file');
	// determine if to use the css from the template or from the component
	$template_name = $app->getTemplate();
	$tPath = JPATH_ROOT.'/'.'templates'.'/'.$template_name.'/'.'html'.'/'.'com_aicontactsafe'.'/'.$css_file;

	// if the css is not defined in the template use the one from the component
	if (JFile::exists($tPath)) {
		$nameCssFile = JURI::root().'templates/'.$template_name.'/html/com_aicontactsafe/'.$css_file;
	} else {
		$nameCssFile = JURI::root().'media/aicontactsafe/cssprofiles/'.$css_file;
	}
	$document->addStyleSheet($nameCssFile);
}

// add javascript
if(version_compare(JVERSION, '1.6.0', 'ge')) {
	$document->addScript( JURI::root(true).'/media/system/js/core.js');
} else {
	$document->addScript( JURI::root(true).'/includes/js/joomla.javascript.js');
}

JHtml::_('behavior.framework');

// load the calendar javascript and style
JHTML::_('behavior.calendar');

$db = JFactory::getDBO();
$query = 'SELECT config_value FROM #__aicontactsafe_config WHERE config_key = ' . $db->quote('keep_session_alive');
$db->setQuery( $query );
$keep_session_alive = (int)$db->loadResult();
if ( $keep_session_alive ) {
	echo JHTML::_('behavior.keepalive');
}

// load the javascript functions
require_once( JPATH_ROOT.'/'.'components'.'/'.'com_aicontactsafe'.'/'.'includes'.'/'.'js'.'/'.'aicontactsafe.js.php' );

// load the language file of com_aicontactsafe
$lang = JFactory::getLanguage();
$lang->load('com_aicontactsafe');
$please_wait_text = JText::_('COM_AICONTACTSAFE_PLEASE_WAIT');

$fd_values = $params->get( 'fd_values', '' );
$current_form_parameters = explode('|',$fd_values);
// put the parameters in an array so they can be easily accessed
$parameters = array();
foreach($current_form_parameters as $parameter) {
	$start_value = strpos($parameter,'=');
	if ( $start_value !== false) {
		$parameters[trim(substr($parameter,0,$start_value))] = trim(substr($parameter,$start_value+1));
	}
}
$parameters['from_module'] = 1;

$r_id = mt_rand();
$postData = array();

$postData['pf'] = $pf;
$postData['dt'] = 1;
JRequest::setVar('dt', 1, 'post');
$postData['r_id'] = $r_id;
// add all parameters to send them to the contact form ( except the text used as link )
foreach($parameters as $key=>$value) {
	$postData[$key] = $value;
}
// get the current url
$uri = JURI::getInstance();
$currentUrl = $uri->toString( array('scheme', 'host', 'port', 'path', 'query', 'fragment'));
if (!array_key_exists('current_url', $parameters)) {
	// set the return link to the current url in case other is not defined
	$postData['current_url'] = $currentUrl;
}
if (!array_key_exists('return_to', $parameters)) {
	// initialize the database
	$db = JFactory::getDBO();
	$query = 'SELECT redirect_on_success FROM #__aicontactsafe_profiles WHERE id = ' . $pf;
	$db->setQuery( $query );
	$redirect_on_success = $db->loadResult();
	// set the return link to the current url or the one setup into the profile
	if (strlen(trim($redirect_on_success)) == 0) {
		$postData['return_to'] = $currentUrl;
	} else {
		$postData['return_to'] = $redirect_on_success;
	}
}
// get the Itemid
$Itemid = JRequest::getInt('Itemid');
if (array_key_exists('Itemid', $parameters)) {
	$Itemid = (int)$parameters['Itemid'];
}

$postData[JSession::getFormToken()] = 1;
$session = JFactory::getSession();
$session->set( 'postData:message_' . $r_id, $postData );
$session->set( 'parameters:message_' . $r_id, $postData );
$session->set( 'isOK:message', false );

// generate the url for ajax
$jfcookie = JRequest::getVar('jfcookie', null ,"COOKIE");
$lang = '';
if (isset($jfcookie["lang"]) && $jfcookie["lang"] != "") {
	$lang = JFilterInput::clean($jfcookie["lang"], 'cmd');
}
if (strlen($lang) == 0) {
	$lang = $app->getUserState('application.lang', 'en');
	$lang = substr($lang,0,2);
}
$lang = JRequest::getCmd('lang', $lang);
$urlAiContactSafe = JURI::root().'index.php?option=com_aicontactsafe&sTask=message&task=message&pf='.$pf.'&next_use_ajax=1&r_id='.$r_id.'&format=raw&lang='.$lang;
// add Itemid into the action url
$urlAiContactSafe .= $Itemid>0?'&Itemid='.$Itemid:'';
// generate the script for ajax
$script = "
	//<![CDATA[
	<!--
	function getAiContactForm_".$pf."() {
		if (document.getElementById('aiContactSafe_module_".$pf."')) {
			var url = '".$urlAiContactSafe."';";
if(version_compare(JVERSION, '1.6.0', 'ge')) {
	$script .= "
				var xCaptcha = new Request({
					url: url, 
					method: 'get', 
					onRequest: function(){
											$('aiContactSafe_module_".$pf."').innerHTML = '".$please_wait_text."';
					},
					onComplete: function(responseText){
											$('aiContactSafe_module_".$pf."').innerHTML = this.response.text;
											setupCalendars(".$pf.");
											changeCaptcha(".$pf.",0);
											resetSubmit(".$pf.");
					}
				}).send();
	";
} else {
	$script .= "
			new Ajax(url, {
				method: 'get',
				onRequest: function(){ 
										$('aiContactSafe_module_".$pf."').setHTML('".$please_wait_text."');
									},
				onComplete: function() { 
										$('aiContactSafe_module_".$pf."').setHTML( this.response.text );
										setupCalendars(".$pf.");
										changeCaptcha(".$pf.",0);
										resetSubmit(".$pf.");
									}
			}).request();
	";
} 
	$script .= "
		}
	}
	window.addEvent('load', function() {
		getAiContactForm_".$pf."();
	});
	//-->
	//]]>
";

$document->addScriptDeclaration($script);
?>

<div class="aiContactSafe_module" id="aiContactSafe_module_<?php echo $pf; ?>">...</div>
