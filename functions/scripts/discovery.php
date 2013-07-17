<?php
require_once( dirname(__FILE__) . '/../../config.php' );
require_once( dirname(__FILE__) . '/../dbfunctions.php' );
require_once( dirname(__FILE__) . '/../functions-network.php' );

/* Find ip addresses that exist in the glpi database but not the phpipam one  */
function find_new_ips($glpi_ip_list, $phpipam_ip_list)
{
	$discovered_ip_list = array();
	foreach ($glpi_ip_list as $ip)
	{
		if (!array_search($ip, $phpipam_ip_list))
        {
            array_push($discovered_ip_list, $ip);
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

	$query = 'SELECT DISTINCT n.id, n.subnet, n.netmask, n.items_id, n.ip, c.name '.
             'FROM glpi_networkports n LEFT JOIN glpi_computers c '.
                'ON n.items_id = c.id '.
				'WHERE n.ip IN (';
	foreach($discovered_ip_list as $ip)
	{
		$query .= '\''. long2ip($ip) .'\',';
	}
	$query = substr_replace($query, ') ', -1);
	$query .= 'AND c.is_deleted = 0 AND '.
                  '(n.subnet LIKE '.subnet_query_builder();
  
	$subnets = $databaseglpi->getArray($query);

	$query = 'SELECT id, subnet, mask '.
             'FROM subnets WHERE ';
	foreach ($subnets as &$subnet)
	{
		$subnet['subnet'] = ip2long($subnet['subnet']);
		$subnet['netmask'] = mask2cidr($subnet['netmask']);
		$query .= '(subnet = \''. $subnet['subnet'] .'\' '.
				  'AND mask = \''. $subnet['netmask'] .'\') OR ';
	}
	$query = substr_replace($query, ';', -4, -1);
	$existing_subnets = $databaseipam->getArray($query);

	foreach ($subnets as &$subnet)
	{
		$query_subnet_id = 'SELECT id FROM subnets WHERE '.
                 'subnet = \''.$subnet['subnet'].'\' AND '.
                 'mask = \''.$subnet['netmask'].'\';';
        $subnet_id = $databaseipam->getRow($query_subnet_id);

		if (count($subnet_id) == 0)
		{
			$query = 'INSERT INTO subnets (subnet, mask, sectionId, masterSubnetId, description) VALUES '.
            	     '(\''.$subnet['subnet'].'\', '.
                	 '\''.$subnet['netmask'].'\', '.
                 	 '\''.$section_id.'\', '.
                 	 '\'0\', \'Discovered subnet\');';
        	$databaseipam->executeQuery($query);
			$subnet_id = $databaseipam->getRow($query_subnet_id);
		}

		$query = 'SELECT id FROM ipaddresses WHERE ip_addr = \''.ip2long($subnet['ip']).'\'';
		$exists = $databaseipam->getRow($query);

		if( (count($exists)) == 0)
		{
		$ip_to_add = array_merge($ip_to_add,array(array("subnet_id" => $subnet_id[0], "ip" => $subnet['ip'], "name" => $subnet['name'])));
		}
	}
	return $ip_to_add;
}

function subnet_query_builder()
{
	global $glpisubnets;
	$query = '';
	$subnets = explode(',', $glpisubnets);

	foreach ($subnets as $subnet)
	{
    	$cidr = explode('/', $subnet);
    	$cidr = parseIpAddress($cidr[0], $cidr[1]);

    	$first = explode('.', $cidr['network']);
    	$last = explode('.', $cidr['broadcast']);
    	$diff = $last[2] - $first[2];

    	if (!($subnet === end($subnets)))
    	{
        	for ($i = 0; $i <= $diff; $i += 1)
        	{
        	    $query .= '\''.$first[0].'.'.$first[1].'.'.($first[2]+$i).'%\' '.
            	          'OR n.subnet LIKE ';
        	}
    	}
    	else
    	{
        	for ($i = 0; $i <= $diff; $i += 1)
        	{
            	$query .= '\''.$first[0].'.'.$first[1].'.'.($first[2]+$i).'%\'';
        	}
        	$query .= ') group by n.ip;';
    	}
	}
	return $query;
}

/* Add the discovered ip addresses under the its subnet */
function add_ip_addresses($ip_to_add, $databaseipam)
{
	$query = 'INSERT INTO ipaddresses (subnetId, ip_addr, dns_name, description) VALUES ';

	foreach ($ip_to_add as $ip)
	{
		$query .= ' (\''. $ip['subnet_id'] .'\','.
				  ' \''. ip2long($ip['ip']) .'\','.
                  ' \''. $ip['name'] .'\','.
                  ' \'Discovered ip address\'),';
	}
	$databaseipam->executeQuery(substr_replace($query, ';', -1));
}

function link_to_glpi ($databaseipam, $databaseglpi, $section_id)
{
	$query = 'SELECT ip_addr '.
			 'FROM ipaddresses '.
				'WHERE (glpiId = \'\' OR glpiId is NULL);';
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

function updateHostsName($databaseipam, $databaseglpi)
{
	$query = "SELECT glpiId, dns_name FROM ipaddresses WHERE glpiId != '';";
	$ipAddresses = $databaseipam->getArray($query);

	$query = 'SELECT id, name from glpi_computers WHERE id IN (';
	foreach ($ipAddresses as $ip)
	{
    	$query .= "'$ip[glpiId]',";
	}
	$query = substr_replace($query, ');', -1);
	
	$names = $databaseglpi->getArray($query);

	foreach ($names as $name)
	{
    	if ($name['name'] != '')
    	{
        	$query = "UPDATE ipaddresses SET dns_name = '$name[name]' WHERE glpiId = '$name[id]';";
        	$databaseipam->executeQuery($query);
    	}
	}
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
			  'FROM ipaddresses;';
$phpipam_ip_list = $databaseipam->getArray($query_ipam);

/* Rearrange the glpi and phpipam ip list for better comparison */
foreach($glpi_ip_list as &$ip)
{
	$ip = ip2long($ip['ip']);
}
foreach($phpipam_ip_list as &$ip)
{
	$ip = $ip['ip_addr'];
}

$discovered_ip_list = find_new_ips($glpi_ip_list, $phpipam_ip_list);
$section_id = get_section_id($databaseipam);
$ip_to_add = create_subnets($discovered_ip_list, $databaseglpi, $databaseipam, $section_id);

if (count($ip_to_add) > 0)
{
	add_ip_addresses($ip_to_add, $databaseipam);
}

link_to_glpi($databaseipam, $databaseglpi, $section_id);
updateHostsName($databaseipam, $databaseglpi);

$databaseglpi->close();
$databaseipam->close();
?>

<!-- header -->
<div class="pHeader">Discovery result</div>
<div class="pContent">
    <table class="table table-noborder table-condensed">

    <?php
        if (count($ip_to_add) == 0)
        {
            echo '<td class="info">No new ip addresses discovered.</td>'.
                 '</div></table>'.
                 '<div class="pFooter">'.
                     '<button class="btn btn-small hidePopups">Done</button>'.
                 '</div>';
            exit;
        }

        foreach($ip_to_add as $ip)
        {
    ?>

    <tr>
        <td class="middle"><?php echo 'ip : '.$ip['ip']; ?></td>
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

