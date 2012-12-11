<?php

/**
 * Script to manage sections
 *************************************************/

/* verify that user is admin */
checkAdmin();

?>

<h4>Import subnets from RIPE</h4>
<hr><br>

<div class="alert alert-info">This script imports subnets from RIPE database for specific AS. Enter desired AS to search for subnets.</div>

<form name="ripeImport" id="ripeImport">
	<div class="input-append">
		<input class="span2 search" id="appendedInputButton" placeholder="number" name="as" size="16" type="text"><input type="submit" class="btn" value="Search">
	</div>
</form>

<!-- result -->
<div class="ripeImportTelnet"></div>