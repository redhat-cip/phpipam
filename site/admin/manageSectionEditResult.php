<?php

/** 
 * Function to add / edit / delete section
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* verify post */
CheckReferrer();


/* get variables */
$update['action']      = $_POST['action'];
$update['name']        = htmlentities($_POST['name'], ENT_COMPAT | ENT_HTML401, "UTF-8");	//prevent XSS
$update['description'] = htmlentities($_POST['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	//prevent XSS
$update['id']          = $_POST['id'];


/* do action! */
if (UpdateSection ($update)) {
    print '<div class="success">'. $update['action'] .'ed Successful!</div>';
}

?>