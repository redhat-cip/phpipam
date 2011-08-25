<?php

/**
 *	Script to write instructions for users
 ******************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* fetch instructions and print them in instructions div */
$instructions = fetchInstructions();

/* count rows */
$rowcount = substr_count($instructions[0]['instructions'], "\n");
$rowcount++;

if($rowcount < 22) {
	$rowcount = 22;
}

?>

<!-- title -->
<h3>Edit user instructions</h3>
<div class="hint">* You can use standard HTML formatting tags!</div>


<!-- form -->
<form name="instructions" id="instructions">

	<!-- instructions -->
	<textarea name="instructions" id="instructions" rows="<?php print $rowcount; ?>"><?php print $instructions[0]['instructions']; ?></textarea>
	
	<!-- preview, submit -->
	<br>
	<div style="text-align:right;">
		<input type="button" id="preview" value="preview">
		<input type="submit" class="submit" value="Save">
	</div>
</form>


<!-- result holder -->
<div class="instructionsResult"></div>

<!-- preview holder -->
<div class="instructionsPreview"></div>