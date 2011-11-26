<?php

/**
 *
 * Script to check if all required extensions are compiled and loaded in PHP
 *
 *
 * We need the following mudules:
 *      - mysqli
 *      - session
 *      - gmp
 *
 ************************************/
 
/**
 * php debugging on/off - ignore notices
 */
if ($debugging == 0) {
    ini_set('display_errors', 0);
}
else{
    ini_set('display_errors', 1); 
    error_reporting( E_ALL );
/*     error_reporting( E_ALL & ~E_NOTICE ); */
}


/* Required extensions */
$requiredExt  = array("session", "mysqli", "gmp", "ldap");

/* Available extensions */
$availableExt = get_loaded_extensions();

/* Empty missing array to prevent errors */
$missingExt[0] = "";

/* if not all are present create array of missing ones */
foreach ($requiredExt as $extension) {
    if (!in_array($extension, $availableExt)) {
        $missingExt[] = $extension;
    }
}

/* if any extension is missing print error and die! */
if (sizeof($missingExt) != 1) {

	/* HMTL frame */
	print '<html>' . "\n";
	print '<head>' . "\n";
	print '	<title>IPAM error</title>' . "\n";
	print '	<link rel="stylesheet" type="text/css" href="'. $locationPrefix .'css/style.css">' . "\n";
	print '</head>' . "\n";
	print '</html>' . "\n";

    /* remove dummy 0 line */
    unset($missingExt[0]);
    
    $error  = '<ul>' . "\n";
    foreach ($missingExt as $missing) {
        $error .= '<li>'. $missing .'</li>' . "\n";
    }
    $error .= '</ul>' . "\n";
    $error .= 'Please recompile PHP to include missing extensions.' . "\n";
    
    die('<div class="error extError"><img src="'. $locationPrefix .'css/images/error.png"><h3>The following required PHP extensions are missing:</h3> '. $error .'<div>');
}


/**
 *
 * We must also check database connection to se if all is configured properly
 *
 */
$mysqli = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']); 

/* check connection */
if (mysqli_connect_errno()) {

	/* HMTL frame */
	print '<html>' . "\n";
	print '<head>' . "\n";
	print '	<title>phpipam error</title>' . "\n";
	print '	<link rel="stylesheet" type="text/css" href="'. $locationPrefix .'css/style.css">' . "\n";
	print '</head>' . "\n";
	print '</html>' . "\n";

	/* die with error */
    die('<div class="error extError"><img src="'. $locationPrefix .'css/images/error.png"><h3>Database connection failed!</h3>Error:<br>'. mysqli_connect_error() .'<br><br></div>');
}
?>