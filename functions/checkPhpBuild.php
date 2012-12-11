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


/* Required extensions */
$requiredExt  = array("session", "mysqli", "gmp");

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

/* check if mod_rewrite is enabled in apache */
$modules = apache_get_modules();
if(!in_array("mod_rewrite", $modules)) {
	$missingExt[] = "mod_rewrite (Apache module)";
}

/* if any extension is missing print error and die! */
if (sizeof($missingExt) != 1) {

    /* remove dummy 0 line */
    unset($missingExt[0]);
    
    /* headers */
    $error   = "<html>";
    $error  .= "<head>";
    $error  .= '<link rel="stylesheet" type="text/css" href="/css/bootstrap/bootstrap.css">';
	$error  .= '<link rel="stylesheet" type="text/css" href="/css/bootstrap/bootstrap-custom.css">';
	$error  .= "</head>";
    $error  .= "<body>";
    $error  .= '<div id="header">';
    $error  .= '<div class="hero-unit">';
	$error  .= '<a href="/">phpIPAM error</a>';
	$error  .= '</div>';
	$error  .= '</div>';
    /* error */
    $error  .= "<div class='alert alert-error' style='margin-top:110px;'><strong>The following required PHP extensions are missing:</strong><br><hr>";
    $error  .= '<ul>' . "\n";
    foreach ($missingExt as $missing) {
        $error .= '<li>'. $missing .'</li>' . "\n";
    }
    $error  .= '</ul>' . "\n";
    $error  .= 'Please recompile PHP to include missing extensions and restart Apache.' . "\n";
    
    $error  .= "</body>";
    $error  .= "</html>";
    
    die($error);
}


/**
 *
 * We must also check database connection to se if all is configured properly
 *
 */
$mysqli = @new mysqli($db['host'], $db['user'], $db['pass'], $db['name']); 

/* check connection */
if ($mysqli->connect_errno) {
	/* die with error */
    die('<div class="alert alert-error"><strong>Database connection failed!</strong><br><hr>Error: '. mysqli_connect_error() .'</div>');
}
?>