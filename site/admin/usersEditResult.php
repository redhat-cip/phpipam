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
	# for length check
	$userModDetails['password1orig'] = $_POST['password1'];
	$userModDetails['password2orig'] = $_POST['password2'];	
}


/**
 * Based on action verify the input
 */
if ($userModDetails['action'] == "add") {
    $errors = verifyUserModInput($userModDetails);
}
else if ($userModDetails['action'] == "edit") {
    $errors = verifyUserModInput($userModDetails);
}
else if ($userModDetails['action'] == "delete") {
    if (!deleteUserById($userModDetails['userId'], $userModDetails['username'])) { print '<div class="alert alert-error>Cannot delete user '. $userModDetails['username'] .'!</div>"'; }
    else 																		 { print '<div class="alert alert-success">User deleted successfully!</div>'; }
    //stop script execution
    die();
}


/**
 *	Create array of permitted networks
 */
if($userModDetails['role'] == "Administrator") {
	$userModDetails['groups'] = "";
}
else {
	foreach($userModDetails as $key=>$post) {
		if(substr($key, 0,5) == "group") {
			unset($userModDetails[$key]);
			$group[substr($key, 5)] = substr($key, 5);
		}
	}
	$userModDetails['groups'] = json_encode($group);
}


/**
 * If no errors are present add / edit user
 */
if (sizeof($errors) != 0) {
    print '<div class="alert alert-error">';
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
    }
    else {
        print '<div class="alert alert-success">User '. $userModDetails['action'] .' successfull!</div>';
        //send notification mail if checked
        if ($userModDetails['notifyUser']) {
        	include('usersEditEmailNotif.php');
        }
    }

}

?>