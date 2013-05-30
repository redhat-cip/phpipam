<?php

/**
 * Script to print log files!
 ********************************/

/* verify that user is admin */
checkAdmin();
?>

<h4><?php print _('Log files'); ?>:</h4>
<hr>

<!-- severity filter -->
<form id="logs" name="logs">
    <?php print _('Informational'); ?>	<input type="checkbox" name="Informational" value="Informational" checked> ::
    <?php print _('Notice'); ?>			<input type="checkbox" name="Notice"        value="Notice"        checked> ::
    <?php print _('Warning'); ?>			<input type="checkbox" name="Warning"       value="Warning"       checked>

	<!-- download log files -->
	<button id="downloadLogs" class="btn btn-small" style="margin-left:20px"><?php print _('Download logs'); ?></button>

	<!-- download log files -->
	<button id="clearLogs" class="btn btn-small"><i class="icon-gray icon-trash"></i> <?php print _('Clear logs'); ?></button>
   
	<span class="pull-right" id="logDirection">
		<button class="btn btn-small" data-direction="prev" name="next" rel="tooltip" title="<?php print _('Previous page'); ?>"><i class="icon-chevron-left"></i></button>
		<button class="btn btn-small" data-direction="next" name="next" rel="tooltip" title="<?php print _('Next page'); ?>"><i class="icon-chevron-right"></i></button>
	</span>
</form>


<!-- show table -->
<div class="normalTable logs">
<?php include('logResult.php'); ?>
</div>		<!-- end filter overlay div -->