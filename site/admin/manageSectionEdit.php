<?php

/*
 * Print edit sections form
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* verify post */
CheckReferrer();

/**
 * Fetch section info
 */
$section = getSectionDetailsById ($_POST['sectionId']);

?>



<!-- header -->
<div class="pHeader"><?php print ucwords($_POST['action']); ?> Section</div>


<!-- content -->
<div class="pContent">

	<!-- form -->
	<form id="sectionEdit" name="sectionEdit">

		<!-- edit table -->
		<table class="table">
	
		<!-- section name -->
		<tr>
			<td>Name</td>
			<td>
				<input type="text" name="name" value="<?php print $section['name']; ?>" size="30" <?php if ($_POST['action'] == "Delete" ) { print ' readonly '; } ?> placeholder="Section name">
				<!-- hidden -->
				<input type="hidden" name="action" 	value="<?php print $_POST['action']; ?>">
				<input type="hidden" name="id" 		value="<?php print $_POST['sectionId']; ?>">
			</td>
		</tr>

		<!-- description -->
		<tr>
			<td>Description</td>
			<td>
				<input type="text" name="description" value="<?php print $section['description']; ?>" size="30" <?php if ($_POST['action'] == "Delete") {print " readonly ";}?> placeholder="Section description">
			</td>
		</tr>

		</table>	<!-- end table -->
	</form>		<!-- end form -->
	
	<!-- delete warning -->
	<?php
	if ($_POST['action'] == "delete") {
		print '<div class="alert alert-warn"><b>Warning!</b><br>Deleting Section will delete all belonging subnets and IP addresses!</div>' . "\n";
	}
	?>
</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Cancel</button>
	<button class="btn btn-small" id="editSectionSubmit"><i class="icon-gray icon-ok"></i> <?php print ucwords($_POST['action']); ?> section</button>

	<!-- result holder -->
	<div class="sectionEditResult"></div>
</div>	
		