<?php

/**
 * Script tomanage custom IP fields
 ****************************************/

/* verify that user is admin */
checkAdmin();

/* get all custom fields */
$myFields = getCustomIPaddrFields();
/* Custom fields by number */
$myFieldsNum = getCustomIPaddrFieldsNumArr();


/* get all custom subnet fields */
$myFieldsSubnets = getCustomSubnetFields();
/* Custom fields by number */
$myFieldsNumSubnets = getCustomSubnetsFieldsNumArr();

/* get all custom VLAN fields */
$myFieldsVLAN = getCustomVLANFields();
/* Custom VLAN fields by number */
$myFieldsNumVLAN = getCustomVLANFieldsNumArr();
?>


<h4>Custom IP address fields</h4>
<hr>

<div class="alert alert-info">You can add additional custom fields to IP addresses and subnets (like CustomerId, location, ...).</div>


<table class="customIP table table-striped table-auto table-top">

<!-- headers -->
<tr>
	<th colspan="4">My  IP address fields:</th>
</tr>

<?php
/* no results */
if(sizeof($myFields) == 0) {
	print '<tr>'. "\n";
	print '<td colspan="4"><div class="alert alert-info alert-nomargin">No custom fields created yet</div></td>'. "\n";
	print '</tr>'. "\n";
}
/* already available */
else {
	# get size
	$size = sizeof($myFields);
	$m = 0;

	foreach($myFields as $field)
	{
	print '<tr>'. "\n";

	# ordering
	if (( ($m+1) != $size) ) 	{ print "<td><button class='btn btn-small down' data-direction='down' rel='tooltip' title='Move down' data-fieldname='$myFieldsNum[$m]' data-nextfieldname='".$myFieldsNum[$m+1]."'><i class='icon-gray icon-chevron-down'></i></button></td>";	}
	else 						{ print '<td></td>'. "\n";}
	
	print '<td class="name">'. $field['name'] .'</td>'. "\n";

	#actions
	print "<td>";
	print "	<button class='btn btn-small' data-action='edit'   data-fieldname='$field[name]'><i class='icon-gray icon-edit'></i> Edit</button>";
	print "	<button class='btn btn-small' data-action='delete' data-fieldname='$field[name]'><i class='icon-gray icon-remove'></i> Delete</button>";
	
	# warning for older versions
	if((is_numeric(substr($field['name'], 0, 1))) || (!preg_match('!^[\w_ ]*$!', $field['name'])) ) { print '<span class="alert alert-warning"><strong>Warning</strong>: Invalid field name!</span>'; }
	
	print "</td>";

	print '</tr>'. "\n";
	
	$prevName = $field['name'];
	$m++;	
	}
}
?>

<!-- add -->
<tr>
	<td colspan="4">
		<button class='btn btn-small' data-action='add'  data-fieldname='<?php print $field['name']; ?>' rel='tooltip' data-placement='right' title='Add new custom IP address field'><i class='icon-gray icon-plus'></i> Add new</button>
	</td>
</tr>
</table>

<!-- result -->
<div class="customIPResult"></div>



<br>
<h4>Custom subnet fields</h4>
<hr>


<table class="customSubnet table table-striped table-auto table-top">

<!-- headers -->
<tr>
	<th colspan="4">My  IP address fields:</th>
</tr>

<?php
/* no results */
if(sizeof($myFieldsSubnets) == 0) {
	print '<tr>'. "\n";
	print '<td colspan="4"><div class="alert alert-info alert-nomargin">No custom subnet fields created yet</div></td>'. "\n";
	print '</tr>'. "\n";
}
/* already available */
else {
	# get size
	$size = sizeof($myFieldsSubnets);
	$m = 0;

	foreach($myFieldsSubnets as $field)
	{
	print '<tr>'. "\n";

	# ordering
	if (( ($m+1) != $size) ) 	{ print "<td><button class='btn btn-small down' data-direction='down' rel='tooltip' title='Move down' data-fieldname='$myFieldsNumSubnets[$m]' data-nextfieldname='".$myFieldsNumSubnets[$m+1]."'><i class='icon-gray icon-chevron-down'></i></button></td>";	}
	else 						{ print '<td></td>'. "\n";}
	
	print '<td class="name">'. $field['name'] .'</td>'. "\n";

	#actions
	print "<td>";
	print "	<button class='btn btn-small' data-action='edit'   data-fieldname='$field[name]'><i class='icon-gray icon-edit'></i> Edit</button>";
	print "	<button class='btn btn-small' data-action='delete' data-fieldname='$field[name]'><i class='icon-gray icon-remove'></i> Delete</button>";
	
	# warning for older versions
	if((is_numeric(substr($field['name'], 0, 1))) || (!preg_match('!^[\w_ ]*$!', $field['name'])) ) { print '<span class="alert alert-warning"><strong>Warning</strong>: Invalid field name!</span>'; }
	
	print "</td>";

	print '</tr>'. "\n";
	
	$prevName = $field['name'];
	$m++;	
	}
}
?>

<!-- add -->
<tr>
	<td colspan="4">
		<button class='btn btn-small' data-action='add'  data-fieldname='<?php print $field['name']; ?>' rel='tooltip' data-placement='right' title='Add new custom subnet field'><i class='icon-gray icon-plus'></i> Add new</button>
	</td>
</tr>
</table>

<!-- result -->
<div class="customSubnetResult"></div>




<br>
<h4>Custom VLAN fields</h4>
<hr>


<table class="customVLAN table table-striped table-auto table-top">

<!-- headers -->
<tr>
	<th colspan="4">My VLAN fields:</th>
</tr>

<?php
/* no results */
if(sizeof($myFieldsVLAN) == 0) {
	print '<tr>'. "\n";
	print '<td colspan="4"><div class="alert alert-info alert-nomargin">No custom VLAN fields created yet</div></td>'. "\n";
	print '</tr>'. "\n";
}
/* already available */
else {
	# get size
	$size = sizeof($myFieldsVLAN);
	$m = 0;

	foreach($myFieldsVLAN as $field)
	{
	print '<tr>'. "\n";

	# ordering
	if (( ($m+1) != $size) ) 	{ print "<td><button class='btn btn-small down' data-direction='down' rel='tooltip' title='Move down' data-fieldname='$myFieldsNumVLAN[$m]' data-nextfieldname='".$myFieldsNumVLAN[$m+1]."'><i class='icon-gray icon-chevron-down'></i></button></td>";	}
	else 						{ print '<td></td>'. "\n";}
	
	print '<td class="name">'. $field['name'] .'</td>'. "\n";

	#actions
	print "<td>";
	print "	<button class='btn btn-small' data-action='edit'   data-fieldname='$field[name]'><i class='icon-gray icon-edit'></i> Edit</button>";
	print "	<button class='btn btn-small' data-action='delete' data-fieldname='$field[name]'><i class='icon-gray icon-remove'></i> Delete</button>";

	# warning for older versions
	if((is_numeric(substr($field['name'], 0, 1))) || (!preg_match('!^[\w_ ]*$!', $field['name'])) ) { print '<span class="alert alert-warning"><strong>Warning</strong>: Invalid field name!</span>'; }

	print "</td>";

	print '</tr>'. "\n";
	
	$prevName = $field['name'];
	$m++;	
	}
}
?>

<!-- add -->
<tr>
	<td colspan="4">
		<button class='btn btn-small' data-action='add'  data-fieldname='<?php print $field['name']; ?>' rel='tooltip' data-placement='right' title='Add new custom VLAN field'><i class='icon-gray icon-plus'></i> Add new</button>
	</td>
</tr>
</table>

<!-- result -->
<div class="customVLANResult"></div>