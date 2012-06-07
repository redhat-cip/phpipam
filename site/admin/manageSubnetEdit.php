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
/* from IPaddresses - cen de also deleted */
if ($subnetData['location'] == "IPaddresses") {
	$delete = true;
}
/* if request came from subnets table set different name and ID! (Or ipcalc) */
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

	<!-- title -->
	<?php
	print '<tr class="th">'. "\n";
	print '	<th colspan="3" style="text-align:left; padding:5px;">'. $subnetData['subnetAction'] .' subnet</th>'. "\n";
	print '</tr>'. "\n";
	?>

    <!-- name -->
    <tr>
        <td>Subnet</td>
        <td>
            <input type="text" name="subnet"   placeholder="subnet in CIDR"   value="<?php 
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
            <input type="text" name="description"  placeholder="subnet description" value="<?php if(isset($subnetDataOld['description'])) {print $subnetDataOld['description'];} ?>">
        </td>
        <td class="info">Enter subnet description</td>
    </tr>  
    
    <!-- vlan -->
    <tr>
        <td>VLAN</td>
        <td> 
            <select name="vlanId">
            	<option disabled="disabled">Select VLAN:</option>
            <?php
           		$vlans = getAllVLANs();
           		
           		if($subnetData['subnetAction'] == "Add") {
           			
           			$vlan['vlanId'] = 0;
           		}

           		$tmp[0]['vlanId'] = 0;
           		$tmp[0]['number'] = 'No VLAN';
           		
           		array_unshift($vlans, $tmp[0]);
            
            	foreach($vlans as $vlan) {
            		/* set structure */
            		$printVLAN = $vlan['number'];
            		if(!empty($vlan['name'])) {
            		$printVLAN .= ' - '. $vlan['name'];
            		}
            		
            		/* selected? */
            		if($subnetDataOld['vlanId'] == $vlan['vlanId']) {
            			print '<option value="'. $vlan['vlanId'] .'" selected>'. $printVLAN .'</option>'. "\n";
            		}
            		else {
	            		print '<option value="'. $vlan['vlanId'] .'">'. $printVLAN .'</option>'. "\n";
            		}
            	}
            ?>
            </select>
        </td>
        <td class="info">Select VLAN</td>
    </tr>

    <!-- Master subnet -->
    <tr>
        <td>Master Subnet</td>
        <td>
            <select name="masterSubnetId">
            	<option value="" disabled="disabled">Root subnets:</option>
            	<option value="0" selected="selected">root</option>
            	<?php
            	/* fetch all subnets in section */
            	$subnets = fetchSubnets($subnetData['sectionId']);

            	/* print all master subnets */
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
            			//store Id of L1 for non-root subnets below
            			$nonRoot[] = $subnet['id'];
            		}
            	}

            	/* print all non-root subnets nested under L1 (under root) */
            	print '<option value="" disabled="disabled">Non-root subnets:</option>'. "\n";
            	foreach($subnets as $subnet) {
            		//only non-root L1 subnets
            		if((!empty($subnet['masterSubnetId'])) || ($subnet['masterSubnetId'] != 0)) {
            			//must be under L1!
            			if(in_array($subnet['masterSubnetId'], $nonRoot))
            			{
            				//for edit - if selected
            				if (($subnet['id'] == $subnetDataOld['masterSubnetId']) && ($subnetDataOld['masterSubnetId'] != 0 )) {
            					print '<option value="'. $subnet['id'] .'" selected>'. Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .'</option>';
            				}
            				else {
            					print '<option value="'. $subnet['id'] .'">'. Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .'</option>';
            				}
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


	/* show names instead of ip address! */
		print '<tr>' . "\n";
        print '	<td>Show as name</td>' . "\n";
        print '	<td>' . "\n";
        print '		<input type="checkbox" name="showName" value="1" ' . "\n";
        
        if( isset($subnetDataOld['showName']) && ($subnetDataOld['showName'] == 1)) {
        	print 'checked';
        }
        
        print ' >'. "\n";
        print '	</td>' . "\n";
        print '	<td class="info">Show Subnet name instead of subnet IP address;!</td>' . "\n";
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
            <!-- submit, cancel, delete -->       
            <input type="submit"                        value="<?php print $subnetData['subnetAction']; ?>">
            <input type="button"                        value="Cancel" class="cancel">
            <?php
            if( ($delete) && ($subnetData['subnetAction'] == "Edit") ) {
            	print '<br><input type="button" value="Delete subnet" class="subnetDeleteFromIP">';
            }
            ?>
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