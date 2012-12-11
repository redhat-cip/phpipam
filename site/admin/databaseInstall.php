<?php

/*
 *	Script to upgrade database
 **************************************/

/* use required functions */
require_once('../../config.php');
require_once('../../functions/functions-install.php');

/* get root username and pass */
$root['user'] = $_POST['mysqlrootuser'];
$root['pass'] = $_POST['mysqlrootpass'];

/* try to install new database */
if(installDatabase($root)) {
	print '<div class="alert alert-success">Database installed successfully! <a href="login/">Please login to continue!</a></div>';
}

?>