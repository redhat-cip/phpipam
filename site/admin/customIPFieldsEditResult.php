<?php

/** 
 * Edit custom IP field
 ************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();


/* checks */
if($_POST['action'] == "delete") {
	# no cehcks
}
else {
	# length > 4 and < 12
	if( (strlen($_POST['name']) < 4) || (strlen($_POST['name']) > 12) ) {
		$errors[] = 'Name must be between 4 and 12 characters!';
	}
	# no numbers
	if (strcspn($_POST['name'], '0123456789') != strlen($_POST['name'])) {
		$errors[] = 'Name must not contain any numbers!';
	}
}



/* die if errors otherwise execute */
if(sizeof($errors) != 0) {
	print '<div class="error" style="width:290px;">Please correct the following errors:'. "\n";
	print '<ul>'. "\n";
	foreach($errors as $error) {
		print '<li style="text-align:left">'. $error .'</li>'. "\n";
	}
	print '</ul>'. "\n";
	print '</div>'. "\n";
}
else {
	if(!updateCustomIPField($_POST)) {
		print '<div class="error">Failed to '. $_POST['action'] .' field!</div>';
	}
	else {
		print '<div class="success">Field '. $_POST['action'] .' success!</div>';
	}

}

?>