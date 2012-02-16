<?php

/**
 * Script to print log files!
 ********************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

?>

<h3>Log files:</h3>

<!-- severity filter -->
<form id="logs" name="logs">
    Informational	<input type="checkbox" name="Informational" value="Informational" checked> ::
    Notice			<input type="checkbox" name="Notice"        value="Notice"        checked> ::
    Warning			<input type="checkbox" name="Warning"       value="Warning"       checked>

	<!-- download log files -->
	<button id="downloadLogs" style="margin-left:20px">Download logs</button>

	<!-- download log files -->
	<button id="clearLogs">Clear logs</button>
   
	<span style="float:right" class="logDirection">
		<input type="button" value="<<" class="prev" name="next" title="Previous page">
		<input type="button" value=">>"	class="next" name="next" title="Next page">
	</span>
</form>


<!-- show table -->
<div class="normalTable logs">
<?php include('logResult.php'); ?>
</div>		<!-- end filter overlay div -->