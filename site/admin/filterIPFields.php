<?php

/**
 * Script to get all active IP requests
 ****************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();


/* get all fields in IP table */
$fields = getIPaddrFields();

/* get all selected fields */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);


/* unset mandatory fields -> id,subnetid,ip_addr */
unset($fields['id'], $fields['subnetId'], $fields['ip_addr'], $fields['description'], $fields['dns_name']);

?>


<h3>Filter which fields to display in IP list</h3>

You can select which fields are actually being used for IP management, so you dont show any overhead if not used. IP, hostname and description are mandatory.


<div class="normalTable filterIP">
<form id="filterIP">
<table class="normalTable filterIP">

<!-- headers -->
<tr class="th">
	<th colspan="2">Check which fields to show:</th>
</tr>

<!-- fields -->
<?php
foreach($fields as $field)
{
	print '<tr>'. "\n";
	
	/* check if active - in array */
	if(in_array($field, $setFields)) {
		$checked = "checked";
	}
	else {
		$checked = "";
	}

	/* print */
	print '<td style="width:10px;padding-left:10px;"><input type="checkbox" name="'. $field .'" value="'. $field .'" '. $checked .'></td>';
	print '<td>'. ucfirst($field) .'</td>';
	
	print '</tr>';
}

?>

<!-- submit -->
<tr class="th" style="border-top:1px solid white">
	<td></td>
	<td><input type="submit" value="Set fields"></td>
</tr>

<!-- result -->
<tr class="th">
	<td></td>
	<td>
		<div class="filterIPResult" style="displa:none"></div>
	</td>
</tr>

</table>
</form>
</div>