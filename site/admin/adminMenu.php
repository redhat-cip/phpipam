<?php
/*
 * Print Admin menu pn left if user is admin
 *************************************************/

/* verify that user is admin */
checkAdmin();

/* get all site settings */
$settings = getAllSettings();
?>

<table class="subnets admin normalTable">

    <!-- Server management -->
    <tr class="th">
        <th>Server management</th>
    </tr>
    
    <!-- Site settings -->
    <tr id="settings" class="settings">
        <td link="Administration|settings">Server management</td>
    </tr>

    <!-- modify / delete user -->
    <tr id="userMod" class="userMod">
        <td link="Administration|userMod">User management</td>
    </tr>

   <!-- Domain settings -->
    <?php
    /* show domain settings if enabled in config!  */
    if($settings['domainAuth'] == 1) {    
   		print '<tr id="manageAD" class="manageAD">'. "\n";
        print '<td link="Administration|manageAD">AD settings</td>'. "\n";
    	print '</tr>'. "\n";
    }
    ?>

    <!-- edit instructions -->
    <tr id="instructions" class="instructions">
        <td link="Administration|instructions">Edit instructions</td>
    </tr>
   
    <!-- log -->
    <tr id="log" class="log">
        <td link="Administration|log">Log Files</td>
    </tr>
    
    <!-- space holder -->    
    <tr class="th">
        <td>&nbsp;</td>
    </tr>


    <!-- IP address management -->
    <tr class="th">
        <th>IP address management</th>
    </tr>
    
    <!-- section management -->
    <tr id="manageSection" class="manageSection">
        <td link="Administration|manageSection">Section management</td>
    </tr>
    
    <!-- subnet management -->
    <tr id="manageSubnet" class="manageSubnet">
        <td link="Administration|manageSubnet">Subnet management</td>
    </tr>

    <!-- Switch management -->
    <tr id="manageSwitches" class="manageSwitches">
        <td link="Administration|manageSwitches">Switch management</td>
    </tr>

    <!-- VLAN management -->
    <tr id="manageVLANs" class="manageVLANs">
        <td link="Administration|manageVLANs">VLAN management</td>
    </tr>


   <!-- VRF management -->
    <?php
    /* show IP request link if enabled in config file!  */
    if($settings['enableVRF'] == 1) {    
   		print '<tr id="manageVRF" class="manageVRF">'. "\n";
        print '<td link="Administration|manageVRF">VRF management</td>'. "\n";
    	print '</tr>'. "\n";
    }
    ?>

    <!-- RIPE import -->
    <tr id="ripeImport" class="ripeImport">
        <td link="Administration|ripeImport">RIPE import</td>
    </tr>

    <!-- IP requests -->
    <?php
    /* show IP request link if enabled in config file!  */
    if($settings['enableIPrequests'] == 1) {    
    	print '<tr id="manageRequests" class="manageRequests">' . "\n";
        print '	<td link="Administration|manageRequests">IP requests ' . "\n";
        $requestNum = countRequestedIPaddresses();
        if($requestNum != 0) {
        	print '<span style="color:#AC001B;font-weight:bold">('. $requestNum . ')</span>' . "\n";
        }
        print '</td>' . "\n";
    	print '</tr>' . "\n";
    }
    ?>

    <!-- Filter IP list -->
    <tr id="filterIPFields" class="filterIPFields">
        <td link="Administration|filterIPFields">Filter IP fields</td>
    </tr>

    <!-- Custom IP fields -->
    <tr id="customIPFields" class="customIPFields">
        <td link="Administration|customIPFields">Custom IP fields</td>
    </tr>
    
    

    <!-- space holder -->    
    <tr class="th">
        <td>&nbsp;</td>
    </tr>

    <!-- other -->
    <tr class="th">
        <th>Other</th>
    </tr>

    <!-- version check-->
    <tr id="versionCheck" class="versionCheck">
        <td link="Administration|versionCheck">Version check</td>
    </tr>

   <!-- verify database-->
    <tr id="verifyDatabase" class="verifyDatabase">
        <td link="Administration|verifyDatabase">Verify database</td>
    </tr>

   <!-- replace fields-->
    <tr id="replaceFields" class="replaceFields">
        <td link="Administration|replaceFields">Replace fields</td>
    </tr>

    <!-- export database -->
    <tr id="export" class="export">
        <td link="Administration|export">Export database</td>
    </tr>
                
</table>