<?php

/**
 * Check if database needs upgrade to newer version
 ****************************************************/


/**
 * checks
 *
 *	$settings['version'] = installed version (from database)
 *	VERSION 			 = file version
 *	LAST_POSSIBLE		 = last possible for upgrade
 */


// not logged in users
if (isUserAuthenticatedNoAjax()) {
	header("Location: login/");	
}
// logged in, but not admins
elseif (!checkAdmin(false)) {
	//version ok
	if ($settings['version'] == VERSION) {
		header("Location: login/");
	} 
	//upgrade needed
	else {
		print '<h4>phpIPAM upgrade script</h4><hr>';
		print '<div class="alert alert-error">Database needs upgrade. Please contact site administrator (<a href="mailto:'. $settings['siteAdminMail'] .'">'. $settings['siteAdminName'] .'</a>)!</div>';
	}
}
// admins
elseif(checkAdmin(false)) {
	//version ok
	if ($settings['version'] == VERSION) {
		print "<h4>Database upgrade script</h4><hr>";
		print "<div class='alert alert-success'>Database seems up to date and doesn't need to be upgraded!</div>";
		print '<a href=""><button class="btn btn-small">Go to dashboard</button></a>';		
	}
	//version too old
	elseif ($settings['version'] < LAST_POSSIBLE) {
		die("<div class='alert alert-error'>Your phpIPAM version is too old to be upgraded, at least version ".LAST_POSSIBLE." is required for upgrade.</div>");
	}
	//upgrade needed
	elseif ($settings['version'] < VERSION) {
		//upgrade html + script
		include('upgradePrint.php');
	}
	//upgrade not needed
	else {
		header("Location: login/");		
	}
}
//default, smth is wrong
else {
	header("Location: login/");		
}