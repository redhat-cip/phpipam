<?php

/*
 * Script to display search results
 **********************************/

/* if method is post get query, otherwise use $serachTerm */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_POST['ip'])) {
		$searchTerm = $_REQUEST['ip'];
		
		//remove default
		if($searchTerm == "search") {
			$searchTerm = "";
		}
	}
	require_once('../../functions/functions.php');
}


/* hide errors! */
ini_set('display_errors', 0);


/* change * to % for database wildchar */
$searchTerm = str_replace("*", "%", $searchTerm);


/* check if mac address */
if(strlen($searchTerm) == 17) {
	//count : -> must be 5
	if(substr_count($searchTerm, ":") == 5) {
		$type = "mac";
	}
}
else if(strlen($searchTerm) == 12) {
	//no dots or : -> mac without :
	if( (substr_count($searchTerm, ":") == 0) && (substr_count($searchTerm, ".") == 0) ) {
		$type = "mac";
	}	
}
/* ok, not MAC! */
else {
	/* identify address type */
	$type = IdentifyAddress( $searchTerm );
}


if ($type == "IPv4") {
	/* reformat the IPv4 address! */
	$searchTermEdited = reformatIPv4forSearch ($searchTerm);
}
else if ($type == "mac") {
}
else {
	/* reformat the IPv4 address! */
	$searchTermEdited = reformatIPv6forSearch ($searchTerm);
}

/* check also subnets! */
$subnets = searchSubnets ($searchTerm, $searchTermEdited);


/* check also VLANS! */
$vlans = searchVLANs ($searchTerm);


/* get all custom fields */
$myFields = getCustomIPaddrFields();


/* set the query */
$query  = 'select * from ipaddresses where ';
/* $query .= 'ip_addr like "' . $searchTerm . '%" '; */					//ip address in decimal
$query .= '`ip_addr` between "'. $searchTermEdited['low'] .'" and "'. $searchTermEdited['high'] .'" ';	//ip range
$query .= 'or `dns_name` like "%' . $searchTerm . '%" ';					//hostname
$query .= 'or `owner` like "%' . $searchTerm . '%" ';						//owner
# custom!
# custom fields
if(sizeof($myFields) > 0) {
	foreach($myFields as $myField) {
		$query .= 'or `'. $myField['name'] .'` like "%' . $searchTerm . '%" ';
	}
}
$query .= 'or `switch` like "%' . $searchTerm . '%" ';
$query .= 'or `port` like "%' . $searchTerm . '%" ';						//port search
$query .= 'or `description` like "%' . $searchTerm . '%" ';					//descriptions
$query .= 'or `note` like "%' . $searchTerm . '%" ';						//note
$query .= 'or `mac` like "%' . $searchTerm . '%" ';							//mac
$query .= 'order by `ip_addr` asc;';


/* get result */
$result = searchAddresses ($query);


/* get all selected fields for IP print */
$setFieldsTemp = getSelectedIPaddrFields();
/* format them to array! */
$setFields = explode(";", $setFieldsTemp);


/* get all selected fields */
$myFields = getCustomIPaddrFields();


# set col size
$fieldSize 	= sizeof($setFields);
$mySize 	= sizeof($myFields);
$colSpan 	= $fieldSize + $mySize + 3;

?>

<h3>
	Search results (IP address list): 
	<?php
	if(sizeof($result) != 0) {
		print('<a href="" id="exportSearch" title="Export All results to XLS"><img src="css/images/download.png"></a>');
	}	
	?>
</h3>

<!-- export holder -->
<div class="exportDIVSearch"></div>

<!-- search result table -->
<div class="searchTable normalTable">
<table class="searchTable normalTable">

<!-- headers -->
<tr class="th" id="searchHeader">
<?php

	print '<th>IP address</th>'. "\n";
	print '<th>VLAN</th>'. "\n";
	# description
	print '<th>Description</th>'. "\n";
	print '<th>Hostname</th>'. "\n";
	# mac
	if(in_array('mac', $setFields)) {
	print '<th></th>'. "\n";
	}
	# switch
	if(in_array('switch', $setFields)) {
	print '<th>Switch</th>'. "\n";
	}
	# port
	if(in_array('port', $setFields)) {
	print '<th>Port</th>'. "\n";
	}
	
	# owner and note
	if( (in_array('owner', $setFields)) && (in_array('note', $setFields)) ) {
	print '<th colspan="2">Owner</th>'. "\n";
	}
	else if (in_array('owner', $setFields)) {
	print '<th>Owner</th>'. "\n";	
	}
	else if (in_array('note', $setFields)) {
	print '<th></th>'. "\n";
	}
	
	# custom fields
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) {
			print '<th>'. $myField['name'] .'</th>'. "\n";
		}
	}
?>
</tr>

<!-- IP addresses -->
<?php

/* if no result print nothing found */
if(sizeof($result) == 0) {
	print('<tr class="th"><td>Nothing found for search query "'. $_REQUEST['ip'] .'" in ip address list!</td><tr>');
}
else {
	$m = 0;		//for section change
	/* print content */
	foreach ($result as $line) {

		//get the Subnet details
		$subnet = getSubnetDetailsById ($line['subnetId']);
		//get vlan number
		$vlan   = subnetGetVLANDetailsById($subnet['vlanId']);
		//get section
		$section = getSectionDetailsById ($subnet['sectionId']);
	
		//detect section change and print headers
		if ($result[$m]['subnetId'] != $result[$m-1]['subnetId']) {
			print '<tr class="th">' . "\n";
			print '	<th colspan="'. $colSpan .'">'. $section['name'] . ' :: ' . $subnet['description'] .' ('. transform2long($subnet['subnet']) .'/'. $subnet['mask'] .')</th>' . "\n";
			print '</tr>';
		}
		$m++;
	
		//print table
		print '<tr class="ipSearch" id="'. $line['id'] .'" subnetId="'. $line['subnetId'] .'" sectionId="'. $subnet['sectionId'] .'" link="'. $section['name'] .'|'. $subnet['id'] .'">'. "\n";
	
		print ' <td>'. transform2long($line['ip_addr'])  .'</td>' . "\n";
		print ' <td>'. $vlan['number']  .'</td>' . "\n";
		print ' <td>'. ShortenText($line['description'], $chars = 50) .'</td>' . "\n";
	
		print ' <td>'. $line['dns_name']  .'</td>' . "\n";
		
		# mac
		if(in_array('mac', $setFields)) {
			print '	<td>'. "\n";
			if(strlen($line['mac']) > 0) {
				print '<img class="info" src="css/images/lan.png" title="MAC: '. $line['mac'] .'">'. "\n";
			}
		print '	</td>'. "\n";
		}
		
		# switch
		if(in_array('switch', $setFields)) {
		print ' <td>'. $line['switch']  .'</td>' . "\n";
		}
		# port
		if(in_array('port', $setFields)) {
		print ' <td>'. $line['port']  .'</td>' . "\n";
		}
		
		# owner and note
		if((in_array('owner', $setFields)) && (in_array('note', $setFields)) ) {
		
			print ' <td>'. $line['owner']  .'</td>' . "\n";
			print ' <td class="note">' . "\n";
			if(!empty($line['note'])) {
				$line['note'] = str_replace("\n", "<br>",$line['note']);
				print '	<img class="info" src="css/images/note.png" title="'. $line['note']. '">' . "\n";
			}
			print '</td>'. "\n";
		}
		# owner only
		else if (in_array('owner', $setFields)) {
			print ' <td>'. $line['owner']  .'</td>' . "\n";		
		}
		# note only
		else if (in_array('note', $setFields)) {
			print '<td class="note">' . "\n";
			if(!empty($line['note'])) {
				$line['note'] = str_replace("\n", "<br>",$line['note']);
				print '	<img class="info" src="css/images/note.png" title="'. $line['note']. '">' . "\n";
			}
			print '</td>'. "\n";
		}
		
		# custom
		if(sizeof($myFields) > 0) {
			foreach($myFields as $myField) {
				print '<td class="customField">'. $line[$myField['name']] .'</td>'. "\n";
			}
		}

		print '</tr>' . "\n";
	}
}
?>
</table>
</div>



<!-- search result table -->
<h3>Search results (Subnet list):</h3>

<div class="searchTable normalTable subnetSearch">
<table class="searchTable normalTable subnetSearch">

<!-- headers -->
<tr class="th" id="searchHeader">
	<th>Section</th>
	<th>Subnet</th>
	<th>Mask</th>
	<th>Description</th>
	<th>Master subnet</th>
	<th>VLAN</th>
	<th>Requests</th>
	<th><img src="css/images/lock.png"></th>
</tr>


<?php
if(sizeof($subnets) == 0) {
	print '<tr class="th"><td colspan="9">Nothing found for search query "'. $_REQUEST['ip'] .'" in subnets!</td><tr>'. "\n";
}
else {

	foreach($subnets as $line) {

		//get section details 
		$section = getSectionDetailsById ($line['sectionId']);
		//get vlan number
		$vlan   = subnetGetVLANDetailsById($line['vlanId']);
	
		//format requests
		if($line['allowRequests'] == 1) { $line['allowRequests'] = "enabled"; }
		else 							{ $line['allowRequests'] = "disabled"; }
	
		//format lock
		if($line['adminLock'] == 1) 	{ $img = '<img src="css/images/lock.png">'; }
		else 							{ $img = ""; }
	
		//format master subnet
		if($line['masterSubnetId'] == 0) { $line['masterSubnetId'] = "/"; }
		else {
			$line['masterSubnetId'] = getSubnetDetailsById ($line['masterSubnetId']);
			$line['masterSubnetId'] = transform2long($line['masterSubnetId']['subnet']) .'/'. $line['masterSubnetId']['mask'];
		}
		
/* 		print '<tr class="subnetSearch" subnetId="'. $line['subnetId'] .'" sectionId="'. $subnet['sectionId'] .'" link="'. $section['name'] .'|'. $subnet['id'] .'">'. "\n"; */
		print '<tr class="subnetSearch" subnetId="'. $line['id'] .'" sectionName="'. $section['name'] .'" sectionId="'. $section['id'] .'" link="'. $section['name'] .'|'. $subnet['id'] .'">'. "\n";

		print '	<td>'. $section['name'] . '</td>'. "\n"; 
		print '	<td>'. transform2long($line['subnet']) . '</td>'. "\n"; 
		print ' <td>'. $line['mask'] .'</td>' . "\n";
		print ' <td>'. $line['description'] .'</td>' . "\n";
		print ' <td>'. $line['masterSubnetId'] .'</td>' . "\n";
		print ' <td>'. $vlan['number'] .'</td>' . "\n";
		print ' <td>'. $line['allowRequests'] .'</td>' . "\n";
		print ' <td>'. $img .'</td>' . "\n";
	
		print '</tr>'. "\n";
	}
}
?>

</table>
</div>




<!-- search result table -->
<h3>Search results (VLANs):</h3>

<div class="searchTable normalTable vlanSearch">
<table class="searchTable normalTable vlanSearch">

<!-- headers -->
<tr class="th" id="searchHeader">
	<th>Name</th>
	<th>Number</th>
	<th>Description</th>
	<th>Belonging subnets</th>
	<th>Section</th>
</tr>


<?php
if(sizeof($vlans) == 0) {
	print '<tr class="th"><td colspan="4">Nothing found for search query "'. $_REQUEST['ip'] .'" in VLANs!</td><tr>'. "\n";
}
else {

	foreach($vlans as $vlan) {

		/* get all subnets in VLAN */
		$subnets = getSubnetsByVLANid ($vlan['vlanId']);
		
		/* no belonging subnets! */
		if(sizeof($subnets) == 0) {
			print '<tr class="nolink">' . "\n";
			print ' <td><dd>'. $vlan['number']      .'</dd></td>' . "\n";
			print ' <td><dd>'. $vlan['name']        .'</dd></td>' . "\n";
			print ' <td><dd>'. $vlan['description'] .'</dd></td>' . "\n";				
			print ' <td>----</td>' . "\n";
			print ' <td>----</td>' . "\n";
			print '</tr>'. "\n";
		}
		
		/* for each subnet print tr */
		foreach($subnets as $subnet)
		{
			/* get section details */
			$section = getSectionDetailsById ($subnet['sectionId']);	

			# detect change
			$vlanNew = $subnet['vlanId'];
			if($vlanNew == $vlanOld) { $change = ''; }
			else 					 { $change = 'style="border-top:1px dashed white"'; $vlanOld = $vlanNew; }

			print '<tr class="link vlanSearch" '. $change .' sectionId="'. $section['id'] .'" subnetId="'. $subnet['id'] .'" link="'. $section['name'] .'|'. $subnet['id'] .'">' . "\n";

			/* print first 3 only if change happened! */
			if(strlen($change) > 0) {
				print ' <td><dd>'. $vlan['number']         .'</dd></td>' . "\n";
				print ' <td><dd>'. $vlan['name']           .'</dd></td>' . "\n";
				print ' <td><dd>'. $vlan['description'] .'</dd></td>' . "\n";			
			}
			else {
				print '<td></td>';
				print '<td></td>';
				print '<td></td>';	
			} 

			if ($subnet['id'] != null) {
				# subnet
				print ' <td>'. transform2long($subnet['subnet']) .'/'. $subnet['mask'] .'</td>' . "\n";

				# section
				print ' <td>'. $section['name'] .'</td>'. "\n";
			}
			else {
    		    print '<td>---</td>'. "\n";
    		    print '<td>---</td>'. "\n";
    		}
    		
    		print '</tr>' . "\n";
    	}

    }
}
?>

</table>
</div>