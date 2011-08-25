<?php

/**
 * Script to display devices by hostname
 *
 */

/* include required scripts */
require_once('../../functions/functions.php');

/* verify that user is authenticated! */
isUserAuthenticated ();
?>

<!-- autocomplete -->
<link type="text/css" href="css/ui-darkness/jquery-ui-1.8.14.custom.css" rel="stylesheet">	
<script type="text/javascript" src="js/jquery-ui-1.8.14.custom.min.js"></script>
<script>
$(function() {
	//get all swiches
	var hostnames = [
		<?php 
		$allSwitches = getUniqueHosts ();
		foreach ($allSwitches as $switch) {
			print '"'. $switch['dns_name'] .'", ';
		}
		?>
	];
		
	//autocomplete hostnames
	$( "#hostsFilter" ).autocomplete({ source: hostnames });
});
</script>



<?php

/* set title */
print '<h3>List of available hosts</h3>'. "\n";

/* search form */
print '<form name="hosts" id="hosts">'. "\n";
print 'Search filter: <input type="text" name="hostname" id="hostsFilter">'. "\n";
print ' <input type="submit" value="Filter">'. "\n";
print '</form>'. "\n";

/* result */
print '<div class="hostsFilterResult">'. "\n";
include_once('hostsResult.php');	
print '</div>'. "\n";
?>