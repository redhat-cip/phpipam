<?php

/*
 * Print graph of Top IPv4 hosts by percentage
 **********************************************/

/* required functions */
require_once('../../functions/functions.php'); 


/*
	fetch uniques IPv4 subnets
	foreach fetch subnet and used hosts
	calculate percentage
	sort by percentage
*/

/* fetch all subnets */
$subnets = fetchAllSubnets ();


/* go through array and only use IPv4 + subnet mask for each subnet */
unset($subnetHost);
foreach ($subnets as $subnet) 
{
	/* IPv4 number cannot be higher than 4294967295 (255.255.255.255) */
	if ($type == "IPv4") {
		if ($subnet['subnet'] < 4294967295) {
			$i								= $subnet['id'];
			$subnetHost[$i]['id']			= $subnet['id'];
			$subnetHost[$i]['subnet']		= $subnet['subnet'];
			$subnetHost[$i]['mask']			= $subnet['mask'];
			$subnetHost[$i]['description']	= $subnet['description'];
		}
	}
	if ($type == "IPv6") {
		if ($subnet['subnet'] > 4294967295) {
			$i								= $subnet['id'];
			$subnetHost[$i]['id']			= $subnet['id'];
			$subnetHost[$i]['subnet']		= $subnet['subnet'];
			$subnetHost[$i]['mask']			= $subnet['mask'];
			$subnetHost[$i]['description']	= $subnet['description'];		
		}
	}
}


/* we have subnets now. Calculate usage for each */
foreach ($subnetHost as $subnet)
{
	$i = $subnet['id'];
	/* get count */
	$count = countIpAddressesBySubnetId ($subnet['id']);
	
	/* add to existing array */
	$subnetHost[$i]['usage'] = $count;
	
	/* calculate percentage */
/* 	$subnetHost[$i]['percentage'] = round( $subnetHost[$i]['usage'] / pow(2, ( 32 - $subnetHost[$i]['mask']) ), 3) * 100; */
	
	$temp = calculateSubnetDetails ( $subnetHost[$i]['usage'], $subnetHost[$i]['mask'], $subnetHost[$i]['subnet'] );
	$subnetHost[$i]['percentage'] = 100 - $temp['freehosts_percent'];
}


/* sort by usage - keys change! */
unset($usageSort);
foreach ($subnetHost as $key => $row) {
    $usageSort[$key]  = $row['percentage']; 	
}
array_multisort($usageSort, SORT_DESC, $subnetHost);


/* remove all but top 5 */
$max = sizeof($subnetHost);

for ($m = 0; $m <= $max; $m++) {
	if ($m > 10) {
		unset($subnetHost[$m]);
	}
}

?>





<!-- graph holder -->
<div id="<?php print $type; ?>top10" style="height:200px"></div>


<!-- create data! -->
<script type="text/javascript">

var chart1; // globally available
$(document).ready(function() {
	chart1 = new Highcharts.Chart({
	
	chart: {
		renderTo: '<?php print $type; ?>top10',
 		defaultSeriesType: 'column',
		borderColor: 'rgba(64,64,64,0.1)',
		borderWidth: 2,
	},
	title: {
		text: '',
        floating: true
	},
    tooltip: {
        borderWidth: 0,
        formatter: function() {
            return '<b>' + this.point.name + '</b><br>' + this.y + '% <?php print $type; ?> addresses used';
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
			foreach ($subnetHost as $subnet) {
				$subnet['subnet'] = long2ip($subnet['subnet']);
/* 				print "'" . $subnet['subnet'] . "',"; */
				$subnet['description'] = ShortenText($subnet['description'], 8);
				print "'" . $subnet['description'] . "',";
			}
			?>
		],
		labels: {
            rotation: 300,
            align: 'right'
        }
	},
	yAxis: {
/* 		max: 100, */
		title: {
			text: '% <?php print $type; ?> address used'
		}
	},
    series: [{
         name: 'Used %',
         data: [         	
         	<?php
				foreach ($subnetHost as $subnet) {
					print "{ name: '" . $subnet['description'] . "<br>" . transform2long($subnet['subnet']) . "/" . $subnet['mask'] . "', y:" . $subnet['percentage'] . "},";
				}         	
         	?>
         	]   
    }]  
    });
});
   
</script>