<?php
require_once( dirname(__FILE__) . '/../../config.php' );
require_once( dirname(__FILE__) . '/../dbfunctions.php' );

/* Find ip addresses that exist in the glpi database but not the phpipam one  */
function find_new_ips($glpi_ip_list, $phpipam_ip_list)
{
	$discovered_ip_list = array();

    for ($i = 0; $i < count($glpi_ip_list); $i += 1)
    {
        if (!array_search($glpi_ip_list[$i], $phpipam_ip_list))
        {
            array_push($discovered_ip_list, $glpi_ip_list[$i]);
        }
    }
	
	unset($GLOBALS['glpi_ip_list']);
	unset($GLOBALS['phpipam_ip_list']);
	return $discovered_ip_list;
}

/* Create the discovered ip addresses' subnets if they do not exist and return 
   the list of ip addresses that needs to be added in the phpipam database     */
function create_subnets($discovered_ip_list, $databaseglpi, $databaseipam, $section_id)
{
	$ip_to_add = array();
	$j = 0;

/* TODO : automate the subnet selection in $query */
	for ($i = 0; $i < count($discovered_ip_list); $i += 1)
	{
		$query = 'SELECT DISTINCT n.subnet, n.netmask, n.items_id, c.name '.
				 'FROM glpi_networkports n LEFT JOIN glpi_computers c '.
					'ON n.items_id = c.id '.
					'WHERE ip = \''. long2ip($discovered_ip_list[$i]) .'\' '.
					'AND subnet NOT LIKE \'94.143.110%\' AND '.
                        'subnet NOT LIKE \'94.143.111%\' AND '.
						'subnet NOT LIKE \'46.231.136%\' AND '.
                        'subnet NOT LIKE \'46.231.137%\' AND '.
                        'subnet NOT LIKE \'46.231.138%\' AND '.
                        'subnet NOT LIKE \'46.231.139%\' AND '.
						'(subnet LIKE \'94.143.11%\' OR '.
						 'subnet LIKE \'46.231.128%\' OR '.
                         'subnet LIKE \'46.231.129%\' OR '.
                         'subnet LIKE \'46.231.13%\' OR '.
                         'subnet LIKE \'198.154.188%\' OR '.
                         'subnet LIKE \'198.154.189%\');';
		$subnet = $databaseglpi->getArray($query);

		/* Check if a ip address' subnet already exists in phpipam */
		if (count($subnet) > 0)
		{
			$query = 'SELECT id '.
					 'FROM subnets '.
						'WHERE subnet = \''. ip2long($subnet[0]['subnet']) .'\' '.
						'AND mask = \''. mask2cidr($subnet[0]['netmask']) .'\';';
			$subnet_id = $databaseipam->getRow($query);

			$machine_name = $subnet[0]['name'];

			/* If the subnet already exists, append the ip to the ip_to_add list */
			if (count($subnet_id) > 0)
			{
				$ip_to_add[$j] = array($subnet_id[0], $discovered_ip_list[$i], $machine_name);
			}
			/* Else add the subnet in the phpipam database and then append the ip to the list */
			else
			{
				$query2 = 'INSERT into subnets (subnet, mask, sectionId, masterSubnetId) '.
						  'VALUES (\''. ip2long($subnet[0]['subnet']) .'\', \''. 
										mask2cidr($subnet[0]['netmask']) .'\', \''. 
										$section_id .'\', \'0\');';
				$databaseipam->executeQuery($query2);
				$subnet_id = $databaseipam->getRow($query);
				$ip_to_add[$j] = array($subnet_id[0], $discovered_ip_list[$i], $machine_name);	
			}
			$j += 1;
		}
	}
	return $ip_to_add;
}

/* Add the discovered ip addresses under the its subnet */
function add_ip_addresses($ip_to_add, $databaseipam)
{
	for ($i = 0; $i < count($ip_to_add); $i += 1)
	{
		$query = 'INSERT INTO ipaddresses (subnetId, ip_addr, dns_name, description) '.
				 'VALUES (\''. $ip_to_add[$i][0] .'\', '.
				 		 '\''. $ip_to_add[$i][1] .'\', '.
						 '\''. $ip_to_add[$i][2] .'\', '.
						 '\'Discovery\');';
		$databaseipam->executeQuery($query);
	}
}

function link_to_glpi ($databaseipam, $databaseglpi, $section_id)
{
	$query = 'SELECT ip_addr '.
			 'FROM ipaddresses '.
				'WHERE (glpiId = \'\' OR glpiId is NULL) '.
				'AND subnetId in ('.
					'SELECT id '.
					'FROM subnets '.
						'WHERE sectionId = \''. $section_id .'\');';
	$ip_not_linked = $databaseipam->getArray($query);

	for ($i = 0; $i < count($ip_not_linked); $i += 1)
	{
		$query = 'SELECT glpi_networkports.items_id '.
				 'FROM glpi_networkports '.
					'inner join glpi_computers '.
						    'on glpi_networkports.ip = \''. long2ip($ip_not_linked[$i]['ip_addr']) .'\' '.
					'AND glpi_networkports.items_id = glpi_computers.id '.
					'AND glpi_computers.is_deleted = 0;';
		$glpi_id = $databaseglpi->getRow($query);

		if (count($glpi_id > 0))
		{
			$query = 'UPDATE ipaddresses '.
					 'SET glpiID = \''. $glpi_id[0] .'\' '.
						'WHERE ip_addr = \''. $ip_not_linked[$i]['ip_addr'] .'\'';
			$databaseipam->executeQuery($query);
		}
	}
}

function get_section_id($databaseipam)
{
    /* Get the discovery section's id */
    $query = 'SELECT id '.
             'FROM sections '.
                'WHERE name = \'Discovery\';';
    $section_id = $databaseipam->getRow($query);

	return $section_id[0];
}

/* Function to convert a mask into a cidr integer */
function mask2cidr($mask){
  $long = ip2long($mask);
  $base = ip2long('255.255.255.255');
  return 32-log(($long ^ $base)+1,2);
}


/* Open a connection to both the phpipam and glpi data */
$databaseipam = new database($db['host'], $db['user'], $db['pass'], $db['name']);
$databaseglpi = new database($db['glpi_host'], $db['glpi_user'], $db['glpi_pass'], $db['glpi_name']);

/* Gather the ip list (machine list) from the glpi database */
$query_glpi = 'SELECT DISTINCT ip '.
			  'FROM glpi_networkports '.
				'WHERE ip IS NOT NULL '.
				'AND ip NOT LIKE \'\' '.
				'AND ip NOT LIKE \'10.%\' '.
				'AND ip NOT LIKE \'127%\' '.
				'AND ip NOT LIKE \'192.168%\';';
$glpi_ip_list = $databaseglpi->getArray($query_glpi);

/* Gather the ip list (machine list) from the phpipam database */
$query_ipam = 'SELECT DISTINCT ip_addr '.
			  'FROM ipaddresses '.
				'WHERE ip_addr != \'0\';';
$phpipam_ip_list = $databaseipam->getArray($query_ipam);

/* Rearrange the glpi and phpipam ip list for better comparison */
for ($i = 0; $i < count($phpipam_ip_list); $i += 1)
{
	$phpipam_ip_list[$i] = $phpipam_ip_list[$i]['ip_addr'];
}
for ($i = 0; $i < count($glpi_ip_list); $i += 1)
{
	$glpi_ip_list[$i] = ip2long($glpi_ip_list[$i]['ip']);
}


$discovered_ip_list = find_new_ips($glpi_ip_list, $phpipam_ip_list);
$section_id = get_section_id($databaseipam);
$ip_to_add = create_subnets($discovered_ip_list, $databaseglpi, $databaseipam, $section_id);
add_ip_addresses($ip_to_add, $databaseipam);
link_to_glpi($databaseipam, $databaseglpi, $section_id);

$databaseglpi->close();
$databaseipam->close();
?>
