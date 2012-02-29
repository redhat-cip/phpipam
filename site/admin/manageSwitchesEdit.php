<?php

/**
 *	Edit switch details
 ************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* get switch detaild by Id! */
if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) {
	$switch = getSwitchDetailsById($_POST['switchId']);
}
?>

<div class="normalTable switchManagementEdit2">
<form id="switchManagementEdit">
<table class="normalTable switchManagementEdit2">

<tr class="th">
	<th colspan="2"><?php print $_POST['action']; ?> switch</th>
</tr>

<?php
if ($_POST['action'] == "delete") 	{ $readonly = "readonly"; }
else 								{ $readonly = ""; }
?>

<!-- hostname  -->
<tr>
	<td>Hostname</td>
	<td>
		<input type="text" name="hostname" placeholder="hostname" value="<?php if(isset($switch['hostname'])) print $switch['hostname']; ?>" <?php print $readonly; ?>>
	</td>
</tr>

<!-- IP address -->
<tr>
	<td>IP address</td>
	<td>
		<input type="text" name="ip_addr" placeholder="IP address" value="<?php if(isset($switch['ip_addr'])) print $switch['ip_addr']; ?>" <?php print $readonly; ?>>
	</td>
</tr>

<!-- Vendor -->
<tr>
	<td>Vendor</td>
	<td>
		<input type="text" name="vendor" placeholder="Switch vendor" value="<?php if(isset($switch['vendor'])) print $switch['vendor']; ?>" <?php print $readonly; ?>>
	</td>
</tr>

<!-- Model -->
<tr>
	<td>Model</td>
	<td>
		<input type="text" name="model" placeholder="Switch model" value="<?php if(isset($switch['model'])) print $switch['model']; ?>" <?php print $readonly; ?>>
	</td>
</tr>

<!-- Version -->
<tr>
	<td>SW version</td>
	<td>
		<input type="text" name="version" placeholder="Software version" value="<?php if(isset($switch['version'])) print $switch['version']; ?>" <?php print $readonly; ?>>
	</td>
</tr>

<!-- Description -->
<tr>
	<td>Description</td>
	<td>
		<textarea name="description" placeholder="Description" <?php print $readonly; ?>><?php if(isset($switch['description'])) print $switch['description']; ?></textarea>

	</td>
</tr>

<!-- Sections -->
<tr>
	<td colspan="2">Sections to display switch in:</td>
</tr>
<tr>
	<td></td>
	<td>
		<?php
		$sections = fetchSections();
		
		/* reformat switch sections */ 
		$switchSections = reformatSwitchSections($switch['sections']);
		
		foreach($sections as $section) {
		
			if(in_array($section['id'], $switchSections)) {
				print '<input type="checkbox" name="section-'. $section['id'] .'" value="on" checked>'. $section['name'] .'<br>'. "\n";			
			}
			else {
				print '<input type="checkbox" name="section-'. $section['id'] .'" value="on">'. $section['name'] .'<br>'. "\n";
			}
		}
		?>
	</td>
</tr>

<!-- submit -->
<tr>
	<td></td>
	<td>
		<?php
		if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) {
			print '<input type="hidden" name="switchId" value="'. $_POST['switchId'] .'">'. "\n";
		}
		?>
		<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
		<input type="submit" value="<?php print $_POST['action']; ?> switch">
	</td>
</tr>

<!-- result -->
<tr>
	<td colspan="2">
		<div class="switchManagementEditResult"></div>
	</td>
</tr>

</table>
</form>
</div>