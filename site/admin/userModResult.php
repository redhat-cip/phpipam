<?php

/**
 * Script to display usermod result
 *************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();
 
 

/**
 * First get posted variables
 */
$userModDetails = $_POST;
$userModDetails['plainpass'] = $userModDetails['password1'];


/**
 * Hash passwords if changed
 */
if (strlen($userModDetails['password1']) != 0) {
	$userModDetails['password1'] = md5($userModDetails['password1']);
	$userModDetails['password2'] = md5($userModDetails['password2']);
}

/**
 * Based on action verify the input
 */
if ($userModDetails['action'] == "Add") {
    $errors = verifyUserModInput($userModDetails);
}
else if ($userModDetails['action'] == "Edit") {
    $errors = verifyUserModInput($userModDetails);
}
else if ($userModDetails['action'] == "Delete") {
    if (!deleteUserById($userModDetails['userId'], $userModDetails['username'])) {
        print '<div class="error>Cannot delete user '. $userModDetails['username'] .'!</div>"';
    }
    else {
        print '<div class="success">User deleted successfully!</div>';
    }
    //stop script execution
    die();
}


/**
 * If no errors are present add / edit user
 */
if (sizeof($errors) != 0) {
    print '<div class="error">';
    foreach ($errors as $error) {
        print $error .'<br>';
    }
    print '</div>';
    die();
}
else
{
    //if no ID is present treat it as add new!
    if(!updateUserById($userModDetails)) {
        print '<div class="error">Cannot '. $userModDetails['action'] .' user!</div>';
    }
    else {
        print '<div class="success">User '. $userModDetails['action'] .' successfull!</div>';
        //send notification mail if checked
        if ($userModDetails['notifyUser']) {
        	include('userModEmailNotif.php');
        }
    }

}

?>