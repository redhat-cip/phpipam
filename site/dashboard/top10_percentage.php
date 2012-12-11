<?php

/*
 * Print graph of Top IPv4 hosts by percentage
 **********************************************/

/* required functions */
/* require_once('../../functions/functions.php');  */
# no errors!
ini_set('display_errors', 0);

# get subnets statistic
$subnetHost = getSubnetStatsDashboard("IPv4", "0");

if(sizeof($subnetHost) != 0) {
	$i = 0;
	/* we have subnets now. Calculate usage for each */
	foreach ($subnetHost as $subnet)
	{
		$temp = calculateSubnetDetails ( $subnetHost[$i]['usage'], $subnetHost[$i]['mask'], $subnetHost[$i]['subnet'] );
		$subnetHost[$i]['percentage'] = 100 - $temp['freehosts_percent'];
		
		$i++;
	}
	
	/* sort by percentage - keys change! */
	unset($usageSort);
	foreach ($subnetHost as $key => $row) {
	    $usageSort[$key]  = $row['percentage']; 	
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
<div id="<?php print $type; ?>top10" class="top10" style="height:200px;">
	<div class="alert alert-warn"><strong>Info:</strong> No <?php print $type; ?> host configured!</div>
</div>


<!-- create data! -->
<script type="text/javascript">

var chart1; // globally available
$(document).ready(function() {
	chart1 = new Highcharts.Chart({
	
	chart: {
		renderTo: '<?php print $type; ?>top10',
 		defaultSeriesType: 'column'
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
			if(sizeof($subnetHost) > 0) {
			foreach ($subnetHost as $subnet) {
				$subnet['subnet'] = long2ip($subnet['subnet']);
/* 				print "'" . $subnet['subnet'] . "',"; */
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
/* 		max: 100, */
		title: {
			text: '% <?php print $type; ?> address used'
		}
	},
    series: [{
         name: 'Used %',
         data: [         	
         	<?php
         		if(sizeof($subnetHost) > 0) {
				foreach ($subnetHost as $subnet) {
					print "{ name: '" . $subnet['description'] . "<br>" . transform2long($subnet['subnet']) . "/" . $subnet['mask'] . "', y:" . $subnet['percentage'] . "},";
				}   
				}      	
         	?>
         	]   
    }]  
    });
});
   
</script>