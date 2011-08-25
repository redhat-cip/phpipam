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

<!-- jQuery uploader -->
<script src="js/jquery.fileUploader.js" type="text/javascript"></script>

<!-- overlay -->
<div class="csvImportOverlay">

<!-- title -->
<h3>XLS / CSV subnet import</h3>

<!-- notes -->
To successfully import data please use the following XLS/CSV structure:<br>( ip | Desc | hostname | Switch | Port | Owner | Note )
<br>
<img src="css/images/csvuploadexample.jpg" style="border:1px solid #999999">
<br>
<hr>

<!-- Upload file form -->
<h3>1.) Upload file:</h3>
<form name="csvimport" id="csvimport" enctype="multipart/form-data" action="site/admin/CSVimportVerify.php">
	<!-- file -->
	Select CSV file: <input type="file" name="file" id="csvfile">
	
	<!-- submit -->
	<input type="button" value="Clear" id="csvclear">
	<input type="submit" value="Upload" id="csvsubmit">
</form>

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
<h3>2.) Import file:</h3>

<!-- import button -->
<input type="button" value="Import" id="csvimportcheck">

<!-- verification holder -->
<div class="csvimportverify"></div>

</div>	<!-- end csvimportoverlay -->