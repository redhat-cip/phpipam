<?php

/**
 *
 * Script to verify userentered input and verify it against database
 *
 * If successfull write values to session and go to main page! 
 *
 */


/* require scripts */
require_once('../../functions/functions-install.php');

/* fetch username / pass if they are provided */
if( !empty($_POST['ipamusername']) && !empty($_POST['ipampassword']) )  {
	$ipamusername = $_POST['ipamusername'];
	$ipampassword['raw'] = $_POST['ipampassword'];
	$ipampassword['md5'] = md5($_POST['ipampassword']);

	/* check local login */
	checkLogin ($ipamusername, $ipampassword['md5'], $ipampassword['raw']);
}
//Username / pass not provided
else {
	die('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button>'._('Please enter your username and password').'!</div>');
}

?>