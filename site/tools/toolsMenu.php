<?php

/**
 * Script to display switches
 *
 */

/* include required scripts */
require_once('../../functions/functions.php');

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all unique switches */
$settings = getAllSettings();

/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);
?>

<table class="menu normalTable">

    <!-- title -->
    <tr class="th">
        <th>Tools</th>
    </tr>
    
    <!-- IP calculator -->
    <tr id="ipCalc" class="ipCalc">
        <td>
            <a href="#tools|ipCalc" id="ipCalc">IP calculator</a>
        </td>
    </tr>

    <!-- Informations -->
    <tr id="info" class="info">
        <td>
            <a href="#tools|instructions" id="instructions">Instructions</a>
        </td>
    </tr>

    <!-- search -->
    <?php
    # for non-viewers only
    if(!isUserViewer()) {
		print '<tr id="search" class="search">'. "\n";
		print '	<td>'. "\n";
		print '		<a href="#tools|search" id="search">Search</a>'. "\n";
		print ' </td>'. "\n";
		print '</tr>'. "\n";
    }
    else {
    	print '</table>'. "\n";
    	die();
    }
    ?>
    
    <!-- space holder -->    
    <tr class="th">
        <td>&nbsp;</td>
    </tr>
    
    <!-- space holder -->    
    <tr class="th">
        <th>Device list</th>
    </tr>

    <!-- switches -->
    <?php
    if(in_array("switch", $setFields)) {
    	print '<tr id="switches" class="switches">'. "\n";
    	print '	<td>'. "\n";
    	print '		<a href="#tools|switches" id="switches">Switches</a>'. "\n";
    	print '	</td>'. "\n";
    	print '</tr>'. "\n";
    }
    
    ?>

    <!-- VRF -->
    <?php 
    if($settings['enableVRF'] == 1) {
    	print '<tr id="vrf" class="vrf">'. "\n";
        print '	<td>'. "\n";
        print '		<a href="#tools|vrf" id="vrf">VRF</a>'. "\n";
        print '	</td>'. "\n";
    	print '</tr>'. "\n";
    }
    
    ?>

    <!-- hosts -->
    <tr id="hosts" class="hosts">
        <td>
            <a href="#tools|hosts" id="hosts">Hosts</a>
        </td>
    </tr>

    
    <!-- VLAN data -->
    <tr id="vlan" class="vlan">
        <td>
            <a href="#tools|vlan" id="vlan">VLANs</a>
        </td>
    </tr>


    <!-- subnets -->
    <tr id="subnets" class="subnets">
        <td>
            <a href="#tools|subnets" id="subnets">Subnets</a>
        </td>
    </tr>

    <!-- space holder -->    
    <tr class="th">
        <td>&nbsp;</td>
    </tr>

    <!-- space holder -->    
    <tr class="th">
        <th>Edit my account</th>
    </tr>

    <!-- userMenu -->
    <tr id="userMenu" class="userMenu">
        <td>
            <a href="#tools|userMenu" id="userMenu">User menu</a>
        </td>
    </tr>
                    
</table>