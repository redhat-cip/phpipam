<?php

/**
 * Script to print subnets from selected section
 *************************************************/
 
/* include functions */
require_once('../functions/functions.php');

/* check referer and requested with */
CheckReferrer();

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get requested section and format it to nice output */
$sectionId = $_REQUEST['section'];

/* if it is not numeric than get ID from provided name */
if ( (!is_numeric($sectionId)) && ($sectionId != "Administration") ) {
    $sectionId = getSectionIdFromSectionName ($sectionId);
}

/**
 * Admin check, otherwise load requested subnets
 */
if ($sectionId == 'Administration')
{
    /* Print all Admin actions af user is admin :) */
    if (!checkAdmin()) {
        print '<div class="error">Sorry, must be admin!</div>';
    }
    else {
        include('admin/adminMenu.php');
    }
}
else 
{
    /* get all subnets in section */
    $subnets = fetchMasterSubnets ($sectionId);
    
    /* get section name */
    $sectionName = getSectionDetailsById ($sectionId);
    
    /* print subnets table */
    print '<table class="subnets normalTable">' . "\n";

    /* IP / mask header */
    print '<tr class="th">' . "\n";
    print '<th class="hideSubnets" title="Hide Subnet list"><img src="css/images/rewind.png" class="rewind" title="Hide subnets"></th>'. "\n";
    print '<th colspan=2>Subnets in "'. $sectionName['name'] .'"</th>'. "\n";
    print '</tr>' . "\n";

    /* @subnets - if not empty ---------- */
    if (sizeof($subnets) != 0) 
    {
        foreach ( $subnets as $subnet ) 
        {
	        //check if it contains slaves
	        $slaves = subnetContainsSlaves($subnet['id']);

			if(strlen($subnet['description']) == 0) { $slave['description'] = "no description";}	#fix for no description title
			if($subnet['description'] == "") { $subnet['description'] = "No description"; }			#fix for no description title       
        
	        //If slaves print link and expand, otherwise subnet
	        if($slaves)
	        {		
	        	/* @L1 ------------- */    
			    print '<tr id="'. $subnet['id'] .'" class="'. $subnet['id'] .'">' . "\n";        
	        	/* subnet */
	        	print '	<td class="subnet" colspan="2" title="'. $subnet['description'] .'">' . "\n";
				print '	<dd class="slavesToggle" section="'. $sectionName['name'] .'|'. $subnet['id'] .'" id="'. $subnet['id'] .'">' . Transform2long($subnet['subnet']) .'/'. $subnet['mask'] .'</dd>' . "\n";  
				print '	</td>' . "\n";
			
				/* structure image for drilldown */
				print '	<td class="structure"><img class="structure" src="css/images/expand.png" subnetId="'. $subnet['id'] .'" title="Expand/Collapse subnet"></td>'. "\n";
		    	print '</tr>'. "\n";
	    	
	    	
		    	/* @L2 slaves ------------- */
		    	print '<tr class="th">' . "\n";
				print '<td colspan="3" class="slaveSubnets"><div class="slaveSubnets slaveSubnets-'. $subnet['id'] .'">'. "\n";
				
				$slaveSubnets = getAllSlaveSubnetsBySubnetId($subnet['id']);
					
				print '<table class="normalTable slaves">' . "\n";
				
				foreach ($slaveSubnets as $slave) 
				{
					//check if it contains subSlaves
	        		$subSlaves = subnetContainsSlaves($slave['id']);
	        		
	        		if($subSlaves)
	        		{
	        			/* @L2 slaves ---------- */
					    print '<tr id="'. $slave['id'] .'" class="'. $slave['id'] .'">' . "\n";        
			        	/* subnet */
			        	print '	<td class="subnet" colspan="2" title="'. $slave['description'] .'">' . "\n";
						print '	<dd class="slavesToggle" section="'. $sectionName['name'] .'|'. $slave['id'] .'" id="'. $slave['id'] .'">' . Transform2long($slave['subnet']) .'/'. $slave['mask'] .'</dd>' . "\n";  
						print '	</td>' . "\n";
			
						/* structure image for drilldown */
						print '	<td class="structure"><img class="structure" src="css/images/expand.png" subnetId="'. $slave['id'] .'" title="Expand/Collapse subnet"></td>'. "\n";
		    			print '</tr>'. "\n";

		    			/* @L3 slaves ------------- */
		    			print '<tr class="th">' . "\n";
						print '<td colspan="3" class="slaveSubnets"><div class="slaveSubnets slaveSubnets-'. $slave['id'] .'">'. "\n";
				
						$subSlaveSubnets = getAllSlaveSubnetsBySubnetId($slave['id']);

						print '<table class="normalTable slaves subSlaves">' . "\n";
				
						foreach ($subSlaveSubnets as $subSlaveSubnet) 
						{
	        				//fix no description title
							if(strlen($subSlaveSubnet['description']) == 0) $subSlaveSubnet['description'] = "no description";
				
							print '<tr id="'. $subSlaveSubnet['id'] .'" class="'. $subSlaveSubnet['id'] .'">' . "\n";
	            			print '	<td class="subnet slave" title="'. $subSlaveSubnet['description'] .'">' . "\n";
	            			print '		<dd section="'. $sectionName['name'] .'|'. $subSlaveSubnet['id'] .'" id="'. $subSlaveSubnet['id'] .'">' . Transform2long($subSlaveSubnet['subnet']) .'/'. $subSlaveSubnet['mask'] .'</dd>' . "\n";
	            			print '	</td>' . "\n";
	        				print '</tr>';
						}
						
						print '</table>';
				
						print '</div></td>' . "\n";
						print '</tr>' . "\n"; 	        		
	        		}
	        		else 
	        		{
	        			//fix no description title
						if(strlen($slave['description']) == 0) $slave['description'] = "no description";
				
						print '<tr id="'. $slave['id'] .'" class="'. $slave['id'] .'">' . "\n";
	            		print '	<td colspan="2" class="subnet slave" title="'. $slave['description'] .'">' . "\n";
	            		print '		<dd section="'. $sectionName['name'] .'|'. $slave['id'] .'" id="'. $slave['id'] .'">' . Transform2long($slave['subnet']) .'/'. $slave['mask'] .'</dd>' . "\n";
	            		print '	</td>' . "\n";
	            		/* we dont need any structure image */
						print '	<td class="structure"></td>'. "\n";	
	        			print '</tr>';
	        		}
	        	}

				print '</table>';
				
				print '</div></td>' . "\n";
				print '</tr>' . "\n"; 			
       		}
       	
       		/* No slaves - L1 print */
        	else 
        	{   
			    print '<tr id="'. $subnet['id'] .'" class="'. $subnet['id'] .'">' . "\n";        
        
        		/* subnet */
        		print '	<td colspan="2" class="subnet" title="'. $subnet['description'] .'">' . "\n";    
				print '	<dd section="'. $sectionName['name'] .'|'. $subnet['id'] .'" id="'. $subnet['id'] .'">' . Transform2long($subnet['subnet']) .'/' . $subnet['mask'] .'</dd>' . "\n";    				
				print '	</td>' . "\n"; 
			
				/* we dont need any structure image */
				print '	<td class="structure"></td>'. "\n";	
				print '</tr>'. "\n";		
        	}
    	}	# end foreach subnet		
    }	# end if subnets
    else {
        print '<tr class="th info"><td colspan="3">No subnets available!</td></tr>';
    }
    
    /* admin-only addnew link */
	if(checkAdmin(false, false)) {
    	print '<tr class="th info">'. "\n";
    	print '	<td></td><td class="addSubnet">Add new subnet</td>'. "\n";
    	print '	<td class="plusSubnet" class="addSubnet" id="'. $sectionId .'" title="Add new Subnet to '. $sectionName['name'] .' section"></td>'. "\n";
    	print '</tr>'. "\n";
	} 

    print '</table>';    

	/* Script to show slave subnets on load! */
	if(empty($_POST['slaveId'])) $_POST['slaveId'] = "10000000000";
	print '<script type="text/javascript">'. "\n";
	print '$(document).ready(function () {'. "\n";
	print '$("table.slaves tr." + '. $_POST['slaveId'].').closest("table").parent().parent().children("div.slaveSubnets").delay(200).slideDown("fast");'. "\n";
	print '});'. "\n";
	print '</script>'. "\n";
}