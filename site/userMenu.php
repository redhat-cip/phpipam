<?php

/**
 *
 * Display usermenu on top right
 *
 */


/* get username */
/* session_start(); */
$ipamusername = $_SESSION['ipamusername'];
session_write_close();

?>
<ul class="topNav">
    
    <!-- userdata link -->
    <li link="tools|userMenu" id="userMenu"> <img src="css/images/user.png"> <?php print $ipamusername; ?>
        <ul class="subNav">
            <li>
                <a href="login">Logout</a>
            </li>
            <li link="tools|userMenu" id="userMenu">My account</li>
        </ul>
    </li>
    
    <?php
    if(!isUserViewer()) {
    	# search
    	print '<li id="search">'. "\n";
    	print '<form id="userMenuSearch" name="userMenuSearch">'. "\n";
        print '	<input type="text" class="search" size="12" name="ip" placeholder="Search">'. "\n";
        print '	<input type="submit" value="Search">'. "\n";
        print '</form>'. "\n";
    	print '</li>'. "\n";
    }
    ?>
</ul>