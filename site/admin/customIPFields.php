<?php

/**
 * Script tomanage custom IP fields
 ****************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* get all custom fields */
$myFields = getCustomIPaddrFields();

/* Custom fields by number */
$myFieldsNum = getCustomIPaddrFieldsNumArr();
?>


<h3>Custom IP address fields</h3>

You can add additional custom fields to IP address table (like CustomerId, location, ...).

<div class="normalTable customIP">
<table class="normalTable customIP">

<!-- headers -->
<tr class="th">
	<th colspan="5">My custom fields:</th>
</tr>


<?php

/* no results */
if(sizeof($myFields) == 0) {

	print '<tr>'. "\n";
	print '<td colspan="5">No custom fields created yet</td>'. "\n";
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
	print '<td class="name">'. $field['name'] .'</td>'. "\n";
	print '<td>'. $field['type'] .'</td>'. "\n";
	# ordering
	if (( ($m+1) != $size) ) {
	print '<td class="img"><img src="css/images/down.png" class="down" title="Move down" fieldName="'. $myFieldsNum[$m] .'" nextFieldName="'. $myFieldsNum[$m+1] .'"></td>'. "\n";		
	}
	else {
		print '<td></td>'. "\n";
	}
	#actions
	print '<td class="img"><img src="css/images/edit.png" class="edit" title="Edit field" fieldName="'. $field['name'] .'"></td>'. "\n";
	print '<td class="img"><img src="css/images/deleteIP.png" class="delete" title="Delete field" fieldName="'. $field['name'] .'"></td>'. "\n";
	print '</tr>'. "\n";
	
	$prevName = $field['name'];
	$m++;	
	}
}

?>

<!-- add -->
<tr class="th" style="border-top:1px solid white">
	<td colspan="5" class="img">
		<img src="css/images/add.png" class="add" title="Add new field"> Add new custom field
	</td>
</tr>

</table>
</div>


<!-- result -->
<div class="customIPResult"></div>