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


/* change * to % for database wildchar */
$searchTerm = str_replace("*", "%", $searchTerm);


/* identify address type */
$type = IdentifyAddress( $searchTerm );

if ($type == "IPv4") {
	/* reformat the IPv4 address! */
	$searchTermEdited = reformatIPv4forSearch ($searchTerm);
}
else {
	/* reformat the IPv4 address! */
	$searchTermEdited = reformatIPv6forSearch ($searchTerm);
}

/* set the query */
$query  = 'select * from ipaddresses where ';
/* $query .= 'ip_addr like "' . $searchTerm . '%" '; */					//ip address in decimal
$query .= 'ip_addr between "'. $searchTermEdited['low'] .'" and "'. $searchTermEdited['high'] .'" ';	//ip range
$query .= 'or dns_name like "%' . $searchTerm . '%" ';					//hostname
$query .= 'or owner like "%' . $searchTerm . '%" ';						//owner
$query .= 'or switch like "%' . $searchTerm . '%" ';
$query .= 'or port like "%' . $searchTerm . '%" ';						//port search
$query .= 'or description like "%' . $searchTerm . '%" ';				//descriptions
$query .= 'or note like "%' . $searchTerm . '%" ';						//note
$query .= 'order by ip_addr asc;';

/* get result */
$result = searchAddresses ($query);
?>

<h3>Search results:</h3>

<!-- search result table -->
<div class="searchTable normalTable">
<table class="searchTable normalTable">

<!-- headers -->
<tr class="th" id="searchHeader">
<!--
	<th>Section</th>
	<th>Subnet</th>
-->
	<th>IP address</th>
	<th>VLAN</th>
	<th colspan="2">Description</th>
	<th>Hostname</th>
	<th>Switch</th>
	<th>Port</th>
	<th>Owner</th>
</tr>

<!-- content -->
<?php

/* if no result print nothing found */
if(sizeof($result) == 0) {
	die('<tr class="th"><td>Nothing found for search query "'. $_REQUEST['ip'] .'" in ip address list!</td><tr>');
}


$m = 0;		//for section change
/* print content */
foreach ($result as $line) {

	//get the Subnet details
	$subnet = getSubnetDetailsById ($line['subnetId']);
	//get section
	$section = getSectionDetailsById ($subnet['sectionId']);
	
	//detect section change and print headers
	if ($result[$m]['subnetId'] != $result[$m-1]['subnetId']) {
		print '<tr class="th">' . "\n";
		print '	<th colspan="8">'. $section['name'] . ' :: ' . $subnet['description'] .' ('. transform2long($subnet['subnet']) .'/'. $subnet['mask'] .')</th>' . "\n";
		print '</tr>';
	}
	$m++;
	
	//print table
	print '<tr id="'. $line['id'] .'" subnetId="'. $line['subnetId'] .'" sectionId="'. $subnet['sectionId'] .'" link="'. $section['name'] .'|'. $subnet['id'] .'">'. "\n";
	/*
	print ' <td>'. $section['name']  .'</td>' . "\n";
	print '	<td>'. transform2long($subnet['subnet']) . ' (' . $subnet['description'] .')</td>' . "\n";
*/
	
	print ' <td>'. transform2long($line['ip_addr'])  .'</td>' . "\n";
	print ' <td>'. $subnet['VLAN']  .'</td>' . "\n";
	print ' <td>'. ShortenText($line['description'], $chars = 50) .'</td>' . "\n";
	
	// print info button for hover
	print '<td class="note">' . "\n";
	if(!empty($line['note'])) {
		$line['note'] = str_replace("\n", "<br>",$line['note']);
		print '	<img class="info" src="css/images/infoIP.png" title="'. $line['note']. '">' . "\n";
	}
	print '</td>'. "\n";
	
	print ' <td>'. $line['dns_name']  .'</td>' . "\n";
	print ' <td>'. $line['switch']  .'</td>' . "\n";
	print ' <td>'. $line['port']  .'</td>' . "\n";
	print ' <td>'. $line['owner']  .'</td>' . "\n";
	print '</tr>' . "\n";
}

?>

</table>
</div>