<?php

/**
 * Script to display available VLANs
 *
 */

/* include required scripts */
require_once('../../functions/functions.php');

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get all VLANs and subnet descriptions */
$vlans = getAllVlans (true);

/*  print VLANs */
print '<div class="normalTable vlans">';
print '<table class="normalTable vlans">';

/* headers */
print '<tr class="th">' . "\n";
print ' <th>Number</th>' . "\n";
print ' <th>Name</th>' . "\n";
print ' <th>Description</th>' . "\n";
print ' <th>Subnet</th>' . "\n";
print ' <th>Master Subnet</th>' . "\n";
print ' <th>Used</th>' . "\n";
print ' <th>free [%]</th>' . "\n";
print ' <th>Requests</th>' . "\n";
print ' <th class="lock" title="Admin lock"></th>' . "\n";
print '</tr>' . "\n";


foreach ($vlans as $vlan) {

/*
echo "VLAN: " . $vlan['VLAN'] . "<br/>";
echo "subnetId: " . $vlan['subnetId'] . "<br/>";
echo "number: " . $vlan['number'] . "<br/>";
echo "<hr>";
*/
$section = getSectionDetailsById($vlan['sectionId']);

/* check if it is master */
if( ($vlan['masterSubnetId'] == 0) || (empty($vlan['masterSubnetId'])) ) {
        $masterSubnet = true;
}
else {
        $masterSubnet = false;
}

//identify slaves for CSS
if(!$masterSubnet) {
        print '<tr class="vlanLink slaveSubnet"';
}
else {
        print '<tr class="vlanLink masterSubnet"';
}

//reformat empty VLAN
if(empty($vlan['VLAN']) || $vlan['VLAN'] == 0) {
        $vlan['VLAN'] = "";
}

print ' sectionId="'. $section['id'] .'" subnetId="'. $vlan['subnetId'] .'" link="'. $section['name'] .'|'. $vlan['subnetId'] .'">' . "\n";
print ' <td><dd>'. $vlan['number']         .'</dd></td>' . "\n";
print ' <td><dd>'. $vlan['name']           .'</dd></td>' . "\n";
print ' <td><dd>'. $vlan['description'] .'</dd></td>' . "\n";
if ($vlan['subnetId'] != null) {
        print ' <td>'. transform2long($vlan['subnet']) .'/'. $vlan['mask'] .'</td>' . "\n";

        if($masterSubnet) {
                print ' <td>/</td>' . "\n";

        }
        else {
                $master = getSubnetDetailsById ($vlan['masterSubnetId']);
        print ' <td>'. transform2long($master['subnet']) .'/'. $master['mask'] .'</td>' . "\n";
        }

        //details
        if( (!$masterSubnet) || (!subnetContainsSlaves($vlan['subnetId']))) {
                $ipCount = countIpAddressesBySubnetId ($vlan['subnetId']);
                $calculate = calculateSubnetDetails ( gmp_strval($ipCount), $vlan['mask'], $vlan['subnet'] );

                print ' <td class="used">'. reformatNumber($calculate['used']) .'/'. reformatNumber($calculate['maxhosts']) .'</td>'. "\n";
                print ' <td class="free">'. reformatNumber($calculate['freehosts_percent']) .' %</td>';
        }

        //allow requests
        if($vlan['allowRequests'] == 1) {
                print '<td class="allowRequests requests" title="IP requests are enabled">enabled</td>';
        }
        else {
                print '<td class="allowRequests"></td>';
        }

        //check if it is locked for writing
        if(isSubnetWriteProtected($vlan['subnetId'])) {
                print '<td class="lock" title="Subnet is locked for writing!"></td>';
        } else {
                print '<td class="nolock"></td>';
        }
}
else {
        print '<td>---</td>'. "\n";
        print '<td>---</td>'. "\n";
        print '<td>---</td>'. "\n";
        print '<td>---</td>'. "\n";
        print '<td>---</td>'. "\n";
}




print '</tr>' . "\n";

}


print '</table>';
print '</div>';

print '<div class="error" hidden></div>';
?>
