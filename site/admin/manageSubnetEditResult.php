<?php

/** 
 * Function to add / edit / delete section
 ********************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* verify post */
CheckReferrer();

/* get all settings */
$settings = getAllSettings();


/**
 * If request came from IP address subnet edit and
 * action2 is Delete then change action
 */
if(	(isset($_POST['action2'])) && ($_POST['action2'] == "delete") ) {
	$_POST['action'] = $_POST['action2'];
}


/**
 *	If section changes then do checks!
 */
if ( ($_POST['sectionId'] != $_POST['sectionIdNew']) && $_POST['action'] == "edit" ) {
	
	# reset masterId - we are putting it to root
	$_POST['masterSubnetId'] = "0";

    # check for overlapping
    if($settings['strictMode'] == 1) {
    	/* verify that no overlapping occurs if we are adding root subnet */
    	if ( $overlap = verifySubnetOverlapping ($_POST['sectionIdNew'], $_POST['subnet'], $_POST['vrfId']) ) {
    		$errors[] = $overlap;
    	}   
    }
}
/**
 * Execute checks on add only and when root subnet is being added
 */
else if (($_POST['action'] == "add") && ($_POST['masterSubnetId'] == 0)) {

    /* first verify user input */
    $errors   	= verifyCidr ($_POST['subnet']);

    /* check for overlapping */
    if($settings['strictMode'] == 1) {
    	/* verify that no overlapping occurs if we are adding root subnet 
	       only check for overlapping if vrf is empty or not exists!
    	*/
    	if ( $overlap = verifySubnetOverlapping ($_POST['sectionId'], $_POST['subnet'], $_POST['vrfId']) ) {
    		$errors[] = $overlap;
    	}   
    }
}
/**
 * Execute different checks on add only and when subnet is nested
 */
else if ($_POST['action'] == "add") {

    /* first verify user input */
    $errors   	= verifyCidr ($_POST['subnet']);

    /* verify that nested subnet is inside root subnet */
    if($settings['strictMode'] == 1) {
	    if ( !$overlap = verifySubnetNesting ($_POST['masterSubnetId'], $_POST['subnet']) ) {
	    	$errors[] = 'Nested subnet not in root subnet!';
	    }
    }
    /* verify that no overlapping occurs if we are adding nested subnet */
    if ( $overlap = verifyNestedSubnetOverlapping ($_POST['sectionId'], $_POST['subnet'], $_POST['vrfId']) ) {
    	$errors[] = $overlap;
    }    
} 
/**
 * Check if slave is under master
 */
else if ($_POST['action'] == "edit") {
    if($settings['strictMode'] == 1) {
    	/* verify that nested subnet is inside root subnet */
    	if ( (!$overlap = verifySubnetNesting($_POST['masterSubnetId'], $_POST['subnet'])) && $_POST['masterSubnetId']!=0) {
    		$errors[] = 'Nested subnet not in root subnet!';
    	}   
    }
    /* for nesting - MasterId cannot be the same as subnetId! */
    if ( $_POST['masterSubnetId'] == $_POST['subnetId'] ) {
    	$errors[] = 'Subnet cannot nest behind itself!';
    }    
}
else {}

/* but always verify vlan! */
$vlancheck = validateVlan($_POST['VLAN']);

if($vlancheck != 'ok') {
    $errors[] = $vlancheck;
}



//custom
$myFields = getCustomSubnetFields();
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		# replace possible ___ back to spaces!
		$myField['nameTest']      = str_replace(" ", "___", $myField['name']);
		
		if(isset($_POST[$myField['nameTest']])) { $_POST[$myField['name']] = $_POST[$myField['nameTest']];}
	}
}


/* sanitize description */
$_POST['description'] = htmlentities($_POST['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	//prevent XSS


/* If no errors are present execute request */
if (sizeof($errors) != 0) 
{
    print '<div class="alert alert-error"><strong>Please fix following problems</strong>:';
    foreach ($errors as $error) { print "<br>".$error; }
    print '</div>';
    die();
}
else
{
	# failed
    if (!modifySubnetDetails ($_POST)) 		{ print '<div class="alert alert-error">Error adding new subnet!</div>'; }
    # all good
    else {
    	if($_POST['action'] == "delete") 	{ print '<div class="alert alert-success">Subnet, IP addresses and all belonging subnets deleted successfully!</div>'; } 
    	else 								{ print '<div class="alert alert-success">Subnet '. $_POST['action'] .' successfully!</div>';  }
    }
}

?>