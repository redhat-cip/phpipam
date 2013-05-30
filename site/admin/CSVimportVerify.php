<?php
/*
 * CSV import verify + parse data
 *************************************************/

/* required functions */
/* require_once('../../functions/functions.php');  */

/* verify that user is admin */
/* checkAdmin(); */

/* verify post */
/* CheckReferrer(); */


/* get extension */
$filename = $_FILES['file']['name'];
$filename = end(explode(".", $filename));


/* upload */
if ($_FILES["file"]["error"] > 0){
	//if upload fails
	print '<div id="output">failed</div>';
	print '<div id="message">'._('Cannot upload - Return Code:').' ' . $_FILES["file"]["error"] . '</div>';
}
else {
	//if cannot move
	if(!move_uploaded_file($_FILES["file"]["tmp_name"], "csvupload/import.". $filename )) {
		print '<div id="output">failed</div>';
		print '<div id="message">'._('Cannot move file to upload dir').'!</div>';
	}
	else {
		//upload is ok, file overwritten!
		print '<div id="output">'._('Success').'</div>';
		print '<div id="message">';
		print '</div>';
	}
}	

print '</div>';

?>