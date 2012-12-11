<?php

/**
 * Script to print log files!
 ********************************/

/* verify that user is admin */
checkAdmin();
?>

<h4>Log files:</h4>
<hr>

<!-- severity filter -->
<form id="logs" name="logs">
    Informational	<input type="checkbox" name="Informational" value="Informational" checked> ::
    Notice			<input type="checkbox" name="Notice"        value="Notice"        checked> ::
    Warning			<input type="checkbox" name="Warning"       value="Warning"       checked>

	<!-- download log files -->
	<button id="downloadLogs" class="btn btn-small" style="margin-left:20px">Download logs</button>

	<!-- download log files -->
	<button id="clearLogs" class="btn btn-small">Clear logs</button>
   
	<span class="pull-right" id="logDirection">
		<button class="btn btn-small" data-direction="prev" name="next" rel="tooltip" title="Previous page"><i class="icon-chevron-left"></i></button>
		<button class="btn btn-small" data-direction="next" name="next" rel="tooltip" title="Next page"><i class="icon-chevron-right"></i></button>
	</span>
</form>


<!-- show table -->
<div class="normalTable logs">
<?php include('logResult.php'); ?>
</div>		<!-- end filter overlay div -->