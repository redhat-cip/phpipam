<?php

/**
 * Script to print mail notification form
 ********************************************/
 
/* use required functions */
require_once('../../functions/functions.php');

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

/* get VLAN details */
$subnet['VLAN'] = subnetGetVLANdetailsById($subnet['vlanId']);
$subnet['vlan'] = $subnet['VLAN']['number'];
if(!empty($subnet['VLAN']['name'])) {
	$subnet['vlan'] .= ' ('. $subnet['VLAN']['name'] .')';
}

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
if(!empty($subnet['vlan'])) {
$content .= '&bull; VLAN: ' . "\t\t" 	 . $subnet['vlan'] . "\n";
}
# Switch
if(!empty($ip['switch'])) {
$content .= "&bull; Switch:\t\t"		 . $ip['switch'] . "\n";
}
# port
if(!empty($ip['port'])) {
$content .= "&bull; Port:\t"			 . $ip['port'] . "\n";
}
# custom
$myFields = getCustomIPaddrFields();
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		if(!empty($ip[$myField['name']])) {
			$content .=  '&bull; '. $myField['name'] .":\t". $ip[$myField['name']] ."\n";
		}
	}
}

$content .= "\n" . 'Contact: '. $userDetails['email'] . "\n";
$content .= 'www: <a href="'. $settings['siteURL'] .'">'. $settings['siteURL'] ."</a>";


?>



<!-- header -->
<div class="pHeader">Send email notification</div>

<!-- content -->
<div class="pContent mailIPAddress">

	<!-- sendmail form -->
	<form name="mailNotify" id="mailNotify">
	<table id="mailNotify" class="table table-striped table-condensed">

	<!-- recipient -->
	<tr>
		<th>Recipients</th>
		<td>
			<input type="text" name="recipients" style="width:400px;">
			<i class="icon-gray icon-info-sign" rel="tooltip" data-placement="bottom" title="Separate multiple recepients with ,"></i>
		</td>
	</tr>

	<!-- title -->
	<tr>
		<th>title</t>
		<td>
			<input type="text" name="subject" style="width:400px;" value="<?php print $title; ?>">
		</td>
	</tr>
	
	<!-- content -->
	<tr>
		<th>Content</th>
		<td style="padding-right:20px;">
			<textarea name="content" rows="7" style="width:100%;"><?php print $content ?></textarea>
		</td>
	</tr>

	</table>
	</form>

	<!-- holder for result -->
	<div class="sendmail_check"></div>
</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Cancel</button>
	<button class="btn btn-small" id="mailIPAddressSubmit">Send Mail</button>
</div>