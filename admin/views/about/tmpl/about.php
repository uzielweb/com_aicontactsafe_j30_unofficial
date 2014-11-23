<?php
/**
 * @version     $Id$ 2.0.10 b
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.10.b
 * - added Bulgarian translation
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

<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="50%" valign="top">
			<img src="<?php echo JURI::root().'administrator/components/com_aicontactsafe/images/logo.gif' ;?>" border="0" /><br/>
			<br/>
			<?php echo JText::_('COM_AICONTACTSAFE_AI_DESCRIPTION'); ?><br/>
			<?php echo JText::_('COM_AICONTACTSAFE_VERSION'); ?>&nbsp;<?php echo $this->_version; ?><br/>
			<?php echo JText::_('COM_AICONTACTSAFE_AI_PROGRAMMER'); ?><br/>
			<br/>
			<div style="margin-left:0px; margin-right:auto; width:120px; height:90px;"><iframe src="http://www.algisinfo.com/donate/" style="width:120px; height:90px; border:0px solid #FFFFFF;"><a href="http://www.algisinfo.com/donate/" target="_blank">You can help us</a></iframe></div>
		</td>
		<td width="50%" valign="top">
			<?php echo JText::_('COM_AICONTACTSAFE_AI_CREDITS'); ?><br/>
			<br/>
			<?php echo JText::_('COM_AICONTACTSAFE_AI_CREDITS_CAPTCHA'); ?><br/>
			<br/>
			<?php echo JText::_('COM_AICONTACTSAFE_AI_CREDITS_ICONS'); ?><br/>
			<br/>
			<b>SqueezeBox</b> : <br/>
			- made by Harald Kirschner from <a href="http://digitarald.de/project/squeezebox/" target="_blank">digitarald.de/project/squeezebox/</a><br/>
			- implemented in aiContactSafe by Alexandru Dobrin &lt;alex@algis.ro&gt;<br/>
			<br/>
			<b>Arabic translation</b> : <br/>- Dr. Ossama Abou Issa<br/>
			<br/>
			<b>Bulgarian translation</b> : <br/>- Eli Jeleva <a href="http://harrisonroyce.com" target="_blank">harrisonroyce.com</a><br/>
			<br/>
			<b>Czech translation</b> : <br/>- Martin Halík<br/>
			<br/>
			<b>Danish translation</b> : <br/>- Mads Andersen <a href="http://madsandersen.dk" target="_blank">madsandersen.dk</a><br/>
			<br/>
			<b>German translation</b> : <br/>- Pawel Koch <a href="http://www.le5.ch" target="_blank">www.le5.ch</a><br/>
			<br/>
			<b>Greek translation</b> : <br/>- Themistoklis Georgiadis <a href="http://www.globalinfoweb.com" target="_blank">www.globalinfoweb.com</a><br/>
			<br/>
			<b>English translation</b> : <br/>- Nic Irvine <a href="http://www.swanshops.com" target="_blank">www.swanshops.com</a><br/>
			<br/>
			<b>Persian translation</b> : <br/>- Mohammad Hasani Eghtedar<br/>
			<br/>
			<b>Spanish translation</b> : <br/>- Pablo Soto <a href="http://www.tecnoartestudio.com" target="_blank">www.tecnoartestudio.com</a><br/>
			<br/>
			<b>French translation</b> : <br/>- Mihàly Marti <a href="http://www.sarki.ch" target="_blank">www.sarki.ch</a><br/>
			<br/>
			<b>Hungarian translation</b> : <br/>- Balogh Zoltán <a href="http://birdcreation.com" target="_blank">birdcreation.com</a><br/>
			<br/>
			<b>Italian translation</b> : <br/>- Fabrizio Degni <a href="http://www.trioptimumcorporation.com" target="_blank">www.trioptimumcorporation.com</a><br/>
			<br/>
			<b>Lithuanian translation</b> : <br/>- Andrius Barkauskas <a href="http://www.barkauskas.lt" target="_blank">www.barkauskas.lt</a><br/>
			<br/>
			<b>Norvegian translation</b> : <br/>- Goran Aasen <a href="http://www.gaatec.com" target="_blank">www.gaatec.com</a><br/>
			<br/>
			<b>Dutch translation</b> : <br/>- Christof Vandewalle <a href="http://www.plus-it.be" target="_blank">www.plus-it.be</a><br/>
			<br/>
			<b>Polish translation</b> : <br/>- Stefan Wajda <a href="http://www.joomla.pl" target="_blank">www.joomla.pl</a><br/>
			<br/>
			<b>Brazilian Portuguese translation</b> : <br/>- Éder Almeida Costa<br/>
			<br/>
			<b>Portuguese translation</b> : <br/>- Rui Pedro <a href="http://www.elojasonline.com" target="_blank">www.elojasonline.com</a><br/>
			<br/>
			<b>Russian translation</b> : <br/>- Gruz <a href="http://ukrstyle.com" target="_blank">www.ukrstyle.com</a><br/>
			<br/>
			<b>Slovak translation</b> : <br/>- Peter Tanuska<br/>
			<br/>
			<b>Serbian (Cyrillic) translation</b> : <br/>- krca437<br/>
			<br/>
			<b>Swedish translation</b> : <br/>- Janne Sandgren <a href="http://www.hippijannessilverringar.se" target="_blank">www.hippijannessilverringar.se</a><br/>
			<br/>
			<b>Turkish translation</b> : <br/>- Kazim Çolpan <a href="http://www.tatlisubalikavi.net" target="_blank">www.tatlisubalikavi.net</a><br/>
			<br/>
			<b>Ukrainian translation</b> : <br/>- Gruz <a href="http://ukrstyle.com" target="_blank">www.ukrstyle.com</a><br/>
			<br/>
			<b>Tranditional Chinese</b> : <br/>- Jerry Chiu <a href="http://blog.cmsart.net/" target="_blank">blog.cmsart.net/</a><br/>
			<br/>
			<br/>
		</td>
	</tr>
</table>

<?php 
	// footer of the adminForm
	// don't remove this line
	echo $this->getTmplFooter();
?>
