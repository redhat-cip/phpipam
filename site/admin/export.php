<?php

/**
 *	Script to export IP database to excel file!
 **********************************************/


/* verify that user is admin */
checkAdmin();

?>

<h4>phpIPAM database export</h4>
<hr><br>

<div class="alert alert-info">You can download MySQL dump of database or generate XLS file of IP addresses!</div>

<!-- MySQL dump -->
<h4>Create MySQL database dump</h4>
<button class="btn btn-small" id="MySQLdump"><i class="icon-gray icon-download"></i> Prepare MySQL dump</button>

<!-- XLS dump -->
<h4>Create XLS file of IP addresses</h4>
<button class="btn btn-small" id="XLSdump"><i class="icon-gray icon-download"></i> Prepare XLS dump</button>

<!-- XLS dump -->
<h4>Create hostfile dump</h4>
<button class="btn btn-small" id="hostfileDump"><i class="icon-gray icon-download"></i> Prepare hostfile dump</button>