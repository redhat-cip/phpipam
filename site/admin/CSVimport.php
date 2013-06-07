<?php

/*
 * CSV import form + guide
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

# permissions
$permission = checkSubnetPermission ($_POST['subnetId']);

# die if write not permitted
if($permission < 2) { die("<div class='alert alert-error'>"._('You cannot write to this subnet')."!</div>"); }

/* verify post */
CheckReferrer();
?>

<!-- header -->
<div class="pHeader"><?php print _('XLS / CSV subnet import'); ?></div>


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
	<?php print _('To successfully import data please use the following XLS/CSV structure:<br>( ip | State | Description | hostname | MAC | Owner | Device | Port | Note '); ?> <?php print $custFields; ?> )
	<br>
	<img src="css/images/csvuploadexample.jpg" style="border:1px solid #999999">
	<br><br>

	<!-- Upload file form -->
	<h4>1.) <?php print _('Upload file'); ?>:</h4>
	<hr>
	<form name="csvimport" id="csvimport" enctype="multipart/form-data" action="site/admin/CSVimportVerify.php">
		<!-- file -->
		<?php print _('Select CSV file'); ?>: <input type="file" name="file" id="csvfile">
		<!-- submit -->
		<input type="button" class="btn btn-small" value="<?php print _('Clear'); ?>" id="csvclear"> 
		<input type="submit" class="btn btn-small" value="<?php print _('Upload'); ?>" id="csvsubmit">
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
			successOutput: _('File Uploaded to csvupload'),
			errorOutput: _('Failed'),
			inputName: 'userfile',
			inputSize: 30,
			allowedExtension: 'csv|xls',
			callback: function(e) {
			}
		});
	});
	</script>

	<!-- Import file -->
	<h4>2.) <?php print _('Import file'); ?>:</h4>
	<hr>

	<!-- import button -->
	<input type="button" class="btn btn-small" value="<?php print _('Show uploaded subnets'); ?>" id="csvimportcheck">

	<!-- verification holder -->
	<div class="csvimportverify"></div>

</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Close window'); ?></button>
	<!-- result -->
	<div class="csvImportResult"></div>
</div>