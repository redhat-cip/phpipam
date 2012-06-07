<?php

/*
 *	Script to inserte imported file to database!
 **********************************************/
 
/* we need functions */
require_once('../../functions/functions.php');

/* get subnet ID and type */
$subnetId = $_POST['subnetId'];
$filetype = $_POST['filetype'];
$filetype = end(explode(".", $filetype));


/* get $outFile based on provided filetype */
if ($filetype == "csv") {
	/* get file to string */
	$outFile = file_get_contents('csvupload/import.csv') or die ('Cannot open csvupload/import.csv');

	/* format file */
	$outFile = str_replace( array("\r\n","\r") , "\n" , $outFile);	//replace windows and Mac line break
	$outFile = explode("\n", $outFile);
}
else {

	/* get excel file */
	require_once('../../functions/excel_reader2.php');				//excel reader 2.21
	$data = new Spreadsheet_Excel_Reader('csvupload/import.xls' ,false);	
	
	//get number of rows
	$numRows = $data->rowcount(0);
	$numRows++;
	
	//get all to array!
	for($m=0; $m < $numRows; $m++) {
	
		//IP must be present!
		if($data->val($m,'A') > 4 ) {
			$outFile[] = $data->val($m,'A') . ',' . $data->val($m,'B'). ',' . $data->val($m,'C'). ',' . $data->val($m,'D'). ',' . $data->val($m,'E'). ',' . $data->val($m,'F'). ',' . $data->val($m,'G'). ',' . $data->val($m,'H'). ',' . $data->val($m,'I');
		}
	}
}

/* import each value */
foreach($outFile as $line) {

	//must be longer than 5
	if(strlen($line) > 5) {
		//import
		if (!importCSVline ($line, $subnetId)) {
			$errors[] = $line;
		}
	}
}


/* print errors */
if($errors) {
	print '<div class="error">error importing to database!<br>';
	foreach ($errors as $error) {
		print $error . "<br>";
	}
	print '</div>';
}
else {
	print '<div class="success">Import successfull!</div>';
}

/* erase file! */
unlink('csvupload/import.'.$filetype);

?>