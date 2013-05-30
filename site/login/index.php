<?php 
/* if title is missing set it to install */
if(!$settings['siteTitle']) { $settings['siteTitle'] = "phpipam IP management installation"; }

/* destroy session */
if ($_REQUEST['page'] == "logout") 	{ updateLogTable ('User '. $_SESSION['ipamusername'] .' has logged out', 0); }

/* destroy session */
session_start();
session_destroy();
?>
	
<?php 
if($_REQUEST['page'] == "login" || $_REQUEST['page'] == "logout") 	{ include_once('loginForm.php'); }
else if ($_REQUEST['page'] == "request_ip") 						{ include_once('requestIPform.php'); }
else 																{ $_REQUEST['eid'] = "404"; print "<div id='error'>"; include_once('site/error.php'); print "</div>"; }
?>

<!-- login response -->
<div id="loginCheck"><?php if ($_REQUEST['page'] == "logout") print '<div class="alert alert-success">'._('You have logged out').'</div>'; ?></div>