<?php

/**
 *
 * Script to verify userentered input and verify it against database
 *
 * If successfull write values to session and go to main page! 
 *
 */


/* require scripts */
require_once('../functions/loginFunctions.php');

/* fetch username / pass if they are provided */
if( !empty($_POST['ipamusername']) && !empty($_POST['ipampassword']) )  {
	$ipamusername = $_POST['ipamusername'];
	$ipampassword['raw'] = $_POST['ipampassword'];
	$ipampassword['md5'] = md5($_POST['ipampassword']);

	/* check login */
	$result = checkLogin ($ipamusername, $ipampassword['md5'], $ipampassword['raw']);
}

/** 
 * if not display error 
 * 
 * else write vars to session div and redirect to page
 *
 */
if (!isset($result)) 
{
    die('<div class="error">Please enter your username and password!</div>');
}
else if (sizeof($result) == 0) {
    die('<div class="error">Wrong username or password!</div>');
}
else {
    /* start session and set variables */
    session_start();
    $_SESSION['ipamusername'] = $ipamusername;
    session_write_close();
    /* 
       redirect to page - since it is ajax-loaded header method does not work
       We need to redirect with javascript
    */
    print '<div class="success">Login successful!</div>';
}

?>