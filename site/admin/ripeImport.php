<?php

/**
 * Script to manage sections
 *************************************************/

/* verify that user is admin */
checkAdmin();

?>

<h4><?php print _('Import subnets from RIPE'); ?></h4>
<hr><br>

<div class="alert alert-info alert-absolute"><?php print _('This script imports subnets from RIPE database for specific AS. Enter desired AS to search for subnets'); ?>.</div>

<form name="ripeImport" id="ripeImport" style="margin-top:50px;">
	<div class="input-append">
		<input class="span2 search" id="appendedInputButton" placeholder="<?php print _('AS number'); ?>" name="as" size="16" type="text"><input type="submit" class="btn" value="<?php print _('Search'); ?>">
	</div>
</form>

<!-- result -->
<div class="ripeImportTelnet"></div>