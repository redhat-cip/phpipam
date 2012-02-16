<?php

/**
 *	@ download and clear log files
 **********************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* clear logs */
if(!clearLogs()) {
	print '<div class="error">Error clearing logs!</div>';
}
else {
	print '<div class="success">Logs cleared successfully!</div>';
}

?>