<?php

/**
 *	@ download and clear log files
 **********************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* clear logs */
if(!clearLogs()) 	{ print '<div class="alert alert-error alert-absolute">'._('Error clearing logs').'!</div>'; }
else 				{ print '<div class="alert alert-success alert-absolute">'._('Logs cleared successfully').'!</div>'; }

?>