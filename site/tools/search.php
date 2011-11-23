<?php

/* verify that user is authenticated! */
require_once('../../functions/functions.php');
isUserAuthenticated ();

/* get posted search term */
if($_POST['ip']) {
	$searchTerm = $_POST['ip'];
}
?>

<!-- search form -->
<form id="search" name="search">
	Enter search term (IP address / hostname / description / switch / port):<br>
	<input type="text" name="ip" value="<?php print $searchTerm; ?>" style="width:250px" class="search">
	<input type="submit" value="search">
</form>


<!-- result -->
<div class="searchResult">
<?php
/* include results if IP address is posted */
if ($searchTerm) {
	include('searchResults.php');
}
else {
	include('searchTips.php');
}
?>
</div>