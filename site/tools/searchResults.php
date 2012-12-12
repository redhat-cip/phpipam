<?php

/*
 * Script to display search results
 **********************************/

/* if method is post get query, otherwise use $serachTerm */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_POST['ip'])) {
		$searchTerm = $_REQUEST['ip'];
		//remove default
		if($searchTerm == "search") { $searchTerm = ""; }
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
	if(substr_count($searchTerm, ":") == 5) 												{ $type = "mac"; }
}
else if(strlen($searchTerm) == 12) {
	//no dots or : -> mac without :
	if( (substr_count($searchTerm, ":") == 0) && (substr_count($searchTerm, ".") == 0) ) 	{ $type = "mac"; }	
}
/* ok, not MAC! */
else 																						{ $type = IdentifyAddress( $searchTerm ); }		# identify address type


# reformat
if ($type == "IPv4") 		{ $searchTermEdited = reformatIPv4forSearch ($searchTerm);}		# reformat the IPv4 address!
else if ($type == "mac") 	{  }
else 						{ $searchTermEdited = reformatIPv6forSearch ($searchTerm); }	# reformat the IPv4 address!

# check also subnets! 
$subnets = searchSubnets ($searchTerm, $searchTermEdited);

# check also VLANS!
$vlans = searchVLANs ($searchTerm);

# get all custom fields 
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
$colSpan 	= $fieldSize + $mySize + 4;

# disable export for viewers
if(checkAdmin(false) == false) 	{ $uClass = "disabled"; }
else 							{ $uClass = ""; }
?>

<h4> Search results (IP address list): <?php if(sizeof($result) != 0) { print('<a href="" id="exportSearch" class="'.$uClass.'" rel="tooltip" title="Export All results to XLS"><button class="btn btn-small '.$uClass.'"><i class="icon-download"></i></button></a>');} ?></h4>
<hr>

<!-- export holder -->
<div class="exportDIVSearch"></div>

<!-- search result table -->
<table class="searchTable table table-striped table-condensed table-top table-hover">

<!-- headers -->
<tr id="searchHeader">
<?php

	print '<th>IP address</th>'. "\n";
	print '<th>VLAN</th>'. "\n";
	# description
	print '<th>Description</th>'. "\n";
	print '<th>Hostname</th>'. "\n";
	# mac
	if(in_array('mac', $setFields)) 										{ print '<th></th>'. "\n"; }
	# switch
	if(in_array('switch', $setFields))										{ print '<th>Switch</th>'. "\n"; }
	# port
	if(in_array('port', $setFields)) 										{ print '<th>Port</th>'. "\n"; }
	# owner and note
	if( (in_array('owner', $setFields)) && (in_array('note', $setFields)) ) { print '<th colspan="2">Owner</th>'. "\n"; }
	else if (in_array('owner', $setFields)) 								{ print '<th>Owner</th>'. "\n";	}
	else if (in_array('note', $setFields)) 									{ print '<th></th>'. "\n"; }
	
	# custom fields
	if(sizeof($myFields) > 0) {
		foreach($myFields as $myField) 										{ print '<th>'. $myField['name'] .'</th>'. "\n"; }
	}
	
	# actions
	print '<th class="actions" width="10px"></th>';
?>
</tr>

<!-- IP addresses -->
<?php

/* if no result print nothing found */
if(sizeof($result) == 0) {
	print('<tr><td colspan="'.$colSpan.'"><div class="alert alert-warn alert-nomargin">Nothing found for search query "'. $_REQUEST['ip'] .'" in ip address list!</div></td><tr>');
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
			print '<tr>' . "\n";
			print '	<th colspan="'. $colSpan .'">'. $section['name'] . ' :: ' . $subnet['description'] .' ('. transform2long($subnet['subnet']) .'/'. $subnet['mask'] .')</th>' . "\n";
			print '</tr>';
		}
		$m++;
		
		$stateClass = "";
	    if(in_array('state', $setFields)) {
		    if ($line['state'] == "0") 	 	{ $stateClass = "offline"; }
		    else if ($line['state'] == "2") { $stateClass = "reserved"; }
		    else if ($line['state'] == "3") { $stateClass = "DHCP"; }
		}
	
		//print table
		print '<tr class="ipSearch '.$stateClass.'" id="'. $line['id'] .'" subnetId="'. $line['subnetId'] .'" sectionId="'. $subnet['sectionId'] .'" link="'. $section['name'] .'|'. $subnet['id'] .'">'. "\n";
	
		print ' <td>'. transform2long($line['ip_addr']);
		if(in_array('state', $setFields)) 				{ print reformatIPState($line['state']); }	
		print ' </td>' . "\n";
		print ' <td>'. $vlan['number']  .'</td>' . "\n";
		print ' <td>'. ShortenText($line['description'], $chars = 50) .'</td>' . "\n";
	
		print ' <td>'. $line['dns_name']  .'</td>' . "\n";
		
		# mac
		if(in_array('mac', $setFields)) {
			print '	<td>'. "\n";
			if(strlen($line['mac']) > 0) {
				print '<i class="icon-mac" rel="tooltip" title=""MAC: '. $line['mac'] .'"></i>'. "\n";
			}
		print '	</td>'. "\n";
		}
		
		# switch
		if(in_array('switch', $setFields)) 										{ print ' <td>'. $line['switch']  .'</td>' . "\n"; }
		# port
		if(in_array('port', $setFields)) 										{ print ' <td>'. $line['port']  .'</td>' . "\n"; }
		# owner and note
		if((in_array('owner', $setFields)) && (in_array('note', $setFields)) ) {
			print ' <td>'. $line['owner']  .'</td>' . "\n";
			print ' <td class="note">' . "\n";
			if(!empty($line['note'])) {
				$line['note'] = str_replace("\n", "<br>",$line['note']);
				print '<i class="icon-gray icon-comment" rel="tooltip" title="'. $line['note']. '"></i>' . "\n";
			}
			print '</td>'. "\n";
		}
		# owner only
		else if (in_array('owner', $setFields)) 								{ print ' <td>'. $line['owner']  .'</td>' . "\n";	}
		# note only
		else if (in_array('note', $setFields)) {
			print '<td class="note">' . "\n";
			if(!empty($line['note'])) {
				$line['note'] = str_replace("\n", "<br>",$line['note']);
				print '	<i class="icon-gray icon-comment" rel="tooltip" title="'. $line['note']. '"></i>' . "\n";
			}
			print '</td>'. "\n";
		}
		# custom
		if(sizeof($myFields) > 0) {
			foreach($myFields as $myField) 										{ print '<td class="customField">'. $line[$myField['name']] .'</td>'. "\n"; }
		}
		
		# print action links if user can edit 
		if(!$viewer = isUserViewer()) {		
			print "<td class='btn-actions'>";
			print "	<div class='btn-toolbar'>";
			print "	<div class='btn-group'>";

			#locked for writing
			if( (isSubnetWriteProtected($subnet['id'])) && !checkAdmin(false)) {
				print "		<a class='edit_ipaddress   btn btn-mini disabled' rel='tooltip' title='Edit IP address details (disabled)'>		<i class='icon-gray icon-pencil'>  </i></a>";
				print "		<a class='mail_ipaddress   btn btn-mini          ' href='#' data-id='".$line['id']."' rel='tooltip' title='Send mail notification'>															<i class='icon-gray icon-envelope'></i></a>";
				print "		<a class='delete_ipaddress btn btn-mini disabled' rel='tooltip' title='Delete IP address (disabled)'>			<i class='icon-gray icon-remove'>  </i></a>";
			}
			# unlocked
			else {
				print "		<a class='edit_ipaddress   btn btn-mini modIPaddr' data-action='edit'   data-subnetId='$subnet[id]' data-id='".$line['id']."' href='#' 	rel='tooltip' title='Edit IP address details'>		<i class='icon-gray icon-pencil'>  </i></a>";
				print "		<a class='mail_ipaddress   btn btn-mini          ' href='#' data-id='".$line['id']."' rel='tooltip' title='Send mail notification'>															<i class='icon-gray icon-envelope'></i></a>";
				print "		<a class='delete_ipaddress btn btn-mini modIPaddr' data-action='delete' data-subnetId='$subnet[id]' data-id='".$line['id']."' href='#'  rel='tooltip' title='Delete IP address'>			<i class='icon-gray icon-remove'>  </i></a>";
			}
			print "	</div>";
			print "	</div>";
			print "</td>";		
		}
		else {
			print '<td></td>';
		}
		
		print '</tr>' . "\n";
	}
}
?>
</table>



<!-- search result table -->
<br>
<h4>Search results (Subnet list):</h4>
<hr>

<table class="searchTable table table-striped table-condensed table-top table-hover">

<!-- headers -->
<tr id="searchHeader">
	<th>Section</th>
	<th>Subnet</th>
	<th>Mask</th>
	<th>Description</th>
	<th>Master subnet</th>
	<th>VLAN</th>
	<th>Requests</th>
	<th><i class="icon-gray icon-lock"></i></th>
</tr>


<?php
if(sizeof($subnets) == 0) {
	print '<tr class="th"><td colspan="9"><div class="alert alert-warn alert-nomargin">Nothing found for search query "'. $_REQUEST['ip'] .'" in subnets!</div></td><tr>'. "\n";
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
		if($line['adminLock'] == 1) 	{ $img = '<i class="icon-gray icon-lock" rel="tooltip" title="Subnet is locked for writing for non-admins!"></i>'; }
		else 							{ $img = ""; }
	
		//format master subnet
		if($line['masterSubnetId'] == 0) { $line['masterSubnetId'] = "/"; }
		else {
			$line['masterSubnetId'] = getSubnetDetailsById ($line['masterSubnetId']);
			$line['masterSubnetId'] = transform2long($line['masterSubnetId']['subnet']) .'/'. $line['masterSubnetId']['mask'];
		}
		
/* 		print '<tr class="subnetSearch" subnetId="'. $line['subnetId'] .'" sectionId="'. $subnet['sectionId'] .'" link="'. $section['name'] .'|'. $subnet['id'] .'">'. "\n"; */
		print '<tr class="subnetSearch" subnetId="'. $line['id'] .'" sectionName="'. $section['name'] .'" sectionId="'. $section['id'] .'" link="'. $section['name'] .'|'. $line['id'] .'">'. "\n";

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




<!-- search result table -->
<br>
<h4>Search results (VLANs):</h4>
<hr>

<table class="vlanSearch table table-striped table-condensed table-top table-hover">

<!-- headers -->
<tr id="searchHeader">
	<th>Name</th>
	<th>Number</th>
	<th>Description</th>
	<th>Belonging subnets</th>
	<th>Section</th>
</tr>


<?php
if(sizeof($vlans) == 0) {
	print '<tr class="th"><td colspan="6"><div class="alert alert-warn alert-nomargin">Nothing found for search query "'. $_REQUEST['ip'] .'" in VLANs!</div></td><tr>'. "\n";
}
else {

	foreach($vlans as $vlan) {

		/* get all subnets in VLAN */
		$subnets = getSubnetsByVLANid ($vlan['vlanId']);
		
		/* no belonging subnets! */
		if(sizeof($subnets) == 0) {
			print '<tr class="nolink">' . "\n";
			print ' <td><dd>'. $vlan['name']      .'</dd></td>' . "\n";
			print ' <td><dd>'. $vlan['number']        .'</dd></td>' . "\n";
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
				print ' <td><dd>'. $vlan['name']         .'</dd></td>' . "\n";
				print ' <td><dd>'. $vlan['number']           .'</dd></td>' . "\n";
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