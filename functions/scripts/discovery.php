<?php
require_once( dirname(__FILE__) . '/../../config.php' );
require_once( dirname(__FILE__) . '/../dbfunctions.php' );
require_once( dirname(__FILE__) . '/../functions-network.php' );

/* Compute the ip ranges that are managed and specified in config.php */
function getIpRanges()
{
	global $glpisubnets;
	$ip_ranges = [];
	$subnets = explode(',', $glpisubnets);

	foreach ($subnets as $subnet)
	{
		$cidr = explode('/', $subnet);
		$cidr = parseIpAddress($cidr[0], $cidr[1]);
		$range = array(ip2long($cidr['network']),ip2long($cidr['broadcast']));
		array_push($ip_ranges, $range);
	}

	return $ip_ranges;
}

/* Get all the ip addresses from phpipam */
function getIpamIpList($database_ipam)
{
	$query = 'SELECT DISTINCT ip_addr '.
				  'FROM ipaddresses;';
	$ipam_ip_list = $database_ipam->getArray($query);

	foreach($ipam_ip_list as &$ip)
	{
    	$ip = $ip['ip_addr'];
	}

	return $ipam_ip_list;
}

/* Get all the ip addresses from glpi that match the managed subnets and format the list */
function getGlpiIpList($database_glpi, $ip_ranges)
{
	$query = 'SELECT DISTINCT n.ip, n.subnet, n.netmask, c.name '.
			 'FROM glpi_networkports n LEFT JOIN glpi_computers c '.
			 'ON n.items_id = c.id '.
				'WHERE c.is_deleted = 0 '.
    	        'AND n.ip IS NOT NULL '.
        	    'AND n.ip NOT LIKE \'\' '.
            	'AND n.ip NOT LIKE \'10.%\' '.
                'AND n.ip NOT LIKE \'127%\' '.
                'AND n.ip NOT LIKE \'192.168%\' '.
				'GROUP BY n.ip;';
	$glpi_ip_list = $database_glpi->getArray($query);

	foreach ($glpi_ip_list as $key => $glpi_ip)
	{
    	$ip = ip2long($glpi_ip['ip']);
    	$in_range = 0;

    	foreach ($ip_ranges as $range)
    	{
        	if ($ip >= $range[0] && $ip <= $range[1])
        	{
				// Convert the 32bits mask into an integer
				$long = ip2long($glpi_ip['netmask']);
  				$base = ip2long('255.255.255.255');
  				$mask = 32-log(($long ^ $base)+1,2);


            	$glpi_ip_list[$key]['ip'] = ip2long($glpi_ip['ip']);
				$glpi_ip_list[$key]['subnet'] = ip2long($glpi_ip['subnet']);
				$glpi_ip_list[$key]['netmask'] = $mask;
            	$in_range = 1;
        	}
    	}
    	if ($in_range == 0) {unset($glpi_ip_list[$key]);}
	}

	return $glpi_ip_list;
}

/* Compare the ipam ip list with the glpi one and find missing ip addresses */
function findNewIpAddresses($glpi_ip_list, $ipam_ip_list)
{
	$discovered_ip_list = [];

	foreach ($glpi_ip_list as $glpi_ip)
	{
		if (!array_search($glpi_ip['ip'], $ipam_ip_list))
		{
			array_push($discovered_ip_list, $glpi_ip);
		}
	}

	return $discovered_ip_list;
}

/* Get the section id of the Discovery section' */
function getDiscoverySectionId($database_ipam)
{
    $query = 'SELECT id '.
             'FROM sections '.
                'WHERE name LIKE \'_iscovery\';';
    $discovery_section_id = $database_ipam->getRow($query);

    return $discovery_section_id[0];
}

/* Check if each subnet exists, create it if need be, then add its id to the discovered ip list  */
function createNewSubnets(&$discovered_ip_list, $discovery_section_id, $database_ipam)
{
	foreach ($discovered_ip_list as $key => $discovered_ip)
	{
		$query = "SELECT id ".
				 "FROM subnets ".
					"WHERE subnet LIKE '$discovered_ip[subnet]' ".
					"AND mask LIKE $discovered_ip[netmask];";
		$subnet_id = $database_ipam->getRow($query);

		if (count($subnet_id) > 0)
		{
			$discovered_ip_list[$key]['subnetId'] = $subnet_id[0];
		}
		else
		{
			$query = "INSERT INTO subnets (subnet, mask, sectionId, masterSubnetId, description) ".
					 "VALUES ($discovered_ip[subnet], $discovered_ip[netmask], $discovery_section_id, 0, 'Discovered subnet');";
			$database_ipam->executeQuery($query);
			$discovered_ip_list[$key]['subnetId'] = $database_ipam->insert_id;
		}
	}
}

/* Add the discovered ip addresses to the ipam database */
function createNewIpAddresses($discovered_ip_list, $database_ipam)
{
	$query = "REPLACE INTO ipaddresses (ip_addr, dns_name, description, subnetId) VALUES ";
			 foreach ($discovered_ip_list as $ip)
			 {
				$query .= "('$ip[ip]', '$ip[name]', 'Discovered ip', '$ip[subnetId]'), ";
			 }
	$database_ipam->executeQuery(substr_replace($query, ';', -2)); 
}

/* Find/Update the glpi id for each ip addresses in the ipam database */
function link_ip_to_glpi($ipam_ip_list, $database_glpi, $database_ipam)
{
	$query = "SELECT n.ip, n.items_id ".
			 "FROM glpi_networkports n ".
				"INNER JOIN glpi_computers c ".
				"ON n.items_id = c.id ".
				"AND n.ip IN (";
	foreach ($ipam_ip_list as $ip)
	{
		$query .= "'".long2ip($ip)."', ";
	}
	$query = substr_replace($query, ') ', -2);
	$query .= 	"AND c.is_deleted = 0;";
	$links = $database_glpi->getArray($query);

	foreach ($links as $link)
	{
		$query = "UPDATE ipaddresses ".
				  "SET glpiId = '$link[items_id]' ".
					"WHERE ip_addr = '".ip2long($link['ip'])."';";
		$database_ipam->executeQuery($query);
	}
}

/* Update ipam hostnames to match the glpi ones */
function updateHostsName($database_ipam, $database_glpi)
{
	$query = "SELECT glpiId ".
			 "FROM ipaddresses ".
				"WHERE glpiId != '';";
	$hosts = $database_ipam->getArray($query);

	$query = "SELECT id, name ".
			 "FROM glpi_computers ".
				"WHERE id IN (";
	foreach ($hosts as $host)
	{
		$query .= "$host[glpiId], ";
	}
	$hostnames = $database_glpi->getArray(substr_replace($query, ');', -2));

	foreach ($hostnames as $hostname)
	{
		$query = "UPDATE ipaddresses ".
				  "SET dns_name = '$hostname[name]' ".
					"WHERE glpiId = '$hostname[id]';";
		$database_ipam->executeQuery($query);
	}
}


/* Open a connection to both the phpipam and glpi data */
$database_ipam = new database($db['host'], $db['user'], $db['pass'], $db['name']);
$database_glpi = new database($db['glpi_host'], $db['glpi_user'], $db['glpi_pass'], $db['glpi_name']);


$ip_ranges = getIpRanges();
$ipam_ip_list = getIpamIpList($database_ipam);
$glpi_ip_list = getGlpiIpList($database_glpi, $ip_ranges);
$discovered_ip_list = findNewIpAddresses($glpi_ip_list, $ipam_ip_list);
$discovery_section_id = getDiscoverySectionId($database_ipam);

createNewSubnets($discovered_ip_list, $discovery_section_id, $database_ipam);
createNewIpAddresses($discovered_ip_list, $database_ipam);
link_ip_to_glpi($ipam_ip_list, $database_glpi, $database_ipam);
updateHostsName($database_ipam, $database_glpi);

$database_glpi->close();
$database_ipam->close();
?>

<!-- header -->
<div class="pHeader">Discovery result</div>
<div class="pContent">
    <table class="table table-noborder table-condensed">

    <?php
        if (count($discovered_ip_list) == 0)
        {
            echo '<td class="info">No new ip addresses discovered.</td>'.
                 '</div></table>'.
                 '<div class="pFooter">'.
                     '<button class="btn btn-small hidePopups">Done</button>'.
                 '</div>'.
				 '</table>'.
				 '</div>';
            exit;
        }

        foreach($discovered_ip_list as $ip)
        {
    ?>

    <tr>
        <td class="middle"><?php echo 'ip : '.long2ip($ip['ip']); ?></td>
        <td class="info"><?php echo '   hostname : '.$ip['name']; ?></td>
    </tr>

    <?php
        }
    ?>

    </div>
</table>

<div class="pFooter">
    <button class="btn btn-small hidePopup3">Done</button>
</div>

