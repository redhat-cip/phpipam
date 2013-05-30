<?php

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get posted search term */
if($_REQUEST['ip']) { $searchTerm = $_REQUEST['ip']; }
else				{ $searchTerm = ""; }
?>

<h4><?php print _('Search IP database');?></h4>
<hr>

<!-- search form -->
<form id="search" name="search">
	<div class="input-append">
		<input class="span2 search" id="appendedInputButton" name="ip" value="<?php print $searchTerm; ?>" size="16" type="text"><input type="submit" class="btn" value="<?php print _('search');?>">
	</div>
</form>

<!-- result -->
<div class="searchResult">
<?php
/* include results if IP address is posted */
if ($searchTerm) 	{ include('searchResults.php'); }
else 				{ include('searchTips.php');}
?>
</div>