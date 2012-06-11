<?php

/*
 *	Script to parse imported file!
 ********************************/


/* get filetype */
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
		$outFile[] = $data->val($m,'A') . ',' . $data->val($m,'B'). ',' . $data->val($m,'C'). ',' . $data->val($m,'D'). ',' . $data->val($m,'E'). ',' . $data->val($m,'F'). ',' . $data->val($m,'G') . ',' . $data->val($m,'H') . ',' . $data->val($m,'I');
	}
	/* 	echo $data->dump(false,false); */
}


/*
 *	print table
 *********************/
print '<div class="normalTable">';
print '<table class="normalTable">';

/* headers */
print '<tr class="th">';
print '	<th>IP</th>';
print '	<th>Status</th>';
print '	<th>Description</th>';
print '	<th>Hostname</th>';
print '	<th>MAC</th>';
print '	<th>Owner</th>';
print '	<th>Switch</th>';
print '	<th>Port</th>';
print '	<th>Note</th>';
print '</tr>';


/* values - $outFile is provided by showscripts */
foreach($outFile as $line) {

	//put it to array
	$field = explode(",", $line);
	
	//print
	print '<tr>';
	
	foreach ($field as $value) {
		if (!empty($field[0])) {			//IP address must be present otherwise ignore field
			print '<td>'. $value .'</td>';
		}
	}
	
	print '</tr>';
}

print '</table>';
print '</div>';
?>

<!-- confirmation -->
<br>Does this look reasonable? 

<!-- YES / NO -->
<input type="button" value="Yes" id="csvImportYes">
<input type="button" value="No"  id="csvImportNo">

<!-- result -->
<div class="csvImportResult"></div>