<?php

/**
 *	Script to replace fields in IP address list
 ***********************************************/


/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();


?>


<h3>Search and replace fields in IP address list</h3>


<form id="searchReplace">
<table class="normalTable" style="width:auto">

<tr>
	<td>Select field to replace:</td>
	<td>
	<select name="field">
		<option value="description">Description</option>
		<option value="dns_name">Hostname</option>
		<option value="owner">Owner</option>
		<option value="mac">MAC address</option>
		<option value="switch">Switch</option>
		<option value="port">Port</option>
		
		<?php
		#get all custom fields!
		$myFields = getCustomIPaddrFields();
		if(sizeof($myFields) > 0) {
			foreach($myFields as $myField) {
				print '<option value="'. $myField['name'] .'"> '. $myField['name'] .'</option>';
			}
		}
		?>
		
	</select>
	</td>
</tr>

<tr>
	<td>Select search string</td>
	<td>
		<input type="text" name="search" placeholder="search string">
	</td>
</tr>

<tr>
	<td>Select replace string</td>
	<td>
		<input type="text" name="replace" placeholder="replace string">
	</td>
</tr>

<tr class="th">
	<td></td>
	<td>
		<input type="submit" value="Replace">
	</td>
</tr>

</table>
</form>


<!-- result -->
<div class="searchReplaceResult"></div>