<?php

/**
 * Script to display devices by hostname
 *
 */

/* verify that user is authenticated! */
isUserAuthenticated ();


/* die if viewer */
if(isUserViewer()) { die('<div class="alert alert-error">You do not have permissions to access this page!</div>');}
?>

<!-- autocomplete -->
<script>
$(function() {
	//get all hostnames
	var hostnames = [
		<?php 
		$allSwitches = getUniqueHosts ();
		foreach ($allSwitches as $switch) {
			print '"'. $switch['dns_name'] .'", ';
		}
		?>
	];
		
	//autocomplete hostnames
	$( "#hostsFilter" ).autocomplete({ source: hostnames, minLength: 0 }).focus(function(){
	if (this.value == "")
		$(this).trigger('keydown.autocomplete');
	});
});
</script>



<!-- set title -->
<h4>List of available hosts</h4>
<hr><br>

<!-- search form -->
<form id="hosts" name="hosts" action="/">
	<div class="input-append">
		<input class="span2 hostsFilter" id="appendedInputButton" name="hostsFilter" value="<?php print $_REQUEST['hostname']; ?>" size="16" type="text" placeholder="Search filter"><input type="submit" class="btn" value="Filter">
	</div>
</form>

<!-- result -->
<div class="hostsFilterResult">
<?php include_once('hostsResult.php'); ?>
</div>