<?php

/**
 *	Print all available VLANs and configurations
 ************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

?>



           <select name="vlanId">
            	<option disabled="disabled">Select VLAN:</option>
            <?php
           		$vlans = getAllVLANs();
           		
           		if($subnetData['subnetAction'] == "Add") {
           			
           			$vlan['vlanId'] = 0;
           		}

           		$tmp[0]['vlanId'] = 0;
           		$tmp[0]['number'] = 'No VLAN';
           		
           		#on-the-fly
	          	$tmp[1]['vlanId'] = 'Add';	
	           	$tmp[1]['number'] = '+ Add new VLAN';	
           		
           		array_unshift($vlans, $tmp[0]);
           		array_unshift($vlans, $tmp[1]);
            
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
