<?php

/*
 * Print edit sections form
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* verify post */
CheckReferrer();

/**
 * Fetch section info
 */
$section = getSectionDetailsById ($_POST['sectionId']);

?>



<!-- header -->
<div class="pHeader"><?php print ucwords($_POST['action']); ?> Section</div>


<!-- content -->
<div class="pContent">

	<!-- form -->
	<form id="sectionEdit" name="sectionEdit">

		<!-- edit table -->
		<table class="table table-condensed table-noborder">
	
		<!-- section name -->
		<tr>
			<td>Name</td>
			<td colspan="2">
				<input type="text" class='input-xlarge' name="name" value="<?php print $section['name']; ?>" size="30" <?php if ($_POST['action'] == "delete" ) { print ' readonly '; } ?> placeholder="Section name">
				<!-- hidden -->
				<input type="hidden" name="action" 	value="<?php print $_POST['action']; ?>">
				<input type="hidden" name="id" 		value="<?php print $_POST['sectionId']; ?>">
			</td>
		</tr>

		<!-- description -->
		<tr>
			<td>Description</td>
			<td colspan="2">
				<input type="text" class='input-xlarge' name="description" value="<?php print $section['description']; ?>" size="30" <?php if ($_POST['action'] == "delete") {print " readonly ";}?> placeholder="Section description">
			</td>
		</tr>

		<tr>
			<td colspan="3">
				<hr>
			</td>
		</tr>		
		<!-- permissions -->
		<?php
		if(strlen($section['permissions'])>1) {
			$permissions = parseSectionPermissions($section['permissions']);
		}
		else {
			$permissions = "";
		}
		# print for each group
		$groups = getAllGroups();
		$m = 0;
		
		foreach($groups as $g) {
			# structure
			print "<tr>";
			# title
			if($m == 0) { print "<td>Permissions</td>"; }
			else		{ print "<td></td>"; }			
			
			# name
			print "<td>$g[g_name]</td>";
				
			# line
			print "<td>";			
			print "<label class='checkbox inline noborder'>";			

			print "	<input type='radio' name='group$g[g_id]' value='0' checked> na";
			if($permissions[$g['g_id']] == "1")	{ print " <input type='radio' name='group$g[g_id]' value='1' checked> ro"; }			
			else								{ print " <input type='radio' name='group$g[g_id]' value='1'> ro"; }	
			if($permissions[$g['g_id']] == "2")	{ print " <input type='radio' name='group$g[g_id]' value='2' checked> rw"; }			
			else								{ print " <input type='radio' name='group$g[g_id]' value='2'> rw"; }			
			print "</label>";
			print "</td>";
			
			print "</tr>";			
			
			$m++;
		}
		?>
		
		<?php 
		if($_POST['action'] == "edit") { ?>
		<!-- Apply to subnets -->
		<tr>
			<td colspan="3">
				<hr>
			</td>
		</tr>
		<tr>
			<td>Delegate</td>
			<td colspan="2">
				<input type="checkbox" name="delegate" value="1" style="margin-top:0px;"><span class="help help-inline">Check to delegate permissions to all subnets in section</span>
			</td>
		</tr>
		<?php } ?>
		
		</table>	<!-- end table -->
	</form>		<!-- end form -->
	
	<!-- delete warning -->
	<?php
	if ($_POST['action'] == "delete") {
		print '<div class="alert alert-warn"><b>Warning!</b><br>Deleting Section will delete all belonging subnets and IP addresses!</div>' . "\n";
	}
	?>
</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Cancel</button>
	<button class="btn btn-small" id="editSectionSubmit"><i class="icon-gray icon-ok"></i> <?php print ucwords($_POST['action']); ?> section</button>

	<!-- result holder -->
	<div class="sectionEditResult"></div>
</div>	
		