<?php

/*
 * Print edit subnet
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
if (!checkAdmin()) die('');

/* verify post */
CheckReferrer();


/* postdata */
$subnetData = $_POST;

/* if action != Add fetch existing data for subnet! */
if ($subnetData['subnetAction'] != "Add") {
    $subnetDataOld = getSubnetDetailsById ($subnetData['subnetId']);
}

/* if request came from subnets table se different name and ID! (Or ipcalc) */
if (($subnetData['location'] == "subnets") || ($subnetData['location'] == "ipcalc") ) {
    $className = "manageSubnetEditFromSubnets";
    $sectionName = getSectionDetailsById ($subnetData['sectionId']);
    Print '<h3>Add new subnet to '. $sectionName['name'] .'</h3>';
}
else {
    $className = "manageSubnetEdit";
}
?>

<div class="<?php print $className; ?> normalTable">
<form name="<?php print $className; ?>" id="<?php print $className; ?>">
<table class="normalTable <?php print $className; ?>">

    <!-- name -->
    <tr>
        <td>Subnet</td>
        <td>
            <input type="text" name="subnet"      value="<?php 
            	if ($subnetData['subnetAction'] != "Add") {
            		print transform2long($subnetDataOld['subnet']) .'/'. $subnetDataOld['mask'];
            	} 
            	if ($subnetData['location'] == "ipcalc") {
            		print $subnetData['subnet'] .'/'. $subnetData['bitmask'];
            	} 
            ?>" 
            <?php if ($subnetData['subnetAction']=="Edit") print "readonly"; ?>>
        </td>
        <td class="info">Enter subnet in CIDR format (e.g. 192.168.1.1/24)</td>
    </tr>

    <!-- description -->
    <tr>
        <td>Description</td>
        <td>
            <input type="text" name="description" value="<?php if(isset($subnetDataOld['description'])) {print $subnetDataOld['description'];} ?>">
        </td>
        <td class="info">Enter subnet description</td>
    </tr>  
    
    <!-- vlan -->
    <tr>
        <td>VLAN</td>
        <td>
            <input type="text" name="VLAN"        value="<?php if(isset($subnetDataOld['VLAN'])) {print $subnetDataOld['VLAN'];} ?>">
        </td>
        <td class="info">Enter subnet VLAN number</td>
    </tr>

    <!-- Master subnet -->
    <tr>
        <td>Master Subnet</td>
        <td>
            <select name="masterSubnetId">
            	<option value="0">root</option>
            	<?php
            	/* print all master subnets */
/*             	$subnets = fetchAllSubnets(); */
            	$subnets = fetchSubnets($subnetData['sectionId']);
            	foreach($subnets as $subnet) {
            		//only root subnets
            		if(empty($subnet['masterSubnetId']) || $subnet['masterSubnetId'] == 0) {
            			//for edit - if selected
            			if (($subnet['id'] == $subnetDataOld['masterSubnetId']) && ($subnetDataOld['masterSubnetId'] != 0 )) {
            				print '<option value="'. $subnet['id'] .'" selected>'. Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .'</option>';
            			}
            			else {
            				print '<option value="'. $subnet['id'] .'">'. Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .'</option>';
            			}
            		}
            	}
            	?>
            </select>
        </td>
        <td class="info">Enter master subnet if you want to nest it under existing subnet,<br>or select root to create root subnet!</td>
    </tr>

    <?php
    /* get all site settings */
	$settings = getAllSettings();
	$VRFs 	  = getAllVRFs();
	
	/* set default value */
	if(empty($subnetDataOld['vrfId'])) {
		$subnetDataOld['vrfId'] = "0";
	}
	/* set default value */
	if(empty($subnetDataOld['allowRequests'])) {
		$subnetDataOld['allowRequests'] = "1";
	}

	/* if vlan support is enabled print available vlans */	
	if($settings['enableVRF'] == 1) {
	
		print '<tr>' . "\n";
        print '	<td>VRF</td>' . "\n";
        print '	<td>' . "\n";
        print '	<select name="vrfId">'. "\n";
        
        //blank
        print '<option value="0">None</option>';
        
        foreach($VRFs as $vrf) {
        
        	if ($vrf['vrfId'] == $subnetDataOld['vrfId']) {
        		print '<option value="'. $vrf['vrfId'] .'" selected>'. $vrf['name'] .'</option>';
        	}
        	else {
        	    print '<option value="'. $vrf['vrfId'] .'">'. $vrf['name'] .'</option>';
        	}
        }
        
        print ' </select>'. "\n";
        print '	</td>' . "\n";
        print '	<td class="info">Add this subnet to VRF</td>' . "\n";
    	print '</tr>' . "\n";
	
	}
	else {
		print '<tr style="display:none"><td colspan="8"><input type="hidden" name="vrfId" value="'. $subnetDataOld['vrfId'] .'"></td></tr>'. "\n";
	}
	
	
	
	/* allow / deny IP requests if enabled in settings */	
	if($settings['enableIPrequests'] == 1) {
	
		print '<tr>' . "\n";
        print '	<td>IP Requests</td>' . "\n";
        print '	<td>' . "\n";
        print '		<input type="checkbox" name="allowRequests" value="1" ' . "\n";
        
        if( isset($subnetDataOld['allowRequests']) && ($subnetDataOld['allowRequests'] == 1) ) {
        	print 'checked';
        }
        
        print ' >'. "\n";
        print '	</td>' . "\n";
        print '	<td class="info">Allow or deny IP requests for this subnet</td>' . "\n";
    	print '</tr>' . "\n";
	
	}
	else {
		print '<tr style="display:none"><td colspan="8"><input type="hidden" name="allowRequests" value="'. $subnetDataOld['allowRequests'] .'"></td></tr>'. "\n";
	}	
	
	/* option to loch subnet writing only for admins */
		print '<tr>' . "\n";
        print '	<td>Admin lock</td>' . "\n";
        print '	<td>' . "\n";
        print '		<input type="checkbox" name="adminLock" value="1" ' . "\n";
        
        if( isset($subnetDataOld['adminLock']) && ($subnetDataOld['adminLock'] == 1)) {
        	print 'checked';
        }
        
        print ' >'. "\n";
        print '	</td>' . "\n";
        print '	<td class="info">Limit IP editing only to admins!</td>' . "\n";
    	print '</tr>' . "\n";
	    
    ?>

    <!-- submit -->
    <tr class="th">
        <td></td>
        <td>
            <!-- hidden values -->
            <input type="hidden" name="sectionId"       value="<?php print $subnetData['sectionId'];    ?>">
            <input type="hidden" name="subnetId"        value="<?php print $subnetData['subnetId'];     ?>">       
            <input type="hidden" name="subnetAction"    value="<?php print $subnetData['subnetAction']; ?>">
            <!-- if edited form ipaddresses -->
            <input type="hidden" name="location"    	value="<?php print $subnetData['location']; ?>">           
            <input type="submit"                        value="<?php print $subnetData['subnetAction']; ?>">

            <input type="button"                        value="Cancel" class="cancel">
        </td>
        <td></td>
    </tr>
    
    <!-- submit result -->
    <tr class="th">
        <td colspan="3">
            <div class="manageSubnetEditResult"></div>
        </td>
    </tr>
 
</table>
</form> 
</div>