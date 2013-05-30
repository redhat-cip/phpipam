<?php

/**
 * Script to get all active IP requests
 ****************************************/

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
/* unset custom! */
$custom = getCustomIPaddrFields();
if(sizeof($custom) > 0) {
	foreach($custom as $key=>$cust) {
		unset($fields[$key]);
	}
}

?>


<h4><?php print _('Filter which fields to display in IP list'); ?></h4>
<hr>

<div class="alert alert-info"><?php print _('You can select which fields are actually being used for IP management, so you dont show any overhead if not used. IP, hostname and description are mandatory'); ?>.</div>


<form id="filterIP">
<table class="filterIP table table-auto table-striped table-top">

<!-- headers -->
<tr>
	<th colspan="2"><?php print _('Check which fields to use for IP addresses'); ?>:</th>
</tr>

<!-- fields -->
<?php
foreach($fields as $field)
{
	print '<tr>'. "\n";
	
	/* check if active - in array */
	if(in_array($field, $setFields))	{ $checked = "checked"; }
	else 								{ $checked = ""; }

	/* print */
	print '<td style="width:10px;padding-left:10px;"><input type="checkbox" name="'. $field .'" value="'. $field .'" '. $checked .'></td>';
	print '<td>'. ucfirst($field) .'</td>';
	
	print '</tr>';
}

?>

<!-- submit -->
<tr>
	<td></td>
	<td>
		<button class="btn btn-small" id="filterIPSave"><i class="icon-gray icon-ok"></i> <?php print _('Save'); ?></button>
	</td>
</tr>


</table>
</form>


<div class="filterIPResult" style="display:none"></div>