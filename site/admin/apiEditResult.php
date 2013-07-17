<?php

/**
 * Script to disaply api edit result
 *************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* checks */
$error = array();

if($_POST['action']!="delete") {
	# code must be exactly 32 chars long and alfanumeric
	if(strlen($_POST['app_code'])!=32 || !ctype_alnum($_POST['app_code']))									{ $error[] = "Invalid application code"; }
	# name must be more than 2 and alphanumberic
	if(strlen($_POST['app_id'])<3 || strlen($_POST['app_id'])>12 || !ctype_alnum($_POST['app_id']))			{ $error[] = "Invalid application id"; }
	# permissions must be 0,1,2
	if(!($_POST['app_permissions']==0 || $_POST['app_permissions']==1 || $_POST['app_permissions'] ==2 ))	{ $error[] = "Invalid permissions"; }
}

# die if errors
if(sizeof($error) > 0) {
	print "<div class='alert alert-error'>";
	print _('Error');
	print "<ul>";
	foreach($error as $err) {
		print "<li>"._($err)."</li>";
	}
	print "</ul>";
	print "</idv>";
}
else {
	/* try to execute */
	if(!modifyAPI($_POST)) 	{ print "<div class='alert alert-error'  >"._("API $_POST[action] error")."!</div>"; }
	else 					{ print "<div class='alert alert-success'>"._("API $_POST[action] success")."!</div>"; }	
}

?>