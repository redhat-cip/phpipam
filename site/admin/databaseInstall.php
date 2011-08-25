<?php

/*
 *	Script to upgrade database
 **************************************/

/* use required functions */
require_once('../../config.php');
require_once('../../functions/loginFunctions.php');

/* get root username and pass */
$root['user'] = $_POST['mysqlrootuser'];
$root['pass'] = $_POST['mysqlrootpass'];

/* try to install new database */
if(installDatabase($root)) {
	print '<div class="success">Database installed successfully! <a href="">Please login to continue!</a></div>';
}

?>