<?php 

/**
 *	Export search results
 ****************************/
require_once('../../functions/functions.php');

/* hide errors! */
ini_set('display_errors', 0);

/* get query */
$searchTerm = $_REQUEST['searchTerm'];


/* verify that user is admin */
checkAdmin();


/* get result */
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


/* set the query */
$query  = 'select * from ipaddresses where ';
/* $query .= 'ip_addr like "' . $searchTerm . '%" '; */					//ip address in decimal
$query .= '`ip_addr` between "'. $searchTermEdited['low'] .'" and "'. $searchTermEdited['high'] .'" ';	//ip range
$query .= 'or `dns_name` like "%' . $searchTerm . '%" ';					//hostname
$query .= 'or `owner` like "%' . $searchTerm . '%" ';						//owner
$query .= 'or `switch` like "%' . $searchTerm . '%" ';
$query .= 'or `port` like "%' . $searchTerm . '%" ';						//port search
$query .= 'or `description` like "%' . $searchTerm . '%" ';				//descriptions
$query .= 'or `note` like "%' . $searchTerm . '%" ';						//note
$query .= 'or `mac` like "%' . $searchTerm . '%" ';						//mac
$query .= 'order by `ip_addr` asc;';

/* get result */
$result = searchAddresses ($query);




/*
 *	Write xls
 *********************/
require_once '../../functions/PEAR/Spreadsheet/Excel/Writer.php';

// Create a workbook
$filename = "phpipam_search_export_". $searchTerm .".xls";
$workbook = new Spreadsheet_Excel_Writer();

//formatting headers
$format_header =& $workbook->addFormat();
$format_header->setBold();
$format_header->setColor('white');
$format_header->setFgColor('black');

//formatting titles
$format_title =& $workbook->addFormat();
$format_title->setColor('black');
$format_title->setFgColor(22);			//light gray
$format_title->setBottom(2);
$format_title->setLeft(1);
$format_title->setRight(1);
$format_title->setTop(1);
$format_title->setAlign('left');

//formatting content - borders around IP addresses
$format_right =& $workbook->addFormat();
$format_right->setRight(1);
$format_left =& $workbook->addFormat();
$format_left->setLeft(1);
$format_top =& $workbook->addFormat();
$format_top->setTop(1);


$lineCount = 0;		//for line change
$m = 0;				//for section change

// Create a worksheet
$worksheet =& $workbook->addWorksheet('IP Search results');

//write headers
$worksheet->write($lineCount, 0, 'ip address' ,$format_title);
$worksheet->write($lineCount, 1, 'state' ,$format_title);
$worksheet->write($lineCount, 2, 'description' ,$format_title);
$worksheet->write($lineCount, 3, 'hostname' ,$format_title);
$worksheet->write($lineCount, 4, 'switch' ,$format_title);
$worksheet->write($lineCount, 5, 'port' ,$format_title);
$worksheet->write($lineCount, 6, 'owner' ,$format_title);
$worksheet->write($lineCount, 7, 'mac' ,$format_title);
$worksheet->write($lineCount, 8, 'note' ,$format_title);

//new line
$lineCount++;

//Write IP addresses
foreach ($result as $ip) {

	//get the Subnet details
	$subnet = getSubnetDetailsById ($ip['subnetId']);
	//get section
	$section = getSectionDetailsById ($subnet['sectionId']);

	//section change
	if ($result[$m]['subnetId'] != $result[$m-1]['subnetId']) {

		//top border line at bottom of IP addresses
		$worksheet->write($lineCount, 0, "", $format_top);
		$worksheet->write($lineCount, 1, "", $format_top);
		$worksheet->write($lineCount, 2, "", $format_top);
		$worksheet->write($lineCount, 3, "", $format_top);
		$worksheet->write($lineCount, 4, "", $format_top);
		$worksheet->write($lineCount, 5, "", $format_top);
		$worksheet->write($lineCount, 6, "", $format_top);
		$worksheet->write($lineCount, 7, "", $format_top);
		$worksheet->write($lineCount, 8, "", $format_top);
		//new line
		$lineCount++;

		//subnet details
		$worksheet->write($lineCount, 0, transform2long($subnet['subnet']) . "/" .$subnet['mask'] . " - " . $subnet['description'] . ' (vlan: '. $subnet['VLAN'] .')', $format_header );
		$worksheet->mergeCells($lineCount, 0, $lineCount, 8);
	
		//new line
		$lineCount++;
	}
	$m++;
	
	
	//we need to reformat state!
	switch($ip['state']) {
		case 0: $ip['state'] = "Offline";	break;
		case 1: $ip['state'] = "Active";	break;
		case 2: $ip['state'] = "Reserved";	break;
	}
	
		
	$worksheet->write($lineCount, 0, transform2long($ip['ip_addr']), $format_left);
	$worksheet->write($lineCount, 1, $ip['state']);
	$worksheet->write($lineCount, 2, $ip['description']);
	$worksheet->write($lineCount, 3, $ip['dns_name']);
	$worksheet->write($lineCount, 4, $ip['switch']);
	$worksheet->write($lineCount, 5, $ip['port']);
	$worksheet->write($lineCount, 6, $ip['owner']);
	$worksheet->write($lineCount, 7, $ip['mac']);
	$worksheet->write($lineCount, 8, $ip['note'], $format_right);
	
	//new line
	$lineCount++;
}

//top border line at bottom of IP addresses
$worksheet->write($lineCount, 0, "", $format_top);
$worksheet->write($lineCount, 1, "", $format_top);
$worksheet->write($lineCount, 2, "", $format_top);
$worksheet->write($lineCount, 3, "", $format_top);
$worksheet->write($lineCount, 4, "", $format_top);
$worksheet->write($lineCount, 5, "", $format_top);
$worksheet->write($lineCount, 6, "", $format_top);
$worksheet->write($lineCount, 7, "", $format_top);
$worksheet->write($lineCount, 8, "", $format_top);




/**
 *	Subnet results
 ***************************/
/* check also subnets! */
$allSubnets = searchSubnets ($searchTerm, $searchTermEdited);

$lineCount = 0;

$worksheet =& $workbook->addWorksheet('Subnet search results');

//write headers
$worksheet->write($lineCount, 0, 'Section' ,$format_title);
$worksheet->write($lineCount, 1, 'Subet' ,$format_title);
$worksheet->write($lineCount, 2, 'Mask' ,$format_title);
$worksheet->write($lineCount, 3, 'Description' ,$format_title);
$worksheet->write($lineCount, 4, 'Master subnet' ,$format_title);
$worksheet->write($lineCount, 5, 'VLAN' ,$format_title);
$worksheet->write($lineCount, 6, 'allowRequests' ,$format_title);
$worksheet->write($lineCount, 7, 'Admin lock' ,$format_title);

//new line
$lineCount++;

foreach($allSubnets as $line) {

	//get section details 
	$section = getSectionDetailsById ($line['sectionId']);
	
	//format master subnet
	if($line['masterSubnetId'] == 0) { $line['masterSubnetId'] = "/"; }
	else {
		$line['masterSubnetId'] = getSubnetDetailsById ($line['masterSubnetId']);
		$line['masterSubnetId'] = transform2long($line['masterSubnetId']['subnet']) .'/'. $line['masterSubnetId']['mask'];
	}
	//admin lock
	if($line['adminLock'] == 1) { $line['adminLock'] = 'yes'; }
	else 						{ $line['adminLock'] = ''; }
	//allowRequests
	if($line['allowRequest'] == 1) 	{ $line['allowRequest'] = 'yes'; }
	else 							{ $line['allowRequest'] = ''; }

	//print subnet
	$worksheet->write($lineCount, 0, $section['name'], $format_left);
	$worksheet->write($lineCount, 1, transform2long($line['subnet']));
	$worksheet->write($lineCount, 2, $line['mask']);
	$worksheet->write($lineCount, 3, $line['description']);
	$worksheet->write($lineCount, 4, $line['masterSubnetId']);
	$worksheet->write($lineCount, 5, $line['VLAN']);
	$worksheet->write($lineCount, 6, $line['allowRequests']);
	$worksheet->write($lineCount, 7, $line['adminLock'], $format_right);
	
	//new line
	$lineCount++;
}

//top border line at bottom of IP addresses
$worksheet->write($lineCount, 0, "", $format_top);
$worksheet->write($lineCount, 1, "", $format_top);
$worksheet->write($lineCount, 2, "", $format_top);
$worksheet->write($lineCount, 3, "", $format_top);
$worksheet->write($lineCount, 4, "", $format_top);
$worksheet->write($lineCount, 5, "", $format_top);
$worksheet->write($lineCount, 6, "", $format_top);
$worksheet->write($lineCount, 7, "", $format_top);


// sending HTTP headers
$workbook->send($filename);

// Let's send the file
$workbook->close();



?>