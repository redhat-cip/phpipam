<?php

/**
 *	Script to replace fields in IP address list
 ***********************************************/



/* verify that user is admin */
checkAdmin();


?>


<h4>Search and replace fields in IP address list</h4>
<hr><br>


<form id="searchReplace">
<table class="table table-striped" style="width:auto">

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
		<button class="btn btn-small" id="searchReplaceSave"><i class="icon-gray icon-ok"></i> Replace</button>
	</td>
</tr>

</table>
</form>


<!-- result -->
<div class="searchReplaceResult"></div>