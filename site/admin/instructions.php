<?php

/**
 *	Script to write instructions for users
 ******************************************/


/* verify that user is admin */
checkAdmin();

/* fetch instructions and print them in instructions div */
$instructions = fetchInstructions();

/* count rows */
$rowcount = substr_count($instructions[0]['instructions'], "\n");
$rowcount++;

if($rowcount < 18) { $rowcount = 18; }

?>

<!-- title -->
<h4>Edit user instructions</h4>
<hr>

<div class="alert alert-info"><strong>Note:</strong> You can use standard HTML formatting tags!</div>


<!-- form -->
<form name="instructions" id="instructions">

	<textarea style="width:100%;" name="instructions" id="instructions" rows="<?php print $rowcount; ?>"><?php print stripslashes($instructions[0]['instructions']); ?></textarea>
	<!-- preview, submit -->
	<br>
	<div style="text-align:right;">
		<input type="button" class="btn btn-small" id="preview" value="preview">
		<input type="submit" class="btn btn-small" value="Save instructions">
	</div>
</form>


<!-- result holder -->
<div class="instructionsResult"></div>

<!-- preview holder -->
<div class="instructionsPreview"></div>