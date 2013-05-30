<?php 

/**
 * Script to edit VLAN details
 *******************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* get modified details */
$vlan = $_POST;

/* get settings */
$settings = getAllSettings ();

/* if it already exist DIE! */
if($settings['vlanDuplicate'] == "0") {
if($vlan['action'] == "add") {
	if(!getVLANbyNumber($vlan['number'])) 	{ }
	else 									{ die('<div class="alert alert-error">'._('VLAN already exists').'!</div>'); }	
}
}

//custom
$myFields = getCustomVLANFields();
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		# replace possible ___ back to spaces!
		$myField['nameTest']      = str_replace(" ", "___", $myField['name']);
		
		if(isset($_POST[$myField['nameTest']])) { $vlan[$myField['name']] = $vlan[$myField['nameTest']];}
	}
}

/* sanitize post! */
$vlan['name'] 		 = htmlentities($vlan['name'], ENT_COMPAT | ENT_HTML401, "UTF-8");			# prevent XSS
$vlan['number'] 	 = htmlentities($vlan['number'], ENT_COMPAT | ENT_HTML401, "UTF-8");		# prevent XSS
$vlan['description'] = htmlentities($vlan['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	# prevent XSS

/* Hostname must be present! */
if($vlan['number'] == "") 					{ die('<div class="alert alert-error">'._('Number is mandatory').'!</div>'); }

/* update details */
if(!updateVLANDetails($vlan)) 				{ print('<div class="alert alert-error"  >'._('Failed to').' '. _($vlan['action']) .' '._('VLAN').'!</div>'); }
else 										{ print('<div class="alert alert-success">'._('VLAN').' '.      _($vlan['action']) .' '._('successfull').'!</div>'); }

?>