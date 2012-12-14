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
	$outFile = file_get_contents('csvupload/import.csv') or die ('<div class="alert alert-error">Cannot open csvupload/import.csv</div>');

	/* format file */
	$outFile = str_replace( array("\r\n","\r") , "\n" , $outFile);	//replace windows and Mac line break
	$outFile = explode("\n", $outFile);
}
else {
	/* include functions */
	require_once('../../functions/functions.php');
	/* get excel file */
	require_once('../../functions/excel_reader2.php');				//excel reader 2.21
	$data = new Spreadsheet_Excel_Reader('csvupload/import.xls' ,false);	
	
	//get number of rows
	$numRows = $data->rowcount(0);
	$numRows++;

	//get custom fields
	$myFields = getCustomIPaddrFields();
	$myFieldsSize = sizeof($myFields);
	
	//add custom fields
	$numRows = $numRows + $myFieldsSize;
	
	//get all to array!
	for($m=0; $m < $numRows; $m++) {
		//IP must be present!
		if($data->val($m,'A') > 4 ) {
		
		$outFile[$m]  = $data->val($m,'A').','.$data->val($m,'B').','.$data->val($m,'C').','.$data->val($m,'D').',';
		$outFile[$m] .= $data->val($m,'E').','.$data->val($m,'F').','.$data->val($m,'G').','.$data->val($m,'H').',';
		$outFile[$m] .= $data->val($m,'I');
		//add custom fields
		if(sizeof($myFields) > 0) {
			$currLett = "J";
			foreach($myFields as $field) {
				$outFile[$m] .= ",".$data->val($m,$currLett++);
			}
		}
		}
	}
}

/* import each value */
foreach($outFile as $line) {

	//must be longer than 5
	if(strlen($line) > 5) {
	
		//replace states!
		$line = str_replace("Active", "1", $line);
		$line = str_replace("Reserved", "2", $line);
		$line = str_replace("Offline", "0", $line);
		
		//add slashes
		$line = addslashes($line);
		
		//import
		$import = importCSVline ($line, $subnetId);
		if (strlen($import) != 1) {
			$errors[] = $import;
		}
	}
}


/* print errors */
if(isset($errors)) {
	print '<div class="alert alert-error">Errors occured when importing to database!<br>';
	foreach ($errors as $error) {
		print $error . "<br>";
	}
	print '</div>';
}
else {
	print '<div class="alert alert-success">Import successfull!</div>';
}

/* erase file! */
unlink('csvupload/import.'.$filetype);

?>