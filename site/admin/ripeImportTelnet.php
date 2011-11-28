<?php

/**
 * Script to manage sections
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();


$as = $_POST['as'];

//open connection
$povezava = fsockopen("whois.ripe.net", 43, $errno, $errstr, 5); 
if(!$povezava) 	{ echo "$errstr ($errno)";	}
else { 
	//fetch result
	fputs ($povezava, '-GRK -i origin as'. $as ."\r\n"); 
        
	//save result to var out
    while (!feof($povezava)) {
     	$out .= fgets($povezava);
    }
     
    //parse it
    $out = explode("\n", $out);
    
    //we only need route
    foreach($out as $line) {
		if (strlen(strstr($line,"route"))>0) {
		
			//replace route6 with route
			$line = str_replace("route6:", "route:", $line);
		
			//only take IP address
			$line = explode("route:", $line);
			$line = trim($line[1]);
		
			//set result
			$subnet[] = $line;
		}
    }

}


/* get all sections */
$sections = fetchSections();

/* Import */
print '<form name="asImport" id="asImport">'. "\n";

print '<div class="normalTable asImport">'. "\n";
print '<table class="normalTable asImport">'. "\n";


print '<tr class="th">'. "\n";
print '<th colspan="5">I found the following routes belonging to AS '. $as .':</th>'. "\n";
print '</tr>'. "\n";

if(sizeof($subnet) == 0) {
	print '<tr>'. "\n";
	print '<td colspan="5"><div class="error">No subnets found!</div></td>'. "\n";
	print '</tr>'. "\n";
}
else {
	$m = 0;
	foreach ($subnet as $route) {

		print '<tr>'. "\n";

		//delete
		print '<td class="img">'. "\n";
		print '<img class="info" src="css/images/deleteIP.png" title="Remove this subnet">'. "\n";
		print '</td>'. "\n";

		//subnet
		print '<td>'. "\n";
		print 'Subnet: <input type="text" name="subnet-'. $m .'" value="'. $route .'">'. "\n";
		print '</td>'. "\n";

		//section
		print '<td>'. "\n";
		print 'select section: <select name="section-'. $m .'">'. "\n";
	
		foreach($sections as $section) {
			print '<option value="'. $section['id'] .'">'. $section['name'] .'</option>';
		}
	
		print '</select><br>'. "\n";
		print '</td>'. "\n";
	
		//description
		print '<td>'. "\n";
		print 'Description: <input type="text" name="description-'. $m .'">'. "\n";
		print '</td>'. "\n";

		//VLAN
		print '<td>'. "\n";
		print 'VLAN: <input type="text" name="vlan-'. $m .'">'. "\n";
		print '</td>'. "\n";
		
		print '</tr>'. "\n";

		$m++;	
	}

	//submit
	print '<tr style="border-top:1px solid white" class="th">'. "\n";
	print '<td colspan="5" style="text-align:right">'. "\n";
	print '	<input type="submit" value="Import to database">'. "\n";
	print '</td>'. "\n";
	print '</tr>'. "\n";

}

//Result
print '<tr class="th">'. "\n";
print '<td colspan="5" style="text-align:right">'. "\n";
print '	<div class="ripeImportResult"></div>'. "\n";
print '</td>'. "\n";
print '</tr>'. "\n";


print '</table>'. "\n";
print '</div>'. "\n";
print '</form>'. "\n";
?>