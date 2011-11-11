<?php

/**
 * Script to manage sections
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get size of subnets - $_POST /4 */
$size = sizeof($_POST) / 4;


/* get unique keys for subnets because they are not sequential of deleted!!! */
foreach($_POST as $key=>$line) {
	if (strlen(strstr($key,"subnet"))>0) {
		$allSubnets[] = $key;
	}
}

/* import each record to database */
foreach($allSubnets as $subnet) {

	//get number
	$m = str_replace("subnet-", "", $subnet);

	//set subnet details
	$subnetDetails['subnetAction'] = "Add";	
	$subnetDetails['subnet'] 	   = $_POST['subnet-' . $m];	
	$subnetDetails['sectionId']    = $_POST['section-' . $m];
	$subnetDetails['description']  = $_POST['description-' . $m];
	$subnetDetails['VLAN'] 		   = $_POST['vlan-' . $m];
	$subnetDetails['masterSubnetId'] = 0;

	//cidr
	if(verifyCidr ($subnetDetails['subnet'])) {
		$errors[] = verifyCidr ($subnetDetails['subnet']);
	}
	//overlapping
	else if (verifySubnetOverlapping ($subnetDetails['sectionId'], $subnetDetails['subnet'])) {
		$errors[] = verifySubnetOverlapping ($subnetDetails['sectionId'], $subnetDetails['subnet']);
	}
	//nested overlapping
	else if (verifyNestedSubnetOverlapping ($subnetDetails['sectionId'], $subnetDetails['subnet'])) {
		$errors[] = verifyNestedSubnetOverlapping ($subnetDetails['sectionId'], $subnetDetails['subnet']);
	}	
	//set insert
	else {
		$subnetInsert[] = $subnetDetails;
	}
}


/* print errors if they exist or success */
if(!isset($errors)) {

	$errors2 = 0;

	//insert if all other is ok!
	foreach($subnetInsert as $subnetDetails) {
		
		if(!modifySubnetDetails ($subnetDetails)) {
			print '<div class="error">Failed to import subnet '. $subnetDetails['subnet'] .'</div>';
			$errors2++;
		}	
	}
	//check if all is ok and print it!
	if($errors2 == 0) {
		print '<div class="success">Import successfull!</div>';
	}
}
else {
	print '<div class="error">Please fix the following errors before inserting:<br>'. "\n";
	
	foreach ($errors as $line) {
		print $line.'<br>';
	}
	
	print '</div>';
}

?>