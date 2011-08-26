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

/*
print_r($subnetDetails);
die('error');
*/

/**
 * Execute checks on add only and when root subnet is being added
 */
if (($subnetDetails['subnetAction'] == "Add") && ($subnetDetails['masterSubnetId'] == 0))
{
    /* first verify user input */
    $errors   	= verifyCidr ($subnetDetails['subnet']);

    /* verify that no overlapping occurs if we are adding root subnet */
    if ( $overlap = verifySubnetOverlapping ($subnetDetails['sectionId'], $subnetDetails['subnet']) ) {
    	$errors[] = $overlap;
    }
}
/**
 * Execute different checks on add only and when subnet is nested
 */
else if ($subnetDetails['subnetAction'] == "Add")
{
    /* first verify user input */
    $errors   	= verifyCidr ($subnetDetails['subnet']);

    /* verify that nested subnet is inside root subnet */
    if ( !$overlap = verifySubnetNesting ($subnetDetails['masterSubnetId'], $subnetDetails['subnet']) ) {
    	$errors[] = 'Nested subnet not in root subnet!';
    }
    
    /* verify that no overlapping occurs if we are adding nested subnet */
    if ( $overlap = verifyNestedSubnetOverlapping ($subnetDetails['sectionId'], $subnetDetails['subnet']) ) {
    	$errors[] = $overlap;
    }    
} 
else {
}

/* but always verify vlan! */
$vlancheck = validateVlan($subnetDetails['VLAN']);

if($vlancheck != 'ok') {
    $errors[] = $vlancheck;
}


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