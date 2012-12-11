<?php
/*
 * Print graph of Top IPv4 / IPv6 hosts by percentage
 *
 * 		Inout must be IPv4 or IPv6!
 **********************************************/

/* required functions */
/* require_once('../../functions/functions.php');  */
# no errors!
ini_set('display_errors', 0);

/*
	fetch uniques IPv4 subnets
	foreach fetch subnet and used hosts
	sort by used hosts
*/

/* fetch all subnets */
$subnets = fetchAllSubnets ();


/* go through array and only use IPv4 + subnet mask for each subnet */
unset($subnetHost);
foreach ($subnets as $subnet) 
{
	/* IPv4 number cannot be higher than 4294967295 (255.255.255.255) */
	if ( $type == "IPv4" ) {
		if ( $subnet['subnet'] < 4294967295) {
			$i								= $subnet['id'];
			$subnetHost[$i]['id']			= $subnet['id'];
			$subnetHost[$i]['subnet']		= $subnet['subnet'];
			$subnetHost[$i]['mask']			= $subnet['mask'];
			$subnetHost[$i]['description']	= $subnet['description'];
			
			/* Fix empty description */
			if(empty($subnet['description'])) {
			$subnetHost[$i]['description']	= "no_description";
			}
		}
	}
	/* IPv6 number must be higher than 4294967295 */
	if ( $type == "IPv6" ) {
		if ( $subnet['subnet'] > 4294967295) {
			$i								= $subnet['id'];
			$subnetHost[$i]['id']			= $subnet['id'];
			$subnetHost[$i]['subnet']		= $subnet['subnet'];
			$subnetHost[$i]['mask']			= $subnet['mask'];
			$subnetHost[$i]['description']	= $subnet['description'];

			/* Fix empty description */
			if(empty($subnet['description'])) {
			$subnetHost[$i]['description']	= "no_description";
			}
		}
	}
}


if(sizeof($subnetHost) != 0) {
	/* we have subnets now. Calculate usage for each */
	foreach ($subnetHost as $subnet)
	{
		$i = $subnet['id'];
		/* get count */
		$count = countIpAddressesBySubnetId ($subnet['id']);
	
		/* add to existing array */
		$subnetHost[$i]['usage'] = $count;
		
		/* unset empty subnets */
		if($subnetHost[$i]['usage'] == 0) {
			unset($subnetHost[$i]);
		}
	}
	
	/* sort by usage - keys change! */
	unset($usageSort);	

	foreach ($subnetHost as $key => $row) {
	    $usageSort[$key]  = $row['usage']; 	
	}

	array_multisort($usageSort, SORT_DESC, $subnetHost);
}



/* remove all but top 10 */
$max = sizeof($subnetHost);

for ($m = 0; $m <= $max; $m++) {
	if ($m > 10) {
		unset($subnetHost[$m]);
	}
}
?>





<!-- graph holder -->
<div id="<?php print $type; ?>top10Hosts" class="top10" style="height:200px">
	<div class="alert alert-warn"><strong>Info:</strong> No <?php print $type; ?> host configured!</div>
</div>


<!-- create data! -->
<script type="text/javascript">

var chart1; // globally available
$(document).ready(function() {
	chart1 = new Highcharts.Chart({
	
	chart: {
		renderTo: '<?php print $type; ?>top10Hosts',
 		defaultSeriesType: 'column'
	},
	colors: [
		'#AA4643' 
	],
	title: {
		text: '',
        floating: true
	},
    tooltip: {
        borderWidth: 0,
        formatter: function() {
            return '<b>' + this.point.name + '</b><br>' + this.y + ' <?php print $type; ?> addresses used';
        }
    },
    legend: {
        enabled: false
    },
    credits: {
        enabled: false
    },
	xAxis: {
		categories: [
			<?php
			if(sizeof($subnetHost) > 0) {
			foreach ($subnetHost as $subnet) {
				$subnet['subnet'] = long2ip($subnet['subnet']);
				$subnet['description'] = ShortenText($subnet['description'], 8);
				print "'" . $subnet['description'] . "',";
			}
			}
			?>
		],
		labels: {
            rotation: 300,
            align: 'right'
        }
	},
	yAxis: {
		title: {
			text: '<?php print $type; ?> address used'
		}
	},
    series: [{
         name: 'Used %',
         data: [         	
         	<?php
         		if(sizeof($subnetHost) > 0) {
				foreach ($subnetHost as $subnet) {
					print "{ name: '" . $subnet['description'] . "<br>" . transform2long($subnet['subnet']) . "/" . $subnet['mask'] . "', y:" . $subnet['usage'] . "},";
				}         	
				}
         	?>
         	]   
    }]  
    });
});
   
</script>