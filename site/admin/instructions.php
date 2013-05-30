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
<h4><?php print _('Edit user instructions'); ?></h4>
<hr>


<!-- form -->
<form name="instructions" id="instructionsForm">

	<textarea style="width:100%;" name="instructions" id="instructions" rows="<?php print $rowcount; ?>"><?php print stripslashes($instructions[0]['instructions']); ?></textarea>
	
	<script src="js/ckeditor/ckeditor.js"></script>
	<script>
    	CKEDITOR.replace( 'instructions', {
	    	uiColor: '#f9f9f9',
	    	autoParagraph: false		//wrap inside p
    	});
    </script>

	<!-- preview, submit -->
	<br>
	<div style="text-align:right;">
		<input type="button" class="btn btn-small" id="preview" value="<?php print _('preview'); ?>">
		<input type="submit" class="btn btn-small" value="<?php print _('Save instructions'); ?>">
	</div>
</form>


<!-- result holder -->
<div class="instructionsResult"></div>

<!-- preview holder -->
<div class="instructionsPreview"></div>