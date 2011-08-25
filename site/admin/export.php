<?php

/**
 *	Script to export IP database to excel file!
 **********************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

?>

<br>
You can download MySQL dump of database or generate XLS file of IP addresses!
<br>
<br>

<!-- MySQL dump -->
<h3>Create MySQL database dump</h3>
<input type="button" id="MySQLdump" value="Prepare Dump">

<!-- XLS dump -->
<h3>Create XLS file of IP addresses</h3>

<input type="button" id="XLSdump" value="Prepare XLS">

<!--
<br>
XLS export uses Pear's OLE and Spreadsheet_Excel_Writer extensions, so please install them through pear:
<pre>
$ pear install OLE 
$ pear install Spreadsheet_Excel_Writer
</pre>
-->