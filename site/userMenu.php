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
    
    <!-- search link -->
    <li id="search">
    	<form id="userMenuSearch" name="userMenuSearch">
        	<input type="text" class="search" size="12" name="ip" placeholder="Search">
        	<input type="submit" value="Search">
        </form>
    </li>

</ul>