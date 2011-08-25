<?php

/**
 *	Generate XLS file
 *********************************/
/* required functions */
require_once('../../functions/functions.php'); 

/* we dont need any errors! */
ini_set('display_errors', 0);

/* verify that user is admin */
checkAdmin();

//set filename
$filename = "phpipam_IP_adress_export_". date("Y-m-d") .".sql";

//set content
/* $command = "mysqldump --opt -h $db['host'] -u $db['user'] -p $db['pass'] $db['name'] | gzip > $backupFile"; */
$command = "mysqldump --opt -h ". $db['host'] ." -u ". $db['user'] ." -p". $db['pass'] ." ". $db['name'];
$content = shell_exec($command);

/* headers */
header("Cache-Control: private");
header("Content-Description: File Transfer");
header('Content-type: application/octet-stream');
header('Content-Disposition: attachment; filename="'. $filename .'"');

print($content);
?>