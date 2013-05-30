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

/* get all custom User fields */
$myFieldsUser = getCustomUserFields();
/* Custom VLAN fields by number */
$myFieldsNumUser = getCustomUserFieldsNumArr();
?>


<h4><?php print _('Custom fields'); ?></h4>
<hr>

<table class="customIP table table-striped table-auto table-top" style="min-width:400px;">

	<tbody id="ip">
	<!-- Custom IP address fields -->
	<tr>
		<th colspan="4">
			<h5><?php print _('Custom IP address fields'); ?></h5>
		</th>
	</tr>
	<?php
	/* no results */
	if(sizeof($myFields) == 0) { ?>
		<tr>
			<td colspan="4"><div class="alert alert-info alert-nomargin"><?php print _('No custom fields created yet'); ?></div></td>
		</tr>
	<?php 
	} 
	else { 
		# get size
		$size = sizeof($myFields);
		$m = 0;

		foreach($myFields as $field)
		{
			print "<tr>";

			# ordering
			if (( ($m+1) != $size) ) 	{ print "<td style='width:10px;'><button class='btn btn-small down' data-direction='down' rel='tooltip' title='Move down' data-fieldname='$myFieldsNum[$m]' data-nextfieldname='".$myFieldsNum[$m+1]."'><i class='icon-gray icon-chevron-down'></i></button></td>";	}
			else 						{ print "<td style='width:10px;'></td>";}
	
			print "<td class='name'>$field[name]</td>";

			#actions
			print "<td class='actions'>";
			print "	<div class='btn-group'>";
			print "		<button class='btn btn-small' data-action='edit'   data-fieldname='$field[name]'><i class='icon-gray icon-pencil'></i></button>";
			print "		<button class='btn btn-small' data-action='delete' data-fieldname='$field[name]'><i class='icon-gray icon-remove'></i></button>";
			print "	</div>";
	
			# warning for older versions
			if((is_numeric(substr($field['name'], 0, 1))) || (!preg_match('!^[\w_ ]*$!', $field['name'])) ) { print '<span class="alert alert-warning"><strong>Warning</strong>: '._('Invalid field name').'!</span>'; }
	
			print "</td>";
			print "</tr>";
			
			$prevName = $field['name'];
			$m++;	
		}
	}
	?>
	<!-- add -->
	<tr>
		<td colspan="4" style='padding-right:0px;'>
			<button class='btn btn-small pull-right' data-action='add'  data-fieldname='<?php print $field['name']; ?>' rel='tooltip' data-placement='right' title='<?php print _('Add new custom IP address field'); ?>'><i class='icon-gray icon-plus'></i>
		</td>
	</tr>	
	<!-- result -->
	<tr>
		<td colspan="4" class="result">
			<div class="customIPResult"></div>
		</td>
	</tr>
	</tbody>




	<!-- Custom subnet fields -->
	<tbody id="subnet">
	<tr>
		<th colspan="4">
			<h5><?php print _('Custom subnet fields'); ?></h5>
		</th>
	</tr>
	<?php
	/* no results */
	if(sizeof($myFieldsSubnets) == 0) {
		print '<tr>'. "\n";
		print '<td colspan="4"><div class="alert alert-info alert-nomargin">'._('No custom subnet fields created yet').'</div></td>'. "\n";
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
			if (( ($m+1) != $size) ) 	{ print "<td><button class='btn btn-small down' data-direction='down' rel='tooltip' title='"._('Move down')."' data-fieldname='$myFieldsNumSubnets[$m]' data-nextfieldname='".$myFieldsNumSubnets[$m+1]."'><i class='icon-gray icon-chevron-down'></i></button></td>";	}
			else 						{ print '<td></td>'. "\n";}
	
			print '<td class="name">'. $field['name'] .'</td>'. "\n";

			#actions
			print "<td class='actions'>";
			print "	<div class='btn-group'>";
			print "		<button class='btn btn-small' data-action='edit'   data-fieldname='$field[name]'><i class='icon-gray icon-pencil'></i></button>";
			print "		<button class='btn btn-small' data-action='delete' data-fieldname='$field[name]'><i class='icon-gray icon-remove'></i></button>";
			print "	</div>";
	
			# warning for older versions
			if((is_numeric(substr($field['name'], 0, 1))) || (!preg_match('!^[\w_ ]*$!', $field['name'])) ) { print '<span class="alert alert-warning"><strong>'._('Warning').'</strong>: '._('Invalid field name').'!</span>'; }
	
			print "</td>";

			print '</tr>'. "\n";
	
			$prevName = $field['name'];
			$m++;	
		}
	}
	?>

	<!-- add -->
	<tr>
		<td colspan="4" style='padding-right:0px;'>
			<button class='btn btn-small pull-right' data-action='add'  data-fieldname='<?php print $field['name']; ?>' rel='tooltip' data-placement='right' title='<?php print _('Add new custom subnet field'); ?>'><i class='icon-gray icon-plus'></i>
		</td>
	</tr>

	<!-- result -->
	<tr>
		<td colspan="4" class="result">
			<div class="customSubnetResult"></div>
		</td>
	</tr>
	</tbody>





	<!-- Custom VLAN fields -->
	<tbody id="vlan">
	<tr>
		<th colspan="4">
			<h5><?php print _('Custom VLAN fields'); ?></h5>
		</th>
	</tr>
	<?php
	/* no results */
	if(sizeof($myFieldsVLAN) == 0) {
		print '<tr>'. "\n";
		print '<td colspan="4"><div class="alert alert-info alert-nomargin">'._('No custom VLAN fields created yet').'</div></td>'. "\n";
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
			if (( ($m+1) != $size) ) 	{ print "<td><button class='btn btn-small down' data-direction='down' rel='tooltip' title='"._('Move down')."' data-fieldname='$myFieldsNumVLAN[$m]' data-nextfieldname='".$myFieldsNumVLAN[$m+1]."'><i class='icon-gray icon-chevron-down'></i></button></td>";	}
			else 						{ print '<td></td>'. "\n";}
	
			print '<td class="name">'. $field['name'] .'</td>'. "\n";

			#actions
			print "<td class='actions'>";
			print "	<div class='btn-group'>";
			print "		<button class='btn btn-small' data-action='edit'   data-fieldname='$field[name]'><i class='icon-gray icon-pencil'></i></button>";
			print "		<button class='btn btn-small' data-action='delete' data-fieldname='$field[name]'><i class='icon-gray icon-remove'></i></button>";
			print "	</div>";

			# warning for older versions
			if((is_numeric(substr($field['name'], 0, 1))) || (!preg_match('!^[\w_ ]*$!', $field['name'])) ) { print '<span class="alert alert-warning"><strong>'._('Warning').'</strong>: '._('Invalid field name').'!</span>'; }

			print "</td>";

			print '</tr>'. "\n";
	
			$prevName = $field['name'];
			$m++;	
		}
	}
	?>
	<!-- add -->
	<tr>
		<td colspan="4" style='padding-right:0px;'>
			<button class='btn btn-small pull-right' data-action='add'  data-fieldname='<?php print $field['name']; ?>' rel='tooltip' data-placement='right' title='<?php print _('Add new custom VLAN field'); ?>'><i class='icon-gray icon-plus'></i>
		</td>
	</tr>
	<!-- result -->
	<tr>
		<td colspan="4" class="result">
			<div class="customVLANResult"></div>
		</td>
	</tr>
	</tbody>



	<!-- Custom user fields -->
	<tbody id="customUser">
	<tr>
		<th colspan="4">
			<h5><?php print _('Custom User fields'); ?></h5>
		</th>
	</tr>
	<?php
	/* no results */
	if(sizeof($myFieldsUser) == 0) {
		print '<tr>'. "\n";
		print '<td colspan="4"><div class="alert alert-info alert-nomargin">'._('No custom user fields created yet').'</div></td>'. "\n";
		print '</tr>'. "\n";
	}
	/* already available */
	else {
		# get size
		$size = sizeof($myFieldsUser);
		$m = 0;

		foreach($myFieldsUser as $field)
		{
			print '<tr>'. "\n";

			# ordering
			if (( ($m+1) != $size) ) 	{ print "<td><button class='btn btn-small down' data-direction='down' rel='tooltip' title='"._('Move down')."' data-fieldname='$myFieldsNumUser[$m]' data-nextfieldname='".$myFieldsNumUser[$m+1]."'><i class='icon-gray icon-chevron-down'></i></button></td>";	}
			else 						{ print '<td></td>'. "\n";}
	
			print '<td class="name">'. $field['name'] .'</td>'. "\n";

			#actions
			print "<td class='actions'>";
			print "	<div class='btn-group'>";
			print "		<button class='btn btn-small' data-action='edit'   data-fieldname='$field[name]'><i class='icon-gray icon-pencil'></i></button>";
			print "		<button class='btn btn-small' data-action='delete' data-fieldname='$field[name]'><i class='icon-gray icon-remove'></i></button>";
			print "	</div>";

			# warning for older versions
			if((is_numeric(substr($field['name'], 0, 1))) || (!preg_match('!^[\w_ ]*$!', $field['name'])) ) { print '<span class="alert alert-warning"><strong>'._('Warning').'</strong>: '._('Invalid field name').'!</span>'; }

			print "</td>";

			print '</tr>'. "\n";
	
			$prevName = $field['name'];
			$m++;	
		}
	}
	?>
	<!-- add -->
	<tr>
		<td colspan="4" style='padding-right:0px;'>
			<button class='btn btn-small pull-right' data-action='add'  data-fieldname='<?php print $field['name']; ?>' rel='tooltip' data-placement='right' title='<?php print _('Add new custom User field'); ?>'><i class='icon-gray icon-plus'></i>
		</td>
	</tr>
	<!-- result -->
	<tr>
		<td colspan="4" class="result">
			<div class="customUserResult"></div>
		</td>
	</tr>
	</tbody>


</table>

<hr>
<div class="alert alert-info"><?php print _('You can add additional custom fields to IP addresses and subnets (like CustomerId, location, ...)'); ?>.</div>
