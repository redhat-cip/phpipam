<?php

/*
 * CSV import form + guide
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* verify post */
CheckReferrer();
?>

<!-- header -->
<div class="pHeader">XLS / CSV subnet import</div>


<!-- content -->
<div class="pContent">
	
	<?php  
	# get custom fields
	#get all custom fields!
	$myFields = getCustomIPaddrFields();
	if(sizeof($myFields) > 0) {
		$custFields = " | ";
		foreach($myFields as $myField) {
			$custFields .= "$myField[name] | ";
		}
		# remove last |
		$custFields = substr($custFields, 0,-2);
	}
	?>

	<!-- notes -->
	To successfully import data please use the following XLS/CSV structure:<br>( ip | State | Description | hostname | MAC | Owner | Switch | Port | Note <?php print $custFields; ?> )
	<br>
	<img src="css/images/csvuploadexample.jpg" style="border:1px solid #999999">
	<br><br>

	<!-- Upload file form -->
	<h4>1.) Upload file:</h4>
	<hr>
	<form name="csvimport" id="csvimport" enctype="multipart/form-data" action="site/admin/CSVimportVerify.php">
		<!-- file -->
		Select CSV file: <input type="file" name="file" id="csvfile">
		<!-- submit -->
		<input type="button" class="btn btn-small" value="Clear" id="csvclear"> 
		<input type="submit" class="btn btn-small" value="Upload" id="csvsubmit">
	</form>

	<!-- jQuery uploader -->
	<script src="js/jquery.fileUploader.js" type="text/javascript"></script>
	
	<!-- Upload JS -->
	<script type="text/javascript">
	$(function(){
		$('#csvfile').fileUploader({
			limit: '',
			imageLoader: '',
			buttonUpload: '#csvsubmit',
			buttonClear: '#csvclear',
			successOutput: 'File Uploaded to csvupload',
			errorOutput: 'Failed',
			inputName: 'userfile',
			inputSize: 30,
			allowedExtension: 'csv|xls',
			callback: function(e) {
			}
		});
	});
	</script>

	<!-- Import file -->
	<h4>2.) Import file:</h4>
	<hr>

	<!-- import button -->
	<input type="button" class="btn btn-small" value="Show uploaded subnets" id="csvimportcheck">

	<!-- verification holder -->
	<div class="csvimportverify"></div>

</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Close window</button>
	<!-- result -->
	<div class="csvImportResult"></div>
</div>