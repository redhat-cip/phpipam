<?php

/**
 *	Script to replace fields in IP address list
 ***********************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();


/* verify posts */
if(empty($_POST['search'])) {
	die('<div class="alert alert-error alert-absolute">'._('Please enter something in search field').'!</div>');
}
/* if switch verify that it exists! */
if($_POST['field'] == "switch") {
	if(!verifySwitchByName ($_POST['search'])) 	{ die('<div class="alert alert-error alert-absolute">'._('Switch').' "<i>'. $_POST['search']  .'</i>" '._('does not exist, first create switch under admin menu').'!</div>'); }
	if(!verifySwitchByName ($_POST['replace'])) { die('<div class="alert alert-error alert-absolute">'._('Switch').' "<i>'. $_POST['replace'] .'</i>" '._('does not exist, first create switch under admin menu').'!</div>'); }
}

/* set query! */
$query = 'update `ipaddresses` set `'. $_POST['field'] .'` = replace(`'. $_POST['field'] .'`, "'. $_POST['search'] .'", "'. $_POST['replace'] .'");';

/* update */
searchAndReplace($query, $_POST);

?>