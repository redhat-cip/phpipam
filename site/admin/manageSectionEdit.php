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
 * First we need to identify action
 */
$action = $_POST['action'];
$id     = $_POST['id'];


/* format action */
if ($action == "sectionAdd") {
    $action = "Add";
}
else if ($action == "sectionEdit") {
    $action = "Edit";
}
else if ($action == "sectionDelete") {
     $action = "Delete"; 
}


/**
 * Fetch section info
 */
$section = getSectionDetailsById ($id);

?>

<!-- action table -->
<div class="normalTable edit">
<!-- form -->
<form class="sectionEdit" name="sectionEdit">

<!-- edit table -->
<table class="edit">

<!-- title -->
	<tr>
		<th colspan="2"><?php print $action;?> Section</th>
	</tr>
	
	<!-- section name -->
	<tr>
		<td>Name</td>
		<td>
			<input type="text" name="name" value="<?php print $section['name']; ?>" size="30" <?php if ($action == "Delete" ) { print ' readonly '; } ?>>
		</td>
	</tr>

	<!-- description -->
	<tr>
		<td>Description</td>
		<td><input type="text" name="description" value="<?php print $section['description']; ?>" size="30" <?php if ($action == "Delete") {print " readonly ";}?>></td>
	</tr>

	<!-- submit -->
	<tr>
		<td></td>
		<td>
			<input type="hidden" name="action" 	value="<?php print $action; ?>">
			<input type="hidden" name="id" 		value="<?php print $id; ?>">
			<input type="submit" 				value="<?php print $action; ?>">
			<input type="button" 				value="Cancel" class="cancelSectionEdit">
		</td>

	<!-- delete warning -->
	<?php
	if ($action == "Delete") {
    	print '<div class="error"><b>!!! Please note !!!</b><br>Deleting Section will delete all belonging subnets and IP addresses!</div>' . "\n";
	}
	?>


</table>	<!-- end table -->
</form>		<!-- end form -->

</div>		<!-- end overlay div -->


<!-- result holder -->
<div class="sectionEditResult"></div>