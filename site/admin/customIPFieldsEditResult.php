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
	# remove spaces
	$_POST['name'] = trim($_POST['name']);
	
	# length > 4 and < 12
	if( (strlen($_POST['name']) < 3) || (strlen($_POST['name']) > 24) ) 	{ $errors[] = _('Name must be between 4 and 24 characters'); }
	
	/* validate HTML */
	
	# must not start with number
	if(is_numeric(substr($_POST['name'], 0, 1))) 							{ $errors[] = _('Name must not start with number'); }		

	# only alphanumeric and _ are allowed
	if(!preg_match('!^[\w_ ]*$!', $_POST['name'])) 							{ $errors[] = _('Only alphanumeric, spaces and underscore characters are allowed'); }
}


/* die if errors otherwise execute */
if(sizeof($errors) != 0) {
	print '<div class="alert alert-error">'._('Please correct the following errors').':'. "\n";
	print '<ul>'. "\n";
	foreach($errors as $error) {
		print '<li style="text-align:left">'. $error .'</li>'. "\n";
	}
	print '</ul>'. "\n";
	print '</div>'. "\n";
}
else {
	if(!updateCustomIPField($_POST)) 	{ print '<div class="alert alert-error"  >'._('Failed to').' '. _($_POST['action']) .' '._('field').'!</div>';}
	else 								{ print '<div class="alert alert-success">'._('Field').' '.     _($_POST['action']) .' '._('success').'!</div>';}
}

?>