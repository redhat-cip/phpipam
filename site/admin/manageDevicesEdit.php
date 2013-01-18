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

if ($_POST['action'] == "delete") 	{ $readonly = "readonly"; }
else 								{ $readonly = ""; }
?>


<!-- header -->
<div class="pHeader"><?php print ucwords($_POST['action']); ?> device</div>


<!-- content -->
<div class="pContent">

	<form id="switchManagementEdit">
	<table class="table table-striped table-condensed">

	<!-- hostname  -->
	<tr>
		<td>Hostname</td>
		<td>
			<input type="text" name="hostname" placeholder="Hostname" value="<?php if(isset($switch['hostname'])) print $switch['hostname']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- IP address -->
	<tr>
		<td>IP address</td>
		<td>
			<input type="text" name="ip_addr" placeholder="IP address" value="<?php if(isset($switch['ip_addr'])) print $switch['ip_addr']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- Type -->
	<tr>
		<td>Device type</td>
		<td>
			<select name="type">
			<?php
			$types = getSwitchTypes();
			foreach($types as $key=>$name) {
				if($switch['type'] == $key)	{ print "<option value='$key' selected='selected'>$name</option>"; }
				else						{ print "<option value='$key' >$name</option>"; }
			}
			?>
			</select>
		</td>
	</tr>

	<!-- Vendor -->
	<tr>
		<td>Vendor</td>
		<td>
			<input type="text" name="vendor" placeholder="Vendor" value="<?php if(isset($switch['vendor'])) print $switch['vendor']; ?>" <?php print $readonly; ?>>
		</td>
	</tr>

	<!-- Model -->
	<tr>
		<td>Model</td>
		<td>
			<input type="text" name="model" placeholder="Model" value="<?php if(isset($switch['model'])) print $switch['model']; ?>" <?php print $readonly; ?>>
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
			<?php
			if( ($_POST['action'] == "edit") || ($_POST['action'] == "delete") ) {
				print '<input type="hidden" name="switchId" value="'. $_POST['switchId'] .'">'. "\n";
			} ?>
			<input type="hidden" name="action" value="<?php print $_POST['action']; ?>">
		</td>
	</tr>

	<!-- Sections -->
	<tr>
		<td colspan="2">Sections to display device in:</td>
	</tr>
	<tr>
		<td></td>
		<td>
		<?php
		$sections = fetchSections();
		
		/* reformat switch sections */ 
		$switchSections = reformatSwitchSections($switch['sections']);
		
		foreach($sections as $section) {
			if(in_array($section['id'], $switchSections)) 	{ print '<input type="checkbox" name="section-'. $section['id'] .'" value="on" checked> '. $section['name'] .'<br>'. "\n"; }
			else 											{ print '<input type="checkbox" name="section-'. $section['id'] .'" value="on"> '. $section['name'] .'<br>'. "\n"; }
		}
		?>
		</td>
	</tr>

	</table>
	</form>
</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Cancel</button>
	<button class="btn btn-small" id="editSwitchsubmit"><i class="icon-gray icon-ok"></i> <?php print ucwords($_POST['action']); ?> Device</button>

	<!-- result -->
	<div class="switchManagementEditResult"></div>
</div>