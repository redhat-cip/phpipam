<?php

/**
 * Script to print mail notification form
 ********************************************/
 
/* use required functions */
require_once('../config.php');
require_once('../functions/functions.php');

/* First chech referer and requested with */
CheckReferrer();

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all site settings */
$settings = getAllSettings();

/* user details */
$userDetails = getActiveUserDetails();


/* get IP address id */
$id = $_REQUEST['id'];

/* fetch all IP address details */
$ip 	= getIpAddrDetailsById ($id);
$subnet = getSubnetDetailsById ($ip['subnetId']);

/* set title */
$title = 'IP address details :: ' . $ip['ip_addr'];



/* Preset content */
$content  = '<b><u>IP address details - '	 . $ip['ip_addr'] .'</b></u>' . "\n\n";
$content .= '&bull; IP address: ' . "\t" . $ip['ip_addr'] . '/' . $subnet['mask']. "\n";
# desc
if(!empty($ip['description'])) {
$content .= '&bull; Description:' . "\t" . $ip['description'] . "\n";
}
# hostname
if(!empty($ip['dns_name'])) {
$content .= '&bull; Hostname:' . "\t" 	 . $ip['dns_name'] . "\n";
}
# subnet desc
if(!empty($subnet['description'])) {
$content .= '&bull; Subnet desc: ' . "\t" . $subnet['description']. "\n";
}
# VLAN
if(!empty($subnet['VLAN'])) {
$content .= '&bull; VLAN: ' . "\t\t" 	 . $subnet['VLAN'] . "\n";
}
# Switch
if(!empty($ip['switch'])) {
$content .= "&bull; Switch:\t\t"		 . $ip['switch'] . "\n";
}
# port
if(!empty($ip['port'])) {
$content .= "&bull; Port:\t"			 . $ip['port'] . "\n";
}
$content .= "\n" . 'Contact: '. $userDetails['email'] . "\n";
$content .= 'www: <a href="http://'. $settings['siteURL'] .'">'. $settings['siteURL'] ."</a>";

?>

<!-- sendmail form -->
<form name="mailNotify" id="mailNotify">
<table id="mailNotify" class="normalTable">

	<!-- title -->
	<tr>
		<th colspan="2">Send email notification</th>
	</tr>

	<!-- recipient -->
	<tr>
		<td class="info">Recipients</td>
		<td>
			<input type="text" name="recipients" style="width:300px;">
		</td>
	</tr>

	<!-- title -->
	<tr>
		<td class="info">title</td>
		<td>
			<input type="text" name="subject" style="width:300px;" value="<?php print $title; ?>">
		</td>
	</tr>
	
	<!-- content -->
	<tr>
		<td class="info">Content</td>
		<td>
			<textarea name="content" rows="7" style="width:300px;"><?php print $content ?></textarea>
		</td>
	</tr>
	
	<!-- submit -->
	<tr>
		<td></td>
		<td class="mailSubmit">
			<input type="button" value="Cancel" class="cancel">
			<input type="submit" value="Send Mail">
		</td>
	</tr>

</table>
</form>

<!-- holder for result -->
<div class="sendmail_check"></div>