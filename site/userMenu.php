<?php

/**
 *
 * Display usermenu on top right
 *
 */


/* get username */
$ipamusername = $_SESSION['ipamusername'];
$userDetails = getActiveUserDetails ();

?>

<div id="userNav">
		
	<!-- search -->
    <?php
    print "<div class='input-append'>";
    print "<form id='userMenuSearch' name='userMenuSearch' method='post' action='tools/search/'>";
    print "	<input class='span2 search' name='ip' placeholder='"._('Search string')."' id='appendedInputButton' size='16' type='text' value='$_REQUEST[ip]'><input class='btn' type='submit' value='"._('Search')."'>";
    print "</form>";
  	print "</div>";
    ?>

	<!-- settings -->
	<a href="tools/userMenu/"><?php print _('Hi'); ?>,   <?php print $userDetails['real_name'];  ?></a><br>
	<span class="info"><?php print _('Logged in as'); ?>  <?php print " "._("$userDetails[role]"); ?></span><br>
	
	<!-- logout -->
	<a  href="logout/"><?php print _('Logout'); ?>  <i class="icon-off icon-white"></i></a>
</div>