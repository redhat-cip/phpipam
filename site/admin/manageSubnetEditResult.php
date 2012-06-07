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

/* set post data */
$subnetDetails = $_POST;

/* get all settings */
$settings = getAllSettings();

/*
print '<pre>';
print_r($subnetDetails);
die('error');
*/


/**
 * If request came from IP address subnet edit and
 * subnetAction2 is Delete then change subnetAction
 */
if(	(isset($subnetDetails['subnetAction2'])) && ($subnetDetails['subnetAction2'] == "Delete") ) {
	$subnetDetails['subnetAction'] = $subnetDetails['subnetAction2'];
}


/**
 * Execute checks on add only and when root subnet is being added
 */
if (($subnetDetails['subnetAction'] == "Add") && ($subnetDetails['masterSubnetId'] == 0)) {

    /* first verify user input */
    $errors   	= verifyCidr ($subnetDetails['subnet']);

    if($settings['strictMode'] == 1) {
    	/* verify that no overlapping occurs if we are adding root subnet */
    	if ( $overlap = verifySubnetOverlapping ($subnetDetails['sectionId'], $subnetDetails['subnet']) ) {
    		$errors[] = $overlap;
    	}   
    }
}
/**
 * Execute different checks on add only and when subnet is nested
 */
else if ($subnetDetails['subnetAction'] == "Add") {

    /* first verify user input */
    $errors   	= verifyCidr ($subnetDetails['subnet']);

    /* verify that nested subnet is inside root subnet */
    if($settings['strictMode'] == 1) {
	    if ( !$overlap = verifySubnetNesting ($subnetDetails['masterSubnetId'], $subnetDetails['subnet']) ) {
	    	$errors[] = 'Nested subnet not in root subnet!';
	    }
    }
    /* verify that no overlapping occurs if we are adding nested subnet */
/*
    if ( $overlap = verifyNestedSubnetOverlapping ($subnetDetails['sectionId'], $subnetDetails['subnet']) ) {
    	$errors[] = $overlap;
    }    
*/
} 
/**
 * Check if slave is under master
 */
else if ($subnetDetails['subnetAction'] == "Edit") {
    if($settings['strictMode'] == 1) {
    	/* verify that nested subnet is inside root subnet */
    	if ( (!$overlap = verifySubnetNesting($subnetDetails['masterSubnetId'], $subnetDetails['subnet'])) && $subnetDetails['masterSubnetId']!=0) {
    		$errors[] = 'Nested subnet not in root subnet!';
    	}   
    }
    /* for nesting - MasterId cannot be the same as subnetId! */
    if ( $subnetDetails['masterSubnetId'] == $subnetDetails['subnetId'] ) {
    	$errors[] = 'Subnet cannot nest behind itself!';
    }    
}
else {
}

/* but always verify vlan! */
$vlancheck = validateVlan($subnetDetails['VLAN']);

if($vlancheck != 'ok') {
    $errors[] = $vlancheck;
}


/* sanitize description */
$subnetDetails['description'] = htmlentities($subnetDetails['description'], ENT_COMPAT | ENT_HTML401, "UTF-8");	//prevent XSS


/**
 * If no errors are present execute request
 */
if (sizeof($errors) != 0) 
{
    print '<div class="error">';
        
    foreach ($errors as $error) {
        print $error .'<br>';
    }
        
    print '</div>';
    die();
}
else
{
    if (!modifySubnetDetails ($subnetDetails)) {
        print '<div class="error">Error adding new subnet!</div>';
    }
    else {
        print '<div class="success">Subnet '. $subnetDetails['subnetAction'] .' successfully!</div>';
    }    
}

?>