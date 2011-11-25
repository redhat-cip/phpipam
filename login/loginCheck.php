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

	/* check local login */
	$result = checkLogin ($ipamusername, $ipampassword['md5'], $ipampassword['raw']);
	
	/* fetch settings */
	$settings = getAllSettings();

	/* if local fails */
	if ( sizeof($result) == 0 ) {
	
	
		if($settings['domainAuth'] == 1) {
			/* check if user exist in database and has domain user flag */		
			$authAD = checkADLogin ($ipamusername, $ipampassword['raw']);
		
			if($authAD == "ok") {

	    		/* start session and set variables */
	    		session_start();
	    		$_SESSION['ipamusername'] = $ipamusername;
	    		session_write_close();
    		
	    		die('<div class="success">AD login successful!</div>');		
			}
			else if ($authAD == 'Failed to connect to AD!') {
			    die('<div class="error">Failed to connect to AD!</div>');		
			}
			else if ($authAD == 'Failed to authenticate user via AD!') {
			    die('<div class="error">Failed to authenticate user via AD!</div>');		
			}
			else {
			    die('<div class="error">Wrong username or password!</div>');
			}		
		}
		else {
			    die('<div class="error">Authentication failed!</div>');			
		}	
	}
	/* local is ok */
	else {
    	/* start session and set variables */
    	session_start();
    	$_SESSION['ipamusername'] = $ipamusername;
    	session_write_close();
    	/* 
       		redirect to page - since it is ajax-loaded header method does not work
       		We need to redirect with javascript
    	*/
    	die('<div class="success">Login successful!</div>');	
	}
}
//Username / pass not provided
else {
	die('<div class="error">Please enter your username and password!</div>');
}

?>