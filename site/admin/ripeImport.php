<?php

/**
 * Script to manage sections
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

?>

<h3>Import subnets from RIPE</h3>

This script imports subnets from RIPE database for specific AS. Enter desired AS to start import process:


<br><br>
<form name="ripeImport" id="ripeImport">
	AS<input type="text" name="as" placeholder="number">
	<input type="submit" value="Import">
</form>

<!-- result -->
<div class="ripeImportTelnet"></div>