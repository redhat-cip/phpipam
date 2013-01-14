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
    print "	<input class='span2 search' name='ip' placeholder='Search string' id='appendedInputButton' size='16' type='text' value='$_REQUEST[ip]'><input class='btn' type='submit' value='Search'>";
    print "</form>";
  	print "</div>";
    ?>

	<!-- settings -->
	<a href="tools/userMenu/">Hi, <?php print $userDetails['real_name']; ?></a><br>
	<span class="info">Logged in as  <?php print $userDetails['role']; ?></span><br>
	
	<!-- logout -->
	<a  href="logout/">Logout <i class="icon-off icon-white"></i></a>
</div>