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

# get subnets statistic
$subnetHost = getSubnetStatsDashboard($type);

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