<?php
/**
 * @version     $Id$ 2.0.15 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.10.c
 * - fixed the problem when the text "please wait..." translated contains an apostrophe ( ' )
 * - the language is checked in the Joom!Fish cookie if not determined by the url
 * added/fixed in version 2.0.12
 * - fixed the problem with reCaptcha in aiContactSafeModule and aiContactSafePlugin
 * - check for the load.gif in the template and use the one delivered with the extension if it is not found
 * added/fixed in version 2.0.13
 * - added SqueezeBox for aiContactSafe feed-back
 * - fixed the problem with SSL when loading recaptcha
 * added/fixed in version 2.0.14
 * - filter variables read with JRequest::getVar
 * added/fixed in version 2.0.15
 * - fixed the problem with loading SqueezeBox for module and plugins
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$root_url = JURI::root();
$language = JFactory::getLanguage();
$language->load('com_aicontactsafe');

// import joomla clases to manage file system
jimport('joomla.filesystem.file');
// generate the path to the load.gif image
$app = JFactory::getApplication();
$template_name = $app->getTemplate();
$tPath = JPATH_ROOT.'/'.'templates'.'/'.$template_name.'/'.'html'.'/'.'com_aicontactsafe'.'/'.'message'.'/'.'load.gif';
if (JFile::exists($tPath)) {
	$loadImage = JURI::root().'templates/'.$template_name.'/html/com_aicontactsafe/message/load.gif';
} else {
	$loadImage = JURI::root().'components/com_aicontactsafe/includes/images/load.gif';
}

$loading_img = '&nbsp;&nbsp;'.str_replace("'","\'",JText::_('COM_AICONTACTSAFE_PLEASE_WAIT')).'&nbsp;<img id="imgLoading" border="0" src="'.$loadImage.'" />&nbsp;&nbsp;';
$urlDeleteUploadedFile = $root_url.'index.php?option=com_aicontactsafe&sTask=message&task=deleteUploadedFile&filename=';

$jfcookie = JRequest::getVar('jfcookie', null ,"COOKIE");
$lang = '';
if (isset($jfcookie["lang"]) && $jfcookie["lang"] != "") {
	$lang = JFilterInput::clean($jfcookie["lang"], 'cmd');
}
if (strlen($lang) == 0) {
	$app  = JFactory::getApplication();
	$lang = $app->getUserState('application.lang', 'en');
	$lang = substr($lang,0,2);
}
$lg = JRequest::getCmd('lang', $lang);

$db = JFactory::getDBO();

$query = 'SELECT config_value FROM `#__aicontactsafe_config` WHERE `config_key` = \'use_SqueezeBox\'';
$db->setQuery( $query );
$use_SqueezeBox = (int)$db->loadResult();

$script = "
	//<![CDATA[
	<!--
	function resetSubmit( pf ) {
		$('adminForm_'+pf).addEvent('submit', function(e) {
			new Event(e).stop();";
if(version_compare(JVERSION, '1.6.0', 'ge')) {
	$script .= "
				e.stop();
				var xSubmit = new Request.HTML(
					{url:'".$root_url."index.php?option=com_aicontactsafe',
					evalScripts:false,
					update:$('displayAiContactSafeForm_'+pf),
					onRequest: function(){ 
										document.getElementById('adminForm_'+pf).elements['task'].value = 'ajaxform'; 
										document.getElementById('adminForm_'+pf).elements['use_ajax'].value = '1';
										$('aiContactSafeSend_loading_'+pf).innerHTML = '".$loading_img."';
										document.getElementById('adminForm_'+pf).elements['aiContactSafeSendButton'].disabled = true;
					},
					onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) { 
										changeCaptcha(pf,0); 
										document.getElementById('adminForm_'+pf).elements['aiContactSafeSendButton'].removeAttribute('disabled');
										if (document.getElementById('adminForm_'+pf).elements['ajax_return_to']) {
											var ajax_return_to = document.getElementById('adminForm_'+pf).elements['ajax_return_to'].value;
											if (ajax_return_to.length > 0) {
												window.location = ajax_return_to;
											}
										} else {
											if (document.getElementById('adminForm_'+pf).elements['ajax_message_sent']) {
												var return_to = document.getElementById('adminForm_'+pf).elements['return_to'].value;
												return_to = return_to.replace('&#38;', '&');
												var current_url = document.getElementById('adminForm_'+pf).elements['current_url'].value;
												current_url = current_url.replace('&#38;', '&');
												if (return_to.length > 0 && return_to != current_url) {													
													window.location = return_to;
												}
											}
										}
										$('aiContactSafeSend_loading_'+pf).innerHTML = '&nbsp;';
										setupCalendars(pf);
										if(".$use_SqueezeBox." == 1 && typeof SqueezeBox != 'undefined' && $('system-message')) {
											SqueezeBox.initialize();
											SqueezeBox.open($('system-message'), {
												handler: 'adopt',
												size: {x: $('system-message').offsetWidth+30, y: $('system-message').offsetHeight+30}
											});
										}
					}}
				).post($('adminForm_'+pf));
	";
} else {
	$script .= "
			this.send({
				onRequest: function(){ 
										document.getElementById('adminForm_'+pf).elements['task'].value = 'ajaxform'; 
										document.getElementById('adminForm_'+pf).elements['use_ajax'].value = '1';
										$('aiContactSafeSend_loading_'+pf).innerHTML = '".$loading_img."';
										document.getElementById('adminForm_'+pf).elements['aiContactSafeSendButton'].disabled = true;
									},
				onComplete: function() { 
										$('displayAiContactSafeForm_'+pf).innerHTML = this.response.text;
										changeCaptcha(pf,0); 
										document.getElementById('adminForm_'+pf).elements['aiContactSafeSendButton'].removeAttribute('disabled');
										if (document.getElementById('adminForm_'+pf).elements['ajax_return_to']) {
											var ajax_return_to = document.getElementById('adminForm_'+pf).elements['ajax_return_to'].value;
											if (ajax_return_to.length > 0) {
												window.location = ajax_return_to;
											}
										} else {
											if (document.getElementById('adminForm_'+pf).elements['ajax_message_sent']) {
												var return_to = document.getElementById('adminForm_'+pf).elements['return_to'].value;
												return_to = return_to.replace('&#38;', '&');
												var current_url = document.getElementById('adminForm_'+pf).elements['current_url'].value;
												current_url = current_url.replace('&#38;', '&');
												if (return_to.length > 0 && return_to != current_url) {													
													window.location = return_to;
												}
											}
										}
										$('aiContactSafeSend_loading_'+pf).innerHTML = '&nbsp;';
										setupCalendars(pf);
										if(".$use_SqueezeBox." == 1 && typeof SqueezeBox != 'undefined' && $('system-message')) {
											SqueezeBox.initialize();
											SqueezeBox.open($('system-message'), {
												handler: 'adopt',
												size: {x: $('system-message').offsetWidth+30, y: $('system-message').offsetHeight+30}
											});
										}
									}
			});
	";
}
$script .= "
		});
	}
	function checkEditboxLimit( pf, editbox_id, chars_limit ){
		if (document.getElementById('adminForm_'+pf).elements[editbox_id]) {
			if (document.getElementById('adminForm_'+pf).elements[editbox_id].value.length > chars_limit) {
				alert('".str_replace("'","\'",JText::_('COM_AICONTACTSAFE_MAXIMUM_CHARACTERS_EXCEEDED'))." !');
				document.getElementById('adminForm_'+pf).elements[editbox_id].value = document.getElementById('adminForm_'+pf).elements[editbox_id].value.substring(0,chars_limit);
			} else {
				if (document.getElementById('adminForm_'+pf).elements['countdown_'+editbox_id]) {
					document.getElementById('adminForm_'+pf).elements['countdown_'+editbox_id].value = chars_limit - document.getElementById('adminForm_'+pf).elements[editbox_id].value.length;
				}
			}
		}
	}
	function changeCaptcha( pf, modifyFocus ) {
		if (document.getElementById('div_captcha_img_'+pf)) {
			var set_rand = Math.floor(Math.random()*10000000001);
			var r_id = document.getElementById('adminForm_'+pf).elements['r_id'].value;
			var captcha_file = '".$root_url."index.php?option=com_aicontactsafe&sTask=captcha&task=captcha&pf='+pf+'&r_id='+r_id+'&lang=".$lg."&format=raw&set_rand='+set_rand;
			if (window.ie6) {
				var url = '".$root_url."index.php?option=com_aicontactsafe&sTask=captcha&task=newCaptcha&pf='+pf+'&r_id='+r_id+'&lang=".$lg."&format=raw&set_rand='+set_rand;";
if(version_compare(JVERSION, '1.6.0', 'ge')) {
	$script .= "
				var xCaptcha = new Request({
					url: url, 
					method: 'get', 
					onRequest: function(){
											$('div_captcha_img_'+pf).innerHTML = '".str_replace("'","\'",JText::_('COM_AICONTACTSAFE_PLEASE_WAIT'))."';
					},
					onComplete: function(responseText){
											$('div_captcha_img_'+pf).innerHTML = responseText;
					}
				}).send();
	";
} else {
	$script .= "
				new Ajax(url, {
					method: 'get',
					update: $('div_captcha_img_'+pf),
					onRequest: function(){ $('div_captcha_img_'+pf).innerHTML = '".str_replace("'","\'",JText::_('COM_AICONTACTSAFE_PLEASE_WAIT'))."'; }
				}).request();
	";
}
$script .= "
			} else {
				$('div_captcha_img_'+pf).innerHTML = '<img src=\"'+captcha_file+'\" alt=\"&nbsp;\" id=\"captcha\" border=\"0\" />';
			}
			if (modifyFocus && document.getElementById('captcha-code')) {
				document.getElementById('captcha-code').focus();
			}
		}
		if (document.getElementById('aiContactSafe_form_'+pf) || document.getElementById('aiContactSafe_module_'+pf)) {
			if (document.getElementById('reCaptchaReset')) {
				if (document.getElementById('reCaptchaReset').value == 1 && document.getElementById('recaptcha_div')) {
					if (document.getElementById('reCaptchaPublicKey')) {
						var reCaptchaPublicKey = document.getElementById('reCaptchaPublicKey').value;
					} else {
						var reCaptchaPublicKey = '';
					}
					if (document.getElementById('reCaptchaTheme')) {
						var reCaptchaTheme = document.getElementById('reCaptchaTheme').value;
					} else {
						var reCaptchaTheme = '';
					}
					Recaptcha.create(reCaptchaPublicKey, 'recaptcha_div',  { theme:reCaptchaTheme });
				}
			}
		}
		if (document.getElementById('captcha-code')) {
			$('captcha-code').value = '';
		} else if (document.getElementById('captcha_code')) {
			$('captcha_code').value = '';
		} else if (document.getElementById('mathguard_answer')) {
			$('mathguard_answer').value = '';
		} else if (document.getElementById('recaptcha_response_field')) {
			$('recaptcha_response_field').value = '';
		}
	}
	function setDate( pf, newDate, idDate ) {
		if (document.getElementById('adminForm_'+pf).elements['day_'+idDate]) {
			document.getElementById('adminForm_'+pf).elements['day_'+idDate].value = newDate.substr(8,2);
		}
		if (document.getElementById('adminForm_'+pf).elements['month_'+idDate]) {
			var selMonth = newDate.substr(5,2);
			if(selMonth.substr(0,1) == '0') {
				selMonth = selMonth.substr(1,1);
			}
			selMonth = parseInt(selMonth) - 1;
			document.getElementById('adminForm_'+pf).elements['month_'+idDate].options[selMonth].selected = true;
		}
		if (document.getElementById('adminForm_'+pf).elements['year_'+idDate]) {
			document.getElementById('adminForm_'+pf).elements['year_'+idDate].value = newDate.substr(0,4);
		}
	}
	function daysInFebruary( year ){
		var days = (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
		return days;
	}
	function daysInMonth( month, year ) {
		var days = 31;
		switch( true ) {
			case month == 2 :
				days = daysInFebruary( year );
				break;
			case month == 4 || month == 6 || month == 9 || month == 11 :
				days = 30;
				break;
		}
	   return days;
	}
	function checkDate( pf, idDate ) {
		var year = 0;
		var month = 0;
		var day = 0;
		if (document.getElementById('adminForm_'+pf).elements['year_'+idDate]) {
			year = document.getElementById('adminForm_'+pf).elements['year_'+idDate].value;
		}
		if (document.getElementById('adminForm_'+pf).elements['month_'+idDate]) {
			month = document.getElementById('adminForm_'+pf).elements['month_'+idDate].value;
		}
		if (document.getElementById('adminForm_'+pf).elements['day_'+idDate]) {
			day = document.getElementById('adminForm_'+pf).elements['day_'+idDate].value;
		}
		if (day > 0 && month > 0 && year > 0) {
			var days = daysInMonth( month, year );
			if (day > days) {
				day = days;
				document.getElementById('adminForm_'+pf).elements['day_'+idDate].value = days;
				var error = '" . str_replace("'","\'",JText::_('COM_AICONTACTSAFE_MAXIMUM_DAYS_IN_MONTH_ERROR')) . "';
				alert( error.replace( '%days%', days ) );
			}
		}
		if (document.getElementById('adminForm_'+pf).elements[idDate]) {
			document.getElementById('adminForm_'+pf).elements[idDate].value = year+'-'+month+'-'+day;
		}
	}
	function clickCheckBox( pf, idTag, ckChecked ) {
		document.getElementById('adminForm_'+pf).elements[idTag].value = ckChecked?1:0;
	}
	function hideUploadField(file_field, pf) {
		$('upload_'+pf+'_file_'+file_field).setStyle('display','none');
	}
	function showUploadField(file_field, pf) {
		$('upload_'+pf+'_file_'+file_field).setStyle('display','inline');
	}
	function resetUploadField(file_field, pf) {
		var var_file_field = \"'\"+file_field+\"'\";
		$('upload_'+pf+'_file_'+file_field).innerHTML = '<input type=\"file\" name=\"'+file_field+'\" id=\"'+file_field+'\" onchange=\"startUploadFile('+var_file_field+','+pf+')\" />';
	}
	function hideFileField(file_field, pf) {
		$('cancel_upload_'+pf+'_file_'+file_field).setStyle('display','none');
	}
	function showFileField(file_field, pf) {
		$('cancel_upload_'+pf+'_file_'+file_field).setStyle('display','inline');
	}
	function hideWaitFileField(file_field, pf) {
		$('wait_upload_'+pf+'_file_'+file_field).setStyle('display','none');
	}
	function showWaitFileField(file_field, pf) {
		$('wait_upload_'+pf+'_file_'+file_field).setStyle('display','inline');
	}
	function cancelUploadFile(file_field, pf) {
		hideFileField(file_field, pf);
		deleteUploadedFile(file_field, pf);
		$('adminForm_'+pf).elements[file_field+'_attachment_name'].value = '';
		$('adminForm_'+pf).elements[file_field+'_attachment_id'].value = '';
		resetUploadField(file_field, pf);
		showUploadField(file_field, pf);
	}
	function deleteUploadedFile(file_field, pf) {
		var file_name = $('adminForm_'+pf).elements[file_field+'_attachment_name'].value;
		var r_id = document.getElementById('adminForm_'+pf).elements['r_id'].value;
		var url = '".$urlDeleteUploadedFile."'+file_name+'&r_id='+r_id+'&format=raw'";
if(version_compare(JVERSION, '1.6.0', 'ge')) {
	$script .= "
		var xUpload = new Request({
			url: url, 
			method: 'get'
		}).send();
	";
} else {
	$script .= "
		new Ajax(url, { method: 'get' }).request();
	";
}
$script .= "
	}
	function startUploadFile(file_field, pf) {
		var r_id = document.getElementById('adminForm_'+pf).elements['r_id'].value;
		$('adminForm_'+pf).setProperty('action','".$root_url."index.php?option=com_aicontactsafe&field='+file_field+'&r_id='+r_id+'&format=raw');
		$('adminForm_'+pf).setProperty('target','iframe_upload_file_'+pf+'_file_'+file_field);
		$('adminForm_'+pf).elements['task'].value = 'uploadFile';
		hideUploadField(file_field, pf);
		hideFileField(file_field, pf);
		showWaitFileField(file_field, pf);
		$('adminForm_'+pf).submit();
		resetUploadField(file_field, pf);
	}
	function endUploadFile(pf, file_field, attachment_name, attachment_id, error_type, error_message) {
		error_type = parseInt(error_type);
		hideWaitFileField(file_field, pf);
		switch( error_type ) {
			case 0 :
				$('adminForm_'+pf).elements[file_field+'_attachment_name'].value = attachment_name;
				$('adminForm_'+pf).elements[file_field+'_attachment_id'].value = attachment_id;
				showFileField(file_field, pf);
				break;
			case 1 :
				alert('".str_replace("'","\'",JText::_('COM_AICONTACTSAFE_THIS_TYPE_OF_ATTACHEMENT_IS_NOT_ALLOWED'))." ( '+error_message+' ) ');
				cancelUploadFile(file_field, pf);
				break;
			case 2 :
				alert('".str_replace("'","\'",JText::_('COM_AICONTACTSAFE_FILE_TOO_BIG'))." ( '+error_message+' ) ');
				cancelUploadFile(file_field, pf);
				break;
			case 3 :
				alert('".str_replace("'","\'",JText::_('COM_AICONTACTSAFE_OTHER_ERROR'))." ( '+error_message+' ) ');
				cancelUploadFile(file_field, pf);
				break;
		}
		resetSendButtonTarget(pf);
	}
	function resetSendButtonTarget(pf) {
		$('adminForm_'+pf).setProperty('action','".$root_url."index.php?option=com_aicontactsafe');
		$('adminForm_'+pf).setProperty('target','_self');
		$('adminForm_'+pf).elements['task'].value = 'message';
	}
	function setupCalendars(pf) {
		var calendars_imgs = $$('#adminForm_'+pf+' img.calendar');
		var countCalendars = calendars_imgs.length;
		for(var i=0;i<countCalendars;i++) {
			var imgid = calendars_imgs[i].getProperty('id');
			if (imgid.substr(imgid.length-4)=='_img') {
				fieldid = imgid.substr(0,imgid.length-4);
				Calendar.setup({inputField : fieldid, ifFormat: \"%Y-%m-%d\", button : imgid, align : \"Tl\", singleClick : true});
			}
		}
	}
	//-->
	//]]>
";

$document = JFactory::getDocument();
$document->addScriptDeclaration($script);
$captchaPlugin = JPluginHelper::getPlugin('content', 'captcha');
if (isset($captchaPlugin->params)) {
	$captchaPluginParameters = new JParameter($captchaPlugin->params);
	if ( $captchaPluginParameters->get( 'captcha_systems' ) == 'recaptcha' ) {
		$uri = JFactory::getURI();
		$document->addScript( ($uri->isSSL()?'https://api-secure':'http://api').'.recaptcha.net/js/recaptcha_ajax.js' );
	}
}

if ( $use_SqueezeBox ) {
	$document->addStyleSheet(JURI::root(true).'/components/com_aicontactsafe/includes/squeezebox/assets/SqueezeBox.css');
	$document->addScript(JURI::root(true).'/components/com_aicontactsafe/includes/squeezebox/SqueezeBox.js');
}
