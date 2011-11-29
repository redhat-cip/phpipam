<?php

/**
 *	Generate XLS file for subnet
 *********************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* we dont need any errors! */
ini_set('display_errors', 0);

/* verify that user is admin */
checkAdmin();

require_once '../../functions/PEAR/Spreadsheet/Excel/Writer.php';

// Create a workbook
$filename = "phpipam_subnet_export.xls";
$workbook = new Spreadsheet_Excel_Writer();

//get requested subnet Id
$subnetId = $_REQUEST['subnetId'];

//get all subnet details
$subnet = getSubnetDetailsById ($subnetId);

//get all IP addresses in subnet
$ipaddresses = getIpAddressesBySubnetId ($subnetId);

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


// Create a worksheet
$worksheet =& $workbook->addWorksheet($subnet['description']);

$lineCount = 0;

//Write title
$worksheet->write($lineCount, 0, transform2long($subnet['subnet']) . "/" .$subnet['mask'] . " - " . $subnet['description'] . ' (vlan: '. $subnet['VLAN'] .')', $format_header );
$worksheet->mergeCells($lineCount, 0, $lineCount, 8);
		
$lineCount++;
		
//write headers
	$worksheet->write($lineCount, 0, 'ip address' ,$format_title);
	$worksheet->write($lineCount, 1, 'ip state' ,$format_title);
	$worksheet->write($lineCount, 2, 'description' ,$format_title);
	$worksheet->write($lineCount, 3, 'hostname' ,$format_title);
	$worksheet->write($lineCount, 4, 'mac' ,$format_title);
	$worksheet->write($lineCount, 5, 'owner' ,$format_title);
	$worksheet->write($lineCount, 6, 'switch' ,$format_title);
	$worksheet->write($lineCount, 7, 'port' ,$format_title);
	$worksheet->write($lineCount, 8, 'note' ,$format_title);
			
	$lineCount++;
		
//write all IP addresses
foreach ($ipaddresses as $ip) {
		
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
	$worksheet->write($lineCount, 4, $ip['mac']);
	$worksheet->write($lineCount, 5, $ip['owner']);
	$worksheet->write($lineCount, 6, $ip['switch']);
	$worksheet->write($lineCount, 7, $ip['port']);
	$worksheet->write($lineCount, 8, $ip['note'], $format_right);
			
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

//new line
$lineCount++;

// sending HTTP headers
$workbook->send($filename);

// Let's send the file
$workbook->close();

?>