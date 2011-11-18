<?php

/**
 * Script to print switches
 ***************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* title */
print '<h3>Switch management</h3>'. "\n";

/* get current switches */
$switches = getAllUniqueSwitches();


print_r($switches);
?>