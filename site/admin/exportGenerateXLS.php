<?php

/**
 *	Generate XLS file
 *********************************/
/* required functions */
require_once('../../functions/functions.php'); 

/* we dont need any errors! */
ini_set('display_errors', 0);

/* verify that user is admin */
checkAdmin();

require_once '../../functions/PEAR/Spreadsheet/Excel/Writer.php';

// Create a workbook
$filename = "phpipam_IP_adress_export_". date("Y-m-d") .".xls";
$workbook = new Spreadsheet_Excel_Writer();

//fetch sections, and for each section write new tab, inside tab write all values!
$sections = fetchSections();


//get all custom fields!
$myFields = getCustomIPaddrFields();
$myFieldsSize = sizeof($myFields);

$colSize = 8 + $myFieldsSize;

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


foreach ($sections as $section)
{
	// Create a worksheet
	$worksheet =& $workbook->addWorksheet($section['name']);
	
	//get all subnets in this section
	$subnets = fetchSubnets ($section['id']);
	
	$lineCount = 0;
	//Write titles
	foreach ($subnets as $subnet) {
		//subnet details
		$vlan = subnetGetVLANdetailsById($subnet['vlanId']);
		if(strlen($vlan['number']) > 0) {
			$vlanText = " (vlan: " . $vlan['number'];
			if(strlen($vlan['name']) > 0) {
				$vlanText .= ' - '. $vlan['name'] . ')';
			}
			else {
				$vlanText .= ")";
			}
		}
		
		$worksheet->write($lineCount, 0, transform2long($subnet['subnet']) . "/" .$subnet['mask'] . " - " . $subnet['description'] . $vlanText, $format_header );
		$worksheet->mergeCells($lineCount, 0, $lineCount, $colSize);
		
		$lineCount++;
		
		//IP addresses in subnet
		$ipaddresses = getIpAddressesBySubnetId ($subnet['id']);
		
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
			$m = 9;
			//custom
			if(sizeof($myFields) > 0) {
				foreach($myFields as $myField) {
					$worksheet->write($lineCount, $m, $myField['name'] ,$format_title);
					$m++;
				}
			}
			
			$lineCount++;
		
		if(sizeof($ipaddresses) > 0) {

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
				$worksheet->write($lineCount, 8, $ip['note']);
				//custom
				$m = 9;
				if(sizeof($myFields) > 0) {
						foreach($myFields as $myField) {
						$worksheet->write($lineCount, $m, $ip[$myField['name']]);
						$m++;
					}
				}
			
				$lineCount++;
			}
		
		}
		else {
			$worksheet->write($lineCount, 0, 'No hosts');
			$lineCount++;
		}
		
		//new line
		$lineCount++;
	}
}

// sending HTTP headers
$workbook->send($filename);

// Let's send the file
$workbook->close();

?>