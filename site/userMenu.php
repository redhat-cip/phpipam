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

    <!-- ipCalc link -->
    <li link="tools|ipCalc" id="ipCalc">IP calculator</li>

    <!-- Switches link -->
    <li link="tools|switches" id="switches">Switches</li>

    <!-- hosts link -->
    <li link="tools|hosts" id="hosts">Hosts</li>
    
    <!-- Vlan table link -->
    <li link="tools|vlan" id="vlan">Vlan Table</li>
    
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
        	<input type="text" value="search" class="search" size="12" name="ip">
        	<input type="submit" value="Search">
        </form>
    </li>

</ul>