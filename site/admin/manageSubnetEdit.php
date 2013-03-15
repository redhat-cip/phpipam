<?php

/*
 * Print edit subnet
 *********************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user has permissions if add */
if($_POST['action'] == "add") {
	$sectionPerm = checkSectionPermission ($_POST['sectionId']);
	if($sectionPerm != "2") {
		die("<div class='alert alert-error'>You do not have permissions to add new subnet in this section!</div>");
	}
}
/* otherwise check subnet permission */
else {
	$subnetPerm = checkSubnetPermission ($_POST['subnetId']);
	if($subnetPerm != "2") {
		die("<div class='alert alert-error'>You do not have permissions to add edit/delete this subnet!</div>");
	}
}

/* verify post */
CheckReferrer();


/*
	This can be called from subnetManagement, subnet edit in IP details page and from IPCalc!
	
	From IP address list we must also provide delete button!
	
	From search we directly provide 
		subnet / mask
	
*/

# we are editing or deleting existing subnet, get old details
if ($_POST['action'] != "add") {
    $subnetDataOld = getSubnetDetailsById ($_POST['subnetId']);
}
# we are adding new subnet - get section details
else {
	# for selecting master subnet if added from subnet details!
	if(strlen($_REQUEST['subnetId']) > 0) {
    	$tempData = getSubnetDetailsById ($_POST['subnetId']);	
    	$subnetDataOld['masterSubnetId'] = $tempData['id'];
	}
	$sectionName = getSectionDetailsById ($_POST['sectionId']);
}

/* get custom subnet fields */
$customSubnetFields = getCustomSubnetFields();


# set readonly flag
if($_POST['action'] == "edit" || $_POST['action'] == "delete")	{ $readonly = true; }
else															{ $readonly = false; }
?>



<!-- header -->
<div class="pHeader"><?php print ucwords($_POST['action']); ?> subnet</div>


<!-- content -->
<div class="pContent">

	<form id="editSubnetDetails">
	<table class="editSubnetDetails table table-noborder table-condensed">

    <!-- name -->
    <tr>
        <td class="middle">Subnet</td>
        <td>
        	<?php
        	# set CIDR
        	if ($_POST['location'] == "ipcalc") { $cidr = $_POST['subnet'] .'/'. $_POST['bitmask']; }  
            if ($_POST['action'] != "add") 		{ $cidr = transform2long($subnetDataOld['subnet']) .'/'. $subnetDataOld['mask']; }       	
        	?>
            <input type="text" name="subnet"   placeholder="subnet in CIDR"   value="<?php print $cidr; ?>" <?php if ($readonly) print "readonly"; ?>>
        </td>
        <td class="info">
        	<button class="btn btn-small"  id='get-ripe' rel='tooltip' data-placement="bottom" title='Get information from RIPE database'><i class="icon-refresh icon-gray"></i></button>
        	Enter subnet in CIDR format (e.g. 192.168.1.1/24)
        </td>
    </tr>

    <!-- description -->
    <tr>
        <td class="middle">Description</td>
        <td>
            <input type="text" id="field-description" name="description"  placeholder="subnet description" value="<?php if(isset($subnetDataOld['description'])) {print $subnetDataOld['description'];} ?>">
        </td>
        <td class="info">Enter subnet description</td>
    </tr>  

    <?php if($_POST['action'] != "add") { ?>
    <!-- section -->
    <tr>
        <td class="middle">Section</td>
        <td>
        	<select name="sectionIdNew">
            	<?php
           		$sections = fetchSections();
            
            	foreach($sections as $section) {
            		/* selected? */
            		if($_POST['sectionId'] == $section['id']) { print '<option value="'. $section['id'] .'" selected>'. $section['name'] .'</option>'. "\n"; }
            		else 									  { print '<option value="'. $section['id'] .'">'. $section['name'] .'</option>'. "\n"; }
            	}
            ?>
            </select>
        	
        	</select>
        </td>
        <td class="info">Move to different section</td>
    </tr>  
    <?php } ?>
    
    <!-- vlan -->
    <tr>
        <td class="middle">VLAN</td>
        <td id="vlanDropdown"> 
            <select name="vlanId">
            	<option disabled="disabled">Select VLAN:</option>
            	<?php
           		$vlans = getAllVLANs();
           		
           		if($_POST['action'] == "Add") { $vlan['vlanId'] = 0; }

           		$tmp[0]['vlanId'] = 0;
           		$tmp[0]['number'] = 'No VLAN';
           		
           		# on-the-fly
	          	$tmp[1]['vlanId'] = 'Add';	
	           	$tmp[1]['number'] = '+ Add new VLAN';	
           		
           		array_unshift($vlans, $tmp[0]);
           		array_unshift($vlans, $tmp[1]);
            
            	foreach($vlans as $vlan) {
            		/* set structure */
            		$printVLAN = $vlan['number'];
            		
            		if(!empty($vlan['name'])) { $printVLAN .= " ($vlan[name])"; }
            		
            		/* selected? */
            		if($subnetDataOld['vlanId'] == $vlan['vlanId']) { print '<option value="'. $vlan['vlanId'] .'" selected>'. $printVLAN .'</option>'. "\n"; }
            		else 											{ print '<option value="'. $vlan['vlanId'] .'">'. $printVLAN .'</option>'. "\n"; }
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
        	<?php printDropdownMenuBySection($_POST['sectionId'], $subnetDataOld['masterSubnetId']); ?>
        </td>
        <td class="info">Enter master subnet if you want to nest it under existing subnet, or select root to create root subnet!</td>
    </tr>

    <?php
    /* get all site settings */
	$settings = getAllSettings();
	$VRFs 	  = getAllVRFs();
	
	/* set default value */
	if(empty($subnetDataOld['vrfId'])) 			{ $subnetDataOld['vrfId'] = "0"; }
	/* set default value */
	if(empty($subnetDataOld['allowRequests'])) 	{ $subnetDataOld['allowRequests'] = "0"; }

	/* if vlan support is enabled print available vlans */	
	if($settings['enableVRF'] == 1) {
	
		print '<tr>' . "\n";
        print '	<td class="middle">VRF</td>' . "\n";
        print '	<td>' . "\n";
        print '	<select name="vrfId">'. "\n";
        
        //blank
        print '<option disabled="disabled">Select VRF</option>';
        print '<option value="0">None</option>';
        
        if(sizeof($VRFs) > 0) {
        foreach($VRFs as $vrf) {
        
        	if ($vrf['vrfId'] == $subnetDataOld['vrfId']) 	{ print '<option value="'. $vrf['vrfId'] .'" selected>'. $vrf['name'] .'</option>'; }
        	else 											{ print '<option value="'. $vrf['vrfId'] .'">'. $vrf['name'] .'</option>'; }
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

	?>
	<?php if($_POST['action'] == "edit") { ?>
	<!-- resize / split -->
	<tr>
        <td class="middle">Resize / split</td>
        <td>
        <div class="btn-toolbar" style="margin:0px;">
	    <div class="btn-group">
        	<button class="btn btn-small" id="resize" rel="tooltip" title="Resize subnet" data-subnetId="<?php print $_POST['subnetId']; ?>"><i class="icon-gray icon-resize-vertical"></i></button>
        	<?php
        	# check if it has slaves - if yes it cannot be splitted!
        	$slaves = subnetContainsSlaves($_POST['subnetId']);
        	?>
        	<button class="btn btn-small <?php if($slaves) print "disabled"; ?>" id="split"    rel="tooltip" title="Split subnet"    data-subnetId="<?php print $_POST['subnetId']; ?>"><i class="icon-gray icon-resize-full"></i></button>
        	<button class="btn btn-small <?php if($slaves) print "disabled"; ?>" id="truncate" rel="tooltip" title="Truncate subnet" data-subnetId="<?php print $_POST['subnetId']; ?>"><i class="icon-gray icon-trash"></i></button>
	    </div>
        </div>
        </td>
        <td class="info">Resize, split or truncate this subnet</td>
    </tr>
    <?php } ?>
	
	<?php
	/* allow / deny IP requests if enabled in settings */	
	if($settings['enableIPrequests'] == 1) {
	
		if( isset($subnetDataOld['allowRequests']) && ($subnetDataOld['allowRequests'] == 1) )	{ $checked = "checked"; }
		else																					{ $checked = ""; }
	
		print '<tr>' . "\n";
        print '	<td>IP Requests</td>' . "\n";
        print '	<td>' . "\n";
        print '		<input type="checkbox" name="allowRequests" value="1" '.$checked.'>'. "\n";
        print '	</td>' . "\n";
        print '	<td class="info">Allow or deny IP requests for this subnet</td>' . "\n";
    	print '</tr>' . "\n";
	
	}
	else {
		print '<tr style="display:none"><td colspan="8"><input type="hidden" name="allowRequests" value="'. $subnetDataOld['allowRequests'] .'"></td></tr>'. "\n";
	}	

	/* show names instead of ip address! */
		print '<tr>' . "\n";
        print '	<td>Show as name</td>' . "\n";
        print '	<td>' . "\n";
        print '		<input type="checkbox" name="showName" value="1" ' . "\n";
        
        if( isset($subnetDataOld['showName']) && ($subnetDataOld['showName'] == 1)) {
        	print 'checked';
        }
        
        print ' >'. "\n";
        
        # hidden ones
        ?>
            <!-- hidden values -->
            <input type="hidden" name="sectionId"       value="<?php print $_POST['sectionId'];    ?>">
            <input type="hidden" name="subnetId"        value="<?php print $_POST['subnetId'];     ?>">       
            <input type="hidden" name="action"    		value="<?php print $_POST['action']; ?>">
            <input type="hidden" name="location"    	value="<?php print $_POST['location']; ?>">        
        <?php
        print '	</td>' . "\n";
        print '	<td class="info">Show Subnet name instead of subnet IP address</td>' . "\n";
    	print '</tr>' . "\n";	    


    	# custom Subnet fields
	    if(sizeof($customSubnetFields) > 0) {
	    	print "<tr>";
	    	print "	<td colspan='3' class='hr'><hr></td>";
	    	print "</tr>";
		    foreach($customSubnetFields as $field) {
		    	# replace spaces
		    	$field['nameNew'] = str_replace(" ", "___", $field['name']);
		    	# retain newlines
		    	$subnetDataOld[$field['name']] = str_replace("\n", "\\n", $subnetDataOld[$field['name']]);
		    	
			    print "<tr>";
			    print "	<td class='middle'>$field[name]</td>";
			    print "	<td colspan='2'>";
			    print "	<input type='text' class='input-xxlarge' id='field-$field[nameNew]' name='$field[nameNew]' value='".$subnetDataOld[$field['name']]."' placeholder='".$subnetDataOld[$field['name']]."'>";
			    print " </td>";
			    print "</tr>";
		    }
	    }
	    
	    # divider
	    print "<tr>";
	    print "	<td colspan='3' class='hr'><hr></td>";
	    print "</tr>";
    ?>
    
    </table>
    </form> 
    
    <?php
    # warning if delete
    if($_POST['action'] == "delete" || $_POST['location'] == "IPaddresses") {
	    print "<div class='alert alert-warn' style='margin-top:0px;'><strong>Warning</strong><br>Removing subnets will delete ALL underlaying subnets and belonging IP addresses!</div>";
    }
    ?>


</div>


<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups">Cancel</button>
	<?php
	//if action == edit and location = IPaddresses print also delete form
	if(($_POST['action'] == "edit") && ($_POST['location'] == "IPaddresses") ) {
		print "<button class='btn btn-small editSubnetSubmitDelete editSubnetSubmit'><i class='icon-gray icon-remove'></i> Delete subnet</button>";
	}
	?>
	<button class="btn btn-small editSubnetSubmit"><i class="icon-gray icon-ok"></i> <?php print ucwords($_POST['action']); ?> subnet</button>

	<div class="manageSubnetEditResult"></div>
	<!-- vlan add holder from subnets -->
	<div id="addNewVlanFromSubnetEdit" style="display:none"></div>
</div>